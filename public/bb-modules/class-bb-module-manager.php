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

	/**
	 * Get settings array for a button.
	 * 
	 * @since    1.0.0
	 * 
	 * @param	string	$field_prefix	prefix to use for fields (required if more than one button in module)
	 * @param	string	$tab			the tab that the fields are in.
	 * @param	string	$section		the section the fields are in.
	 * @return	array
	 */
	public static function get_button_settings_fields( $field_prefix = '', $tabs = array( 'module' ), $sections = array( 'button' ) ) {
		
		if ( $field_prefix ) {
			$field_prefix .= '_';
		}
		
		// Generate a dropdown for each available post type.
		$post_types = ST_BB_Utility::get_post_types();

		$posts = $dropdown_fields = array();

		// Make the first choice a standard url
		$link_types = array(
			'url'		=> __( 'URL', ST_BB_TD ),
			'anchor'	=> __( 'Anchor', ST_BB_TD ),
		);

		$toggle_fields = array(
			'url'	=>	array(
				'fields'    =>  array(
					$field_prefix . 'button_url',
					$field_prefix . 'button_new_window',
					$field_prefix . 'button_nofollow'
				),
				'sections'  =>  $sections,
				'tabs'      =>  $tabs,
			),
			'anchor'	=>	array(
				'fields'    =>  array( $field_prefix . 'button_anchor_target' ),
				'sections'  =>  $sections,
				'tabs'      =>  $tabs,
			),
		);

		// Add in link type and toggle field for each post type.
		foreach ( $post_types as $post_type => $post_type_object ) {

			$link_types[ $post_type ] = $post_type_object->labels->singular_name;
			
			$toggle_fields[ $post_type ] = array(
				'fields'	=>	array( $field_prefix . 'select_' . $post_type . '_id' ),
				'sections'  =>  $sections,
				'tabs'      =>  $tabs,
			);
			
			$args = array(
				'post_type'			=>	$post_type,
				'post_status'		=>	'publish',
				'orderby'			=>	'title',
				'order'				=>	'ASC',
				'posts_per_page'	=>	-1,
			);

			// Get the posts for this post type.
			$posts = get_posts( $args );
			if ( $posts ) {
				
				// Make the dropdown for this post type.
				$options = array();
				foreach ( $posts as $post ) {
					$options['id_' . $post->ID] = $post->post_title;
				}

				$dropdown_fields[ $field_prefix . 'select_' . $post_type . '_id' ] = array(
					'type'          => 'select',
					'label'         => $post_type_object->labels->singular_name,
					'default'       => '',
					'options'       =>  $options,
					'sanitize'		=>	'sanitize_text_field',
				);

			}

		}
		
		$fields = array(
			$field_prefix . 'button_text'	=> array(
				'type'          => 'text',
				'label'         => 'Text',
				'default'       => '',
				'preview'	=> array(
					'type'		=> 'text',
					'selector'	=> '.' . $field_prefix . 'st-bb-btn',
				),
				'sanitize'	=>	'sanitize_text_field',
			),
			$field_prefix . 'button_url_type'	=> array(
				'type'          =>	'select',
				'label'         =>	__( 'Link type', ST_BB_TD ),
				'default'       =>	'url',
				'options'       =>  $link_types,
				'toggle'		=>  $toggle_fields,
				'sanitize'		=>	'sanitize_text_field',
			),
			$field_prefix . 'button_url'	=> array(
				'type'          =>	'link',
				'label'         =>	__( 'Link', ST_BB_TD ),
				'default'       =>	'',
				'sanitize'		=>	'esc_url_raw',
			),
			$field_prefix . 'button_new_window'	=> array(
				'type'          =>	'select',
				'label'         =>	__( 'New window', ST_BB_TD ),
				'default'       =>	'no',
				'options'       =>  array(
					'no'	=>	__( 'No', ST_BB_TD ),
					'yes'	=>	__( 'Yes', ST_BB_TD ),
				),
				'help'	=>	__( 'Choose Yes to make the link open in a new tab or window.', ST_BB_TD ),
				'sanitize'		=>	'sanitize_text_field',
			),
			$field_prefix . 'button_nofollow'	=> array(
				'type'          =>	'select',
				'label'         =>	__( 'No follow', ST_BB_TD ),
				'default'       =>	'no',
				'options'       =>  array(
					'no'	=>	__( 'No', ST_BB_TD ),
					'yes'	=>	__( 'Yes', ST_BB_TD ),
				),
				'help'	=>	__( 'Choose Yes to indicate to search engines that the link links to an unendorsed document, like a paid link.', ST_BB_TD ),
				'sanitize'		=>	'sanitize_text_field',
			),
			$field_prefix . 'button_anchor_target'	=> array(
				'type'          =>	'text',
				'label'         =>	__( 'Anchor target', ST_BB_TD ),
				'help'			=>	__( 'Do not include the # symbol', ST_BB_TD ),
				'sanitize'		=>	'ST_BB_Module_Manager::sanitize_anchor_target',
			),
		);

		$fields = array_merge( $fields, $dropdown_fields );

		return $fields;

	}

}