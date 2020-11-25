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
	 * Initialize all BB modules.
	 *
	 * @since    1.0.0
	 * @hooked init
	 */
	public static function init_bb_modules() {
		
		// Load parent class.
		require_once ST_BB_DIR . 'public/bb-modules/class-st-bb-module.php';
		
		// Load all modules.
		$module_dirs = scandir( ST_BB_DIR . 'public/bb-modules/modules' );
		
		$modules = array(
			'hero',
		);
		foreach ( $modules as $module ) {
			require_once ST_BB_DIR . 'public/bb-modules/modules/' . $module . '/' . $module . '.php';
		}
	}

}
