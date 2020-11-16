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
    
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module( 'ST_BB_Hero_Module', array(
	'module'      => array(
		'title'         =>  'Content',
		'sections'		=>  array(
            'image'     =>  array(
                'title'         =>  'Image',
                'fields'        =>  array(
                    'background_color'  =>  array(
                        'type'          =>  'color',
                        'label'         => __( 'Background Colour', ST_BB_TD ),
                        'default'       =>  '',
                        'show_reset'    =>  true,
                        'help'          =>  __( 'Background colour for the whole section. If an image is set this color acts as an overlay. Black or white is recommended to darken or lighten the image respectively.', ST_BB_TD ),
                    ),
                    'background_opacity'  =>  array(
                        'type'          =>  'unit',
                        'label'         => __( 'Opacity (%)', ST_BB_TD ),
                        'default'       =>  100,
                        'slider'        =>  array(
                            'min'   =>  0,
                            'max'   =>  100,
                            'step'  =>  1,
                        )
                    ),
                    'image' => array(
                        'type'          => 'photo',
                        'label'         => __( 'Background Image', ST_BB_TD ),
                        'show_remove'       => false,
                    ),
                    
                ),
            ),
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
		),
	),
));
?>