<?php
/**
 * Defines the Custom Carousel module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the Custom Carousel module.
 *
 * Defines the Custom Carousel module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Custom_Carousel_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( 'Custom Carousel', ST_BB_TD ),
            'description'       =>  __( 'Custom Carousel', ST_BB_TD ),
            'icon'              =>  'format-gallery.svg',
            'partial_refresh'   =>  true,
            'config'            =>  array(
                'acf_version'       =>  true,
                'js'                =>  array(
                    array(
                        'handle'    =>  'bb-swiper',
                        'src'       =>  'https://unpkg.com/swiper/swiper-bundle.min.js',
                    ),
                ),
                'css'                =>  array(
                    array(
                        'handle'    =>  'bb-swiper',
                        'src'       =>  'https://unpkg.com/swiper/swiper-bundle.css'
                    ),
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
            'module'    =>  array(
                'title' =>  __( 'Configuration', ST_BB_TD ),
                'sections'		=>  array(
                    'carousel_config'     =>  array(
                        'title'         =>  __( 'Carousel ID', ST_BB_TD ),
                        'fields'        =>  array(
                            'custom_carousel_id'  => array(
                                'type'          =>  'text',
                                'label'         =>  __( 'Carousel ID', ST_BB_TD ),
                                'help'          =>  __( 'Give the carousel a unique ID. Slides content can then be added using the filter \'st-bb-custom-carousel-slides-{$your_id}\'.', ST_BB_TD ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                            'include_nav'       =>  array(
                                'type'      =>  'select',
                                'label'     => __( 'Show left-right navigation arrows', ST_BB_TD ),
                                'default'   =>  'yes',
                                'options'   =>  array(
                                    'yes'   =>  'Yes',
                                    'no'    =>  'No'
                                ),
                                'sanitize'  =>  'sanitize_text_field'
                            ),
                        ),
                    ),
                ),
            ),
            'styling'      => array(
                'title'         =>  __( 'Styling', ST_BB_TD ),
                'sections'		=>  array(
                    'layout'     =>  array(
                        'title'         =>  __( 'Layout', ST_BB_TD ),
                        'fields'        =>  array(
                            'full_width'  =>  array(
                                'type'          =>  'select',
                                'label'         => __( 'Full width', ST_BB_TD ),
                                'default'       =>  'no',
                                'options'       =>  array(
                                    'no'    =>  __( 'No', ST_BB_TD ),
                                    'yes'   =>  __( 'Yes', ST_BB_TD ),
                                ),
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