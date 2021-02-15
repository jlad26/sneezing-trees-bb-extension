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
class ST_BB_Custom_Carousel_Module extends ST_BB_Carousel_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        
        // Get the generic configuration for all carousel modules.
        $config = self::get_generic_module_config();

        // Add module specific config.
        $config['name'] = __( 'Custom Carousel', ST_BB_TD );
        $config['description'] = __( 'Custom Carousel', ST_BB_TD );
        
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
        return self::get_generic_module_init_settings();
    }
    
}
?>