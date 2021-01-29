<?php
/**
 * Defines the Full Width Image module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the Full Width Image module.
 *
 * Defines the Full Width Image module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Full_Width_Image_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( 'Full width image', ST_BB_TD ),
            'description'       =>  __( 'Image that extends full width of page', ST_BB_TD ),
            'icon'              =>  'format-image.svg',
            'partial_refresh'   =>  true,
            'preview'		    => false,
            'config'            =>  array(
                'acf_version'       =>  true,
                'container_classes' =>  array(
                    'st-bb-module-container'    =>  'st-bb-module-container',
                ),
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
    protected static function get_module_config() {
        return array(
            'module' =>  array(
                'title'     =>  __( 'Image', ST_BB_TD ),
                'sections'  =>  array(
                    'image_layout'     =>  array(
                        'title'         =>  __( 'Layout', ST_BB_TD ),
                        'fields'        =>  array(
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
        );
    }
    
}
?>