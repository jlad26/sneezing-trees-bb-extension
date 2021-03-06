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
	 * Initialize ACF component of the plugin.
	 *
	 * @since    1.0.0
	 * @hooked plugins_loaded
	 */
	public function init_acf_module_mgr() {
		new ST_BB_ACF_Module_Manager( $this->plugin_name, $this->version );
	}

	/**
	 * Register the stylesheets and Javascript for the admin area.
	 *
	 * @since    1.0.0
	 * @hooked	admin_enqueue_scripts
	 */
	public function enqueue_admin_styles_and_scripts() {

		$url_base = plugin_dir_url( dirname( __FILE__ ) ) . 'public/';
		
		wp_enqueue_style( $this->plugin_name . '-admin', $url_base . 'css/admin.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name . '-admin-js', $url_base . 'js/admin.js', array( 'jquery' ), $this->version, false );

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
		wp_enqueue_style( $this->plugin_name . '-public', $url_base . 'css/public.css', array(), $this->version, 'all' );
		
		// Bootstrap grid.
		wp_enqueue_style( $this->plugin_name . '-bootstrap', $url_base . 'css/bootstrap-grid.' . $min . 'css', array(), $this->version, 'all' );

		// Remove the bootstrap tour
		wp_dequeue_style( 'bootstrap-tour' );
		wp_dequeue_script( 'bootstrap-tour' );
		
		/**
		 * We enqueue this only so that we have a handle for ACF module-specific JS to depend on,
		 * and set to enqueue in footer as required for LightGallery.
		 */
		wp_enqueue_script( $this->plugin_name . '-public', $url_base . 'js/public.js', array(), $this->version, true );

	}

	/**
	 * Disable all standard BB modules.
	 *
	 * @since    1.0.0
	 * @hooked fl_builder_register_module
	 */
	public function disable_standard_modules( $enabled, $instance ) {
		if ( ! is_subclass_of( $instance, 'ST_BB_Module' ) ) {
			$enabled = false;
		}
		return $enabled;
	}

	/**
     * Remove rows functionality from panel.
     * @hooked	fl_builder_content_panel_data
     */
	public static function remove_panel_rows_functionality( $data ) {
		unset( $data['tabs']['rows'] );
		return $data;
	}

	/**
	 * Remove the bootstrap dep
	 * @hooked	wp_enqueue_scripts
	 */
	public static function remove_bb_styles() {
		wp_dequeue_style( 'bootstrap-tour' );
		wp_dequeue_script( 'bootstrap-tour' );
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
	 * @hooked	fl_builder_before_render_shortcodes
	 */
	public function remove_bb_frontend_content_wrap( $content ) {
		if ( ! is_admin() && ! isset( $_GET['fl_builder'] ) ) {

			// Strip off opening <div> tag and closing </div> tag.
			$matches = array();
			preg_match( '/<div(.*?)>/', $content, $matches );
			$content = substr( $content, strlen( $matches[0] ), -6 );

		}
		return $content;
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
	 * Set defaults on vertical spacing.
	 * @hooked	fl_builder_register_settings_form
	 */
	public static function add_padding_settings( $form, $id ) {

		// Only add if there is a default padding to add.
		if ( ! isset( FlBuilderModel::$modules[ $id ]->config['default_padding'] ) ) {
			return $form;
		}

		$module_padding_default = FlBuilderModel::$modules[ $id ]->config['default_padding'];
		
		// Set the default padding
		$form['styling']['sections']['layout']['fields']['vspace']['options'][ $module_padding_default ] .= ' (' . __( 'Default', ST_BB_TD ) . ')';
		$form['styling']['sections']['layout']['fields']['vspace']['default'] = $module_padding_default;
		
		return $form;

	}

	/**
	 * Set the location of the module html file for when saving content to the standard DB location.
	 * @hooked	fl_builder_render_module_html
	 */
	public static function render_modules_html_file( $path, $type, $settings, $module ) {
		if ( is_subclass_of( $module, 'ST_BB_Module' ) ) {
			$path = ST_BB_DIR . 'public/partials/frontend-module.php';
		}
		return $path;
	}

	/**
	 * Add classes to sections.
	 * @hooked	fl_builder_module_attributes
	 */
	public function add_section_classes( $attrs, $module ) {
		
		$section_classes = array();

		// Add in classes generic to this module type.
		if ( isset( $module->config['section_classes'] ) ) {
			foreach ( $module->config['section_classes'] as $section_class ) {
				$section_classes[] = $section_class;
			}
		}

		// Add in classes specific to this instance.
		if ( isset( $module->settings->section_classes ) ) {
			if ( ! empty( $module->settings->section_classes ) ) {
				$instance_classes = explode( ' ', $module->settings->section_classes );
				$section_classes = array_merge( $section_classes, $instance_classes );
			}
		}

		// Add in padding class.
		if ( isset( $module->settings->vspace ) ) {
			if ( 'none' != $module->settings->vspace ) {
				$section_classes = array_merge( $section_classes, array( $module->settings->vspace ) );
			}
		}

		// Add in vertical centering if set to full screen.
		if ( isset( $module->settings->row_height ) ) {
			if ( 'screen' == $module->settings->row_height ) {
				$section_classes = array_merge( $section_classes, array( 'd-flex', 'align-items-center' ) );
			}
		}

		// Allow amendment of section classes.
		$section_classes = apply_filters( 'st_bb_section_classes', $section_classes, $module );

		if ( ! empty( $section_classes ) ) {
			$attrs['class'] = array_merge( $attrs['class'],  $section_classes );
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

	/**
	 * Add instance JS to all modules.
	 * @hooked	fl_builder_render_js
	 */
	public function add_instance_js( $js, $nodes, $global_settings, $include_global ) {
		
		foreach ( $nodes['modules'] as $module ) {
			$settings = $module->settings;
			$id       = $module->node;
			ob_start();
			include ST_BB_DIR . 'public/bb-modules/includes/frontend.js.php';
			FLBuilderCSS::render();
			$js .= ob_get_clean();
		}
		return $js;
	}

}
