<?php

/**
 * Manages BB Modules.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public/bb-modules
 */

/**
 * Manages BB Modules.
 *
 * Handles initialization and registering of BB modules.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public/bb-modules
 */
class ST_BB_Module_Manager {

	/**
	 * Modules available for use by the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $modules    Array of slugs of available modules.
	 */
	public static $modules = array();

	/**
	 * ACF Modules available for use by the plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $modules    Array of available ACF module settings with slugs as keys.
	 */
	public static $acf_modules = array();

	
	
	/**
	 * Initialize all BB modules.
	 *
	 * @since    1.0.0
	 * @hooked init
	 */
	public static function init_bb_modules() {
		
		// Load parent class.
		require_once ST_BB_DIR . 'public/bb-modules/class-st-bb-module.php';
		
		// Load and register all modules.
		$module_dirs = scandir( ST_BB_DIR . 'public/bb-modules/modules' );
		foreach ( $module_dirs as $key => $dir ) {
			if ( ! in_array( $dir, array( '.', '..' ) ) ) {
				
				// Load module.
				require_once ST_BB_DIR . 'public/bb-modules/modules/' . $dir . '/' . $dir . '.php';
				
				// Init module.
				$class_name = self::get_class_name_from_dir( $dir, 'ST_BB_', '_Module' );
				call_user_func( array( $class_name, 'init' ) );

				// Register module with plugin.
				$instance = new $class_name();
				self::$modules[] = $instance->slug;

				// Generate ACF field.
				self::$acf_modules[ $instance->slug ] = ST_BB_ACF_Module_Manager::get_acf_settings_from_bb_module( $instance->slug );

			}
		}

	}

	/**
	 * Get module class name from directory containing class.
	 * Strips 'st-bb-' prefix, then converts dashes to underscores and capitalizes first letters.
	 *
	 * @since    1.0.0
	 */
	private static function get_class_name_from_dir( $dirname, $prepend = '', $append = '' ) {
		$module_id = str_replace( 'st-bb-', '', $dirname );
		return $prepend . implode( '_', array_map( 'ucfirst', explode( '-', $module_id ) ) ) . $append;
	}

	/**
	 * Get all registered modules.
	 *
	 * @since    1.0.0
	 * @return 	array	array of module instances
	 */
	public static function get_registered_modules() {
		$modules = array();
		foreach ( self::$modules as $module ) {
			$modules[ $module ] = FlBuilderModel::$modules[ $module ];
		}
		return $modules;
	}

}