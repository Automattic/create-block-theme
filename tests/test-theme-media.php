<?php
/**
 * @package Create_Block_Theme
 */
class Test_Create_Block_Theme_Media extends WP_UnitTestCase {

	public function test_make_images_block_local() {
		$template          = new stdClass();
		$template->content = '
			<!-- wp:image -->
			<figure class="wp-block-image"><img src="http://example.com/image.jpg" alt="Alternative Text" /></figure>
			<!-- /wp:image -->
		';
		$new_template      = Theme_Media::make_template_images_local( $template );

		// The image should be replaced with a relative URL
		$this->assertStringNotContainsString( 'http://example.com/image.jpg', $new_template->content );
		$this->assertStringContainsString( 'get_stylesheet_directory_uri', $new_template->content );
		$this->assertStringContainsString( '/assets/images', $new_template->content );

	}

	public function test_make_cover_block_local() {
		$template          = new stdClass();
		$template->content = '
			<!-- wp:cover {"url":"http://example.com/image.jpg"} -->
				<div class="wp-block-cover">
					<img class="wp-block-cover__image-background wp-image-628" alt="" src="http://example.com/image.jpg" data-object-fit="cover"/>
					<div class="wp-block-cover__inner-container">
					</div>
				</div>
			<!-- /wp:cover -->
		';
		$new_template      = Theme_Media::make_template_images_local( $template );

		// The image should be replaced with a relative URL
		$this->assertStringNotContainsString( 'http://example.com/image.jpg', $new_template->content );
		$this->assertStringContainsString( 'get_stylesheet_directory_uri', $new_template->content );
		$this->assertStringContainsString( '/assets/images', $new_template->content );
	}

	// TODO: Test additional blocks
	// TODO: Test nested blocks

}
