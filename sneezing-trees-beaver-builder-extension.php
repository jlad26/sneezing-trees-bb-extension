<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * Plugin Name:       Sneezing Trees Beaver Builder Extension
 * Description:       Extends Beaver Builder with various modules also suitable for use by ACF.
 * Version:           1.0.0
 * Author:            Jon Anwyl
 * Author URI:        https://www.sneezingtrees.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sneezing-trees-bb-extension
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ST_BB_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
function activate_st_bb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	ST_BB_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
function deactivate_st_bb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	ST_BB_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_st_bb' );
register_deactivation_hook( __FILE__, 'deactivate_st_bb' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-st-bb.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_st_bb() {

	$plugin = new ST_BB();
	$plugin->run();

}
run_st_bb();
