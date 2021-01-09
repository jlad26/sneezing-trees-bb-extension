<?php
/**
 * Defines the Two Column Free Edit module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the Two Column Free Edit module.
 *
 * Defines the Two Column Free Edit module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Two_Col_Free_Edit_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( '2 Column Free Edit', ST_BB_TD ),
            'description'       =>  __( 'Free editing in two column', ST_BB_TD ),
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
                'title'     =>  __( 'Column One', ST_BB_TD ),
                'sections'		=>  array(
                    'text_valign'     =>  array(
                        'title'         =>  __( 'Alignment', ST_BB_TD ),
                        'fields'        =>  array(
                            'text_valign_1'  => array(
                                'type'          =>  'select',
                                'label'         => __( 'Vertical Alignment', ST_BB_TD ),
                                'default'       =>  'top',
                                'options'       =>  array(
                                    'stretch'       =>  __( 'Top', ST_BB_TD ),
                                    'center'        =>  __( 'Middle', ST_BB_TD ),
                                    'flex-end'      =>  __( 'Bottom', ST_BB_TD ),
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    ),
                    'editor'		=>  array(
                        'title'         =>  __( 'Editor', ST_BB_TD ),
                        'fields'        =>  array(
                            'free_content_1'		=>  array(
                                'type'          =>  'editor',
                                'rows'			=>  8,
                                'wpautop'		=>  false,
                                'media_buttons'	=>  true,
                                'preview'		=> array(
                                    'type'		=> 'text',
                                    'selector'	=> '.st-bb-rich-text-1',
                                ),
                                'sanitize'		=>	'wp_kses_post',
                            ),
                        ),
                    ),
                    'button'	=> array(
						'title'		=>	__( 'Button', ST_BB_TD ),
						'fields'	=>	ST_BB_Module_Manager::get_button_settings_fields( 'col_1', array( 'module', 'column_two' ) )
					),
                ),
            ),
            'column_two'    => array(
                'title'     =>  __( 'Column Two', ST_BB_TD ),
                'sections'		=>  array(
                    'text_valign'     =>  array(
                        'title'         =>  __( 'Alignment', ST_BB_TD ),
                        'fields'        =>  array(
                            'text_valign_2'  => array(
                                'type'          =>  'select',
                                'label'         => __( 'Vertical Alignment', ST_BB_TD ),
                                'default'       =>  'top',
                                'options'       =>  array(
                                    'stretch'       =>  __( 'Top', ST_BB_TD ),
                                    'center'        =>  __( 'Middle', ST_BB_TD ),
                                    'flex-end'      =>  __( 'Bottom', ST_BB_TD ),
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    ),
                    'editor'		=>  array(
                        'title'         =>  __( 'Editor', ST_BB_TD ),
                        'fields'        =>  array(
                            'free_content_2'		=>  array(
                                'type'          =>  'editor',
                                'rows'			=>  8,
                                'wpautop'		=>  false,
                                'media_buttons'	=>  true,
                                'preview'		=> array(
                                    'type'		=> 'text',
                                    'selector'	=> '.st-bb-rich-text-2',
                                ),
                                'sanitize'		=>	'wp_kses_post',
                            ),
                        ),
                    ),
                    'button'	=> array(
						'title'		=>	__( 'Button', ST_BB_TD ),
						'fields'	=>	ST_BB_Module_Manager::get_button_settings_fields( 'col_2', array( 'module', 'column_two' ) )
					),
                ),
            ),
        );
    }
    
}
?>