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
 * Defines the  Image Carousel module.
 *
 * Defines the  Image Carousel module class and settings.
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
                'acf_version'       =>  false
            )
        ) );

    }

    /**
	 * Enqueue Swiper JS and CSS.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    public function enqueue_scripts() {
        $this->add_js( 'bb-swiper', 'https://unpkg.com/swiper/swiper-bundle.min.js', array(), false, true );
        $this->add_css( 'bb-swiper', 'https://unpkg.com/swiper/swiper-bundle.css', array(), false, false );
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