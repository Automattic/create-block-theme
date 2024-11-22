<?php

/**
 * The core Create Block Theme plugin class.
 *
 * @since      0.0.2
 * @package    Create_Block_Theme
 * @subpackage Create_Block_Theme/includes
 * @author     WordPress.org
 */
#[AllowDynamicProperties]
class CBT_Plugin
{

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    0.0.2
	 */
	public function __construct()
	{

		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    0.0.2
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-create-block-theme-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-create-block-theme-api.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-create-block-theme-editor-tools.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-create-block-theme-admin-landing.php';

		$this->loader = new CBT_Plugin_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.0.2
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_api    = new CBT_Theme_API();
		$editor_tools  = new CBT_Editor_Tools();
		$admin_landing = new CBT_Admin_Landing();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.2
	 */
	public function run()
	{
		$this->loader->run();
	}
}

function CBT_register_theme_synced_block_patterns() {

	$patterns = CBT_get_theme_block_patterns();

	foreach ($patterns as $pattern) {

		// if it is a synced pattern manage the post
		if ($pattern['synced'] === 'yes') {

			//search for post by slug
			$pattern_post = get_page_by_path(sanitize_title($pattern['slug']), OBJECT, 'wp_block');

			if ($pattern_post) {
				$post_id = $pattern_post->ID;
				// the synced pattern already exists
				// should we update it?
				// That's the question of the day!
				// update the post with the content
				wp_update_post(array(
					'ID' => $post_id,
					'post_content' => $pattern['content'],
				));
			} else {
				$post_id = wp_insert_post(array(
					'post_title' => $pattern['title'],
					'post_name' => $pattern['slug'],
					'post_content' => $pattern['content'],
					'post_type' => 'wp_block',
					'post_status' => 'publish',
					'ping_status' => 'closed',
					'comment_status' => 'closed',
				));
			}

			// add the pattern as an UNsynced pattern TOO so that it can be used in templates.
			// this pattern injects a synced pattern block as the content.
			register_block_pattern(
				$pattern['slug'],
				array(
					'title'   => $pattern['title'],
					'inserter' => false,
					'content' => '<!-- wp:block {"ref":' . $post_id . '} /-->',
				)
			);
		} else {
			// register the pattern and hide from the inserter
			register_block_pattern(
				$pattern['slug'],
				array(
					'title'   => $pattern['title'],
					'inserter' => false,
					'content' => $pattern['content'],
				)
			);
		}
	}
}

function CBT_render_pattern($pattern_file) {
	ob_start();
	include $pattern_file;
	return ob_get_clean();
}


function CBT_get_theme_block_patterns()
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

			$pattern_data = get_file_data( $pattern_file, $default_headers );

			$pattern_data['pattern_file'] = $pattern_file;
			$pattern_data['content'] = CBT_render_pattern($pattern_file);

			$all_patterns[] = $pattern_data;
		}
	}

	return $all_patterns;
}

function format_pattern_for_response( $pattern_data ) {
	return array(
		'id' => $pattern_data['id'] ?? 'CBT_' . $pattern_data['slug'],
		'file_path' => $pattern_data['pattern_file'],
		'slug' => $pattern_data['slug'] ?? null,
		'status' => 'publish',
		'type' => 'wp_block',
		'title' => array(
			'raw' => $pattern_data['title'] ?? null,
		),
		'content' => array(
			'raw' => $pattern_data['content'] ?? null,
			'protected' => false,
			'block_version' => null,
		),
		'excerpt' => array(
			'raw' => $pattern_data['description'] ?? null,
			'rendered' => null,
			'protected' => false,
		),
		'wp_pattern_category' => array(),
		'wp_pattern_sync_status' => $pattern_data['synced'] === 'yes' ? "" : "unsynced",
	);
}

// Add in the block patterns from the theme to the collection of blocks
function CBT_filter_blocks_api_response($response, $server, $request)
{
	if ($request->get_route() !== '/wp/v2/blocks') {
		return $response;
	}

	$data = $response->get_data();
	$patterns = CBT_get_theme_block_patterns();

	// filter out the synced patterns
	// filter out the patterns marked hidden
	$patterns = array_filter($patterns, function ($pattern) {
		return $pattern['synced'] !== 'yes' && $pattern['inserter'] !== 'no';
	});



	$patterns = array_map( 'format_pattern_for_response', $patterns);

	$response->set_data(array_merge($data, $patterns));

	return $response;
}

// Handle CBT block updates
function CBT_filter_block_update($result, $server, $request)
{
	$route = $request->get_route();

	if ( strpos( $route, '/wp/v2/blocks/' ) !== 0 ) {
		return $result;
	}

	if ( str_contains($route, 'CBT_')) {
		$pattern_slug = ltrim(strstr($route, 'CBT_'), 'CBT_');
	}
	else {
		//get the slug for the post with the pattern id
		$pattern_id = $request->get_param('id');
		if ( ! $pattern_id ) {
			//get the ID from the route
			$pattern_id = str_replace('/wp/v2/blocks/', '', $route);
		}
		$pattern_slug = get_post_field('post_name', $pattern_id);
	}

	$theme_patterns = CBT_get_theme_block_patterns();

	// if a pattern with a matching slug exists in the theme, do work on it
	foreach ($theme_patterns as $pattern) {

		if (sanitize_title($pattern['slug']) === sanitize_title($pattern_slug)) {

			// if the request is a GET, return the pattern content
			if ($request->get_method() === 'GET') {
				// which we do below
			}

			// if the request is a PUT or POST, create/update the pattern content file
			if ($request->get_method() === 'PUT' || $request->get_method() === 'POST') {
				$block_content = $request->get_param('content');
				$synced_status = $pattern['synced'] === 'yes' ? 'Synced: yes' : '';
				$file_content = <<<PHP
				<?php
				/**
				 * Title: {$pattern['title']}
				 * Slug: {$pattern['slug']}
				 * Categories: {$pattern['categories']}
				 * {$synced_status}
				 */
				?>
				{$block_content}
				PHP;

				file_put_contents($pattern['pattern_file'], $file_content);

				$pattern['content'] = $block_content;
			}

			// if the request is a DELETE, delete the pattern content file
			if ($request->get_method() === 'DELETE') {
				unlink($pattern['pattern_file']);
			}

			// if we pulled the real ID then we also want to do work on the database;
			// return null to allow the natural action to happen too.
			if ( $pattern_id ) {
				return null;
			}

			return rest_ensure_response(format_pattern_for_response($pattern));
		}

	}

	return $result;
}


// don't register the theme block patterns
remove_action('init', '_register_theme_block_patterns');
add_action('init', 'CBT_register_theme_synced_block_patterns');

// add the theme block patterns to the block collection
add_filter('rest_post_dispatch', 'CBT_filter_blocks_api_response', 10, 3);
add_filter( 'rest_pre_dispatch', 'CBT_filter_block_update', 10, 3 );
