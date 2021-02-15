<?php
/**
 * Defines the Image Carousel module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the Image Carousel module.
 *
 * Defines the Image Carousel module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Image_Carousel_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( 'Image Carousel', ST_BB_TD ),
            'description'       =>  __( 'Image Carousel', ST_BB_TD ),
            'icon'              =>  'format-image.svg',
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
    protected static function get_module_init_settings() {
        return array(
            'module'    =>  array(
                'title' =>  __( 'Images', ST_BB_TD ),
                'sections'		=>  array(
                    'Images'     =>  array(
                        'title'         =>  __( 'Images', ST_BB_TD ),
                        'fields'        =>  array(
                            'carousel_image_ids'  => array(
                                'type'          =>  'multiple-photos',
                                'label'         => __( 'Images', ST_BB_TD ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    
}
?>