<?php
/**
 * Defines the Free Edit module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the Free Edit module.
 *
 * Defines the Free Edit module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Free_Edit_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( 'Free Edit', ST_BB_TD ),
            'description'       =>  __( 'Free editing using the classic WP editor', ST_BB_TD ),
            'icon'              =>  'text.svg',
            'partial_refresh'   =>  true,
            'config'            =>  array(
                'section_classes'   =>  array( 'd-flex', 'align-items-center' ),
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
    protected static function get_module_config() {
        return array(
            'module'    => array(
                'sections'		=>  array(
                    'editor'		=>  array(
                        'title'         =>  __( 'Editor', ST_BB_TD ),
                        'fields'        =>  array(
                            'free_content'		=>  array(
                                'type'          =>  'editor',
                                'rows'			=>  13,
                                'wpautop'		=>  false,
                                'media_buttons'	=>  true,
                                'preview'		=> array(
                                    'type'		=> 'text',
                                    'selector'	=> '.st-bb-free-edit',
                                ),
                                'sanitize'		=>	'wp_kses_post',
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    
}
?>