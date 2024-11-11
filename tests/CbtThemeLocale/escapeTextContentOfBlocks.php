<?php

require_once __DIR__ . '/base.php';

/**
 * Tests for the CBT_Theme_Locale::escape_text_content_of_blocks method.
 *
 * @package Create_Block_Theme
 * @covers CBT_Theme_Locale::escape_text_content_of_blocks
 * @group locale
 */
class CBT_Theme_Locale_EscapeTextContentOfBlocks extends CBT_Theme_Locale_UnitTestCase {

	/**
	 * @dataProvider data_test_escape_text_content_of_blocks
	 */
	public function test_escape_text_content_of_blocks( $block_markup, $expected_markup ) {
		// Parse the block markup.
		$blocks = parse_blocks( $block_markup );
		// Escape the text content of the blocks.
		$escaped_blocks = CBT_Theme_Locale::escape_text_content_of_blocks( $blocks );
		// Serialize the blocks to get the markup.
		$escaped_markup = serialize_blocks( $escaped_blocks );

		$this->assertEquals( $expected_markup, $escaped_markup, 'The markup result is not as the expected one.' );
	}

	public function data_test_escape_text_content_of_blocks() {
		return array(

			'paragraph'                  => array(
				'block_markup'    => '<!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center">This is a test text.</p><!-- /wp:paragraph -->',
				'expected_markup' => '<!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center"><?php esc_html_e(\'This is a test text.\', \'test-locale-theme\');?></p><!-- /wp:paragraph -->',
			),

			'paragraph on nested groups' => array(
				'block_markup'    =>
					'<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"","wideSize":""}} -->
                    <div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"constrained","contentSize":"565px"}} -->
                    <div class="wp-block-group"><!-- wp:paragraph {"align":"center"} -->
                    <p class="has-text-align-center">This is a test text.</p>
                    <!-- /wp:paragraph --></div>
                    <!-- /wp:group --></div>
                    <!-- /wp:group -->',
				'expected_markup' =>
					'<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}}},"layout":{"type":"constrained","contentSize":"","wideSize":""}} -->
                    <div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:group {"style":{"spacing":{"blockGap":"0px"}},"layout":{"type":"constrained","contentSize":"565px"}} -->
                    <div class="wp-block-group"><!-- wp:paragraph {"align":"center"} -->
                    <p class="has-text-align-center"><?php esc_html_e(\'This is a test text.\', \'test-locale-theme\');?></p>
                    <!-- /wp:paragraph --></div>
                    <!-- /wp:group --></div>
                    <!-- /wp:group -->',
			),

			'heading 1'                  => array(
				'block_markup'    =>
					'<!-- wp:heading {"textAlign":"center","className":"is-style-asterisk"} -->
                    <h1 class="wp-block-heading has-text-align-center is-style-asterisk">A passion for creating spaces</h1>
                    <!-- /wp:heading -->',
				'expected_markup' =>
					'<!-- wp:heading {"textAlign":"center","className":"is-style-asterisk"} -->
                    <h1 class="wp-block-heading has-text-align-center is-style-asterisk"><?php esc_html_e(\'A passion for creating spaces\', \'test-locale-theme\');?></h1>
                    <!-- /wp:heading -->',
			),

			'heading 2'                  => array(
				'block_markup'    =>
					'<!-- wp:heading {"textAlign":"center","className":"is-style-asterisk"} -->
                    <h2 class="wp-block-heading has-text-align-center is-style-asterisk">A passion for creating spaces</h2>
                    <!-- /wp:heading -->',
				'expected_markup' =>
					'<!-- wp:heading {"textAlign":"center","className":"is-style-asterisk"} -->
                    <h2 class="wp-block-heading has-text-align-center is-style-asterisk"><?php esc_html_e(\'A passion for creating spaces\', \'test-locale-theme\');?></h2>
                    <!-- /wp:heading -->',
			),

			'list item'                  => array(
				'block_markup'    =>
					'<!-- wp:list {"style":{"typography":{"lineHeight":"1.75"}},"className":"is-style-checkmark-list"} -->
                    <ul style="line-height:1.75" class="is-style-checkmark-list"><!-- wp:list-item -->
                    <li>Collaborate with fellow architects.</li>
                    <!-- /wp:list-item -->
                    <!-- wp:list-item -->
                    <li>Showcase your projects.</li>
                    <!-- /wp:list-item -->
                    <!-- wp:list-item -->
                    <li>Experience the world of architecture.</li>
                    <!-- /wp:list-item --></ul>
                    <!-- /wp:list -->',
				'expected_markup' =>
					'<!-- wp:list {"style":{"typography":{"lineHeight":"1.75"}},"className":"is-style-checkmark-list"} -->
                    <ul style="line-height:1.75" class="is-style-checkmark-list"><!-- wp:list-item -->
                    <li><?php esc_html_e(\'Collaborate with fellow architects.\', \'test-locale-theme\');?></li>
                    <!-- /wp:list-item -->
                    <!-- wp:list-item -->
                    <li><?php esc_html_e(\'Showcase your projects.\', \'test-locale-theme\');?></li>
                    <!-- /wp:list-item -->
                    <!-- wp:list-item -->
                    <li><?php esc_html_e(\'Experience the world of architecture.\', \'test-locale-theme\');?></li>
                    <!-- /wp:list-item --></ul>
                    <!-- /wp:list -->',
			),

			'verse'                      => array(
				'block_markup'    =>
					'<!-- wp:verse {"style":{"layout":{"selfStretch":"fit","flexSize":null}}} -->
                    <pre class="wp-block-verse">Ya somos el olvido que seremos.<br>El polvo elemental que nos ignora<br>y que fue el rojo Adán y que es ahora<br>todos los hombres, y que no veremos.</pre>
                    <!-- /wp:verse -->',
				'expected_markup' =>
					'<!-- wp:verse {"style":{"layout":{"selfStretch":"fit","flexSize":null}}} -->
                    <pre class="wp-block-verse"><?php /* Translators: 1. is a \'br\' HTML element, 2. is a \'br\' HTML element, 3. is a \'br\' HTML element */ ' . "\n" . 'echo sprintf( esc_html__( \'Ya somos el olvido que seremos.%1$sEl polvo elemental que nos ignora%2$sy que fue el rojo Adán y que es ahora%3$stodos los hombres, y que no veremos.\', \'test-locale-theme\' ), \'<br>\', \'<br>\', \'<br>\' ); ?></pre>
                    <!-- /wp:verse -->',
			),

			'button'                     => array(
				'block_markup'    =>
					'<!-- wp:button -->
                    <div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Sign up</a></div>
                    <!-- /wp:button -->',
				'expected_markup' =>
					'<!-- wp:button -->
                    <div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php esc_html_e(\'Sign up\', \'test-locale-theme\');?></a></div>
                    <!-- /wp:button -->',
			),

			'image'                      => array(
				'block_markup'    =>
					'<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
                    <figure class="wp-block-image size-large"><img src="http://example.org/image.webp" alt="image alt text" class="wp-image-151"/><figcaption class="wp-element-caption">Image caption</figcaption></figure>
                    <!-- /wp:image -->',
				'expected_markup' =>
					'<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
                    <figure class="wp-block-image size-large"><img src="http://example.org/image.webp" alt="<?php esc_attr_e(\'image alt text\', \'test-locale-theme\');?>" class="wp-image-151"/><figcaption class="wp-element-caption"><?php esc_html_e(\'Image caption\', \'test-locale-theme\');?></figcaption></figure>
                    <!-- /wp:image -->',
			),

			'cover'                      => array(
				'block_markup'    =>
					'<!-- wp:cover {"url":"http://localhost/wp1/wp-content/uploads/2024/05/image.jpeg","id":39,"alt":"Alternative text for cover image","dimRatio":50,"customOverlayColor":"#1d2b2f","layout":{"type":"constrained"}} -->
                    <div class="wp-block-cover"><span aria-hidden="true" class="wp-block-cover__background has-background-dim" style="background-color:#1d2b2f"></span><img class="wp-block-cover__image-background wp-image-39" alt="Alternative text for cover image" src="http://localhost/wp1/wp-content/uploads/2024/05/image.jpeg" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                    <p class="has-text-align-center has-large-font-size">This is a cover caption</p>
                    <!-- /wp:paragraph --></div></div>
                    <!-- /wp:cover -->',
				'expected_markup' =>
					'<!-- wp:cover {"url":"http://localhost/wp1/wp-content/uploads/2024/05/image.jpeg","id":39,"alt":"Alternative text for cover image","dimRatio":50,"customOverlayColor":"#1d2b2f","layout":{"type":"constrained"}} -->
                    <div class="wp-block-cover"><span aria-hidden="true" class="wp-block-cover__background has-background-dim" style="background-color:#1d2b2f"></span><img class="wp-block-cover__image-background wp-image-39" alt="<?php esc_attr_e(\'Alternative text for cover image\', \'test-locale-theme\');?>" src="http://localhost/wp1/wp-content/uploads/2024/05/image.jpeg" data-object-fit="cover"/><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
                    <p class="has-text-align-center has-large-font-size"><?php esc_html_e(\'This is a cover caption\', \'test-locale-theme\');?></p>
                    <!-- /wp:paragraph --></div></div>
                    <!-- /wp:cover -->',
			),

			'media-text'                 => array(
				'block_markup'    =>
					'<!-- wp:media-text {"mediaId":39,"mediaLink":"http://localhost/wp1/image/","mediaType":"image"} -->
                    <div class="wp-block-media-text is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="http://localhost/wp1/wp-content/uploads/2024/05/image.jpeg" alt="This is alt text" class="wp-image-39 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph {"placeholder":"Content…"} -->
                    <p>Media text content test.</p>
                    <!-- /wp:paragraph --></div></div>
                    <!-- /wp:media-text -->',
				'expected_markup' =>
					'<!-- wp:media-text {"mediaId":39,"mediaLink":"http://localhost/wp1/image/","mediaType":"image"} -->
                    <div class="wp-block-media-text is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="http://localhost/wp1/wp-content/uploads/2024/05/image.jpeg" alt="<?php esc_attr_e(\'This is alt text\', \'test-locale-theme\');?>" class="wp-image-39 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph {"placeholder":"Content…"} -->
                    <p><?php esc_html_e(\'Media text content test.\', \'test-locale-theme\');?></p>
                    <!-- /wp:paragraph --></div></div>
                    <!-- /wp:media-text -->',
			),

			'pullquote'                  => array(
				'block_markup'    =>
					'<!-- wp:pullquote -->
                    <figure class="wp-block-pullquote"><blockquote><p>Yo me equivoqué y pagué, pero la pelota no se mancha.</p><cite>Diego Armando Maradona</cite></blockquote></figure>
                    <!-- /wp:pullquote -->',
				'expected_markup' =>
					'<!-- wp:pullquote -->
                    <figure class="wp-block-pullquote"><blockquote><p><?php esc_html_e(\'Yo me equivoqué y pagué, pero la pelota no se mancha.\', \'test-locale-theme\');?></p><cite><?php esc_html_e(\'Diego Armando Maradona\', \'test-locale-theme\');?></cite></blockquote></figure>
                    <!-- /wp:pullquote -->',
			),

			'table'                      => array(
				'block_markup'    =>
					'<!-- wp:table -->
                    <figure class="wp-block-table"><table><tbody><tr><td>Team</td><td>Points</td></tr><tr><td>Boca</td><td>74</td></tr><tr><td>River</td><td>2</td></tr></tbody></table><figcaption class="wp-element-caption">Score table</figcaption></figure>
                    <!-- /wp:table -->',
				'expected_markup' =>
					'<!-- wp:table -->
                    <figure class="wp-block-table"><table><tbody><tr><td><?php esc_html_e(\'Team\', \'test-locale-theme\');?></td><td><?php esc_html_e(\'Points\', \'test-locale-theme\');?></td></tr><tr><td><?php esc_html_e(\'Boca\', \'test-locale-theme\');?></td><td><?php esc_html_e(\'74\', \'test-locale-theme\');?></td></tr><tr><td><?php esc_html_e(\'River\', \'test-locale-theme\');?></td><td><?php esc_html_e(\'2\', \'test-locale-theme\');?></td></tr></tbody></table><figcaption class="wp-element-caption"><?php esc_html_e(\'Score table\', \'test-locale-theme\');?></figcaption></figure>
                    <!-- /wp:table -->',
			),

			'video'                      => array(
				'block_markup'    => '<!-- wp:video -->
<figure class="wp-block-video"><video controls src="http://example.org/video.mp4"></video><figcaption class="wp-element-caption">Video caption test</figcaption></figure>
<!-- /wp:video -->',
				'expected_markup' =>
					'<!-- wp:video -->
<figure class="wp-block-video"><video controls src="http://example.org/video.mp4"></video><figcaption class="wp-element-caption"><?php esc_html_e(\'Video caption test\', \'test-locale-theme\');?></figcaption></figure>
<!-- /wp:video -->',
			),
		);
	}
}
