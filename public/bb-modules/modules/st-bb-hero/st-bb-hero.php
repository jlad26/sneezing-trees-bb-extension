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
                'section_classes'    =>  array( 'd-flex', 'align-items-center' )
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
                    'text'		=>  array(
                        'title'         =>  __( 'Text', ST_BB_TD ),
                        'fields'        =>  array(
                            'title'		=>  array(
                                'type'          =>  'text',
                                'label'         =>  __( 'Heading', ST_BB_TD ),
                                'preview'       =>   array(
                                    'type'          =>    'text',
                                    'selector'      =>    '.st-bb-hero-title',
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                            'subtitle'		=>  array(
                                'type'          =>  'text',
                                'label'         =>  __( 'Sub-heading', ST_BB_TD ),
                                'preview'       =>   array(
                                    'type'          =>    'text',
                                    'selector'      =>    '.st-bb-hero-subtitle',
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    ),
                    'button'	=> array(
						'title'		=>	'Button',
						'fields'	=>	array(
							'button_text'	=> array(
								'type'          => 'text',
								'label'         => 'Text',
								'default'       => '',
								'preview'         => array(
									'type'            => 'text',
									'selector'        => '.st-bb-btn',
								),
								'sanitize'		=>	'sanitize_text_field',
							),
							'button_url'	=> array(
								'type'          => 'text',
								'label'         => 'Link',
								'default'       => '',
								'sanitize'		=>	'esc_url_raw',
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
                            'text_align'		=>  array(
                                'type'          =>  'select',
                                'label'         =>  __( 'Text', ST_BB_TD ),
                                'default'       =>  'left',
                                'options'	=> array(
                                    'left'      =>  __( 'Left', ST_BB_TD ),
                                    'center'    =>	__( 'Center', ST_BB_TD ),
                                    'right'     =>	__( 'Right', ST_BB_TD ),
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                                'help'          =>  __( 'Determines how text is aligned within the block of content.', ST_BB_TD ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    
}
?>