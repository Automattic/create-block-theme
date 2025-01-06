<?php

class CBT_Theme_Create {

	const ALLOWED_SCREENSHOT_TYPES = array(
		'png' => 'image/png',
	);

	public static function clone_current_theme( $theme ) {
		// Default values for cloned themes
		$theme['is_cloned_theme'] = true;
		$theme['version']         = '1.0';
		$theme['tags_custom']     = wp_get_theme()->get( 'Tags' );

		// Create theme directory.
		$new_theme_path = get_theme_root() . DIRECTORY_SEPARATOR . $theme['slug'];

		if ( file_exists( $new_theme_path ) ) {
			return new WP_Error( 'theme_already_exists', __( 'Theme already exists.', 'create-block-theme' ) );
		}

		wp_mkdir_p( $new_theme_path );

		// Persist font settings for cloned theme.
		CBT_Theme_Fonts::persist_font_settings();

		// Copy theme files.
		$template_options = array(
			'localizeText'   => false,
			'removeNavRefs'  => false,
			'localizeImages' => false,
		);
		CBT_Theme_Utils::clone_theme_to_folder( $new_theme_path, $theme['slug'], $theme['name'] );
		CBT_Theme_Templates::add_templates_to_local( 'all', $new_theme_path, $theme['slug'], $template_options );
		file_put_contents( path_join( $new_theme_path, 'theme.json' ), CBT_Theme_JSON_Resolver::export_theme_data( 'all' ) );
		file_put_contents( path_join( $new_theme_path, 'readme.txt' ), CBT_Theme_Readme::create( $theme ) );
		file_put_contents( path_join( $new_theme_path, 'style.css' ), CBT_Theme_Styles::update_style_css( file_get_contents( path_join( $new_theme_path, 'style.css' ) ), $theme ) );

		switch_theme( $theme['slug'] );
	}

	public static function create_child_theme( $theme, $screenshot ) {

		// Create theme directory.
		$new_theme_path = get_theme_root() . DIRECTORY_SEPARATOR . $theme['slug'];

		if ( file_exists( $new_theme_path ) ) {
			return new WP_Error( 'theme_already_exists', __( 'Theme already exists.', 'create-block-theme' ) );
		}

		wp_mkdir_p( $new_theme_path );

		// Add readme.txt.
		file_put_contents(
			$new_theme_path . DIRECTORY_SEPARATOR . 'readme.txt',
			CBT_Theme_Readme::create( $theme )
		);

		// Add style.css.
		$theme['template'] = wp_get_theme()->get( 'TextDomain' );
		$css_contents      = CBT_Theme_Styles::build_style_css( $theme );
		file_put_contents(
			$new_theme_path . DIRECTORY_SEPARATOR . 'style.css',
			$css_contents
		);

		// Add theme.json
		CBT_Theme_Templates::add_templates_to_local( 'user', $new_theme_path, $theme['slug'] );
		file_put_contents( $new_theme_path . DIRECTORY_SEPARATOR . 'theme.json', CBT_Theme_JSON_Resolver::export_theme_data( 'variation' ) );

		// Add Screenshot
		if ( static::is_valid_screenshot( $screenshot ) ) {
			file_put_contents(
				$new_theme_path . DIRECTORY_SEPARATOR . 'screenshot.png',
				file_get_contents( $screenshot['tmp_name'] )
			);
		} else {
			$source = plugin_dir_path( __DIR__ ) . '../assets/boilerplate/screenshot.png';
			copy( $source, $new_theme_path . DIRECTORY_SEPARATOR . 'screenshot.png' );
		}

		switch_theme( $theme['slug'] );
	}

	public static function create_blank_theme( $theme, $screenshot ) {

		// Create theme directory.
		$source           = plugin_dir_path( __DIR__ ) . '../assets/boilerplate';
		$blank_theme_path = get_theme_root() . DIRECTORY_SEPARATOR . $theme['slug'];

		if ( file_exists( $blank_theme_path ) ) {
			return new WP_Error( 'theme_already_exists', __( 'Theme already exists.', 'create-block-theme' ) );
		}

		wp_mkdir_p( $blank_theme_path );

		// Add readme.txt.
		file_put_contents(
			$blank_theme_path . DIRECTORY_SEPARATOR . 'readme.txt',
			CBT_Theme_Readme::create( $theme )
		);

		// Add new metadata.
		$css_contents = CBT_Theme_Styles::build_style_css( $theme );

		// Add style.css.
		file_put_contents(
			$blank_theme_path . DIRECTORY_SEPARATOR . 'style.css',
			$css_contents
		);

		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator( $source, \RecursiveDirectoryIterator::SKIP_DOTS ),
			\RecursiveIteratorIterator::SELF_FIRST
		);

		foreach (
			$iterator as $item
			) {
			if ( $item->isDir() ) {
				wp_mkdir_p( $blank_theme_path . DIRECTORY_SEPARATOR . $iterator->getSubPathname() );
			} else {
				copy( $item, $blank_theme_path . DIRECTORY_SEPARATOR . $iterator->getSubPathname() );
			}
		}

		// Overwrite default screenshot if one is provided.
		if ( static::is_valid_screenshot( $screenshot ) ) {
			file_put_contents(
				$blank_theme_path . DIRECTORY_SEPARATOR . 'screenshot.png',
				file_get_contents( $screenshot['tmp_name'] )
			);
		}

		if ( ! defined( 'IS_GUTENBERG_PLUGIN' ) ) {
			global $wp_version;
			$theme_json_version = 'wp/' . substr( $wp_version, 0, 3 );
			$schema             = '"$schema": "https://schemas.wp.org/' . $theme_json_version . '/theme.json"';
			$theme_json_path    = $blank_theme_path . DIRECTORY_SEPARATOR . 'theme.json';
			$theme_json_string  = file_get_contents( $theme_json_path );
			$theme_json_string  = str_replace( '"$schema": "https://schemas.wp.org/trunk/theme.json"', $schema, $theme_json_string );
			file_put_contents( $theme_json_path, $theme_json_string );
		}

		switch_theme( $theme['slug'] );
	}

	private static function is_valid_screenshot( $file ) {
		if ( ! $file ) {
			return 0;
		}
		$filetype = wp_check_filetype( $file['name'], self::ALLOWED_SCREENSHOT_TYPES );
		if ( is_uploaded_file( $file['tmp_name'] ) && in_array( $filetype['type'], self::ALLOWED_SCREENSHOT_TYPES, true ) && $file['size'] < 2097152 ) {
			return 1;
		}
		return 0;
	}
}
