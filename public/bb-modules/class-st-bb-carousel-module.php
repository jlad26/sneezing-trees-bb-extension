<?php
/**
 * Defines the Carousel module used as parent by all carousel modules.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the Carousel module used as parent by all carousel modules.
 *
 * Defines the Carousel module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
abstract class ST_BB_Carousel_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct( $config ) {
        parent::__construct( $config );
    }

    /**
	 * Get the settings to be applied on module registration.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    abstract protected static function get_module_init_settings();

    /**
	 * Get the generic config to be applied on module registration.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    protected static function get_generic_module_config() {
        return array(
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
        );
    }

    /**
	 * Get the generic settings to be applied on module registration.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    protected static function get_generic_module_init_settings() {
        return array(
            'module'    =>  array(
                'title' =>  __( 'Configuration', ST_BB_TD ),
                'sections'		=>  array(
                    'carousel_config'     =>  array(
                        'title'         =>  __( 'Configuration', ST_BB_TD ),
                        'fields'        =>  array(
                            'include_nav'       =>  array(
                                'type'      =>  'select',
                                'label'     => __( 'Show left-right navigation arrows', ST_BB_TD ),
                                'default'   =>  'yes',
                                'options'   =>  array(
                                    'yes'   =>  'Yes',
                                    'no'    =>  'No'
                                ),
                                'sanitize'  =>  'sanitize_text_field',
                                'toggle'    =>  array(
                                    'yes'   =>  array(
                                        'fields'    =>  array( 'nav_layout' )
                                    ),
                                )
                            ),
                            'nav_layout'    =>  array(
                                'type'      =>  'select',
                                'label'     => __( 'Navigation arrows positioning', ST_BB_TD ),
                                'default'   =>  'outside',
                                'options'   =>  array(
                                    'outside'   =>  'Outside content',
                                    'overlay'   =>  'Overlaid on content'
                                ),
                                'help'      =>  __( 'Whether navigation arrows should be outside content (meaning slides are narrowed slightly to allow space) or on top of content at the edges (meaning slides are full width).', ST_BB_TD ),
                                'sanitize'  =>  'sanitize_text_field'
                            )
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