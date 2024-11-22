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
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-create-block-theme-synced-pattern-loader.php';

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
		$synced_loader = new CBT_Synced_Pattern_Loader();
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
