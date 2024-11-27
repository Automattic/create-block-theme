<?php

class CBT_Theme_Patterns {
	public static function pattern_from_template( $template, $new_slug = null ) {
		$theme_slug      = $new_slug ? $new_slug : wp_get_theme()->get( 'TextDomain' );
		$pattern_slug    = $theme_slug . '/' . $template->slug;
		$pattern_content = <<<PHP
		<?php
		/**
		 * Title: {$template->slug}
		 * Slug: {$pattern_slug}
		 * Inserter: no
		 */
		?>
		{$template->content}
		PHP;

		return array(
			'slug'    => $pattern_slug,
			'content' => $pattern_content,
		);
	}

	public static function pattern_from_wp_block( $pattern_post ) {
		$pattern               = new stdClass();
		$pattern->id           = $pattern_post->ID;
		$pattern->title        = $pattern_post->post_title;
		$pattern->name         = sanitize_title_with_dashes( $pattern_post->post_title );
		$pattern->slug         = wp_get_theme()->get( 'TextDomain' ) . '/' . $pattern->name;
		$pattern_category_list = get_the_terms( $pattern->id, 'wp_pattern_category' );
		$pattern->categories   = ! empty( $pattern_category_list ) ? join( ', ', wp_list_pluck( $pattern_category_list, 'name' ) ) : '';
		$pattern->sync_status  = get_post_meta( $pattern->id, 'wp_pattern_sync_status', true );
		$pattern->is_synced    = $pattern->sync_status === 'unsynced' ? 'no' : 'yes';
		$pattern->content      = <<<PHP
		<?php
		/**
		 * Title: {$pattern->title}
		 * Slug: {$pattern->slug}
		 * Categories: {$pattern->categories}
		 * Synced: {$pattern->is_synced}
		 */
		?>
		{$pattern_post->post_content}
		PHP;

		return $pattern;
	}

	public static function escape_alt_for_pattern( $html ) {
		if ( empty( $html ) ) {
			return $html;
		}
		$html = new WP_HTML_Tag_Processor( $html );
		while ( $html->next_tag( 'img' ) ) {
			$alt_attribute = $html->get_attribute( 'alt' );
			if ( ! empty( $alt_attribute ) ) {
				$html->set_attribute( 'alt', self::escape_text_for_pattern( $alt_attribute ) );
			}
		}
		return $html->__toString();
	}

	public static function escape_text_for_pattern( $text ) {
		if ( $text && trim( $text ) !== '' ) {
			$escaped_text = addslashes( $text );
			return "<?php esc_attr_e('" . $escaped_text . "', '" . wp_get_theme()->get( 'Name' ) . "');?>";
		}
	}

	public static function create_pattern_link( $attributes ) {
		$block_attributes = array_filter( $attributes );
		$attributes_json  = json_encode( $block_attributes, JSON_UNESCAPED_SLASHES );
		return '<!-- wp:pattern ' . $attributes_json . ' /-->';
	}

	public static function replace_local_synced_pattern_references( $pattern ) {

		// If we save patterns we have to update the templates (or none of the templates).
		// However, we can't save it here because it will overwrite changes we make to the templates RE: Patterns.
		// CBT_Theme_Templates::add_templates_to_local( 'all', null, null, null );

		// List all template and pattern files in the theme
		$base_dir       = get_stylesheet_directory();
		$patterns       = glob( $base_dir . DIRECTORY_SEPARATOR . 'patterns' . DIRECTORY_SEPARATOR . '*.php' );
		$synced_patterns = glob( $base_dir . DIRECTORY_SEPARATOR . 'synced-patterns' . DIRECTORY_SEPARATOR . '*.php' );
		$templates      = glob( $base_dir . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . '*.html' );
		$template_parts = glob( $base_dir . DIRECTORY_SEPARATOR . 'template-parts' . DIRECTORY_SEPARATOR . '*.html' );



		$needle = 'wp:block {"ref":' . $pattern['id'];
		$replacement = 'wp:pattern {"slug":"' . $pattern['slug'] . '"';
		// Replace references to the local patterns in the theme
		foreach ( array_merge( $patterns, $templates, $template_parts, $synced_patterns ) as $file ) {
			$file_content = file_get_contents( $file );
			$file_content = str_replace( $needle, $replacement, $file_content );
			file_put_contents( $file, $file_content );
		}

		// if we clear the template customizations for all templates then we have to SAVE all templates.
		CBT_Theme_Templates::clear_user_templates_customizations();
		CBT_Theme_Templates::clear_user_template_parts_customizations();
	}

