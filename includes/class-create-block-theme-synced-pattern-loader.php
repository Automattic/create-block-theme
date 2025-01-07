<?php


/**
 * The api functionality of the plugin leveraged by the site editor UI.
 *
 * @package    Create_Block_Theme
 * @subpackage Create_Block_Theme/admin
 * @author     WordPress.org
 */
class CBT_Synced_Pattern_Loader
{

	public function __construct()
	{
		remove_action('init', '_register_theme_block_patterns');
		add_action('init', [$this, 'CBT_register_theme_block_patterns']);
	}

	public function CBT_register_theme_block_patterns()
	{
		$registry = WP_Block_Patterns_Registry::get_instance();
		$patterns = self::CBT_get_theme_block_patterns();

		foreach ($patterns as $pattern) {

			if ( $registry->is_registered( $pattern['slug'] ) ) {
				continue;
			}

			// if the pattern is hidden from the inserter just register it, don't add as a post
			if ($pattern['inserter'] === 'no') {
				$pattern['inserter'] = false;
				register_block_pattern( $pattern['slug'], $pattern );
				continue;
			}

			//search for post by slug
			$pattern_post = get_page_by_path(sanitize_title($pattern['slug']), OBJECT, 'wp_block');

			if ($pattern_post) {
				// the pattern already exists
				$post_id = $pattern_post->ID;
			}

			else {
				$post_id = wp_insert_post(array(
					'post_title' => $pattern['title'],
					'post_name' => $pattern['slug'],
					'post_content' => $pattern['content'],
					'post_type' => 'wp_block',
					'post_status' => 'publish',
					'ping_status' => 'closed',
					'comment_status' => 'closed',
					'meta_input' => array(
						'wp_pattern_sync_status' => $pattern['synced'] === 'yes' ? "" : "unsynced",
					),
				));

				$categories = self::get_pattern_categories( $pattern );

				if ( ! empty( $categories ) ) {
					wp_set_object_terms( $post_id, $categories, 'wp_pattern_category' );
				}
			}

			if ( $pattern['synced'] === 'yes' ) {
				// register as an unsynced pattern TOO so that it can be used as pattern blocks in templates
				// this pattern injects a synced pattern block as the content.
				register_block_pattern(
					$pattern['slug'],
					array(
						'title'   => $pattern['title'],
						'inserter' => false,
						'content' => '<!-- wp:block {"ref":' . $post_id . '} /-->',
					)
				);
			}
		}
	}

	private static function CBT_render_pattern($pattern_file)
	{
		ob_start();
		include $pattern_file;
		return ob_get_clean();
	}

	/**
	 * Get all the block patterns from the theme.
	 * Includes the non-standard 'Synced' key.
	 *
	 * @return array
	 */

	public static function CBT_get_theme_block_patterns()
	{
		$default_headers = array(
			'title'         => 'Title',
			'slug'          => 'Slug',
			'description'   => 'Description',
			'viewportWidth' => 'Viewport Width',
			'inserter'      => 'Inserter',
			'categories'    => 'Categories',
			'keywords'      => 'Keywords',
			'blockTypes'    => 'Block Types',
			'postTypes'     => 'Post Types',
			'templateTypes' => 'Template Types',
			'synced'	=> 'Synced',
		);

		$all_patterns = array();
		$themes   = array();
		$theme    = wp_get_theme();
		$themes[] = $theme;

		if ($theme->parent()) {
			$themes[] = $theme->parent();
		}

		foreach ($themes as $theme) {

			$pattern_files = glob($theme->get_stylesheet_directory() . '/patterns/*.php');

			foreach ($pattern_files as $pattern_file) {

				$pattern_data = get_file_data($pattern_file, $default_headers);

				$pattern_data['pattern_file'] = $pattern_file;
				$pattern_data['content'] = self::CBT_render_pattern($pattern_file);

				$all_patterns[] = $pattern_data;
			}
		}

		return $all_patterns;
	}

	private function get_pattern_categories($pattern_data)
	{
		//get the default pattern categories
		$registered_pattern_categories = WP_Block_Pattern_Categories_Registry::get_instance()->get_all_registered();

		$category_ids = array();
		$categories = explode(',', $pattern_data['categories']);
		$terms = get_terms(array(
			'taxonomy' => 'wp_pattern_category',
			'hide_empty' => false,
			'fields' => 'all',

		));
		foreach ($categories as $category) {
			$category = sanitize_title($category);
			$found = false;
			foreach ($terms as $term) {
				if (sanitize_title($term->name) === $category || sanitize_title($term->slug) === $category) {
					$category_ids[] = $term->term_id;
					$found = true;
					break;
				}
			}
			if ( ! $found ) {
				// See if it's in the registered_pattern_categories
				foreach ($registered_pattern_categories as $registered_category) {
					if (
						( isset($registered_category['slug']) && sanitize_title($registered_category['slug']) === $category ) ||
						( isset($registered_category['name']) && sanitize_title($registered_category['name']) === $category)) {
						$term = wp_insert_term($registered_category['name'], 'wp_pattern_category', array(
							'slug' => $registered_category['slug'],
							'description' => $registered_category['description'] ?? '',
						));
						$terms[] = (object) $term;
						$category_ids[] = $term['term_id'];
						$found = true;
						break;
					}
				}
			}
			// if the term is still not found then I guess we're just out of luck.
		}
		return $category_ids;
	}
}
