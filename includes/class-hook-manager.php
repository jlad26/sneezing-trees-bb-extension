<?php

/**
 * The core functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/includes
 */

/**
 * The core functionality of the plugin.
 *
 * Defines the plugin name, version, all hooked core functions.
 *
 * @package    ST_BB
 * @subpackage ST_BB/includes
 */
class ST_BB_Hook_Manager {

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
	 * Initialize all BB modules.
	 *
	 * @since    1.0.0
	 * @hooked init
	 */
	public function init_bb_modules() {
		
		ST_BB_Module_Manager::init_bb_modules();
	}

	/**
	 * Register the stylesheets and Javascript for the admin area.
	 *
	 * @since    1.0.0
	 * @hooked	admin_enqueue_scripts
	 */
	public function enqueue_admin_styles_and_scripts() {

		$url_base = plugin_dir_url( dirname( __FILE__ ) ) . 'public/';
		
		wp_enqueue_style( $this->plugin_name . '-admin-css', $url_base . 'css/admin.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name . '-public-css', $url_base . 'js/admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the stylesheets and Javascript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @hooked	wp_enqueue_scripts
	 */
	public function enqueue_public_styles_and_scripts() {

		$url_base = plugin_dir_url( dirname( __FILE__ ) ) . 'public/';
		
		$min = WP_DEBUG ? '' : 'min.';

		// General public-facing styles.
		wp_enqueue_style( $this->plugin_name . '-public-css', $url_base . 'css/public.' . $min . 'css', array(), $this->version, 'all' );
		
		// Bootstrap grid.
		wp_enqueue_style( $this->plugin_name . '-bootstrap-css', $url_base . 'css/bootstrap-grid.' . $min . 'css', array(), $this->version, 'all' );

		// wp_enqueue_script( $this->plugin_name . '-public-js', $url_base . 'js/public.js', array( 'jquery' ), $this->version, false );

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
