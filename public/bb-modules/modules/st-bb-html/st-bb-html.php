<?php
/**
 * Defines the Html module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the Html module.
 *
 * Defines the Html module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Html_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( 'Html', ST_BB_TD ),
            'description'       =>  __( 'Html', ST_BB_TD ),
            'icon'              =>  'text.svg',
            'partial_refresh'   =>  true,
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
            'module'    => array(
                'sections'		=>  array(
                    'html'		=>  array(
                        'title'         =>  __( 'Html', ST_BB_TD ),
                        'fields'        =>  array(
                            'html'		=>  array(
                                'type'          =>  'textarea',
                                'rows'          =>  6,
                                'label'         =>  __( 'Html', ST_BB_TD ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    
}
?>