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
	 * Fields in ACF format for each BB module.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $module_fields    Key: module slug, value: array of fields.
	 */
	private static $module_fields;

	/**
	 * The ids and fields of modules to be displayed on page / post.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $display_modules    Key: module id, value: array of fields.
	 */
	private static $display_modules = array();

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
	 * Module JS added - used to prevent BB from adding a second time.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $module_generic_js    Key: slug, value: js as string.
	 */
	private static $module_generic_js;

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

		/*====================================================================================================*/
		/* Setup */
		
		// Register custom post types.
		add_action( 'init', array( $this, 'register_post_types' ) );

		// Store BB modules converting fields to ACF format. Priority 20 so after ST_BB_Module_Manager init.
		add_action( 'init', array( $this, 'register_modules_fields' ), 20 );

		/*====================================================================================================*/
		/* Content modules */
		
		// Add content module fields.
		add_action( 'wp_loaded', array( $this, 'add_content_module_fields' ) );

		// Populate choice of ST BB modules when creating new content module.
		add_filter( 'acf/load_field/name=choose_st_bb_module', array( $this, 'populate_st_bb_module_choice' ) );

		// Populate choice of post types when creating new content module.
		add_filter( 'acf/load_field/name=acf_module_location_post_type', array( $this, 'populate_post_type_choice' ) );

		// When deleting content modules, also delete all associated data.
		add_action( 'before_delete_post', array( $this, 'remove_data_on_content_module_deletion' ), 10, 2 );
		
		// When saving, trashing or restoring a content module, create / update fixed content editor as required.
		add_action( 'acf/save_post', array( $this, 'update_fixed_content_editor' ) );
		add_action( 'trashed_post', array( $this, 'update_fixed_content_editor' ) );
		add_action( 'untrashed_post', array( $this, 'update_fixed_content_editor' ) );
		
		// When updating or deleting a content module, record in options where it appears.
		add_action( 'save_post', array( $this, 'update_registered_content_modules' ) );
		add_action( 'delete_post', array( $this, 'update_registered_content_modules' ) );

		// When updating a content module title, update any corresponding fixed content editor title.
		add_action( 'save_post_st-content-module', array( $this, 'update_fixed_content_editor_title' ), 10, 3 );

		/*====================================================================================================*/
		/* Content editors */
		
		// Add variable and fixed content editor fields, adding in content module IDs to make fields unique.
		add_action( 'wp_loaded', array( $this, 'add_content_editor_fields' ), 20 );

		// Display fixed content editor title and location info when editing.
		add_action( 'edit_form_after_title', array( $this, 'add_fc_editor_title_and_location_info' ) );

		// Only display active fixed content editors on Edit Posts screen.
		add_action( 'pre_get_posts', array( $this, 'filter_fc_editors_for_editing' ) );

		// Modify the count of fixed content editors on Edit Posts screen.
		add_action( 'wp_count_posts', array( $this, 'modify_fc_editors_post_count' ), 10, 3 );

		/*====================================================================================================*/
		/* Display modules and CSS */
		
		// Set the modules to be displayed on page / post.
		add_action( 'get_header', array( $this, 'set_modules_to_display' ) );

		// Set the hooks that fixed content modules should to be displayed for.
		add_action( 'get_header', array( $this, 'set_fixed_content_hooks' ), 11 );

		// Set the modules CSS to be added on get_header so we can later strip it from BB styles if needed.
		add_action( 'get_header', array( $this, 'set_modules_css_and_js' ), 11 );

		// Remove any duplicate module CSS / JS from BB CSS / JS.
		add_filter( 'fl_builder_render_css', array( $this, 'remove_duplicate_BB_css' ), 10, 4 );
		add_filter( 'fl_builder_render_js', array( $this, 'remove_duplicate_BB_js' ), 10, 4 );

		// Enqueue any script and style dependencies for modules.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css_and_js_dependencies' ) );
		
		// Enqueue CSS and JS for modules to be displayed.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_modules_css_and_js' ), 15 );

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

		$module_data = array();

		// Cycle through each field sections.
		foreach ( $bb_field_sections as $section => $fields ) {

			// Ignore any that haven't been stored by us.
			if ( 0 !== strpos( $section, 'section_x_' ) ) {
				continue;
			}

			if( $content_module_id == self::get_acf_module_id_from_section_name( $section ) ) {

				// Handle any activation setting.
				if ( strpos( $section, 'acf_content_editor_is_active') ) {
					$module_data[ $section ] = $fields;
				} else { // ...standard field group rather than activation setting.
				
					// Store the fields.
					foreach ( $fields as $field_key => $field_value ) {
						$data_key = str_replace( '_xx_' . $content_module_id, '', $field_key );
						$module_data[ $data_key ] = self::convert_value_to_bb_format( $content_module_fields['choose_st_bb_module'], $data_key, $field_value );
					}

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
	 * Store BB modules converting fields to ACF format.
	 *
	 * @since	1.0.0
	 * @hooked	init
	 */
	public function register_modules_fields() {

		$bb_modules = ST_BB_Module_Manager::get_registered_modules();
		foreach ( $bb_modules as $slug => $bb_module ) {
			self::$module_fields[ $slug ] = self::get_acf_settings_from_bb_module( $slug );
		}

	}


	/**
	 * Display fixed content editor title and location info when editing.
	 *
	 * @since	1.0.0
	 * @hooked	edit_form_after_title
	 */
	public function add_fc_editor_title_and_location_info( $post ) {
		if ( 'st-fc-editor' == $post->post_type ) {

			// Get info of where content is displayed.
			$module_registration = get_option( 'st_acf_module_registration' );
			$this_content_module_id = get_post_meta( $post->ID, 'st_content_module_id', true );

			// Hook info.
			$hooks = array();
			if( get_field( 'acf_module_the_content', $this_content_module_id ) ) {
				$hooks[] = 'before' == get_field( 'acf_module_location', $this_content_module_id ) ? __( 'Before content', ST_BB_TD ) : __( 'After content', ST_BB_TD );
			}
			if ( $custom_hooks = get_field( 'acf_module_hooks', $this_content_module_id ) ) {
				$custom_hooks = explode( "\n", $custom_hooks );
				foreach ( $custom_hooks as $custom_hook ) {
					$custom_hook = explode( ' ', $custom_hook );
					$custom_hook = $custom_hook[0];
					$priority = isset( $hook_info[1] ) ? intval( $hook_info[1] ) : 10;
					/* translators: hook priority */
					$hooks[] = $custom_hook . ' - ' . sprintf( __( 'Priority %s', ST_BB_TD ), $priority );
				}
			}
			if ( ! $hooks ) {
				$hooks[] = __( 'None', ST_BB_TD );
			}

			// Pages / post info
			$location_info = array();
			if ( isset( $module_registration['post_types'] ) && $this_content_module_id ) {
				
				foreach ( $module_registration['post_types'] as $post_type => $content_module_ids ) {
					if ( in_array( $this_content_module_id, $content_module_ids ) ) {
						$post_type_object = get_post_type_object( $post_type );
						$location_info[ $post_type ] = array(
							'label'		=>	$post_type_object->labels->name,
							'post_ids'	=>	array()
						);
						if ( isset( $module_registration[ $post_type . '_ids' ][ $this_content_module_id ] ) ) {
							if ( $post_ids = $module_registration[ $post_type . '_ids' ][ $this_content_module_id ] ) {
								$location_info[ $post_type ]['post_ids'] = $post_ids;
							}
						}
					}
				}

			}

?>
<div id="titlediv">
	<div id="titlewrap">
		<h1><strong><?php esc_html_e( $post->post_title ); ?></strong></h1>
	</div>
</div>
<div id="st-bb-location-info">
	<h3 style="margin-bottom: 5px"><?php _e( 'Display locations', ST_BB_TD ); ?></h3>
	<?php foreach( $hooks as $hook) {
		echo '<p style="margin: 0">' . esc_html( $hook ) . '</p>';
	}
	?>
	<h3 style="margin-bottom: 5px"><?php _e( 'Selected pages / posts', ST_BB_TD ); ?></h3>
	<?php

	if ( $location_info ) {
		foreach( $location_info as $post_type => $post_type_info ) {
			
			// Get all selected post ids.
			$posts = array();
			if ( $post_type_info['post_ids'] ) {
				$posts = get_posts( array(
					'post_type'					=>	$post_type,
					'post__in'					=>	$post_type_info['post_ids'],
					'post_status'				=>	'publish',
					'orderby'					=>	'title',
					'order'						=>	'ASC',
					'posts_per_page'			=>	-1,
					'no_found_rows'				=>	true,
					'cache_results'				=>	false,
					'update_post_term_cache'	=>	false,
					'update_post_meta_cache'	=>	false
				) );
			}

			$qualifier = $posts ? __( 'Selected:', ST_BB_TD ) : __( 'All', ST_BB_TD );
			?>
			<h4 style="margin: 0"><?php esc_html_e( $post_type_info['label'] ); ?> - <?php echo $qualifier; ?></h4>
			<?php
			if ( $posts ) {
				foreach( $posts as $post ) {
					echo '<p style="margin: 0 0 0 10px">' . esc_html( $post->post_title ) . '</p>';
				}
			}
		}
	} else {
		_e( 'No locations set', ST_BB_TD );
	}
	?>
</div>
<?php
		}
	}

	/**
	 * Only display active fixed content editors on Edit Posts screen.
	 *
	 * @since	1.0.0
	 * @hooked	pre_get_posts
	 */
	public function filter_fc_editors_for_editing( $query ) {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}
		if ( $screen = get_current_screen() ) {
			if ( 'edit-st-fc-editor' == $screen->id ) {
				$query->set( 'meta_key', 'st_fc_editor_enabled' );
				$query->set( 'meta_value', 1 );
			}
		}
	}

	/**
	 * Modify the count of fixed content editors on Edit Posts screen.
	 *
	 * @since	1.0.0
	 * @hooked	wp_count_posts
	 */
	public function modify_fc_editors_post_count( object $counts, string $type, string $perm ) {
		// To do.
		return $counts;
	}

	/**
	 * Add content module fields.
	 *
	 * @since	1.0.0
	 * @hooked	wp_loaded
	 */
	public function add_content_module_fields() {
		$fields_group = new ST_BB_Content_Module_Fields_Group();
		$fields_group->add();
	}

	/**
	 * Add variable and fixed content editor fields, adding in content module IDs to make fields unique.
	 *
	 * @since	1.0.0
	 * @hooked	wp_loaded
	 */
	public function add_content_editor_fields() {
		
		// Get all Content Module posts.
		$args = array(
			'post_type'					=>	'st-content-module',
			'posts_per_page'			=>	-1,
			'post_status'				=>	'publish',
			'no_found_rows'				=>	true,
			'update_post_term_cache'	=>	false
		);

		$content_modules = get_posts( $args );
		
		if ( is_array( $content_modules) ) {
			foreach ( $content_modules as $content_module ) {

				$content_module_fields = get_fields( $content_module->ID );

				// Don't display field if the content module is deactivated.
				if ( ! $content_module_fields['acf_module_is_active'] ) {
					continue;
				}
				
				// Get the ACF field module.
				$content_editor_field_group = self::get_content_editor_fields(
					$content_module->ID, $content_module->post_title, $content_module_fields['choose_st_bb_module']
				);

				// Set up the location info.
				$location = array();

				// If we are displaying only on fixed content editors...
				if ( 'fixed' == $content_module_fields['acf_module_content_type'] ) {
					$location[] = array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'st-fc-editor',
						),
						array(
							'param' => 'post',
							'operator' => '==',
							'value' => get_post_meta( $content_module->ID, 'st_fc_editor_id', true ),
						)
					);
				} else { // ...variable content editor so we are displaying on pages / posts.
					
					// Work out whether we need to include page post type.
					$include_page_post_type = true;
					if ( isset( $content_module_fields['acf_module_location_pages'] ) ) {
						if ( ! empty( $content_module_fields['acf_module_location_pages'] ) ) {
							
							// Since we are specifying specific pages we should not include the page post type generally.
							$include_page_post_type = false;

							// Check whether page has been selected as a post type.
							$include_page_ids = false;
							if ( isset( $content_module_fields['acf_module_location_post_type'] ) ) {
								if ( is_array( $content_module_fields['acf_module_location_post_type'] ) ) {
									if ( in_array( 'page', $content_module_fields['acf_module_location_post_type'] ) ) {
										$include_page_ids = true;
									}
								}
							}

							if ( $include_page_ids ) {
								foreach ( $content_module_fields['acf_module_location_pages'] as $page_id ) {
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
					if ( isset( $content_module_fields['acf_module_location_post_type'] ) ) {
						foreach ( $content_module_fields['acf_module_location_post_type'] as $post_type ) {
							
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

				}

				$content_editor_field_group = array_merge( $content_editor_field_group, array(
					'location' => $location,
					'menu_order' => ( isset( $content_module_fields['acf_module_order'] ) && $content_module_fields['acf_module_order'] ) ? $content_module_fields['acf_module_order'] : 0,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => true,
					'description' => '',
				) );

				acf_add_local_field_group( $content_editor_field_group );

			}
		}

	}

	/**
	 * Get the ACF module fields for a module type.
	 *
	 * @since	1.0.0
	 * 
	 * @param	int			$content_module_id				ACF module ID
	 * @param	string		$content_module_title			ACF module title
	 * @param	string		$module_slug		Module slug
	 */
	public static function get_content_editor_fields( $content_module_id, $content_module_title, $module_slug ) {

		$content_editor_fields = array();
		if ( isset( self::$module_fields[ $module_slug ] ) ) {

			$content_editor_fields = self::$module_fields[ $module_slug ];

			// Set overall key and title.
			$content_editor_fields['key'] = '_xx_st_acf_module_' . $content_module_id;
			$content_editor_fields['title'] = $content_module_title;

			// Add in Activation tab and setting.
			$activation_fields = array(
				array(
					'key' => 'st_content_editor_active_tab',
					'label' => __( 'Activation', ST_BB_TD ),
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
					'key' => 'st_content_editor_active_cb',
					'label' => __( 'Active', ST_BB_TD ),
					'name' => 'section_x_acf_content_editor_is_active',
					'type' => 'true_false',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => '',
					'default_value' => 0,
					'ui' => 1,
					'ui_on_text' => '',
					'ui_off_text' => '',
				)
			);

			$content_editor_fields['fields'] = array_merge( $activation_fields, $content_editor_fields['fields'] );

			// Cycle through and make all tab, section and field keys and names unique by adding ACF module ID.
			foreach ( $content_editor_fields['fields'] as $key => $field ) {
				
				// Set the key
				$content_editor_fields['fields'][ $key ]['key'] .= '_xx_' . $content_module_id;
				$content_editor_fields['fields'][ $key ]['name'] .= '_xx_' . $content_module_id;
				if ( isset ( $content_editor_fields['fields'][ $key ]['sub_fields'] ) ) {
					foreach ( $content_editor_fields['fields'][ $key ]['sub_fields'] as $sub_field_key => $sub_field ) {
						$content_editor_fields['fields'][ $key ]['sub_fields'][ $sub_field_key ]['key'] .= '_xx_' . $content_module_id;
						$content_editor_fields['fields'][ $key ]['sub_fields'][ $sub_field_key ]['name'] .= '_xx_' . $content_module_id;
						
						// Handle conditional logic settings.
						if ( $content_editor_fields['fields'][ $key ]['sub_fields'][ $sub_field_key ]['conditional_logic'] ) {
							// NB works only with one condition - which is acceptable as BB will only set one condition.
							$content_editor_fields['fields'][ $key ]['sub_fields'][ $sub_field_key ]['conditional_logic'][0][0]['field'] .= '_xx_' . $content_module_id;					
						}
						
					}
				}

			}

		}

		return $content_editor_fields;

	}

	/**
	 * Get the ACF module ID from a field section name string.
	 *
	 * @since	1.0.0
	 * 
	 * @param	string	$section_name	Section name
	 */
	public static function get_acf_module_id_from_section_name( $section_name ) {
		$start = strrpos( $section_name, '_xx_' );
		return substr( $section_name, $start + 4 );
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
		
		// Define Fixed Content Editor post type
		$labels = array(
			'name'					=> _x( 'Fixed Content Editor', 'post type general name', ST_BB_TD ),
			'singular_name'			=> _x( 'Fixed Content Editor', 'post type singular name', ST_BB_TD ),
			'menu_name'             => __( 'Fixed Content Editors', ST_BB_TD ),
			'add_new'				=> _x( 'Add New', 'Content Module item', ST_BB_TD ),
			'add_new_item'			=> __( 'Add New Fixed Content Editor', ST_BB_TD),
			'new_item'              => __( 'New Fixed Content Editor', ST_BB_TD ),
			'edit_item'				=> __( 'Edit Fixed Content Editor', ST_BB_TD ),
			'view_item'				=> __( 'View Fixed Content Editor', ST_BB_TD ),
			'search_items'			=> __( 'Search Fixed Content Editors', ST_BB_TD ),
			'not_found'				=> __( 'No Fixed Content Editors found', ST_BB_TD ),
			'not_found_in_trash'	=> __( 'No Fixed Content Editors found in Trash', ST_BB_TD ),
			'all_items'				=> __( 'Fixed Content Editors', ST_BB_TD )
		);

		$post_type_data['st-fc-editor'] = array(
			'labels'				=>	$labels,
			'description'         	=>	__( 'Editors for editing Fixed Content modules.', ST_BB_TD ),
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
				'edit_posts' => 'edit_posts',
				'edit_others_posts' => 'edit_posts',
				'publish_posts' => 'edit_posts',
				'read_private_posts' => 'edit_posts',
				'read' => 'edit_posts',
				'delete_posts' => 'do_not_allow',
				'delete_private_posts' => 'do_not_allow',
				'delete_published_posts' => 'do_not_allow',
				'delete_others_posts' => 'do_not_allow',
				'edit_private_posts' => 'edit_posts',
				'edit_published_posts' => 'edit_posts',
				'create_posts' => 'do_not_allow'
			),
			'map_meta_cap'			=>	true,
			'supports'				=>	array( 'none' )
		);
		
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

		$post_type_data['st-content-module'] = array(
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

		foreach( $post_type_data as $post_type => $args ) {
			if ( ! post_type_exists( $post_type ) ) {
				register_post_type( $post_type, $args );
			}
		}

	}

	/**
	 * Populate choice of ST BB modules when creating new content module.
	 *
	 * @since	1.0.0
	 * @hooked acf/load_field/name=choose_st_bb_module
	 */
	public function populate_st_bb_module_choice( $field ) {
		$field['choices'] = array();
		foreach ( ST_BB_Module_Manager::get_registered_modules() as $slug => $module ) {
			if ( isset( $module->config['acf_version'] ) ) {
				if ( $module->config['acf_version'] ) {
					$field['choices'][ $slug ] = $module->name;
				}
			}
		}
		return $field;
	}

	/**
	 * Populate choice of post types when creating new content module.
	 *
	 * @since	1.0.0
	 * @hooked acf/load_field/name=acf_module_location_post_type
	 */
	public function populate_post_type_choice( $field ) {
		$post_types = ST_BB_Utility::get_post_types();
		$field['choices'] = ST_BB_Utility::get_post_types_dropdown_options( $post_types );
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
			'default_value' => isset( $field_contents['default'] ) ? $field_contents['default'] : '',
			'label' => isset( $field_contents['label'] ) ? $field_contents['label'] : '',
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

			case 'editor' :
				$media_buttons = 0;
				if ( isset( $field_contents['media_buttons'] ) ) {
					if ( $field_contents['media_buttons'] ) {
						$media_buttons = 1;
					}
				}
				$acf_field = array_merge( $acf_field, array(
					'type' => 'wysiwyg',
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => $media_buttons,
					'delay' => 0,
				) );			
				break;
			
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

			case 'link' :
				$acf_field['type'] = 'url';
				break;

			case 'unit' :
				$acf_field = array_merge( $acf_field, array(
					'type' => 'range',
					'default_value' => isset( $field_contents['default'] ) ? $field_contents['default'] : 0,
					'min' => isset( $field_contents['slider']['min'] ) ? $field_contents['slider']['min'] : '',
					'max' => isset( $field_contents['slider']['max'] ) ? $field_contents['slider']['max'] : '',
					'step' => isset( $field_contents['slider']['step'] ) ? $field_contents['slider']['step'] : '',
					'prepend' => '',
					'append' => isset( $field_contents['description'] ) ? $field_contents['description'] : '',
				) );
				break;

			case 'photo' :
				$acf_field = array_merge( $acf_field, array(
					'type' => 'image',
					'return_format' => 'id',
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

			case 'multiple-photos' :
				$acf_field = array_merge( $acf_field, array(
					'type' => 'relationship',
					'post_type' => array(
						0 => 'attachment',
					),
					'taxonomy' => '',
					'filters' => array(
						0 => 'search',
					),
					'elements' => array(
						0 => 'featured_image',
					),
					'min' => '',
					'max' => '',
					'return_format' => 'id',
				) );
				break;

			case 'select' :
				$acf_field = array_merge( $acf_field, array(
					'type' => 'select',
					'choices' => $field_contents['options'],
					'allow_null' => 0,
					'multiple' => 0,
					'ui' => 1,
					'ajax' => 0,
					'return_format' => 'value',
					'placeholder' => '',
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

		// BB toggle settings (to be converted to ACF conditional logic).
		$toggles = array();
		
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
					'key' => 'section_x_' . $section,
					'label' => $section_contents['title'],
					'name' => 'section_x_' . $section,
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

					$section_group['sub_fields'][ $sub_field['key'] ] = $sub_field;

					// Add any conditional_logic_rules
					if ( isset( $field_contents['toggle'] ) && ! empty ( $field_contents['toggle'] ) ) {
						$toggles[ $section_group['key'] ][ $sub_field['key'] ] = $field_contents['toggle'];
					}

				}

				$field_group['fields'][ $section_group['key'] ] = $section_group;

			}

		}

		// Add in conditional logic.
		foreach ( $toggles as $section_group_key => $section_group ) {
			foreach ( $section_group as $sub_field_key => $field_toggles ) {

				foreach ( $field_toggles as $selected_value => $data ) {
					$show_fields = $data['fields'];
					foreach ( $show_fields as $show_field_key ) {

						if ( isset( $field_group['fields'][ $section_group_key ]['sub_fields'][ $show_field_key ] ) ) {
							$sub_field = $field_group['fields'][ $section_group_key ]['sub_fields'][ $show_field_key ];
							$sub_field['conditional_logic'] = array(
								array(
									array(
										'field'		=> $sub_field_key,
										'operator'	=> '==',
										'value'		=> $selected_value,
									),
								),
							);
							$field_group['fields'][ $section_group_key ]['sub_fields'][ $show_field_key ] = $sub_field;
						}

					}
				}

			}
		}

		return $field_group;

	}

	/**
	 * When deleting content modules, also delete all associated data.
	 *
	 * @since    1.0.0
	 * @hooked	before_delete_post
	 */
	public function remove_data_on_content_module_deletion( $post_id, $post ) {

		// Only act on content module posts.
		if ( 'st-content-module' != $post->post_type ) {
			return false;
		}

		$module_id = $post_id;

		// If there is an associated content editor, then delete it.
		$fc_editor_id = get_post_meta( $module_id, 'st_fc_editor_id', true );
		if ( ! empty( $fc_editor_id ) ) {
			wp_delete_post( $fc_editor_id, true );
		}
		
		// Get all postmeta entries that are ACF module data.
		global $wpdb;
		$query = 'SELECT meta_id, meta_key FROM ' . $wpdb->postmeta . ' WHERE meta_key LIKE %s';
		$post_meta = $wpdb->get_results(
			$wpdb->prepare( $query, '%section_x_%' ),
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
	 * When saving, trashing or restoring a content module, create / update
	 * fixed content editor as required.
	 *
	 * @since    1.0.0
	 * @hooked	acf/save_post, trashed_post, untrashed_post
	 */
	public function update_fixed_content_editor( $post_id ) {

		// Only when saving a content module.
		if ( 'st-content-module' != get_post_type( $post_id ) ) {
			return false;
		}

		// Add fixed content editor if needed.
		$is_fixed = 'fixed' == get_field( 'acf_module_content_type', $post_id );
		if ( $is_fixed ) {
			self::maybe_add_fixed_content_editor( $post_id );
		}

		/**
		 * Record whether the content editor is enabled or not.
		 * Only enabled if content module is active, fixed content type and published status.
		 * Maybe used to be a fixed content type but now variable, so may need to update even if variable
		 */
		$fc_editor_id = get_post_meta( $post_id, 'st_fc_editor_id', true );
		if ( ! empty( $fc_editor_id ) ) {
			
			$content_module = get_post( $post_id );
			
			// Set fixed content editor enabled status.
			$is_enabled = false;
			if (
				'publish' == $content_module->post_status &&
				get_field( 'acf_module_is_active', $post_id ) &&
				$is_fixed
			) {
				$is_enabled = true;
			}
			update_post_meta( $fc_editor_id, 'st_fc_editor_enabled', $is_enabled );

		}

	}

	/**
	 * Add a fixed content editor if not already in existence.
	 *
	 * @since    1.0.0
	 * @param	int		$content_module_id	ID of associated content module.
	 */
	private static function maybe_add_fixed_content_editor( $content_module_id ) {
		$fc_editor_id = get_post_meta( $content_module_id, 'st_fc_editor_id', true );
		if ( empty( $fc_editor_id ) ) {
			$args = array(
				'post_title'	=>	get_the_title( $content_module_id ),
				'post_type'		=>	'st-fc-editor',
				'post_status'	=>	'publish'
			);	
			$fc_editor_id = wp_insert_post( $args );
			update_post_meta( $content_module_id, 'st_fc_editor_id', $fc_editor_id );
			update_post_meta( $fc_editor_id, 'st_content_module_id', $content_module_id );
		}
	}

	/**
	 * When updating or deleting a content module, record in options where it appears.
	 *
	 * @since    1.0.0
	 * @hooked	acf/save_post, delete_post
	 */
	public function update_registered_content_modules( $post_id ) {
		
		// Only on content module save
		if ( 'st-content-module' !== get_post_type( $post_id ) ) {
			return false;
		}

		// Get all published content modules.
		$args = array(
			'post_type'					=>	'st-content-module',
			'post_status'				=>	'publish',
			'posts_per_page'			=>	-1,
			'no_found_rows'				=>	true,
			'update_post_term_cache'	=>	false
		);

		$content_modules = get_posts( $args );

		$module_registration = array(
			'post_types'	=>	array(),
			'page_ids'		=>	array()
		);

		if ( is_array( $content_modules ) ) {
			foreach( $content_modules as $content_module ) {
					
				$post_types = get_field( 'acf_module_location_post_type', $content_module->ID );
				$page_ids = get_field( 'acf_module_location_pages', $content_module->ID );

				// Add in post types.
				if ( ! empty( $post_types ) ) {
					foreach ( $post_types as $post_type ) {
						if ( ! isset( $module_registration['post_types'][ $post_type ] ) ) {
							$module_registration['post_types'][ $post_type ] = array();
						}
						if ( ! in_array( $content_module->ID, $module_registration['post_types'][ $post_type ] ) ) {
							$module_registration['post_types'][ $post_type ][] = $content_module->ID;
						}
					}
				}

				// Add in page IDs, storing against the IDs of the module they are displaying.
				if ( ! empty( $page_ids ) ) {
					foreach ( $page_ids as $page_id ) {
						if ( ! isset( $module_registration['page_ids'][ $content_module->ID ] ) ) {
							$module_registration['page_ids'][ $content_module->ID ] = array( $page_id );
						}
						if ( ! in_array( $page_id, $module_registration['page_ids'][ $content_module->ID ] ) ) {
							$module_registration['page_ids'][ $content_module->ID ][] = $page_id;
						}
					}
				}

			}
		}

		// Update the stored option, deleting entirely if there is nothing to store.
		if ( empty( $module_registration['post_types'] ) && empty( $module_registration['page_ids'] ) ) {
			delete_option( 'st_acf_module_registration', $module_registration );
		} else {
			update_option( 'st_acf_module_registration', $module_registration );
		}
	}

	/**
	 * When updating a content module title, update any corresponding fixed content editor title.
	 *
	 * @since    1.0.0
	 * @hooked	save_post_st-content-module
	 */
	public function update_fixed_content_editor_title( $post_id, $post, $update ) {

		if ( ! $update ) {
			return false;
		}

		// If this content module has a fixed content editor, update the title
		if ( $fc_editor_id = get_post_meta( $post_id, 'st_fc_editor_id', true ) ) {
			wp_update_post( array(
				'ID'			=>	$fc_editor_id,
				'post_title'	=>	$post->post_title
			) );
		}

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

		// Organize into before and after, removing any that shouldn't be hooked to the_content.
		$display_modules = array(
			'before'	=>	array(),
			'after'		=>	array()
		);
		foreach ( $modules as $module_id => $module ) {

			if ( $module['content_module_fields']['acf_module_the_content'] ) {
				$placement = $module['content_module_fields']['acf_module_location'];
				$order = $module['content_module_fields']['acf_module_order'];
				$display_modules[ $placement ][ $module_id ] = $order;
			}

		}
		
		// Sort the modules.
		asort( $display_modules['before'] );
		asort( $display_modules['after'] );

		// Remove the filter so we don't get caught in a loop if the module uses post content.
		remove_filter( 'the_content', array( $this, 'add_modules_content' ) );
		
		// Set the modules contents.
		$before_content = '';
		foreach ( $display_modules['before'] as $content_module_id => $order ) {
			$before_content .= self::get_module_content( $post->ID, $content_module_id );
		}

		$content = $before_content.$content;

		foreach ( $display_modules['after'] as $content_module_id => $order ) {
			$content .= self::get_module_content( $post->ID, $content_module_id );
		}

		// Add the_content filter back in.
		add_filter( 'the_content', array( $this, 'add_modules_content' ) );

		return $content;
	
	}

	/**
	 * Add in modules content to post / page content.
	 *
	 * @since    1.0.0
	 * 
	 * @param	int		$post_id		post id of post / page to be displayed on
	 * @param	int		$content_module_id	id of the content module
	 * @param	string	html
	 */
	public static function get_module_content( $post_id, $content_module_id ) {

		// End here if all settings are empty.
		$field_settings = self::$display_modules[ $content_module_id ]['settings'];
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
		
		$module_fields = self::$display_modules[ $content_module_id ]['content_module_fields'];
		$settings = (object) $field_settings;

		$registered_modules = ST_BB_Module_Manager::get_registered_modules();

		// Get the class name.
		$module_class = get_class( $registered_modules[ $module_fields['choose_st_bb_module'] ] );
		$module = new $module_class();
		$module->settings = $settings;
		$module->node = $content_module_id . '-' . $post_id;
		
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

		// Stop if no content or this post type is not permitted or does not exist.
		if ( empty( $module_registration ) || empty( $post ) ) {
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

		/**
		 * Create output format with content module fields, removing any inactive and any
		 * that are not hooked to the_content or any action hooks.
		 */
		$modules = array();
		foreach ( $module_ids as $module_id ) {
			$modules[ $module_id ]['content_module_fields'] = get_fields( $module_id );
			$is_active_module = $modules[ $module_id ]['content_module_fields']['acf_module_is_active'];
			$is_hooked_to_the_content = $modules[ $module_id ]['content_module_fields']['acf_module_the_content'];
			$has_action_hooks = ! empty( $modules[ $module_id ]['content_module_fields']['acf_module_hooks'] );
			if ( ! $is_active_module && ! $is_hooked_to_the_content && ! $has_action_hooks ) {
				unset( $modules[ $module_id ] );
			}
		}

		$bb_modules = ST_BB_Module_Manager::get_registered_modules();

		/**
		 * Temporarily unhook do_shortcode from acf_the_content because we are executing earlier display.
		 * Some plugins do things like enqueue scripts (that are then displayed in footer) when shortcode
		 * is run and we don't want to do that too early because the script may not be registered.
		 * We run the shortcodes at display time.
		 * We also need to temporarily remove embedding because that occurs at same time. 
		 */
		remove_filter( 'acf_the_content', 'do_shortcode', 11 );
		if(	isset( $GLOBALS['wp_embed'] ) ) {
			remove_filter( 'acf_the_content', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
			remove_filter( 'acf_the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
		}

		// Add in the BB module settings.
		foreach ( $modules as $module_id => $module ) {
			
			// Set the field types if not already set.
			$module_slug = $module['content_module_fields']['choose_st_bb_module'];
			if ( ! isset( self::$bb_module_field_types[ $module_slug ] ) ) {

				$bb_module_form = $bb_modules[ $module_slug ]->form;
				self::$bb_module_field_types[ $module_slug ] = self::get_bb_field_types( $bb_module_form );


			}
			
			// Get the fields from the page / post.
			if ( 'fixed' == $module['content_module_fields'][ 'acf_module_content_type' ] ) {
				$fc_editor_id = get_post_meta( $module_id, 'st_fc_editor_id', true );
				$fields_location_post_id = empty( $fc_editor_id ) ? 0 : $fc_editor_id;
			} else {
				$fields_location_post_id = $post->ID;
			}
			$field_sections = get_fields( $fields_location_post_id );
			
			if ( ! is_array( $field_sections ) ) {
				$field_sections = array();
			}

			$modules[ $module_id ]['settings'] = self::get_acf_module_data( $module_id, $module['content_module_fields'], $field_sections );

			/**
			 * Unset module if it has no settings (means ACF fields have never been saved and should not be displayed)
			 * or if content editor is inactive.
			 */
			if (
				empty( $modules[ $module_id ]['settings'] ) ||
				! $modules[ $module_id ]['settings']['section_x_acf_content_editor_is_active_xx_' . $module_id ]
			) {
				unset( $modules[ $module_id ] );
			}

		}

		// Put back the do_shortcode and embed filters
		add_filter( 'acf_the_content', 'do_shortcode', 11 );
		if(	isset( $GLOBALS['wp_embed'] ) ) {
			add_filter( 'acf_the_content', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
			add_filter( 'acf_the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );
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
	 * Set the hooks that fixed content modules should to be displayed for.
	 *
	 * @since    1.0.0
	 * @hooked	get_header
	 */
	public function set_fixed_content_hooks() {

		foreach ( self::$display_modules as $content_module_id => $module ) {

			$hooks = get_field( 'acf_module_hooks', $content_module_id );

			if ( $hooks ) {
				foreach ( explode( "\n", $hooks ) as $hook ) {
					$hook_info = explode( ' ', $hook );
					$name = sanitize_text_field( $hook_info[0] );
					$priority = isset( $hook_info[1] ) ? intval( $hook_info[1] ) : 10;
					add_action( $name, array( $this, 'display_hooked_content_' . $content_module_id ), $priority );
				}
			}
		}

	}

	/**
	 * Parse function called by hook into a function and variables.
	 *
	 * @since	1.0.0
	 * @param	string		$method		Name of function called.
	 * @param	array		$args		Any arguments to the called function.
	 */
	public function __call( $method, $args ) {

		// Only do something if method called is in the form 'display_hooked_content_[positive integer]'.
		$method = sanitize_text_field( $method );
		$content_module_id = absint( str_replace( 'display_hooked_content_', '', $method ) );
		if ( $content_module_id ) {
			global $post;

			// remove wpautop as has already been applied where required.
			remove_filter( 'the_content', 'wpautop' );
			echo apply_filters( 'the_content', self::get_module_content( $post->ID, $content_module_id ) );
			add_filter( 'the_content', 'wpautop' );
		}
		
	}

	/**
	 * Set the modules CSS to be added. We do this on get_header so we can later use the
	 * fl_builder_render_css and fl_builder_render_js filters to remove any duplicate CSS / JS.
	 *
	 * @since    1.0.0
	 * @hooked	get_header
	 */
	public function set_modules_css_and_js( $name ) {

		self::$module_generic_css = self::$module_generic_js = array();
		
		$content_modules = self::$display_modules;

		// Do nothing if no modules to display.
		if ( empty( $content_modules ) ) {
			return false;
		}

		// Get the BB modules.
		$bb_modules = ST_BB_Module_Manager::get_registered_modules();

		foreach ( $content_modules as $content_module_id => $content_module ) {

			$content_module_fields = $content_module['content_module_fields'];

			// Add the module CSS / JS if it hasn't already been added.
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

			if ( ! isset( self::$module_generic_js[ $slug ] ) ) {
				$path = $bb_module_dir . 'js/frontend.js';
				$module_js = '';
				if ( file_exists( $path ) ) {
					$module_js = file_get_contents( $path );
				}
				self::$module_generic_js[ $slug ] = $module_js;
			}

		}

	}

	/**
	 * Remove any duplicate module CSS from BB CSS.
	 *
	 * @since    1.0.0
	 * @hooked	fl_builder_render_css
	 */
	public function remove_duplicate_BB_css( $css, $nodes, $global_settings, $include_global ) {
		if ( is_array( self::$module_generic_css ) ) {
			foreach ( self::$module_generic_css as $module_css ) {
				if ( ! empty( $module_css ) ) {
					$css = str_replace( $module_css, '', $css );
				}
			}
		}
		return $css;
	}

	/**
	 * Remove any duplicate module JS from BB JS.
	 *
	 * @since    1.0.0
	 * @hooked	fl_builder_render_js
	 */
	public function remove_duplicate_BB_js( $js, $nodes, $global_settings, $include_global ) {
		if ( is_array( self::$module_generic_js ) ) {
			foreach ( self::$module_generic_js as $module_js ) {
				if ( ! empty( $module_js ) ) {
					$js = str_replace( $module_js, '', $js );
				}
			}
		}
		return $js;
	}

	/**
	 * Enqueue any script and style dependencies for modules. These are generally libraries
	 * like Swiper and LightGallery.
	 *
	 * @since    1.0.0
	 * @hooked	wp_enqueue_scripts
	 */
	public function enqueue_css_and_js_dependencies() {

		$content_modules = self::$display_modules;

		// Do nothing if no modules to display.
		if ( empty( $content_modules ) ) {
			return false;
		}

		global $post;

		// Get the BB modules.
		$bb_modules = ST_BB_Module_Manager::get_registered_modules();

		// Enqueue scripts and styles.
		foreach ( $content_modules as $content_module_id => $content_module ) {
			
			$module_slug = $content_module['content_module_fields']['choose_st_bb_module'];
			$module = $bb_modules[ $module_slug ];
			
			if ( $scripts = $module->config['js'] ) {
				foreach ( $scripts as $script_params ) {
					$script_params = $module->parse_enqueue_params( $script_params, 'js' );
					extract( $script_params );
					wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
				}
			}

			if ( $styles = $module->config['css'] ) {
				foreach ( $styles as $style_params ) {
					$style_params = $module->parse_enqueue_params( $style_params, 'css' );
					extract( $style_params );
					wp_enqueue_style( $handle, $src, $deps, $ver, $media );
				}
			}

		}

	}

	/**
	 * Enqueue CSS and JS for modules to be displayed.
	 *
	 * @since    1.0.0
	 * @hooked	wp_enqueue_scripts
	 */
	public function enqueue_modules_css_and_js() {

		$content_modules = self::$display_modules;

		// Do nothing if no modules to display.
		if ( empty( $content_modules ) ) {
			return false;
		}

		global $post;

		// Get the BB modules.
		$bb_modules = ST_BB_Module_Manager::get_registered_modules();
		
		$css = $js = '';

		// Add the modules CSS.
		foreach ( self::$module_generic_css as $module_css ) {
			$css .= $module_css;
		}

		// Add the modules JS.
		foreach ( self::$module_generic_js as $module_js ) {
			$js .= $module_js;
		}
		
		// Add the instance-specific CSS and JS.
		foreach ( $content_modules as $content_module_id => $content_module ) {

			$content_module_fields = $content_module['content_module_fields'];

			$id = $content_module_id . '-' . $post->ID;
			$settings = (object) $content_module['settings'];
			$module_class = get_class( $bb_modules[ $content_module_fields['choose_st_bb_module'] ] );
			$module = new $module_class();
			
			// Add the instance-specific CSS and JS.
			$slug = $content_module_fields['choose_st_bb_module'];
			$bb_module_dir = $bb_modules[ $slug ]->dir;
			$path = $bb_module_dir . 'includes/frontend.css.php';
			if ( file_exists( $path ) ) {
				ob_start();
				include $path;
				$css .= ob_get_clean();
			}

			$path = $bb_module_dir . 'includes/frontend.js.php';
			if ( file_exists( $path ) ) {
				ob_start();
				include $path;
				$js .= ob_get_clean();
			}

			// Add in the all-modules instance-specific CSS and JS.
			ob_start();
			include ST_BB_DIR . 'public/bb-modules/includes/frontend.css.php';
			$css .= ob_get_clean();
			ob_start();
			include ST_BB_DIR . 'public/bb-modules/includes/frontend.js.php';
			$js .= ob_get_clean();

			$instance_scripts = apply_filters( 'st_bb_js_scripts', array(), $settings->instance_id );
			if ( $instance_scripts ) {
				foreach( $instance_scripts as $script ) {
					$script_params = $module->parse_enqueue_params( $script, 'js' );
					extract( $script_params );
					wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
				}
			}

			$instance_stylesheets = apply_filters( 'st_bb_css_stylesheets', array(), $settings->instance_id );
			if ( $instance_stylesheets ) {
				foreach( $instance_stylesheets as $stylesheet ) {
					$stylesheet_params = $module->parse_enqueue_params( $stylesheet, 'css' );
					extract( $stylesheet_params );
					wp_enqueue_style( $handle, $src, $deps, $ver, $media );
				}
			}

		}

		
	
		$dep_handle = $this->plugin_name . '-public';
		if ( $css ) {
			wp_add_inline_style( $dep_handle, $css );
		}
		if ( $js ) {
			wp_add_inline_script( $dep_handle, $js );
		}

	}

}
