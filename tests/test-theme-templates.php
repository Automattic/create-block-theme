<?php
/**
 * @package Create_Block_Theme
 */
class Test_Create_Block_Theme_Templates extends WP_UnitTestCase {

	public function test_paragraphs_are_localized() {
		$template          = new stdClass();
		$template->content = '<!-- wp:paragraph --><p>This is text to localize</p><!-- /wp:paragraph -->';
		$new_template      = Theme_Templates::escape_text_in_template( $template );
		$this->assertStringContainsString( 'This is text to localize', $new_template->content );
		$this->assertStringNotContainsString( '<p>This is text to localize</p>', $new_template->content );

	}

	public function test_paragraphs_in_groups_are_localized() {
		$template          = new stdClass();
		$template->content = '<!-- wp:group {"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph -->
				<p>This is text to localize</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->';
		$new_template      = Theme_Templates::escape_text_in_template( $template );
		$this->assertStringContainsString( 'This is text to localize', $new_template->content );
		$this->assertStringNotContainsString( '<p>This is text to localize</p>', $new_template->content );
	}

	public function test_buttons_are_localized() {
		$template          = new stdClass();
		$template->content = '<!-- wp:button -->
					<div class="wp-block-button">
						<a class="wp-block-button__link wp-element-button">This is text to localize</a>
					</div>
				<!-- /wp:button -->';
		$new_template      = Theme_Templates::escape_text_in_template( $template );
		$this->assertStringContainsString( 'This is text to localize', $new_template->content );
		$this->assertStringNotContainsString( '<a class="wp-block-button__link wp-element-button">This is text to localize</a>', $new_template->content );
	}

	public function test_headings_are_localized() {
		$template          = new stdClass();
		$template->content = '
			<!-- wp:heading -->
			<h2 class="wp-block-heading">This is a heading to localize.</h2>
			<!-- /wp:heading -->
		';
		$new_template      = Theme_Templates::escape_text_in_template( $template );
		$this->assertStringContainsString( 'This is a heading to localize.', $new_template->content );
		$this->assertStringNotContainsString( '<h2 class="wp-block-heading">This is a heading to localize.</h2>', $new_template->content );
	}

	public function test_eliminate_theme_ref_from_template_part() {
		$template          = new stdClass();
		$template->content = '<!-- wp:template-part {"slug":"header","theme":"testtheme"} /-->';
		$new_template      = Theme_Templates::eliminate_environment_specific_content( $template );
		$this->assertStringContainsString( '<!-- wp:template-part {"slug":"header"} /-->', $new_template->content );
	}

	public function test_eliminate_nav_block_ref() {
		$template          = new stdClass();
		$template->content = '<!-- wp:navigation {"ref":4} /-->';
		$new_template      = Theme_Templates::eliminate_environment_specific_content( $template );
		$this->assertStringContainsString( '<!-- wp:navigation /-->', $new_template->content );
	}

	public function test_eliminate_nav_block_ref_in_nested_block() {
		$template          = new stdClass();
		$template->content = '
			<!-- wp:group {"layout":{"type":"constrained"}} -->
			<div class="wp-block-group"><!-- wp:navigation {"ref":4} /--></div>
			<!-- /wp:group -->
		';
		$new_template      = Theme_Templates::eliminate_environment_specific_content( $template );
		$this->assertStringContainsString( '<!-- wp:navigation /-->', $new_template->content );
	}

	public function test_eliminate_id_from_image() {
		$template          = new stdClass();
		$template->content = '
			<!-- wp:image {"id":635} -->
			<figure class="wp-block-image size-large"><img src="http://example.com/file.jpg" alt="" class="wp-image-635"/></figure>
			<!-- /wp:image -->
		';
		$new_template      = Theme_Templates::eliminate_environment_specific_content( $template );
		$this->assertStringContainsString( '<!-- wp:image -->', $new_template->content );
		$this->assertStringNotContainsString( '<!-- wp:image {"id":635} -->', $new_template->content );
		$this->assertStringNotContainsString( 'wp-image-635', $new_template->content );
	}

