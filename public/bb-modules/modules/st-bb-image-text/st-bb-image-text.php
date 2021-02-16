<?php
/**
 * Defines the Image and Text module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the  Image and Text module.
 *
 * Defines the  Image and Text module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Image_Text_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( 'Image and Text', ST_BB_TD ),
            'description'       =>  __( 'Image and text in two column layout', ST_BB_TD ),
            'icon'              =>  'text.svg',
            'partial_refresh'   =>  true,
            'config'            =>  array(
                'acf_version'       =>  true
            )
        ) );

    }

    /**
	 * Get the settings to be applied on module registration.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    protected static function get_module_init_settings() {
        return array(
            'module'    =>  array(
                'title' =>  __( 'Text', ST_BB_TD ),
                'sections'		=>  array(
                    'text_valign'     =>  array(
                        'title'         =>  __( 'Alignment', ST_BB_TD ),
                        'fields'        =>  array(
                            'text_valign'  => array(
                                'type'          =>  'select',
                                'label'         => __( 'Vertical Alignment', ST_BB_TD ),
                                'default'       =>  'top',
                                'options'       =>  array(
                                    'stretch'       =>  __( 'Top', ST_BB_TD ),
                                    'center'        =>  __( 'Middle', ST_BB_TD ),
                                    'flex-end'      =>  __( 'Bottom', ST_BB_TD ),
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    ),
                    'text'		=>  array(
                        'title'         =>  __( 'Text', ST_BB_TD ),
                        'fields'        =>  array(
                            'text_content'		=>  array(
                                'type'          =>  'editor',
                                'wpautop'		=>  false,
                                'media_buttons'	=>  true,
                                'preview'		=> array(
                                    'type'		=> 'text',
                                    'selector'	=> '.st-bb-rich-text',
                                ),
                                'sanitize'		=>	'wp_kses_post',
                            ),
                        ),
                    ),
                    'button'	=> array(
						'title'		=>	__( 'Button', ST_BB_TD ),
						'fields'	=>	ST_BB_Module_Manager::get_button_settings_fields()
					),
                ),
            ),
            'image' =>  array(
                'title'     =>  __( 'Image', ST_BB_TD ),
                'sections'  =>  array(
                    'image_layout'     =>  array(
                        'title'         =>  __( 'Layout', ST_BB_TD ),
                        'fields'        =>  array(
                            'image_valign'  => array(
                                'type'          =>  'select',
                                'label'         => __( 'Vertical alignment', ST_BB_TD ),
                                'default'       =>  'top',
                                'options'       =>  array(
                                    'stretch'       =>  __( 'Top', ST_BB_TD ),
                                    'center'        =>  __( 'Middle', ST_BB_TD ),
                                    'flex-end'      =>  __( 'Bottom', ST_BB_TD ),
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                            'image_margin_bottom'   => array(
                                'type'          =>  'select',
                                'label'         => __( 'Spacing below', ST_BB_TD ),
                                'default'       =>  'yes',
                                'options'       =>  array(
                                    'yes'       =>  __( 'Yes', ST_BB_TD ),
                                    'no'        =>  __( 'No', ST_BB_TD ),
                                ),
                                'help'          =>  __( 'Whether to leave space below image when in two-column layout', ST_BB_TD ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    ),
                    'image'     =>  array(
                        'title'         =>  __( 'Image', ST_BB_TD ),
                        'fields'        =>  array(
                            'image_id' => array(
                                'type'          => 'photo',
                                'label'         => __( 'Image', ST_BB_TD ),
                                'show_remove'       => true,
                            ),
                            'image_alt'		=>  array(
                                'type'          =>  'text',
                                'label'         =>  __( 'Image alt', ST_BB_TD ),
                                'preview'       =>  false,
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                            'image_caption_type'    =>  array(
                                'type'          =>  'select',
                                'label'         => __( 'Caption type', ST_BB_TD ),
                                'default'       =>  'saved',
                                'options'       =>  array(
                                    'none'      =>  __( 'None', ST_BB_TD ),
                                    'saved'     =>  __( 'Saved', ST_BB_TD ),
                                    'custom'    =>  __( 'Custom', ST_BB_TD )
                                ),
                                'toggle'    =>  array(
                                    'custom'    =>  array(
                                        'fields'    =>  array( 'image_caption' ),
                                    )
                                ),
                                'help'          => __( 'Saved caption is the caption stored against the image in the Media Library. Use Custom to set a different caption in this specific location.', ST_BB_TD ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                            'image_caption'		=>  array(
                                'type'          =>  'text',
                                'label'         =>  __( 'Caption', ST_BB_TD ),
                                'preview'		=> array(
                                    'type'		=> 'text',
                                    'selector'	=> '.st-bb-caption',
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    ),
                )
            ),
            'styling'      => array(
                'sections'		=>  array(
                    'layout'     =>  array(
                        'fields'        =>  array(
                            'image_placement'  =>  array(
                                'type'          =>  'select',
                                'label'         => __( 'Image Placement', ST_BB_TD ),
                                'default'       =>  'left',
                                'options'       =>  array(
                                    'left'      =>  __( 'Left', ST_BB_TD ),
                                    'right'     =>  __( 'Right', ST_BB_TD ),
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                            'mobile_order'  =>  array(
                                'type'          =>  'select',
                                'label'         => __( 'Mobile order', ST_BB_TD ),
                                'default'       =>  'img_first',
                                'options'       =>  array(
                                    'img_first'     =>  __( 'Image first', ST_BB_TD ),
                                    'text_first'    =>  __( 'Text first', ST_BB_TD ),
                                ),
                                'help'          => __( 'Defines whether text or image is shown first when the display is collapsed into a single column on narrow screens.', ST_BB_TD ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    
}
?>