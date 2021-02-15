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
class ST_BB_Image_Carousel_Module extends ST_BB_Carousel_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        
        // Get the generic configuration for all carousel modules.
        $config = self::get_generic_module_config();

        // Add module specific config.
        $config['name'] = __( 'Image Carousel', ST_BB_TD );
        $config['description'] = __( 'Image Carousel', ST_BB_TD );
        
        parent::__construct( $config );

    }

    /**
	 * Get the settings to be applied on module registration.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    protected static function get_module_init_settings() {
        
        // Get the generic configuration for all carousel modules.
        $settings = self::get_generic_module_init_settings();

        // Add module specific settings.
        $additional_config_section = array(
            'Images'     =>  array(
                'title'         =>  __( 'Images', ST_BB_TD ),
                'fields'        =>  array(
                    'carousel_image_ids'  => array(
                        'type'          =>  'multiple-photos',
                        'label'         => __( 'Images', ST_BB_TD ),
                    ),
                ),
            ),
        );

        $settings['module']['sections'] = array_merge( $additional_config_section, $settings['module']['sections'] );
        return $settings;

    }
    
}
?>