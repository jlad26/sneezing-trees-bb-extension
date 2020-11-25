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
	 * @var      array    $modules    Array of available module objects with slugs as keys.
	 */
	public static $modules = array();
	
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
				self::$modules[ $instance->slug ] = $instance;

			}
		}
		
	}

	/**
	 * Get module class name from directory containing class.
	 * Converts dashes to underscores and capitalizes first letters.
	 *
	 * @since    1.0.0
	 * @hooked init
	 */
	private static function get_class_name_from_dir( $dirname, $prepend = '', $append = '' ) {
		return $prepend . implode( '_', array_map( 'ucfirst', explode( '-', $dirname ) ) ) . $append;
	}

}
