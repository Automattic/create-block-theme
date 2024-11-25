<?php

//add action for when editor assets load
add_action('init', function () {

	// if the CBT plugin is active we don't need to do anything
	if (class_exists('CBT_Synced_Pattern_Loader')) {
		return;
	}

	function CBT_render_pattern($pattern_file)
	{
		ob_start();
		include $pattern_file;
		return ob_get_clean();
	}

	$theme    = wp_get_theme();
	$pattern_files = glob($theme->get_stylesheet_directory() . '/patterns/*.php');

	foreach ($pattern_files as $pattern_file) {

		$pattern_data = get_file_data($pattern_file, array(
			'title'         => 'Title',
			'slug'          => 'Slug',
			'description'   => 'Description',
			'inserter'      => 'Inserter',
			'synced'	=> 'Synced',
		));

		// if the pattern is not synced do nothing
		if ($pattern_data['synced'] !== 'yes') {
			continue;
		}

		$pattern_post = get_page_by_path(sanitize_title($pattern_data['slug']), OBJECT, 'wp_block');
		if ($pattern_post) {
			// the post exists
			$pattern_data['id'] = $pattern_post->ID;
			// Note, we are NOT updating the post.  If you want that behavior install the CBT plugin.
		}
		else {
			// the post does not exist.  create it.
			$pattern_data['content'] = CBT_render_pattern($pattern_file);
			$pattern_data['id'] = wp_insert_post(array(
				'post_title' => $pattern_data['title'],
				'post_name' => $pattern_data['slug'],
				'post_content' => $pattern_data['content'],
				'post_type' => 'wp_block',
				'post_status' => 'publish',
				'ping_status' => 'closed',
				'comment_status' => 'closed',
			));
		}

		// UN register the unsynced pattern and RE register it with the reference to the synced pattern
		// this pattern injects a synced pattern block as the content.
		// and allows it to be used by anything that uses the wp:pattern (rather than the wp:block)
		$pattern_registry = WP_Block_Patterns_Registry::get_instance();

		if ( $pattern_registry->is_registered($pattern_data['slug'])){
			$pattern_registry->unregister($pattern_data['slug']);
		}

		$pattern_registry->register(
			$pattern_data['slug'],
			array(
				'title'   => $pattern_data['title'],
				'inserter' => false,
				'content' => '<!-- wp:block {"ref":' . $pattern_data['id'] . '} /-->',
			)
		);

	}
});
