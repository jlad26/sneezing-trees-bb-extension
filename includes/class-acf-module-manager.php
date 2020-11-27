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
class ST_BB_ACF_Module_Manager {

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
		$this->define_hooks();

	}

	/**
	 * Register all hooks.
	 *
	 * @since    1.0.0
	 */
	private function define_hooks() {

		// Register custom post types and associated ACFs.
		add_action( 'init', array( $this, 'register_post_types' ) );

		// Add active field groups.
		add_action( 'init', array( $this, 'add_acf_field_groups' ), 20 );

		// Populate choice of ST BB modules when creating new ACF module.
		add_filter( 'acf/load_field/name=choose_st_bb_module', array( $this, 'populate_st_bb_module_choice' ) );

		// Populate choice of post types when creating new ACF module.
		add_filter( 'acf/load_field/name=acf_module_location_post_type', array( $this, 'populate_post_type_choice' ) );

		// When saving variable content modules, update the page / post postmeta to record which ACF modules are active.
		add_action( 'acf/save_post', array( $this, 'update_postmeta_with_variable_content_modules' ) );

		/**
		 * When deleting variable content modules, also delete all associated custom fields on posts / pages
		 * and update postmeta on those posts / pages.
		 */
		add_action( 'before_delete_post', array( $this, 'remove_post_meta_on_variable_content_module_deletion' ), 10, 2 );

	}

	/**
	 * Render ACF modules.
	 *
	 * @since	1.0.0
	 * 
	 * @param	string	$placement	Either 'before' or 'after' content
	 * @param	int		$post_id	ID of post against which fields are stored
	 * @param	bool	$echo		Whether output will be rendered or returned
	 */
	public static function render_acf_modules( $placement, $post_id = 0, $echo = true ) {

		// Get the post id if needed.
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		// Get all ACF fields.
		$field_sections = get_fields( $post_id );

		// End here if there are no fields.
		if ( empty( $field_sections ) ) {
			return false;
		}

		$modules_data = self::get_acf_modules_data( $placement, $field_sections );
		
		$registered_modules = ST_BB_Module_Manager::get_registered_modules();
		foreach ( $modules_data as $module_data ) {

			// Get the class name.
			$module_class = get_class( $registered_modules[ $module_data['type'] ] );
			$module = new $module_class();
			$settings = (object) $module_data['fields'];
			
			include( ST_BB_DIR . 'public/partials/frontend-module.php' );

		}

	}

	/**
	 * Get sorted modules data for a page / post.
	 *
	 * @since	1.0.0
	 * 
	 * @param	string	$placement			Either 'before' or 'after' content
	 * @param	array		$field_sections		Array of ACF fields sections.
	 */
	private static function get_acf_modules_data( $placement, $field_sections ) {

		$modules = array();
		
		// Cycle through each field sections.
		foreach ( $field_sections as $section => $fields ) {
			
			// Ignore any that haven't been stored by us.
			if ( 0 !== strpos( $section, 'section*_' ) ) {
				continue;
			}

			$acf_module_id = self::get_acf_module_id_from_section_name( $section );
			$acf_module_ids[] = $acf_module_id;

			// Store the fields against ACF module ID.
			foreach ( $fields as $field_key => $field_value ) {
				$data_key = str_replace( '**' . $acf_module_id, '', $field_key );
				$module_data[ $acf_module_id ][ $data_key ] = $field_value;
			}

		}

		// Get ACF module data.
		foreach ( $acf_module_ids as $acf_module_id ) {

			// Only include those with the right placement.
			if ( $placement == get_field( 'acf_module_location', $acf_module_id ) ) {
				
				// Place in array with key as order so can be sorted.
				$modules[ get_field( 'acf_module_order', $acf_module_id ) ] = array(
					'type'		=>	get_field( 'choose_st_bb_module', $acf_module_id ),
					'fields'	=>	$module_data[ $acf_module_id ]
				);

			}

		}

		// Sort the modules.
		ksort( $modules );

		return $modules;

	}

	/**
	 * Add ACF field groups, adding in content module IDs to make fields unique.
	 *
	 * @since	1.0.0
	 */
	public function add_acf_field_groups() {
		
		// Get all Content Module posts.
		$args = array(
			'post_type'		=>	'st-acf-module',
			'numberposts'	=>	-1,
			'post_status'	=>	'publish'
		);

		$field_groups = get_posts( $args );
		
		if ( is_array( $field_groups) ) {
			foreach ( $field_groups as $field_group ) {

				$module_field_group = get_fields( $field_group->ID );

				// Get the ACF field module.
				$acf_module = array();
				if ( isset( ST_BB_Module_Manager::$acf_modules[ $module_field_group['choose_st_bb_module'] ] ) ) {
					$acf_module = ST_BB_Module_Manager::$acf_modules[ $module_field_group['choose_st_bb_module'] ];
				}

				// Set overall key and title.
				$acf_module['key'] = '**st_acf_module_' . $field_group->ID;
				$acf_module['title'] = $field_group->post_title;

				// Cycle through and make all tab, section and field keys and names unique by adding ACF module ID.
				foreach ( $acf_module['fields'] as $key => $field ) {
					
					// Set the key
					$acf_module['fields'][ $key ]['key'] .= '**' . $field_group->ID;
					$acf_module['fields'][ $key ]['name'] .= '**' . $field_group->ID;
					if ( isset ( $acf_module['fields'][ $key ]['sub_fields'] ) ) {
						foreach ( $acf_module['fields'][ $key ]['sub_fields'] as $sub_field_key => $sub_field ) {
							$acf_module['fields'][ $key ]['sub_fields'][ $sub_field_key ]['key'] .= '**' . $field_group->ID;
							$acf_module['fields'][ $key ]['sub_fields'][ $sub_field_key ]['name'] .= '**' . $field_group->ID;
						}
					}
				}

				// Set up the location info.
				$location = array();
				
				// Work out whether we need to include page post type.
				$include_page_post_type = true;
				if ( isset( $module_field_group['acf_module_location_pages'] ) ) {
					if ( ! empty( $module_field_group['acf_module_location_pages'] ) ) {
						
						// Since we are specifying specific pages we should not include the page post type generally.
						$include_page_post_type = false;

						// Check whether page has been selected as a post type.
						$include_page_ids = false;
						if ( isset( $module_field_group['acf_module_location_post_type'] ) ) {
							if ( is_array( $module_field_group['acf_module_location_post_type'] ) ) {
								if ( in_array( 'page', $module_field_group['acf_module_location_post_type'] ) ) {
									$include_page_ids = true;
								}
							}
						}

						if ( $include_page_ids ) {
							foreach ( $module_field_group['acf_module_location_pages'] as $page_id ) {
								$location[] = array(
									array(
										'param' => 'page',
										'operator' => '==',
										'value' => $page_id,
									),
								);
							}
						}

					}
				}

				// Add in post type location info.
				if ( isset( $module_field_group['acf_module_location_post_type'] ) ) {
					foreach ( $module_field_group['acf_module_location_post_type'] as $post_type ) {
						
						// Don't add in page post type if we shouldn't
						if ( 'page' == $post_type && ! $include_page_post_type ) {
							continue;
						}
						
						$location[] = array(
							array(
								'param' => 'post_type',
								'operator' => '==',
								'value' => $post_type,
							),
						);
					}
				}

				$acf_module = array_merge( $acf_module, array(
					'location' => $location,
					'menu_order' => $module_field_group['acf_module_order'] ? $module_field_group['acf_module_order'] : 0,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => true,
					'description' => '',
				) );

				acf_add_local_field_group( $acf_module );

			}
		}

	}

	/**
	 * Get the ACF module ID from a field section name string.
	 *
	 * @since	1.0.0
	 * 
	 * @param	string	$section_name	Section name
	 */
	public static function get_acf_module_id_from_section_name( $section_name ) {
		$start = strrpos( $section_name, '**' );
		return substr( $section_name, $start + 2 );
	}

	/**
	 * Register custom post types.
	 *
	 * @since	1.0.0
	 * @hooked init
	 */
	public function register_post_types() {

		if ( ! is_blog_installed() ) {
			return;
		}

		$post_type_data = array();
		
		// Define Content Module post type
		$labels = array(
			'name'					=> _x( 'Content Module', 'post type general name', ST_BB_TD ),
			'singular_name'			=> _x( 'Content Module', 'post type singular name', ST_BB_TD ),
			'menu_name'             => __( 'Content Modules', ST_BB_TD ),
			'add_new'				=> _x( 'Add New', 'Content Module item', ST_BB_TD ),
			'add_new_item'			=> __( 'Add New Content Module', ST_BB_TD),
			'new_item'              => __( 'New Content Module', ST_BB_TD ),
			'edit_item'				=> __( 'Edit Content Module', ST_BB_TD ),
			'view_item'				=> __( 'View Content Module', ST_BB_TD ),
			'search_items'			=> __( 'Search Content Modules', ST_BB_TD ),
			'not_found'				=> __( 'No Content Modules found', ST_BB_TD ),
			'not_found_in_trash'	=> __( 'No Content Modules found in Trash', ST_BB_TD ),
			'all_items'				=> __( 'Content Modules', ST_BB_TD )
		);

		$post_type_data['st-acf-module'] = array(
			'labels'				=>	$labels,
			'description'         	=>	__( 'Fixed Advanced Custom Fields versions of Beaver Builder modules.', ST_BB_TD ),
			'public'				=>	false,
			'publicly_queryable'	=>	false,
			'show_ui'				=>	true,
			'show_in_menu'			=>	true,
			'menu_position'			=>	21,
			'menu_icon'				=>	'dashicons-text-page',
			'show_in_rest'			=>	false,
			'delete_with_user'		=>	false,
			'hierarchical'			=>	false,
			'capabilities' => array(
				'edit_posts' => 'manage_options',
				'edit_others_posts' => 'manage_options',
				'publish_posts' => 'manage_options',
				'read_private_posts' => 'manage_options',
				'read' => 'manage_options',
				'delete_posts' => 'manage_options',
				'delete_private_posts' => 'manage_options',
				'delete_published_posts' => 'manage_options',
				'delete_others_posts' => 'manage_options',
				'edit_private_posts' => 'manage_options',
				'edit_published_posts' => 'manage_options'
			),
			'map_meta_cap'			=>	true,
			'supports'				=>	array( 'title' )
		);

		$acf_field_groups['st-acf-module'] = array(
			array(
				'key' => 'group_5fbe30305bde3',
				'title' => 'ACF module',
				'fields' => array(
					array(
						'key' => 'field_5fbe30414defe',
						'label' => 'Module',
						'name' => 'choose_st_bb_module',
						'type' => 'select',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(),
						'default_value' => false,
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 1,
						'ajax' => 0,
						'return_format' => 'value',
						'placeholder' => '',
					),
					array(
						'key' => 'field_5fc0c15a03d63',
						'label' => 'Content type',
						'name' => 'acf_module_content_type',
						'type' => 'select',
						'instructions' => __( 'Fixed content modules must be programatically called and have the content set here. Variable content modules appear when editing pages / posts and content is set there.', ST_BB_TD ),
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'variable' => 'Variable Content',
							'fixed' => 'Fixed Content',
						),
						'default_value' => 'variable',
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 1,
						'ajax' => 0,
						'return_format' => 'value',
						'placeholder' => '',
					),
					array(
						'key' => 'field_5fbe3db40af36',
						'label' => 'Show on post types',
						'name' => 'acf_module_location_post_type',
						'type' => 'select',
						'instructions' => __( 'Add page as a post type to then select specific pages', ST_BB_TD ),
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(),
						'default_value' => array(
						),
						'allow_null' => 1,
						'multiple' => 1,
						'ui' => 1,
						'ajax' => 0,
						'return_format' => 'value',
						'placeholder' => '',
					),
					array(
						'key' => 'field_5fbe3d2a0af35',
						'label' => 'Show on pages',
						'name' => 'acf_module_location_pages',
						'type' => 'post_object',
						'instructions' => __( 'Leave blank for all pages (although you must also have selected page in Post Types above)' , ST_BB_TD ),
						'required' => 0,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_5fbe3db40af36',
									'operator' => '==contains',
									'value' => 'page',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'page',
						),
						'taxonomy' => '',
						'allow_null' => 1,
						'multiple' => 1,
						'return_format' => 'id',
						'ui' => 1,
					),
					array(
						'key' => 'field_5fbe603875734',
						'label' => 'Location',
						'name' => 'acf_module_location',
						'type' => 'select',
						'instructions' => 'Module to appear before or after the page / post content',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(
							'before' => 'Before',
							'after' => 'After',
						),
						'default_value' => 'before',
						'allow_null' => 0,
						'multiple' => 0,
						'ui' => 1,
						'ajax' => 0,
						'return_format' => 'value',
						'placeholder' => '',
					),
					array(
						'key' => 'field_5fbe60ad75735',
						'label' => 'Order',
						'name' => 'acf_module_order',
						'type' => 'number',
						'instructions' => 'Lower numbers appear first',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => 0,
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => -50,
						'max' => 50,
						'step' => 1,
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'st-acf-module',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			)

		);

		foreach( $post_type_data as $post_type => $args ) {
			if ( ! post_type_exists( $post_type ) ) {
				register_post_type( $post_type, $args );
			}
		}

		foreach( $acf_field_groups as $post_type => $field_groups ) {
			if ( post_type_exists( $post_type ) ) {
				foreach( $field_groups as $field_group ) {
					acf_add_local_field_group( $field_group );
				}
			}

		}

	}

	/**
	 * Populate choice of ST BB modules when creating new ACF module.
	 *
	 * @since	1.0.0
	 * @hooked acf/load_field/name=choose_st_bb_module
	 */
	public function populate_st_bb_module_choice( $field ) {
		$field['choices'] = array();
		foreach ( ST_BB_Module_Manager::get_registered_modules() as $slug => $module ) {
			$field['choices'][ $slug ] = $module->name;
		}
		return $field;
	}

	/**
	 * Populate choice of post types when creating new ACF module.
	 *
	 * @since	1.0.0
	 * @hooked acf/load_field/name=acf_module_location_post_type
	 */
	public function populate_post_type_choice( $field ) {
		$field['choices'] = array();
		$args = array(
			'public'	=>	true,
			'_builtin'	=>	false
		);
		$post_types = array(
			'page'	=>	get_post_type_object( 'page' ),
			'post'	=>	get_post_type_object( 'post' ),
		);
		$post_types = array_merge( $post_types, get_post_types( $args, 'objects' ) );
		foreach ( $post_types as $name => $post_type ) {
			$field['choices'][ $name ] = $post_type->labels->singular_name;
		}
		return $field;
	}

	/**
	 * Convert BB field to ACF field.
	 *
	 * @since	1.0.0
	 * @param	string	$field_key		Field key
	 * @param	array	$field_contents	BB field settings
	 * @return	array	ACF field settings
	 */
	private static function convert_bb_to_acf_field( $field_key, $field_contents ) {

		$acf_field = array(
			'key' => $field_key,
			'default' => isset( $field_contents['default'] ) ? $field_contents['default'] : '',
			'label' => $field_contents['label'],
			'name' => $field_key,
			'instructions' => isset( $field_contents['help'] ) ? $field_contents['help'] : '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			)
		);
		
		switch ( $field_contents['type'] ) {

			case 'text' :
				$acf_field = array_merge( $acf_field, array(
					'type' => 'text',
					'placeholder' => isset( $field_contents['placeholder'] ) ? $field_contents['placeholder'] : '',
					'prepend' => '',
					'append' => isset( $field_contents['description'] ) ? $field_contents['description'] : '',
					'maxlength' => isset( $field_contents['maxlength'] ) ? $field_contents['maxlength'] : '',
				) );			
				break;

			case 'color' :
				$acf_field['type'] = 'color_picker';
				break;

			case 'unit' :
				$acf_field = array_merge( $acf_field, array(
					'type' => 'range',
					'min' => isset( $field_contents['slider']['min'] ) ? $field_contents['slider']['min'] : '',
					'max' => isset( $field_contents['slider']['max'] ) ? $field_contents['slider']['max'] : '',
					'step' => isset( $field_contents['slider']['step'] ) ? $field_contents['slider']['step'] : '',
					'prepend' => '',
					'append' => isset( $field_contents['description'] ) ? $field_contents['description'] : '',
				) );
				break;

			case 'photo' :
				$acf_field['name'] = $acf_field['name'] . '_src';
				$acf_field = array_merge( $acf_field, array(
					'type' => 'image',
					'return_format' => 'url',
					'preview_size' => 'medium',
					'library' => 'all',
					'min_width' => '',
					'min_height' => '',
					'min_size' => '',
					'max_width' => '',
					'max_height' => '',
					'max_size' => '',
					'mime_types' => '',
				) );
				break;

		}

		return $acf_field;
	}

	/**
	 * Generate the ACF module field format from a module.
	 *
	 * @since    1.0.0
	 * 
	 * @param	string	$slug	module slug
	 * @return	array
	 */
	public static function get_acf_settings_from_bb_module( $slug ) {

		$field_group['fields'] = array();
		
		$form = FLBuilderModel::$modules[ $slug ]->form;
		
		// Cycle through tabs.
		foreach ( $form as $tab => $tab_contents ) {
			
			// Exclude Advancd tab for now.
			if ( 'advanced' == $tab ) {
				continue;
			}
			
			// Add the tab field.
			$field_group['fields'][] = array(
					'key' => 'tab*_' . $tab,
					'label' => $tab_contents['title'],
					'name' => 'tab*_' . $tab,
					'type' => 'tab',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'placement' => 'top',
					'endpoint' => 0,
			);

			// Cycle through the sections.
			$tab_sections = $tab_contents['sections'];
			foreach( $tab_sections as $section => $section_contents ) {

				// Add the section group.
				$section_group = array(
					'key' => 'section*_' . $section,
					'label' => $section_contents['title'],
					'name' => 'section*_' . $section,
					'type' => 'group',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'layout' => 'block',
				);

				// Add the subfields.
				foreach( $section_contents['fields'] as $field_key => $field_contents ) {
					$sub_field = self::convert_bb_to_acf_field( $field_key, $field_contents );

					$section_group['sub_fields'][] = $sub_field;

				}

				$field_group['fields'][] = $section_group;

			}

		}
		
		return $field_group;

	}

	/**
	 * When saving variable content modules, update the page / post postmeta
	 * to record which ACF modules are active.
	 *
	 * @since    1.0.0
	 * @hooked	acf/save_post
	 */
	public function update_postmeta_with_variable_content_modules( $post_id ) {
		
		// Don't update when we are working on a content module itself.
		if ( 'st-acf-module' == get_post_type( $post_id ) ) {
			return false;
		}
		
		$acf_fields = get_fields( $post_id );

		// Cycle through the fields and get the ids of any content modules.
		$acf_module_ids = array();
		if ( is_array( $acf_fields ) ) {

			foreach ( $acf_fields as $acf_field_key => $acf_field_value ) {
				
				// If this is one of our sections, add the adf module id.
				if ( 0 === strpos( $acf_field_key, 'section*_' ) ) {
					$acf_module_ids[ self::get_acf_module_id_from_section_name( $acf_field_key ) ] = true;
				}
			}

		}

		// Should never be empty, but just in case...
		if ( empty( $acf_module_ids ) ) {
			delete_post_meta( $post_id, 'st_bb_acf_modules' );
		} else {
			update_post_meta( $post_id, 'st_bb_acf_modules', $acf_module_ids );
		}

	}

	/**
	 * When deleting variable content modules, also delete all associated custom fields on posts / pages
	 * and update postmeta on those posts / pages.
	 *
	 * @since    1.0.0
	 * @hooked	before_delete_post
	 */
	public function remove_post_meta_on_variable_content_module_deletion( $post_id, $post ) {

		// Only act on ACF module posts.
		if ( 'st-acf-module' != $post->post_type ) {
			return false;
		}

		$module_id = $post_id;

		// Only act on variable content modules.
		if ( 'variable' == get_field( 'acf_module_content_type', $module_id ) ) {
			
			// Get all posts where this ACF module appears.
			$args = array(
				'post_type'	=>	'any',
				'meta_query' => array(
					array(
						'key'		=>	'st_bb_acf_modules',
						'compare'	=>	'EXISTS'
					)
				)
			);

			$q = new WP_Query( $args );
			$posts = $posts_and_revisions = $q->posts;

			if ( ! empty( $posts ) ) {

				// Get all post revisions and include them.
				foreach( $posts as $post ) {
					$revisions = wp_get_post_revisions( $post );
					$posts_and_revisions = array_merge( $posts_and_revisions, $revisions );
				}

				$posts = $posts_and_revisions;
				foreach( $posts as $post ) {

					// Update the postmeta.
					$module_ids = get_post_meta( $post->ID, 'st_bb_acf_modules', true );
					if ( is_array( $module_ids ) ) {
						if ( array_key_exists( $module_id, $module_ids ) ) {
							unset( $module_ids[ $module_id ] );
							if ( empty( $module_ids ) ) {
								delete_post_meta( $post->ID, 'st_bb_acf_modules' );
							} else {
								update_post_meta( $post->ID, 'st_bb_acf_modules', $module_ids );
							}
						}
					}

					// Delete the fields from the DB.
					$post_meta = get_post_meta( $post->ID );
					if ( is_array( $post_meta) ) {
						foreach ( $post_meta as $meta_key => $meta_values_array ) {
							$found_pos = strpos( $meta_key, 'section*_' );
							if ( 1 === $found_pos || 0 === $found_pos ) {
								if ( $module_id == self::get_acf_module_id_from_section_name( $meta_key ) ) {
									delete_metadata ( 'post', $post->ID, $meta_key );
								}
							}
						}
					}

				}
			}

		}

	}

}
