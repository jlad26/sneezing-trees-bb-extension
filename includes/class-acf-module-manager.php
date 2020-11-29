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
	 * The ids and fields of modules to be displayed on page / post.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $display_modules    Key: module id, value: array of fields.
	 */
	private static $display_modules;

	/**
	 * The field types of the BB modules to be displayed.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $bb_module_field_types    Format { slug : { bb_field_name : acf_field_type } }.
	 */
	private static $bb_module_field_types;

	/**
	 * Module CSS added - used to prevent BB from adding a second time.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $module_generic_css    Key: slug, value: css as string.
	 */
	private static $module_generic_css;

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

		// When deleting content modules, also delete all associated custom fields on posts / pages.
		add_action( 'before_delete_post', array( $this, 'remove_post_meta_on_content_module_deletion' ), 10, 2 );

		// When updating or deleting a content module, record in options where it appears.
		add_action( 'save_post', array( $this, 'update_registered_content_modules' ) );
		add_action( 'delete_post', array( $this, 'update_registered_content_modules' ) );

		// Set the modules to be displayed on page / post.
		add_action( 'get_header', array( $this, 'set_modules_to_display' ) );

		// Set the modules CSS to be added on get_header so we can later strip it from BB styles if needed.
		add_action( 'get_header', array( $this, 'set_modules_css' ), 11, 1 );

		// Remove any duplicate module CSS from BB CSS.
		add_filter( 'fl_builder_render_css', array( $this, 'remove_duplicate_BB_CSS' ), 10, 4 );

		// Enqueue CSS for modules to be displayed.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_modules_css' ) );

		// Add in modules content to post / page content.
		add_filter( 'the_content', array( $this, 'add_modules_content' ) );

	}

	/**
	 * Get module data for a page / post.
	 *
	 * @since	1.0.0
	 * 
	 * @param	int		$content_module_id		ID of ACF module or 'fixed'
	 * @param	array	$content_module_fields	array of content module fields.
	 * @param	array	$field_sections			Array of ACF fields sections.
	 * @return	array	Array of module data
	 */
	private static function get_acf_module_data( $content_module_id, $content_module_fields, $bb_field_sections ) {

		$acf_module_id_search_param = 'fixed' == $content_module_fields['acf_module_content_type'] ? 'fixed' : $content_module_id;
		
		$module_data = array();

		// Cycle through each field sections.
		foreach ( $bb_field_sections as $section => $fields ) {
			
			// Ignore any that haven't been stored by us.
			if ( 0 !== strpos( $section, 'section*_' ) ) {
				continue;
			}

			if( $acf_module_id_search_param == self::get_acf_module_id_from_section_name( $section ) ) {

				// Store the fields.
				foreach ( $fields as $field_key => $field_value ) {
					$data_key = str_replace( '**' . $acf_module_id_search_param, '', $field_key );
					$module_data[ $data_key ] = self::convert_value_to_bb_format( $content_module_fields['choose_st_bb_module'], $data_key, $field_value );
				}

			}

		}

		return $module_data;

	}

	/**
	 * Convert a stored field value from ACF format to BB format.
	 *
	 * @since	1.0.0
	 * 
	 * @param	string	$bb_module_slug		the BB module slug
	 * @param	string	$data_key			the field key in BB format
	 * @param	mixed	$field_value		the field value in ACF format
	 * @return	mixed	the field value in BB format
	 */
	private static function convert_value_to_bb_format( $bb_module_slug, $data_key, $field_value ) {

		// Some fields aren't present in BB form e.g. when BB adds {image_field}_src field.
		if ( isset( self::$bb_module_field_types[ $bb_module_slug ][ $data_key ] ) ) {
		
			switch ( self::$bb_module_field_types[ $bb_module_slug ][ $data_key ] ) {
				case 'color' :
					// Strip off the hash.
					$field_value = str_replace( '#', '', $field_value );
					break;
			}

		}

		return $field_value;

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

				// Don't display this module if it's fixed content.
				if ( 'fixed' == $module_field_group['acf_module_content_type'] ) {
					continue;
				}

				// Get the ACF field module.
				$acf_module = self::get_acf_module_fields(
					$field_group->ID, $field_group->post_title, $module_field_group['choose_st_bb_module']
				);	

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
	 * Get the ACF module field for a module type.
	 *
	 * @since	1.0.0
	 * 
	 * @param	int			$acf_id				ACF module ID
	 * @param	string		$acf_title			ACF module title
	 * @param	string		$module_slug		Module slug
	 */
	public static function get_acf_module_fields( $acf_id, $acf_title, $module_slug ) {

		$acf_module = array();
		if ( isset( ST_BB_Module_Manager::$acf_modules[ $module_slug ] ) ) {

			$acf_module = ST_BB_Module_Manager::$acf_modules[ $module_slug ];

			// Set overall key and title.
			$acf_module['key'] = '**st_acf_module_' . $acf_id;
			$acf_module['title'] = $acf_title;

			// Cycle through and make all tab, section and field keys and names unique by adding ACF module ID.
			foreach ( $acf_module['fields'] as $key => $field ) {
				
				// Set the key
				$acf_module['fields'][ $key ]['key'] .= '**' . $acf_id;
				$acf_module['fields'][ $key ]['name'] .= '**' . $acf_id;
				if ( isset ( $acf_module['fields'][ $key ]['sub_fields'] ) ) {
					foreach ( $acf_module['fields'][ $key ]['sub_fields'] as $sub_field_key => $sub_field ) {
						$acf_module['fields'][ $key ]['sub_fields'][ $sub_field_key ]['key'] .= '**' . $acf_id;
						$acf_module['fields'][ $key ]['sub_fields'][ $sub_field_key ]['name'] .= '**' . $acf_id;
					}
				}
			}

		}

		return $acf_module;

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

		$fixed_content_message = __( '<h1><strong>Fixed content</strong></h1><p>This content will appear on all pages and posts selected in <strong>Location on site</strong>. To have content vary by page, choose <strong>Variable content</strong> in <strong>Content type</strong>.', ST_BB_TD );

		$acf_fields = array(
			array(
				'key' => 'field_5fc12a6df548a',
				'label' => __( 'Type', ST_BB_TD ),
				'name' => '',
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
			),
			array(
				'key' => 'field_5fbe30414defe',
				'label' => __( 'Module type', ST_BB_TD ),
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
				'key' => 'field_5fc16ff6ae153',
				'label' => '',
				'name' => '',
				'type' => 'message',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5fc0c15a03d63',
							'operator' => '==',
							'value' => 'fixed',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => $fixed_content_message,
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			),
			array(
				'key' => 'field_5fc165139075e',
				'label' => __( 'Location on site', ST_BB_TD ),
				'name' => '',
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
			),
			array(
				'key' => 'field_5fbe3db40af36',
				'label' => __( 'Show on post types', ST_BB_TD ),
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
				'label' => __( 'Show on pages', ST_BB_TD ),
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
				'key' => 'field_5fc17005ae154',
				'label' => '',
				'name' => '',
				'type' => 'message',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5fc0c15a03d63',
							'operator' => '==',
							'value' => 'fixed',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => $fixed_content_message,
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			),
			array(
				'key' => 'field_5fc16c8377a9e',
				'label' => __( 'Location on page', ST_BB_TD ),
				'name' => '',
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
			),
			array(
				'key' => 'field_5fc107b2491f2',
				'label' => __( 'Add to page / post content automatically', ST_BB_TD ),
				'name' => 'acf_module_the_content',
				'type' => 'true_false',
				'instructions' => __( 'If selected, content will be added automatically. If not, content must be displayed using the function *****', ST_BB_TD ),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 1,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_5fbe603875734',
				'label' => __( 'Location', ST_BB_TD ),
				'name' => 'acf_module_location',
				'type' => 'select',
				'instructions' => __( 'Module to appear before or after the page / post content', ST_BB_TD),
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5fc107b2491f2',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '50%',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'before' => __( 'Before', ST_BB_TD ),
					'after' => __( 'After', ST_BB_TD ),
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
				'label' => __( 'Order', ST_BB_TD ),
				'name' => 'acf_module_order',
				'type' => 'number',
				'instructions' => __( 'Lower numbers appear first', ST_BB_TD ),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '50%',
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
			array(
				'key' => 'field_5fc1700fae155',
				'label' => '',
				'name' => '',
				'type' => 'message',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5fc0c15a03d63',
							'operator' => '==',
							'value' => 'fixed',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => $fixed_content_message,
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			),
			array(
				'key' => 'field_5fc12aaaf548b',
				'label' => __( 'Content type', ST_BB_TD ),
				'name' => '',
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
			),
			array(
				'key' => 'field_5fc0c15a03d63',
				'label' => __( 'Content type', ST_BB_TD ),
				'name' => 'acf_module_content_type',
				'type' => 'select',
				'instructions' => __( 'Fixed content modules have the content set here. Variable content modules appear when editing pages / posts and content is set there.', ST_BB_TD ),
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array(
					'variable' => __( 'Variable Content', ST_BB_TD ),
					'fixed' => __( 'Fixed Content', ST_BB_TD ),
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
				'key' => 'field_5fc1624e1c51a',
				'label' => '',
				'name' => '',
				'type' => 'message',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5fc0c15a03d63',
							'operator' => '==',
							'value' => 'fixed',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => $fixed_content_message,
				'new_lines' => 'wpautop',
				'esc_html' => 0,
			),
		);

		// Add the module fields for the ACF Module CPT.
		foreach ( ST_BB_Module_Manager::$acf_modules as $slug => $instance ) {
			$module_fields = self::get_acf_module_fields( 'fixed', 'Content', $slug );
			foreach ( $module_fields['fields'] as $key => $module_field ) {
				if ( 0 == $key) {
					$module_field['endpoint'] = 1;
				}
				$module_field['conditional_logic'] = array(
					array(
						array(
							'field' => 'field_5fc0c15a03d63',
							'operator' => '==',
							'value' => 'fixed',
						),
					),
				);
				$acf_fields[] = $module_field;
			}
		}

		$acf_field_groups['st-acf-module'] = array(
			'key' => 'group_5fbe30305bde3',
			'title' => __( 'Content module configuration', ST_BB_TD ),
			'fields' => $acf_fields,
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
		);

		foreach( $post_type_data as $post_type => $args ) {
			if ( ! post_type_exists( $post_type ) ) {
				register_post_type( $post_type, $args );
			}
		}

		foreach( $acf_field_groups as $post_type => $field_group ) {
			if ( post_type_exists( $post_type ) ) {
				acf_add_local_field_group( $field_group );
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
	 * When deleting content modules, also delete all associated custom fields on posts / pages.
	 *
	 * @since    1.0.0
	 * @hooked	before_delete_post
	 */
	public function remove_post_meta_on_content_module_deletion( $post_id, $post ) {

		// Only act on ACF module posts.
		if ( 'st-acf-module' != $post->post_type ) {
			return false;
		}

		$module_id = $post_id;

		// Get all postmeta entries that are ACF module data.
		global $wpdb;
		$query = 'SELECT meta_id, meta_key FROM ' . $wpdb->postmeta . ' WHERE meta_key LIKE %s';
		$post_meta = $wpdb->get_results(
			$wpdb->prepare( $query, '%section*_%' ),
			ARRAY_A
		);

		// Delete any post meta that correspond to this module id.
		if ( ! empty( $post_meta ) ) {

			$meta_ids_to_delete = array();
			$batch = 0;
			$rows_in_batch = 0;
			foreach ( $post_meta as $row ) {
				if ( $module_id == self::get_acf_module_id_from_section_name( $row['meta_key'] ) ) {
					if ( 20 == $rows_in_batch ) {
						$rows_in_batch = 0;
						$batch++;
					}
					$meta_ids_to_delete[ $batch ][] = $row['meta_id'];
					$rows_in_batch++;
				}
			}

			foreach( $meta_ids_to_delete as $batch_to_delete ) {
				$query = 'DELETE FROM ' . $wpdb->postmeta . ' WHERE meta_id IN (' . implode( ', ', $batch_to_delete ) . ')';
				$post_meta = $wpdb->query( $query );
			}

		}

	}

	/**
	 * When updating or deleting a content module, record in options where it appears.
	 *
	 * @since    1.0.0
	 * @hooked	acf/save_post, delete_post
	 */
	public function update_registered_content_modules( $post_id ) {
		
		// Only on ACF module save
		if ( 'st-acf-module' !== get_post_type( $post_id ) ) {
			return false;
		}

		// Get all published content modules.
		$args = array(
			'post_type'		=>	'st-acf-module',
			'post_status'	=>	'publish',
			'numberposts'	=>	-1,
		);

		$acf_modules = get_posts( $args );

		$module_registration = array(
			'post_types'	=>	array(),
			'page_ids'		=>	array()
		);

		if ( is_array( $acf_modules ) ) {
			foreach( $acf_modules as $acf_module ) {
					
				$post_types = get_field( 'acf_module_location_post_type', $acf_module->ID );
				$page_ids = get_field( 'acf_module_location_pages', $acf_module->ID );

				// Add in post types.
				if ( ! empty( $post_types ) ) {
					foreach ( $post_types as $post_type ) {
						if ( ! isset( $module_registration['post_types'][ $post_type ] ) ) {
							$module_registration['post_types'][ $post_type ] = array();
						}
						if ( ! in_array( $acf_module->ID, $module_registration['post_types'][ $post_type ] ) ) {
							$module_registration['post_types'][ $post_type ][] = $acf_module->ID;
						}
					}
				}

				// Add in page IDs, storing against the IDs of the module they are displaying.
				if ( ! empty( $page_ids ) ) {
					foreach ( $page_ids as $page_id ) {
						if ( ! isset( $module_registration['page_ids'][ $acf_module->ID ] ) ) {
							$module_registration['page_ids'][ $acf_module->ID ] = array( $page_id );
						}
						if ( ! in_array( $page_id, $module_registration['page_ids'][ $acf_module->ID ] ) ) {
							$module_registration['page_ids'][ $acf_module->ID ][] = $page_id;
						}
					}
				}

			}
		}

		update_option( 'st_acf_module_registration', $module_registration );

	}

	/**
	 * Add in modules content to post / page content.
	 *
	 * @since    1.0.0
	 * @hooked	the_content
	 */
	public function add_modules_content( $content ) {

		global $post;
		
		$modules = self::$display_modules;
		if ( empty( $modules ) ) {
			return $content;
		}

		// Organize into before and after.
		$display_modules['before'] = $display_modules['after'] = array();
		foreach ( $modules as $module_id => $module ) {

			$placement = $module['content_module_fields']['acf_module_location'];
			$order = $module['content_module_fields']['acf_module_order'];
			$display_modules[ $placement ][ $order ] = $module_id;

		}
		
		// Sort the modules.
		ksort( $display_modules['before'] );
		ksort( $display_modules['after'] );

		// Add in the modules contents.
		$before_content = '';
		foreach ( $display_modules['before'] as $acf_module_id ) {
			$before_content .= self::get_module_content( $post->ID, $acf_module_id );
		}

		$content = $before_content.$content;

		foreach ( $display_modules['after'] as $acf_module_id ) {
			$content .= self::get_module_content( $post->ID, $acf_module_id );
		}

		return $content;
	
	}

	/**
	 * Add in modules content to post / page content.
	 *
	 * @since    1.0.0
	 * 
	 * @param	int		$post_id		post id of post / page to be displayed on
	 * @param	int		$acf_module_id	id of the ACF module
	 * @param	string	html
	 */
	public static function get_module_content( $post_id, $acf_module_id ) {

		// End here if all settings are empty.
		$field_settings = self::$display_modules[ $acf_module_id ]['settings'];
		$no_content = true;
		foreach ( $field_settings as $field_setting ) {
			if ( ! empty( $field_setting) ) {
				$no_content = false;
			break;
			}
		}
		if ( $no_content ) {
			return '';
		}
		
		$module_fields = self::$display_modules[ $acf_module_id ]['content_module_fields'];
		$settings = (object) $field_settings;

		$registered_modules = ST_BB_Module_Manager::get_registered_modules();

		// Get the class name.
		$module_class = get_class( $registered_modules[ $module_fields['choose_st_bb_module'] ] );
		$module = new $module_class();
		$module->node = $acf_module_id . '-' . $post_id;
		
		ob_start();
		include( ST_BB_DIR . 'public/partials/frontend-module.php' );
		return ob_get_clean();

	}

	/**
	 * Set the modules to be displayed on page / post.
	 *
	 * @since    1.0.0
	 * @hooked	get_header
	 */
	public function set_modules_to_display( $name ) {
		global $post;

		// Work out whether we have content to display.
		$module_registration = get_option( 'st_acf_module_registration' );

		// Stop if no content or this post type is not permitted.
		if ( empty( $module_registration ) ) {
			return array();
		}
		if ( ! isset( $module_registration['post_types'][ $post->post_type ] ) ) {
			return array();
		}

		$module_ids = array();
		
		if ( 'page' == $post->post_type ) {
			
			// Start with assumption that all modules appear on all pages.
			$module_ids = $module_registration['post_types']['page'];
			
			// Then if module is only allocated to specific pages then remove if this isn't one of them.
			foreach( $module_registration['post_types']['page'] as $page_type_module_id ) {
				if ( isset( $module_registration['page_ids'][ $page_type_module_id ] ) ) {
					if ( ! in_array( $post->ID, $module_registration['page_ids'][ $page_type_module_id ] ) ) {
						unset( $module_ids[ array_search( $page_type_module_id, $module_ids ) ] );
					}
				}
			}

		} else {
			$module_ids = $module_registration['post_types'][ $post_type ];
		}

		// Create output format with content module fields, removing any we aren't displaying automatically.
		$modules = array();
		foreach ( $module_ids as $module_id ) {
			$modules[ $module_id ]['content_module_fields'] = get_fields( $module_id );
			if ( ! $modules[ $module_id ]['content_module_fields']['acf_module_the_content'] ) {
				unset( $module_ids[ $module_id ] );
			}
		}

		$bb_modules = ST_BB_Module_Manager::get_registered_modules();

		// Add in the BB module settings.
		foreach ( $modules as $module_id => $module ) {
			
			// Set the field types if not already set.
			$module_slug = $module['content_module_fields']['choose_st_bb_module'];
			if ( ! isset( self::$bb_module_field_types[ $module_slug ] ) ) {

				$bb_module_form = $bb_modules[ $module_slug ]->form;
				self::$bb_module_field_types[ $module_slug ] = self::get_bb_field_types( $bb_module_form );


			}
			
			// Work out whether we are getting fields from the page / post or from a fixed module.
			if ( 'fixed' == $module['content_module_fields'][ 'acf_module_content_type' ] ) {
				$field_sections = $module['content_module_fields'];
			} else {
				$field_sections = get_fields( $post->ID );
			}
			if ( ! is_array( $field_sections ) ) {
				$field_sections = array();
			}

			$modules[ $module_id ]['settings'] = self::get_acf_module_data( $module_id, $module['content_module_fields'], $field_sections );

		}

		self::$display_modules = $modules;

	}

	/**
	 * Gets an array of bb field types indexed by their field keys.
	 *
	 * @since    1.0.0
	 * 
	 * @param	obj		$bb_module_form		A BB module form
	 * @return	array
	 */
	private static function get_bb_field_types( $bb_module_form ) {

		$field_types = array();
		
		// Cycle through the tabs.
		foreach ( $bb_module_form as $tab => $tab_info ) {

			// Skip the Advanced tab.
			if ( 'advanced' == $tab ) {
				continue;
			}

			// Cycle through the sections.
			foreach ( $tab_info['sections'] as $section_key => $section ) {

				// Cycle through the fields.
				foreach ( $section['fields'] as $field_key => $field_info ) {
					$field_types[ $field_key ] = $field_info['type'];
				}

			}

		}

		return $field_types;

	}

	/**
	 * Set the modules CSS to be added. We do this on get_header so we can later
	 * use the fl_builder_render_css filter to remove any duplicate CSS.
	 *
	 * @since    1.0.0
	 * @hooked	get_header
	 */
	public function set_modules_css( $name ) {

		$content_modules = self::$display_modules;

		// Do nothing if no modules to display.
		if ( empty( $content_modules ) ) {
			return false;
		}

		// Get the BB modules.
		$bb_modules = ST_BB_Module_Manager::get_registered_modules();

		foreach ( $content_modules as $content_module_id => $content_module ) {

			$content_module_fields = $content_module['content_module_fields'];

			// Add the module CSS if it hasn't already been added.
			$slug = $content_module_fields['choose_st_bb_module'];
			$bb_module_dir = $bb_modules[ $slug ]->dir;
		
			if ( ! isset( self::$module_generic_css[ $slug ] ) ) {
				$path = $bb_module_dir . 'css/frontend.css';
				$module_css = '';
				if ( file_exists( $path ) ) {
					$module_css = file_get_contents( $path );
				}
				self::$module_generic_css[ $slug ] = $module_css;
			}

		}

	}

	/**
	 * Remove any duplicate module CSS from BB CSS.
	 *
	 * @since    1.0.0
	 * @hooked	fl_builder_render_css
	 */
	public function remove_duplicate_BB_CSS( $css, $nodes, $global_settings, $include_global ) {
		foreach ( self::$module_generic_css as $module_css ) {
			$css = str_replace( $module_css, '', $css );
		}
		return $css;
	}


	/**
	 * Enqueue CSS for modules to be displayed.
	 *
	 * @since    1.0.0
	 * @hooked	wp_enqueue_scripts
	 */
	public function enqueue_modules_css() {

		$content_modules = self::$display_modules;

		// Do nothing if no modules to display.
		if ( empty( $content_modules ) ) {
			return false;
		}

		global $post;

		// Get the BB modules.
		$bb_modules = ST_BB_Module_Manager::get_registered_modules();
		
		$css = '';

		// Add the modules CSS.
		foreach ( self::$module_generic_css as $module_css ) {
			$css .= $module_css;
		}
		
		// Add the instance-specific CSS.
		foreach ( $content_modules as $content_module_id => $content_module ) {

			$content_module_fields = $content_module['content_module_fields'];

			// Add in the all-modules instance-specific CSS.
			$content_location_post_id = $post->ID;
			if ( 'fixed' == $content_module_fields['acf_module_content_type'] ) {
				$content_location_post_id = $content_module_id;
			}
			
			$id = $content_module_id . '-' . $content_location_post_id;
			$settings = (object) $content_module['settings'];

			ob_start();
			include ST_BB_DIR . 'public/bb-modules/includes/frontend.css.php';
			$css .= ob_get_clean();

			// Add the instance-specific CSS.
			$slug = $content_module_fields['choose_st_bb_module'];
			$bb_module_dir = $bb_modules[ $slug ]->dir;
			$path = $bb_module_dir . 'includes/frontend.css.php';
			if ( file_exists( $path ) ) {
				ob_start();
				include $path;
				$css .= ob_get_clean();
			}

		}

		if ( $css ) {
			$dep_handle = $this->plugin_name . '-public';
			wp_add_inline_style( $dep_handle, $css );
		}

	}

}
