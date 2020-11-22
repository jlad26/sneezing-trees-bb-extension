<?php
/**
 * Defines the Hero module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the Hero module.
 *
 * Defines the Hero module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Hero_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( 'Hero', ST_BB_TD ),
            'description'       =>  __( 'Hero', ST_BB_TD ),
            'dir'               =>  ST_BB_DIR . 'public/bb-modules/hero/',
            'url'               =>  ST_BB_URL . 'public/bb-modules/hero/',
            'icon'              =>  'button.svg',
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
    protected static function get_module_config() {
        return array(
            'module'      => array(
                'sections'		=>  array(
                    'text'		=>  array(
                        'title'         =>  'Text',
                        'fields'        =>  array(
                            'heading'		=>  array(
                                'type'          =>  'text',
                                'label'         =>  'Heading',
                                'preview'       =>    array(
                                    'type'            =>    'text',
                                    'selector'        =>    '.heading',
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    ),
                    'testing'		=>  array(
                        'title'         =>  'Tester',
                        'fields'        =>  array(
                            'heading'		=>  array(
                                'type'          =>  'text',
                                'label'         =>  'Heading',
                                'preview'       =>    array(
                                    'type'            =>    'text',
                                    'selector'        =>    '.heading',
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

/**
 * Register the module and its form settings.
 */
ST_BB_Hero_Module::init();
?>