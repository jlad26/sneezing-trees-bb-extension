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
	 * Data on post types.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $post_types_data    Array of data with post type as key.
	 */
	public static $post_types_data = array();

	/**
	 * Initialize all BB modules.
	 *
	 * @since    1.0.0
	 * @hooked init
	 */
	public static function init_bb_modules() {
		
		// Load parent class.
		require_once ST_BB_DIR . 'public/bb-modules/class-st-bb-module.php';

		// Load carousel parent class.
		require_once ST_BB_DIR . 'public/bb-modules/class-st-bb-carousel-module.php';

		// Set post types and corresponding posts here so it only has to be done once.
		$post_types_data = ST_BB_Utility::get_post_types();
		$post_type_slugs = array();
		foreach ( $post_types_data as $post_type => $post_type_object ) {
			$post_type_slugs[] = $post_type;
		}

		foreach ( $post_types_data as $post_type => $post_type_object ) {
			self::$post_types_data[ $post_type ] = array(
				'label'	=>	$post_type_object->labels->singular_name,
				'posts'	=>	array()
			);
		}

		$args = array(
			'post_type'					=>	$post_type_slugs,
			'post_status'				=>	'publish',
			'orderby'					=>	'title',
			'order'						=>	'ASC',
			'posts_per_page'			=>	-1,
			'no_found_rows'				=>	true,
			'update_post_term_cache'	=>	false,
			'update_post_meta_cache'	=>	false
		);

		$posts = get_posts( $args );

		foreach ( $posts as $post ) {
			self::$post_types_data[ $post->post_type ]['posts'][] = $post;
		}

		// Load and register all modules.
		$module_dirs = scandir( ST_BB_DIR . 'public/bb-modules/modules' );
		foreach ( $module_dirs as $key => $dir ) {
			if ( ! in_array( $dir, array( '.', '..' ) ) ) {
				
				// Load module.
				require_once ST_BB_DIR . 'public/bb-modules/modules/' . $dir . '/' . $dir . '.php';

				// Init module.
				$class_name = self::get_class_name_from_dir( $dir, 'ST_BB_', '_Module' );
				call_user_func( array( $class_name, 'init' ), $class_name );

				// Register module with plugin.
				$instance = new $class_name();
				self::$modules[] = $instance->slug;

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
		foreach ( self::$modules as $slug ) {
			$modules[ $slug ] = FlBuilderModel::$modules[ $slug ];
		}
		return $modules;
	}

	/**
	 * Render module section classes, putting together the BB module classes if
	 * they exist and the section classes.
	 * 
	 * @since    1.0.0
	 */
	public static function render_section_classes( $module ) {
		
		// If this is a BB module then display the BB attributes and our section classes...
		if ( ! empty( $module->form ) ) {
			FlBuilder::render_module_attributes( $module );
		// ...otherwise just use our section classes.
		} else {
			if ( $section_classes = $module->get_section_classes() ) {
				echo 'class="' . esc_attr( implode( ' ',  $section_classes ) ) . ' fl-node-' . $module->node . '"';
			}
		}
	}

	/**
	 * Sanitize anchor target field, stripping off # if accidentally included.
	 * 
	 * @since    1.0.0
	 * 
	 * @param	string	$anchor		value to sanitize.
	 * @return	string	sanitized string
	 */
	public static function sanitize_anchor_target( $anchor ) {

		$anchor = sanitize_text_field( $anchor );
		if ( 0 === strpos( $anchor, '#' ) ) {
			$anchor = substr( $anchor, 1 );
		}
		
		return $anchor;
	}

}