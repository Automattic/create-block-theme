<?php
/**
 * Handles the theme tags functionality.
 *
 * @package Create Block Theme
 */

class CBT_Theme_Tags {
	/**
	 * Build theme tags list for style.css.
	 *
	 * @param array $theme Theme data.
	 * @return string
	 * @since 1.5.2
	 */
	public static function theme_tags_list( $theme ) {
		$checkbox_tags_merged = array_merge(
			$theme['tags-subject'] ?? array(),
			$theme['tags-layout'] ?? array(),
			$theme['tags-features'] ?? array(),
		);
		$custom_tags          = array_map( 'trim', $theme['tags_custom'] ?? array() );
		$tags                 = array_unique( array_merge( $checkbox_tags_merged, $custom_tags ) );

		return implode( ', ', $tags );
	}

	/**
	 * Render theme tags form section.
	 *
	 * @since 1.5.2
	 */
	public static function theme_tags_section() {
		?>
<div role="group" aria-labelledby="theme_tags_label">
	<fieldset id="theme_tags_label">
		<legend><?php _e( 'Theme Tags', 'create-block-theme' ); ?></legend>

		<p class="description">
			<?php
			printf(
				/* Translators: Theme Tags link. */
				esc_html__(
					'Add theme tags to help categorize the theme (%s).',
					'create-block-theme'
				),
				'<a href="' . esc_url( __( 'https://make.wordpress.org/themes/handbook/review/required/theme-tags/', 'create-block-theme' ) ) . '" target="_blank">' . __( 'read more', 'create-block-theme' ) . '</a>'
			);
			?>
		</p>

		<div class="theme-tags">
			<?php
			// Generate a list of theme tags
			$theme_tags = get_theme_feature_list();
			if ( is_array( $theme_tags ) ) {
				// Sort tags by relevance
				krsort( $theme_tags );
				foreach ( $theme_tags as $category => $tags ) {
					self::theme_tags_category( $category, $tags );
				}
			}
			?>
		</div>

		<p>
			<label for="theme-custom-tags"><?php _e( 'Custom Tags', 'create-block-theme' ); ?></label>
			<?php // Regex for pattern attribute ensures only single words or words with hyphens are used, separated by commas ?>
			<input id="theme-custom-tags" placeholder="<?php _e( 'custom, tags, custom-tags', 'create-block-theme' ); ?>" type="text" name="theme[tags_custom]" class="large-text code" pattern="^[a-zA-Z\-]+(\s*,\s*[a-zA-Z\-]+)*$" aria-describedby="custom-tags-description">
		</p>
		<p id="custom-tags-description" class="description" >
			<?php _e( 'Add custom tags (single or hyphenated words, separated by commas)', 'create-block-theme' ); ?>
		</p>

	</fieldset>
</div>
		<?php
	}

	/**
	 * Output theme tags fieldset.
	 */
	protected static function theme_tags_category( $category, $tags ) {
		?>
		<fieldset id="<?php echo esc_attr( strtolower( $category ) ); ?>-tags">
			<?php if ( 'Subject' === $category ) : ?>
				<legend class="large-text"><?php echo esc_html( $category ); ?>&nbsp;<?php _e( '(max 3 tags)', 'create-block-theme' ); ?></legend>
			<?php else : ?>
				<legend class="large-text"><?php echo esc_html( $category ); ?></legend>
			<?php endif; ?>

			<?php foreach ( $tags as $tag => $pretty_tag ) : ?>
				<?php self::tag_checkbox_input( $category, $tag, $pretty_tag ); ?>
			<?php endforeach; ?>
		</fieldset>
		<?php
	}

	/**
	 * Lists default tags.
	 *
	 * @return array
	 */
	protected static function list_default_tags() {
		$default_tags = array( 'full-site-editing' );
		return $default_tags;
	}

	/**
	 * Checks if a tag is a default tag.
	 *
	 * @param string $tag
	 * @return boolean
	 */
	protected static function is_default_tag( $tag ) {
		if ( ! is_string( $tag ) ) {
			return null;
		}

		$tag          = strtolower( $tag );
		$default_tags = self::list_default_tags();

		return in_array( $tag, $default_tags, true );
	}

	/**
	 * Checks if a tag is included in the active theme or the default tags.
	 *
	 * @param string $tag
	 * @return boolean
	 */
	protected static function is_active_theme_tag( $tag ) {
		if ( ! is_string( $tag ) ) {
			return null;
		}

		$tag               = strtolower( $tag );
		$active_theme_tags = wp_get_theme()->get( 'Tags' );
		$default_tags      = self::list_default_tags();
		$merged_tags       = array_unique( array_merge( $default_tags, $active_theme_tags ) );

		return in_array( $tag, $merged_tags, true );
	}

	/**
	 * Build checkbox input for given theme tag.
	 *
	 * @param string $category
	 * @param string $tag
	 * @param string $pretty_tag
	 * @return void
	 */
	protected static function tag_checkbox_input( $category, $tag, $pretty_tag ) {
		$class   = '';
		$checked = '';

		if ( self::is_default_tag( $tag ) ) {
			$class = 'default-tag';
		}

		if ( self::is_active_theme_tag( $tag ) ) {
			$checked = ' checked';
		}
		?>
<div class="theme-tag-form-control">
	<input type="checkbox" id="theme-tag-<?php echo esc_attr( $tag ); ?>" name="theme[tags-<?php echo esc_attr( strtolower( $category ) ); ?>][]" value="<?php echo esc_attr( $tag ); ?>" class="<?php echo esc_attr( $class ); ?>" <?php echo esc_html( $checked ); ?>>
	<label for="theme-tag-<?php echo esc_attr( $tag ); ?>"><?php echo esc_html( $pretty_tag ); ?></label>
</div>
		<?php
	}

}
