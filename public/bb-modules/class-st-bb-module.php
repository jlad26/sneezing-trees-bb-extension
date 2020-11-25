<?php
/**
 * Defines the parent class of all ST BB modules.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the ST BB module parent class.
 *
 * Defines properties that all ST BB modules will inherit.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
abstract class ST_BB_Module extends FLBuilderModule {

    /**
	 * Generic settings that apply to all plugin modules.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $generic_settings    Generic settings.
	 */
    public $generic_settings;

    /**
	 * Utilies.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      ST_BB_Utility    $utilities    Plugin utilities.
	 */
    public $utilities;

    /**
	 * Configuration settings.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $config    Settings used to initialize module.
	 */
    public $config = array();

    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct( $args ) {
        
        // Set defaults.
        $defaults = array(
            'category'  =>  __( 'Sneezing Trees', ST_BB_TD ),
            'group'     =>  __( 'Sneezing Trees', ST_BB_TD ),
        );
        foreach( $defaults as $arg => $default ) {
            if ( ! isset( $args[ $arg ] ) ) {
                $args[ $arg ] = $default;
            }
        }
        
        parent::__construct( $args );
        $this->utilities = new ST_BB_Utility();

        // Set generic config.
        $this->set_config();
        
    }

    /**
	 * Set the generic config for all modules.
	 *
	 * @since    1.0.0
	 */
    protected function set_config() {
        $this->config = apply_filters( 'st_bb_module_config', array(
            'module_classes' => array( 'st-bb-module', 'container' )
        ), $this );
    }

    /**
	 * Get module classes.
	 *
	 * @since    1.0.0
	 */
    public function module_classes( $class_type, $echo = true ) {
        $out = '';
        if ( 'module' == $class_type ) {
            $classes = array( 'st-bb-' . $this->slug );
            if ( isset( $this->config['module_classes'] ) ) {
                $classes = array_merge( $classes, $this->config['module_classes'] );
            }
            $out = implode( ' ', $classes );
            if ( $echo ) {
                echo esc_attr( $out );
            } else {
                return $out;
            }

        }
    }

    /**
	 * Get the settings to be applied on module registration.
     * Adds to or overrides the generic module settings with the child module settings.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    protected static function get_initial_config() {
        $generic_config = self::get_generic_config();
        $initial_config = static::get_module_config();

        // Add in generic config sections if not overridden by module config.
        if ( isset( $generic_config['module'] ) ) {

            // Add in module title if needed.
            if (
                isset( $generic_config['module']['title'] ) &&
                ! isset( $initial_config['module']['title'] )
            ) {
                $initial_config['module']['title'] = $generic_config['module']['title'];
            }

            if ( isset( $generic_config['module']['sections'] ) ) {
                
                $add_generic_sections_before = array();
                
                foreach ( $generic_config['module']['sections'] as $section => $section_params ) {

                    if ( ! isset( $initial_config['module']['sections'][ $section ] ) ) {
                        
                        // Record section to add if not defined by child module.
                        $section_before = isset( $section_params['before'] ) ? $section_params['before'] : 'beginning';
                        $add_generic_sections_before[ $section_before ][] = $section;
                        
                    }

                }

                // Add in the generic sections at the right location.
                if ( ! empty( $add_generic_sections_before) ) {
                    
                    $module_sections = array();
                    
                    if ( isset( $add_generic_sections_before['beginning'] ) ) {
                        foreach ( $add_generic_sections_before['beginning'] as $section ) {
                            $module_sections[ $section ] = $generic_config['module']['sections'][ $section ];
                        }
                    }
                    foreach ( $initial_config['module']['sections'] as $before_section => $section_params ) {
                        if ( isset( $add_generic_sections_before[ $before_section ] ) ) {
                            foreach ( $add_generic_sections_before[ $before_section ] as $section ) {
                                $module_sections[ $section ] = $generic_config['module']['sections'][ $section ];
                            }
                        }
                        $module_sections[ $before_section ] = $section_params;
                    }
                    
                    $initial_config['module']['sections'] = $module_sections;

                }

            }

        }

        return $initial_config;
    }

    /**
	 * Get the generic module settings.
     * Can be added to or overriden by the child module settings.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    private static function get_generic_config() {
        return array(
            'module'      => array(
                'title'         =>  'Content',
                'sections'		=>  array(
                    'background'     =>  array(
                        'title'         =>  'Background',
                        'fields'        =>  array(
                            'row_bg_color'  =>  array(
                                'type'          =>  'color',
                                'label'         => __( 'Colour', ST_BB_TD ),
                                'default'       =>  '',
                                'show_reset'    =>  true,
                                'help'          =>  __( 'Background colour for the whole section. If an image is set this color acts as an overlay. Black or white is recommended to darken or lighten the image respectively.', ST_BB_TD ),
                            ),
                            'row_bg_opacity'  =>  array(
                                'type'          =>  'unit',
                                'label'         => __( 'Opacity (%)', ST_BB_TD ),
                                'default'       =>  100,
                                'slider'        =>  array(
                                    'min'   =>  0,
                                    'max'   =>  100,
                                    'step'  =>  1,
                                )
                            ),
                            'row_image' => array(
                                'type'          => 'photo',
                                'label'         => __( 'Image', ST_BB_TD ),
                                'show_remove'       => true,
                            ),
                            'row_image_alt'		=>  array(
                                'type'          =>  'text',
                                'label'         =>  __( 'Image alt', ST_BB_TD ),
                                'preview'       =>  false,
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    ),
                ),
            ),
        );
    }

    /**
	 * Get the module specific settings to be applied on module registration.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    abstract protected static function get_module_config();
    
    /**
	 * Register the module using intial config settings.
	 *
	 * @since    1.0.0
	 */
    public static function init() {
        FLBuilder::register_module( static::class, static::get_initial_config() );
    }
    
}
?>