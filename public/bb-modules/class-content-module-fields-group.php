<?php

/**
 * A field group for a content module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public/bb-modules
 */

/**
 * A field group for a content module.
 *
 * Defines the field group for a content module.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public/bb-modules
 */
class ST_BB_Content_Module_Fields_Group {

	/**
	 * Fields group.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $fields_group    Array of fields.
	 */
	private $fields_group = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$fields = array(
			array(
				'key' => 'field_5fc12a6df548a',
				'label' => __( 'General', ST_BB_TD ),
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
				'key' => 'field_5fc0c15a03d63',
				'label' => __( 'Content type', ST_BB_TD ),
				'name' => 'acf_module_content_type',
				'type' => 'select',
				'instructions' => __( 'Edit fixed content using the corresponding fixed content editor. Variable content modules appear when editing pages / posts and content is set there.', ST_BB_TD ),
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
				'key' => 'st_cm_active_cb',
				'label' => __( 'Active', ST_BB_TD ),
				'name' => 'acf_module_is_active',
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
				'default_value' => 1,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
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
		);

		$this->fields_group = array(
			'key' => 'group_5fbe30305bde3',
			'title' => __( 'Content module configuration', ST_BB_TD ),
			'fields' => $fields,
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'st-content-module',
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

	}

	/**
	 * Add the field group.
	 *
	 * @since    1.0.0
	 */
	public function add() {
		acf_add_local_field_group( $this->fields_group );
	}

}