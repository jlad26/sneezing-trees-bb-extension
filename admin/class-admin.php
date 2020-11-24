<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ST_BB
 * @subpackage ST_BB/admin
 */
class ST_BB_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Load all BB modules.
	 *
	 * @since    1.0.0
	 */
	public function load_bb_modules() {
		
		// Load parent class.
		require_once ST_BB_DIR . 'public/bb-modules/class-st-bb-module.php';
		
		// Load all modules.
		$modules = array(
			'hero',
		);
		foreach ( $modules as $module ) {
			require_once ST_BB_DIR . 'public/bb-modules/' . $module . '/' . $module . '.php';
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ST_BB_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ST_BB_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name . '-admin-css', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ST_BB_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ST_BB_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name . '-public-css', plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );

	}

	
	/**
	 * Disable Gutenberg editor.
	 *
	 * @since    1.0.0
	 * @hooked use_block_editor_for_post
	 */
	public function disable_gutenburg( $enable ) {
		return false;
	}

	/**
	 * Amend global BB settings, stripping out default padding and margins.
	 *
	 * @since    1.0.0
	 * @hooked fl_builder_register_settings_form
	 */
	public function amend_bb_global_settings( $form, $id ) {
		
		if ( 'global' == $id ) {
			$form['tabs']['general']['sections']['rows']['fields']['row_padding']['default'] = 0;
			$form['tabs']['general']['sections']['rows']['fields']['row_width_default']['default'] = 'full';
			$form['tabs']['general']['sections']['rows']['fields']['row_content_width_default']['default'] = 'full';
			$form['tabs']['general']['sections']['modules']['fields']['module_margins']['default'] = 0;
		}

		return $form;

	}

	/**
	 * Remove BB outer content wrapping on front end.
	 * @hooked	fl_builder_after_render_content
	 */
	public function remove_bb_frontend_content_wrap() {
		if ( ! is_admin() && ! isset( $_GET['fl_builder'] ) ) {
			ob_clean();
			FlBuilder::render_nodes();
		}
	}
	
	/**
	 * Remove BB rows and modules content wrapping on front end by
	 * replacing standard template with our own.
	 * @hooked	fl_builder_template_path
	 */
	public function remove_bb_frontend_row_and_module_wrap( $template_path, $template_base, $slug ) {

		switch( $template_base ) {

			case 'row':
				if ( ! is_admin() && ! isset( $_GET['fl_builder'] ) ) {
					$template_path = ST_BB_DIR . 'public/partials/frontend-row.php';
				}
				break;
			
			case 'module':
				$template_path =  ST_BB_DIR . 'public/partials/frontend-module.php';
				break;


		}

		return $template_path;
	}

	/**
	 * Add class to sections.
	 * @hooked	fl_builder_module_attributes
	 */
	public function add_class_to_sections( $attrs, $module ) {
		if ( is_subclass_of( $module, 'ST_BB_Module' ) ) {
			$attrs['class'][] = 'st-bb-section';
			$attrs['class'] = apply_filters( 'st_bb_section_classes', $attrs['class'], $module );
		}
		return $attrs;
	}

	/**
	 * Add instance CSS to all modules. This handles row background image and colour.
	 * @hooked	fl_builder_render_css
	 */
	public function add_instance_css( $css, $nodes, $global_settings, $include_global ) {
		foreach ( $nodes['modules'] as $module ) {
			$settings = $module->settings;
			$id       = $module->node;
			ob_start();
			include ST_BB_DIR . 'public/bb-modules/includes/frontend.css.php';
			FLBuilderCSS::render();
			$css .= ob_get_clean();
		}
		return $css;
	}

}
