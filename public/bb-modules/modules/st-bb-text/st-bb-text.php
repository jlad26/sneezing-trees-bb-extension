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
 * Defines the Text module.
 *
 * Defines the Text module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Text_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( 'Text', ST_BB_TD ),
            'description'       =>  __( 'Text editing and embedding', ST_BB_TD ),
            'icon'              =>  'text.svg',
            'partial_refresh'   =>  true,
            'config'            =>  array(
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
                            'text_content'		=>  array(
                                'type'          =>  'editor',
                                'rows'			=>  13,
                                'wpautop'		=>  false,
                                'media_buttons'	=>  false,
                                'preview'		=> array(
                                    'type'		=> 'text',
                                    'selector'	=> '.st-bb-text-container',
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