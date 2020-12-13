<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    ST_BB
 * @subpackage ST_BB/includes
 */
class ST_BB {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ST_BB_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'ST_BB_VERSION' ) ) {
			$this->version = ST_BB_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'st-bb';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ST_BB_Loader. Orchestrates the hooks of the plugin.
	 * - ST_BB_i18n. Defines internationalization functionality.
	 * - ST_BB_Admin. Defines all hooks for the admin area.
	 * - ST_BB_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-i18n.php';
		
		/**
		 * The class responsible for defining all utility functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-utility.php';

		/**
		 * The class responsible for defining all hooked functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hook-manager.php';

		/**
		 * The class responsible for managing BB modules.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/bb-modules/class-bb-module-manager.php';

		/**
		 * The class responsible for managing ACF content modules.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-acf-module-manager.php';

		/**
		 * The class responsible for defining ACF content module field groups.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/bb-modules/class-content-module-fields-group.php';

		$this->loader = new ST_BB_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ST_BB_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new ST_BB_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the core plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_hooks() {

		$plugin_hook_mgr = new ST_BB_Hook_Manager( $this->get_plugin_name(), $this->get_version() );

		// Initialize all our BB modules.
		$this->loader->add_action( 'init', $plugin_hook_mgr, 'init_bb_modules' );

		// Disable all standard BB modules.
		$this->loader->add_filter( 'fl_builder_register_module', $plugin_hook_mgr, 'disable_standard_modules', 10, 2 );

		// Remove rows functionality from panel.
		$this->loader->add_action( 'fl_builder_content_panel_data', $plugin_hook_mgr, 'remove_panel_rows_functionality' );

		// Initialize ACF component of the plugin.
		$this->loader->add_action( 'plugins_loaded', $plugin_hook_mgr, 'init_acf_module_mgr' );

		// Enqueue scripts and styles.
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_hook_mgr, 'enqueue_admin_styles_and_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_hook_mgr, 'enqueue_public_styles_and_scripts' );

		// Amend BB global settings.
		$this->loader->add_action( 'fl_builder_register_settings_form', $plugin_hook_mgr, 'amend_bb_global_settings', 10, 2 );
		
		// Remove BB content wrapping on front end, set default margins and padding to zero and width to full width.
		$this->loader->add_action( 'fl_builder_after_render_content', $plugin_hook_mgr, 'remove_bb_frontend_content_wrap' );
		$this->loader->add_filter( 'fl_builder_template_path', $plugin_hook_mgr, 'remove_bb_frontend_row_and_module_wrap', 10, 3 );

		// Set defaults on vertical spacing.
		$this->loader->add_filter( 'fl_builder_register_settings_form', $plugin_hook_mgr, 'add_padding_settings', 10, 2 );
		
		// Set the location of the module html file for when saving content to the standard DB location.
	 	$this->loader->add_filter( 'fl_builder_render_module_html', $plugin_hook_mgr, 'render_modules_html_file', 10, 4 );
		
		// Add classes to sections.
		$this->loader->add_filter( 'fl_builder_module_attributes', $plugin_hook_mgr, 'add_section_classes', 10, 2 );

		// Add in instance CSS for all ST BB modules to handle row background image and colour.
		$this->loader->add_filter( 'fl_builder_render_css', $plugin_hook_mgr, 'add_instance_css', 10, 4 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    ST_BB_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
