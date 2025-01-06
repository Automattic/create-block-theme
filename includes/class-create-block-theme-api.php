<?php

require_once __DIR__ . '/create-theme/resolver_additions.php';
require_once __DIR__ . '/create-theme/theme-locale.php';
require_once __DIR__ . '/create-theme/theme-tags.php';
require_once __DIR__ . '/create-theme/theme-zip.php';
require_once __DIR__ . '/create-theme/theme-media.php';
require_once __DIR__ . '/create-theme/theme-patterns.php';
require_once __DIR__ . '/create-theme/theme-templates.php';
require_once __DIR__ . '/create-theme/theme-styles.php';
require_once __DIR__ . '/create-theme/theme-json.php';
require_once __DIR__ . '/create-theme/theme-utils.php';
require_once __DIR__ . '/create-theme/theme-readme.php';
require_once __DIR__ . '/create-theme/theme-fonts.php';
require_once __DIR__ . '/create-theme/theme-create.php';

/**
 * The api functionality of the plugin leveraged by the site editor UI.
 *
 * @package    Create_Block_Theme
 * @subpackage Create_Block_Theme/admin
 * @author     WordPress.org
 */
class CBT_Theme_API {

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Register the REST routes.
	 */
	public function register_rest_routes() {
		register_rest_route(
			'create-block-theme/v1',
			'/export',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_export_theme' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			)
		);
		register_rest_route(
			'create-block-theme/v1',
			'/update',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_update_theme' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			)
		);
		register_rest_route(
			'create-block-theme/v1',
			'/save',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_save_theme' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			)
		);
		register_rest_route(
			'create-block-theme/v1',
			'/clone',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_clone_theme' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			)
		);
		register_rest_route(
			'create-block-theme/v1',
			'/create-variation',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_create_variation' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			)
		);
		register_rest_route(
			'create-block-theme/v1',
			'/create-blank',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_create_blank_theme' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			)
		);
		register_rest_route(
			'create-block-theme/v1',
			'/create-child',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_create_child_theme' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			)
		);
		register_rest_route(
			'create-block-theme/v1',
			'/get-readme-data',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_readme_data' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			)
		);
		register_rest_route(
			'create-block-theme/v1',
			'/get-theme-data',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_theme_data' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			),
		);
		register_rest_route(
			'create-block-theme/v1',
			'/font-families',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_font_families' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			),
		);
		register_rest_route(
			'create-block-theme/v1',
			'/reset-theme',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'rest_reset_theme' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_theme_options' );
				},
			),
		);
	}

	function rest_get_theme_data( $request ) {
		try {
			$theme_data = CBT_Theme_JSON_Resolver::get_theme_file_contents();
			return new WP_REST_Response(
				array(
					'status'  => 'SUCCESS',
					'message' => __( 'Theme data retrieved.', 'create-block-theme' ),
					'data'    => $theme_data,
				),
			);
		} catch ( Exception $error ) {
			return new WP_REST_Response(
				array(
					'status'  => 'FAILURE',
					'message' => $error->getMessage(),
				)
			);
		}
	}

	function rest_get_readme_data( $request ) {
		try {
			$readme_data = CBT_Theme_Readme::get_sections();
			return new WP_REST_Response(
				array(
					'status'  => 'SUCCESS',
					'message' => __( 'Readme file data retrieved.', 'create-block-theme' ),
					'data'    => $readme_data,
				)
			);
		} catch ( Exception $error ) {
			return new WP_REST_Response(
				array(
					'status'  => 'FAILURE',
					'message' => $error->getMessage(),
				)
			);
		}
	}

	function rest_clone_theme( $request ) {

		$response = CBT_Theme_Create::clone_current_theme( $this->sanitize_theme_data( $request->get_params() ) );

		wp_cache_flush();

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return new WP_REST_Response(
			array(
				'status'  => 'SUCCESS',
				'message' => __( 'Cloned Theme Created.', 'create-block-theme' ),
			)
		);
	}

	function rest_create_child_theme( $request ) {

		$theme                   = $this->sanitize_theme_data( $request->get_params() );
		$theme['is_child_theme'] = true;
		//TODO: Handle screenshots
		$screenshot = null;

		$response = CBT_Theme_Create::create_child_theme( $theme, $screenshot );

		wp_cache_flush();

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return new WP_REST_Response(
			array(
				'status'  => 'SUCCESS',
				'message' => __( 'Child Theme Created.', 'create-block-theme' ),
			)
		);
	}

	function rest_create_variation( $request ) {
		$options = $request->get_params();

		$save_fonts = isset( $options['saveFonts'] ) && true === $options['saveFonts'];

		$response = CBT_Theme_JSON::add_theme_json_variation_to_local(
			'variation',
			$this->sanitize_theme_data( $options ),
			$save_fonts
		);

		wp_cache_flush();

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return new WP_REST_Response(
			array(
				'status'  => 'SUCCESS',
				'message' => __( 'Theme Variation Created.', 'create-block-theme' ),
			)
		);
	}

	function rest_create_blank_theme( $request ) {

		$theme = $this->sanitize_theme_data( $request->get_params() );
		//TODO: Handle screenshots
		$screenshot = null;

		$response = CBT_Theme_Create::create_blank_theme( $theme, $screenshot );

		wp_cache_flush();

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return new WP_REST_Response(
			array(
				'status'  => 'SUCCESS',
				'message' => __( 'Blank Theme Created.', 'create-block-theme' ),
			)
		);
	}

	/**
	 * Export the theme as a ZIP file.
	 */
	function rest_export_theme( $request ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new WP_Error(
				'missing_zip_package',
				__( 'Unable to create a zip file. ZipArchive not available.', 'create-block-theme' ),
			);
		}
		wp_cache_flush();
		$theme_slug = wp_get_theme()->get( 'TextDomain' );

		// Create ZIP file in the temporary directory.
		$filename = tempnam( get_temp_dir(), $theme_slug );
		$zip      = CBT_Theme_Zip::create_zip( $filename, $theme_slug );

		$zip = CBT_Theme_Zip::copy_theme_to_zip( $zip, null, null );

		if ( is_child_theme() ) {
			wp_cache_flush();
			$zip        = CBT_Theme_Zip::add_templates_to_zip( $zip, 'current', $theme_slug );
			$theme_json = CBT_Theme_JSON_Resolver::export_theme_data( 'current' );
		} else {
			$zip        = CBT_Theme_Zip::add_templates_to_zip( $zip, 'all', null );
			$theme_json = CBT_Theme_JSON_Resolver::export_theme_data( 'all' );
		}

		$theme_json = CBT_Theme_Zip::add_activated_fonts_to_zip( $zip, $theme_json );

		$zip = CBT_Theme_Zip::add_theme_json_to_zip( $zip, $theme_json );

		$zip->close();

		wp_cache_flush();

		header( 'Content-Type: application/zip' );
		header( 'Content-Disposition: attachment; filename=' . $theme_slug . '.zip' );
		header( 'Content-Length: ' . filesize( $filename ) );
		flush();
		echo readfile( $filename );
		exit;
	}

	/**
	 * Update the theme metadata and relocate the theme.
	 */
	function rest_update_theme( $request ) {
		$theme = $this->sanitize_theme_data( $request->get_params() );

		// Update the metadata of the theme in the style.css file
		$style_css = file_get_contents( get_stylesheet_directory() . '/style.css' );
		$style_css = CBT_Theme_Styles::update_style_css( $style_css, $theme );
		file_put_contents( get_stylesheet_directory() . '/style.css', $style_css );

		file_put_contents(
			CBT_Theme_Readme::file_path(),
			CBT_Theme_Readme::update( $theme, CBT_Theme_Readme::get_content() )
		);

		// Replace Screenshot
		if ( wp_get_theme()->get_screenshot() !== $theme['screenshot'] ) {
			CBT_Theme_Utils::replace_screenshot( $theme['screenshot'] );
		}

		wp_cache_flush();

		return new WP_REST_Response(
			array(
				'status'  => 'SUCCESS',
				'message' => __( 'Theme Updated.', 'create-block-theme' ),
			)
		);
	}

	/**
	 * Save the user changes to the theme and clear user changes.
	 */
	function rest_save_theme( $request ) {

		$options = $request->get_params();

		if ( isset( $options['saveFonts'] ) && true === $options['saveFonts'] ) {
			CBT_Theme_Fonts::persist_font_settings();
		}

		if ( isset( $options['saveTemplates'] ) && true === $options['saveTemplates'] ) {
			if ( true === $options['processOnlySavedTemplates'] ) {
				CBT_Theme_Templates::add_templates_to_local( 'user', null, null, $options );
			} else {
				if ( is_child_theme() ) {
					CBT_Theme_Templates::add_templates_to_local( 'current', null, null, $options );
				} else {
					CBT_Theme_Templates::add_templates_to_local( 'all', null, null, $options );
				}
			}
			CBT_Theme_Templates::clear_user_templates_customizations();
			CBT_Theme_Templates::clear_user_template_parts_customizations();
		}

		if ( isset( $options['saveStyle'] ) && true === $options['saveStyle'] ) {
			if ( is_child_theme() ) {
				CBT_Theme_JSON::add_theme_json_to_local( 'current', null, null, $options );
			} else {
				CBT_Theme_JSON::add_theme_json_to_local( 'all', null, null, $options );
			}
			CBT_Theme_Styles::clear_user_styles_customizations();
		}

		if ( isset( $options['savePatterns'] ) && true === $options['savePatterns'] ) {
			$response = CBT_Theme_Patterns::add_patterns_to_theme( $options );

			if ( is_wp_error( $response ) ) {
				return $response;
			}
		}

		wp_get_theme()->cache_delete();

		return new WP_REST_Response(
			array(
				'status'  => 'SUCCESS',
				'message' => __( 'Theme Saved.', 'create-block-theme' ),
			)
		);
	}

	/**
	 * Get a list of all the font families used in the theme.
	 *
	 * It includes the font families from the theme.json data (theme.json file + global styles) and the theme style variations.
	 * The font families with font faces containing src urls relative to the theme folder are converted to absolute urls.
	 */
	function rest_get_font_families( $request ) {
		$font_families = CBT_Theme_Fonts::get_all_fonts();

		return new WP_REST_Response(
			array(
				'status'  => 'SUCCESS',
				'message' => __( 'Font Families retrieved.', 'create-block-theme' ),
				'data'    => $font_families,
			)
		);
	}

	/**
	 * Reset the theme to the default state.
	 */
	function rest_reset_theme( $request ) {
		$options = $request->get_params();

		if ( isset( $options['resetStyles'] ) && true === $options['resetStyles'] ) {
			CBT_Theme_Styles::clear_user_styles_customizations();
		}

		if ( isset( $options['resetTemplates'] ) && true === $options['resetTemplates'] ) {
			CBT_Theme_Templates::clear_user_templates_customizations();
		}

		if ( isset( $options['resetTemplateParts'] ) && true === $options['resetTemplateParts'] ) {
			CBT_Theme_Templates::clear_user_template_parts_customizations();
		}

		return rest_ensure_response(
			array(
				'status'  => 'SUCCESS',
				'message' => __( 'Theme Reset.', 'create-block-theme' ),
			)
		);
	}

	private function sanitize_theme_data( $theme ) {
		$sanitized_theme['name']                = sanitize_text_field( $theme['name'] );
		$sanitized_theme['description']         = sanitize_text_field( $theme['description'] ?? '' );
		$sanitized_theme['uri']                 = sanitize_text_field( $theme['uri'] ?? '' );
		$sanitized_theme['author']              = sanitize_text_field( $theme['author'] ?? '' );
		$sanitized_theme['author_uri']          = sanitize_text_field( $theme['author_uri'] ?? '' );
		$sanitized_theme['tags_custom']         = array_map( 'sanitize_text_field', $theme['tags_custom'] ?? array() );
		$sanitized_theme['version']             = sanitize_text_field( $theme['version'] ?? '' );
		$sanitized_theme['screenshot']          = sanitize_text_field( $theme['screenshot'] ?? '' );
		$sanitized_theme['requires_wp']         = sanitize_text_field( $theme['requires_wp'] ?? '' );
		$sanitized_theme['recommended_plugins'] = sanitize_textarea_field( $theme['recommended_plugins'] ?? '' );
		$sanitized_theme['font_credits']        = sanitize_textarea_field( $theme['font_credits'] ?? '' );
		$sanitized_theme['image_credits']       = sanitize_textarea_field( $theme['image_credits'] ?? '' );
		$sanitized_theme['template']            = '';
		$sanitized_theme['slug']                = sanitize_title( $theme['name'] );
		$sanitized_theme['text_domain']         = $sanitized_theme['slug'];
		return $sanitized_theme;
	}
}
