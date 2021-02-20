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
            'icon'              =>  'button.svg',
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
    protected static function get_module_init_settings() {
        return array(
            'module'    => array(
                'sections'		=>  array(
                    'text'		=>  array(
                        'title'         =>  __( 'Text', ST_BB_TD ),
                        'fields'        =>  array(
                            'free_content'		=>  array(
                                'type'          =>  'editor',
                                'wpautop'		=>  false,
                                'media_buttons'	=>  true,
                                'preview'		=>  array(
                                    'type'		=>  'text',
                                    'selector'	=>  '.st-bb-rich-text',
                                ),
                                'sanitize'		=>	'wp_kses_post',
                            ),
                        ),
                    ),
                    'h_align'		=>  array(
                        'title'         =>  __( 'Horizontal alignment', ST_BB_TD ),
                        'fields'        =>  array(
                            'content_align'		=>  array(
                                'type'          =>  'select',
                                'label'         =>  __( 'Content block', ST_BB_TD ),
                                'default'       =>  'left',
                                'options'	=> array(
                                    'left'      =>  __( 'Left', ST_BB_TD ),
                                    'center'    =>	__( 'Center', ST_BB_TD ),
                                    'right'     =>	__( 'Right', ST_BB_TD ),
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                                'help'          =>  __( 'Determines where the block of content is placed.', ST_BB_TD ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    
}
?>