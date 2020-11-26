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
				self::$modules[ $instance->slug ] = $instance;

				// Generate ACF field.
				self::$acf_modules[ $instance->slug ] = self::get_acf_settings_from_bb_module( $instance->slug );

			}
		}

	}

	/**
	 * Get module class name from directory containing class.
	 * Strips 'st-bb-' prefix, then converts dashes to underscores and capitalizes first letters.
	 *
	 * @since    1.0.0
	 * @hooked init
	 */
	private static function get_class_name_from_dir( $dirname, $prepend = '', $append = '' ) {
		$module_id = str_replace( 'st-bb-', '', $dirname );
		return $prepend . implode( '_', array_map( 'ucfirst', explode( '-', $module_id ) ) ) . $append;
	}

	/**
	 * Generate and store the ACF module field format
	 *
	 * @since    1.0.0
	 * 
	 * @param	string	$slug	module slug
	 * @return	array
	 */
	private static function get_acf_settings_from_bb_module( $slug ) {

		$form = FLBuilderModel::$modules[ $slug ]->form;
		cwine_error_log($form);
		
		return array();

		// acf_add_local_field_group(array(
		// 	'key' => 'group_5fbd0eab95390',
		// 	'title' => 'STBB: Hero',
		// 	'fields' => array(
		// 		array(
		// 			'key' => 'field_5fbe45ab558b7',
		// 			'label' => 'Content',
		// 			'name' => '',
		// 			'type' => 'tab',
		// 			'instructions' => '',
		// 			'required' => 0,
		// 			'conditional_logic' => 0,
		// 			'wrapper' => array(
		// 				'width' => '',
		// 				'class' => '',
		// 				'id' => '',
		// 			),
		// 			'placement' => 'top',
		// 			'endpoint' => 0,
		// 		),
		// 		array(
		// 			'key' => 'field_5fbe45d4558b8',
		// 			'label' => 'Text',
		// 			'name' => 'text',
		// 			'type' => 'group',
		// 			'instructions' => '',
		// 			'required' => 0,
		// 			'conditional_logic' => 0,
		// 			'wrapper' => array(
		// 				'width' => '',
		// 				'class' => '',
		// 				'id' => '',
		// 			),
		// 			'layout' => 'block',
		// 			'sub_fields' => array(
		// 				array(
		// 					'key' => 'field_5fbe460f558b9',
		// 					'label' => 'Heading',
		// 					'name' => 'title',
		// 					'type' => 'text',
		// 					'instructions' => '',
		// 					'required' => 0,
		// 					'conditional_logic' => 0,
		// 					'wrapper' => array(
		// 						'width' => '',
		// 						'class' => '',
		// 						'id' => '',
		// 					),
		// 					'default_value' => '',
		// 					'placeholder' => '',
		// 					'prepend' => '',
		// 					'append' => '',
		// 					'maxlength' => '',
		// 				),
		// 				array(
		// 					'key' => 'field_5fbe4620558ba',
		// 					'label' => 'Sub-heading',
		// 					'name' => 'subtitle',
		// 					'type' => 'text',
		// 					'instructions' => '',
		// 					'required' => 0,
		// 					'conditional_logic' => 0,
		// 					'wrapper' => array(
		// 						'width' => '',
		// 						'class' => '',
		// 						'id' => '',
		// 					),
		// 					'default_value' => '',
		// 					'placeholder' => '',
		// 					'prepend' => '',
		// 					'append' => '',
		// 					'maxlength' => '',
		// 				),
		// 			),
		// 		),
		// 		array(
		// 			'key' => 'field_5fbe4520debff',
		// 			'label' => 'Background',
		// 			'name' => '',
		// 			'type' => 'tab',
		// 			'instructions' => '',
		// 			'required' => 0,
		// 			'conditional_logic' => 0,
		// 			'wrapper' => array(
		// 				'width' => '',
		// 				'class' => '',
		// 				'id' => '',
		// 			),
		// 			'placement' => 'top',
		// 			'endpoint' => 0,
		// 		),
		// 		array(
		// 			'key' => 'field_5fbe44716cb3c',
		// 			'label' => 'Colour',
		// 			'name' => 'row_colour',
		// 			'type' => 'group',
		// 			'instructions' => '',
		// 			'required' => 0,
		// 			'conditional_logic' => 0,
		// 			'wrapper' => array(
		// 				'width' => '',
		// 				'class' => '',
		// 				'id' => '',
		// 			),
		// 			'layout' => 'row',
		// 			'sub_fields' => array(
		// 				array(
		// 					'key' => 'field_5fbd0eac76484',
		// 					'label' => 'Colour',
		// 					'name' => 'row_bg_color',
		// 					'type' => 'color_picker',
		// 					'instructions' => '',
		// 					'required' => 0,
		// 					'conditional_logic' => 0,
		// 					'wrapper' => array(
		// 						'width' => '',
		// 						'class' => '',
		// 						'id' => '',
		// 					),
		// 					'default_value' => '',
		// 				),
		// 				array(
		// 					'key' => 'field_5fbd0eac814b0',
		// 					'label' => 'Opacity (%)',
		// 					'name' => 'row_bg_opacity',
		// 					'type' => 'range',
		// 					'instructions' => '',
		// 					'required' => 0,
		// 					'conditional_logic' => 0,
		// 					'wrapper' => array(
		// 						'width' => '',
		// 						'class' => '',
		// 						'id' => '',
		// 					),
		// 					'default_value' => 100,
		// 					'min' => '',
		// 					'max' => '',
		// 					'step' => '',
		// 					'prepend' => '',
		// 					'append' => '',
		// 				),
		// 			),
		// 		),
		// 		array(
		// 			'key' => 'field_5fbd0ed0e940f',
		// 			'label' => 'Image',
		// 			'name' => 'row_image',
		// 			'type' => 'group',
		// 			'instructions' => '',
		// 			'required' => 0,
		// 			'conditional_logic' => 0,
		// 			'wrapper' => array(
		// 				'width' => '',
		// 				'class' => '',
		// 				'id' => '',
		// 			),
		// 			'layout' => 'row',
		// 			'sub_fields' => array(
		// 				array(
		// 					'key' => 'field_5fbd0eac79f31',
		// 					'label' => 'Image',
		// 					'name' => 'row_image_src',
		// 					'type' => 'image',
		// 					'instructions' => '',
		// 					'required' => 0,
		// 					'conditional_logic' => 0,
		// 					'wrapper' => array(
		// 						'width' => '',
		// 						'class' => '',
		// 						'id' => '',
		// 					),
		// 					'return_format' => 'url',
		// 					'preview_size' => 'medium',
		// 					'library' => 'all',
		// 					'min_width' => '',
		// 					'min_height' => '',
		// 					'min_size' => '',
		// 					'max_width' => '',
		// 					'max_height' => '',
		// 					'max_size' => '',
		// 					'mime_types' => '',
		// 				),
		// 				array(
		// 					'key' => 'field_5fbd0eac7da19',
		// 					'label' => 'Image alt',
		// 					'name' => 'row_image_alt',
		// 					'type' => 'text',
		// 					'instructions' => '',
		// 					'required' => 0,
		// 					'conditional_logic' => 0,
		// 					'wrapper' => array(
		// 						'width' => '',
		// 						'class' => '',
		// 						'id' => '',
		// 					),
		// 					'default_value' => '',
		// 					'placeholder' => '',
		// 					'prepend' => '',
		// 					'append' => '',
		// 					'maxlength' => '',
		// 				),
		// 			),
		// 		),
		// 	),
		// 	'location' => array(
		// 		array(
		// 			array(
		// 				'param' => 'page_template',
		// 				'operator' => '==',
		// 				'value' => 'default',
		// 			),
		// 		),
		// 	),
		// 	'menu_order' => 1,
		// 	'position' => 'normal',
		// 	'style' => 'default',
		// 	'label_placement' => 'top',
		// 	'instruction_placement' => 'label',
		// 	'hide_on_screen' => '',
		// 	'active' => true,
		// 	'description' => '',
		// ));

	}

}