	public static function prepare_pattern_for_export( $pattern, $options = null ) {
		if ( ! $options ) {
			$options = array(
				'localizeText'   => false,
				'removeNavRefs'  => true,
				'localizeImages' => true,
			);
		}

		$pattern = CBT_Theme_Templates::eliminate_environment_specific_content( $pattern, $options );

		if ( array_key_exists( 'localizeText', $options ) && $options['localizeText'] ) {
			$pattern = CBT_Theme_Templates::escape_text_in_template( $pattern );
		}

		if ( array_key_exists( 'localizeImages', $options ) && $options['localizeImages'] ) {
			$pattern = CBT_Theme_Media::make_template_images_local( $pattern );

			// Write the media assets if there are any
			if ( $pattern->media ) {
				CBT_Theme_Media::add_media_to_local( $pattern->media );
			}
		}

		return $pattern;
	}

	/**
	 * Copy the local patterns as well as any media to the theme filesystem.
	 */
	public static function add_patterns_to_theme( $options = null ) {
		$pattern_query = new WP_Query(
			array(
				'post_type'      => 'wp_block',
				'posts_per_page' => -1,
			)
		);

		if ( $pattern_query->have_posts() ) {
			foreach ( $pattern_query->posts as $pattern ) {
				$pattern        = self::pattern_from_wp_block( $pattern );
				$pattern        = self::prepare_pattern_for_export( $pattern, $options );

				// Check pattern is synced before adding to theme.

				if ( 'unsynced' === $pattern->sync_status ) {
						self::add_unsynced_pattern_to_theme( $pattern );
				}
				else {
						self::add_synced_pattern_to_theme( $pattern );

				}
			}
		}

		// now replace all instances of synced blocks with pattern blocks
		$patterns = CBT_Synced_Pattern_Loader::CBT_get_theme_block_patterns();
		$patterns = array_filter($patterns, function ($pattern) {
			return $pattern['synced'] === 'yes';
		});
		foreach ($patterns as $pattern) {
			self::replace_local_synced_pattern_references($pattern);
		}
	}

	public static function add_synced_pattern_to_theme($pattern)
	{
		$patterns_dir = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'patterns' . DIRECTORY_SEPARATOR;
		$pattern_file = $patterns_dir . $pattern->name . '.php';

		// If there is no patterns folder, create it.
		if ( ! is_dir( $patterns_dir ) ) {
			wp_mkdir_p( $patterns_dir );
		}

		// Create the pattern file.
		file_put_contents( $pattern_file, $pattern->content);

		// update the post_name value to match the pattern slug
		wp_update_post(
			array(
				'ID' => $pattern->id,
				'post_name' => sanitize_title($pattern->slug),
			)
		);
	}

	public static function add_unsynced_pattern_to_theme($pattern)
	{
		$patterns_dir = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'patterns' . DIRECTORY_SEPARATOR;
		$pattern_file = $patterns_dir . $pattern->name . '.php';

		// If there is no patterns folder, create it.
		if ( ! is_dir( $patterns_dir ) ) {
			wp_mkdir_p( $patterns_dir );
		}

		// Create the pattern file.
		file_put_contents( $pattern_file, $pattern->content);

		// Remove it from the database to ensure that these patterns are loaded from the theme.
		wp_delete_post($pattern->id, true);
	}
}
