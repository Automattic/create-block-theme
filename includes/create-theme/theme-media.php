<?php

require_once( __DIR__ . '/theme-utils.php' );

class CBT_Theme_Media {

	public static function get_media_folder_path_from_url( $url ) {
		$extension        = strtolower( pathinfo( $url, PATHINFO_EXTENSION ) );
		$folder_path      = '';
		$image_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp' );
		$video_extensions = array( 'mp4', 'm4v', 'webm', 'ogv', 'wmv', 'avi', 'mov', 'mpg', 'ogv', '3gp', '3g2' );
		if ( in_array( $extension, $image_extensions, true ) ) {
			$folder_path = '/assets/images/';
		} elseif ( in_array( $extension, $video_extensions, true ) ) {
			$folder_path = '/assets/videos/';
		} else {
			$folder_path = '/assets/';
		}
		return $folder_path;
	}

	/**
	 * Get the absolute URLs of all media files for a template
	 */
	public static function get_media_absolute_urls_from_template( $template ) {

		$template_blocks = parse_blocks( $template->content );
		$blocks          = _flatten_blocks( $template_blocks );

		$media = array();

		foreach ( $blocks as $block ) {
			// Gets the absolute URLs of img in these blocks
			if ( 'core/image' === $block['blockName'] ||
				'core/video' === $block['blockName'] ||
				'core/media-text' === $block['blockName']
			) {
				$html = new WP_HTML_Tag_Processor( $block['innerHTML'] );
				while ( $html->next_tag( 'img' ) ) {
					$url = $html->get_attribute( 'src' );
					if ( CBT_Theme_Utils::is_absolute_url( $url ) ) {
						$media[] = $url;
					}
				}
				$html = new WP_HTML_Tag_Processor( $html->__toString() );
				while ( $html->next_tag( 'video' ) ) {
					$url = $html->get_attribute( 'src' );
					if ( CBT_Theme_Utils::is_absolute_url( $url ) ) {
						$media[] = $url;
					}
					$poster_url = $html->get_attribute( 'poster' );
					if ( CBT_Theme_Utils::is_absolute_url( $poster_url ) ) {
						$media[] = $poster_url;
					}
				}
			}

			// Gets the absolute URLs of background images in these blocks
			if ( 'core/cover' === $block['blockName'] ) {
				if ( isset( $block['attrs']['url'] ) && CBT_Theme_Utils::is_absolute_url( $block['attrs']['url'] ) ) {
					$media[] = $block['attrs']['url'];
				}
			}

			// Gets the absolute URLs of background images in these blocks
			if ( 'core/group' === $block['blockName'] ) {
				if ( isset( $block['attrs']['style']['background']['backgroundImage']['url'] ) && CBT_Theme_Utils::is_absolute_url( $block['attrs']['style']['background']['backgroundImage']['url'] ) ) {
					$media[] = $block['attrs']['style']['background']['backgroundImage']['url'];
				}
			}
		}

		return $media;
	}

	/**
	 * Create a relative URL based on the absolute URL of a media file
	 *
	 * @param string $absolute_url
	 * @return string $relative_url
	 */
	public static function make_relative_media_url( $absolute_url ) {
		if ( ! empty( $absolute_url ) && CBT_Theme_Utils::is_absolute_url( $absolute_url ) ) {
			$folder_path = self::get_media_folder_path_from_url( $absolute_url );
			if ( is_child_theme() ) {
				return '<?php echo esc_url( get_stylesheet_directory_uri() ); ?>' . $folder_path . basename( $absolute_url );
			}
			return '<?php echo esc_url( get_template_directory_uri() ); ?>' . $folder_path . basename( $absolute_url );
		}
		return $absolute_url;
	}

	/**
	 * Add media files to the local theme
	 */
	public static function add_media_to_local( $media ) {

		foreach ( $media as $url ) {

			$download_file = download_url( $url );

			if ( is_wp_error( $download_file ) ) {
				//we're going to try again with a new URL
				//see, we might be running this in a docker container
				//and if that's the case let's try again on port 80
				$parsed_url = parse_url( $url );
				if ( 'localhost' === $parsed_url['host'] && '80' !== $parsed_url['port'] ) {
					$download_file = download_url( str_replace( 'localhost:' . $parsed_url['port'], 'localhost:80', $url ) );
				}
			}

			// TODO: implement a warning if the file is missing
			if ( ! is_wp_error( $download_file ) ) {
				$media_path = get_stylesheet_directory() . DIRECTORY_SEPARATOR . self::get_media_folder_path_from_url( $url );
				if ( ! is_dir( $media_path ) ) {
					wp_mkdir_p( $media_path );
				}
				rename( $download_file, $media_path . basename( $url ) );
			}
		}

	}


	/**
	 * Replace the absolute URLs of media in a template with relative URLs
	 */
	public static function make_template_images_local( $template ) {

		$template->media = self::get_media_absolute_urls_from_template( $template );

		// Replace the absolute URLs with relative URLs in the templates
		foreach ( $template->media as $media_url ) {
			$local_media_url   = CBT_Theme_Media::make_relative_media_url( $media_url );
			$template->content = str_replace( $media_url, $local_media_url, $template->content );
		}

		return $template;
	}
}