	public function test_eliminate_taxQuery_from_query_loop() {
		$template          = new stdClass();
		$template->content = '
		<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{"post_tag":[9]}}} -->
		<div class="wp-block-query">
			<!-- wp:post-template -->
				<!-- wp:post-title {"isLink":true} /-->
				<!-- wp:post-excerpt /-->
			<!-- /wp:post-template -->
		</div>
		<!-- /wp:query -->
		';
		$new_template      = Theme_Templates::eliminate_environment_specific_content( $template );
		$this->assertStringContainsString( '<!-- wp:query', $new_template->content );
		$this->assertStringNotContainsString( '"taxQuery":{"post_tag":[9]}', $new_template->content );
	}

	public function test_properly_encode_quotes_and_doublequotes() {
		$template          = new stdClass();
		$template->content = '<!-- wp:heading -->
			<h3 class="wp-block-heading">"This" is a ' . "'test'" . '</h3>
		<!-- /wp:heading -->';
		$escaped_template  = Theme_Templates::escape_text_in_template( $template );

		/* That looks like a mess, but what it should look like for REAL is <?php echo esc_attr_e( '"This" is a \'test\'', '' ); ?> */
		$this->assertStringContainsString( '<?php echo __(\'"This" is a \\\'test\\\'\', \'\');?>', $escaped_template->content );
	}

	public function test_properly_encode_lessthan_and_greaterthan() {
		$template          = new stdClass();
		$template->content = '<!-- wp:heading -->
			<h3 class="wp-block-heading">&lt;This> is a &lt;test&gt;</h3>
		<!-- /wp:heading -->';
		$escaped_template  = Theme_Templates::escape_text_in_template( $template );

		$this->assertStringContainsString( '<?php echo __(\'&lt;This> is a &lt;test&gt;\', \'\');?>', $escaped_template->content );
	}

	public function test_properly_encode_html_markup() {
		$template          = new stdClass();
		$template->content = '<!-- wp:paragraph -->
			<p><strong>Bold</strong> text has feelings &lt;&gt; TOO</p>
			<!-- /wp:paragraph -->';
		$escaped_template  = Theme_Templates::escape_text_in_template( $template );

		$this->assertStringContainsString( '<?php echo __(\'<strong>Bold</strong> text has feelings &lt;&gt; TOO\', \'\');?>', $escaped_template->content );
	}

	public function test_localize_alt_text_from_image() {
		$template          = new stdClass();
		$template->content = '
			<!-- wp:image -->
			<figure class="wp-block-image"><img src="http://example.com/file.jpg" alt="This is alt text" /></figure>
			<!-- /wp:image -->
		';
		$new_template      = Theme_Templates::escape_text_in_template( $template );
		$this->assertStringContainsString( 'alt="<?php echo __(\'This is alt text\', \'\');?>"', $new_template->content );
	}

	public function test_localize_alt_text_from_cover() {
		$template          = new stdClass();
		$template->content = '
			<!-- wp:cover {"url":"http://example.com/file.jpg","alt":"This is alt text"} -->
			<div class="wp-block-cover">
			<span aria-hidden="true" class="wp-block-cover__background"></span>
			<img class="wp-block-cover__image-background" alt="This is alt text" src="http://example.com/file.jpg" data-object-fit="cover"/>
			<div class="wp-block-cover__inner-container">
				<!-- wp:paragraph -->
				<p></p>
				<!-- /wp:paragraph -->
			</div>
			</div>
			<!-- /wp:cover -->
		';
		$new_template      = Theme_Templates::escape_text_in_template( $template );
		$this->assertStringContainsString( 'alt="<?php echo __(\'This is alt text\', \'\');?>"', $new_template->content );
		$this->assertStringContainsString( '"alt":"<?php echo __(\'This is alt text\', \'\');?>"', $new_template->content );
	}

}


