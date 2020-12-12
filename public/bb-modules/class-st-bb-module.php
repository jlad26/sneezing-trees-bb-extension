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
	 * Configuration settings.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $config    Configuration specific to the module.
	 */
    public $config = array();

    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct( $args ) {
        
        $class_info     = new ReflectionClass( $this );
		$class_path     = $class_info->getFileName();
        $class_dir      = basename( dirname( $class_path ) ) . '/';
        
        // Set defaults.
        $defaults = array(
            'category'  =>  __( 'Sneezing Trees', ST_BB_TD ),
            'dir'       =>  ST_BB_DIR . 'public/bb-modules/modules/' . $class_dir,
            'url'       =>  ST_BB_URL . 'public/bb-modules/modules/' . $class_dir,
        );
        foreach( $defaults as $arg => $default ) {
            if ( ! isset( $args[ $arg ] ) ) {
                $args[ $arg ] = $default;
            }
        }
        
        parent::__construct( $args );
        
        // Set generic config.
        $this->set_config( $args );
        
    }

    /**
	 * Set the generic config for all modules.
	 *
	 * @since    1.0.0
     * @param   array   $args   Arguments passed to the constructor.
	 */
    protected function set_config( $args ) {

        // Set generic config here that can be over-written
        $config = array();
        
        if ( isset( $args['config'] ) ) {
            $config = array_merge(  $config, $args['config'] );
        }

        // Add additional required classes, using matched key and value for easy unsetting using filters.
        $class_types = array( 'container', 'section' );
        $required_classes = array(
            'container_classes' =>  array( 
                'st-bb-module-container'    =>  'st-bb-module-container',
                'container'                 =>  'container'
            ),
            'section_classes'   =>  array( 
                'st-bb-section' =>  'st-bb-section',
                $this->slug     =>  $this->slug
            )
        );
        foreach ( $class_types as $class_type ) {
            if ( ! isset( $config[ $class_type . '_classes' ] ) ) {
                $config[ $class_type . '_classes' ] = array();
            }
            $config[ $class_type . '_classes' ] = array_merge( $config[ $class_type . '_classes' ], $required_classes[ $class_type . '_classes' ] );
        }

        $this->config = apply_filters( 'st_bb_module_config', $config, $this );
        
    }

    /**
	 * Get module section classes. Used only for ACF versions of modules.
	 *
	 * @since    1.0.0
	 */
    public function get_section_classes() {
        if ( isset ( $this->config['section_classes'] ) ) {
            $classes = $this->config['section_classes'];
        }
        return $classes;
    }

    /**
	 * Get module container classes.
	 *
	 * @since    1.0.0
	 */
    public function container_classes( $echo = true ) {
        $out = '';
        $classes = array( $this->slug . '-container' );
        if ( isset( $this->config['container_classes'] ) ) {
            $classes = array_merge( $classes, $this->config['container_classes'] );
        }
        $classes = apply_filters( 'st_bb_module_container_classes', $classes, $this );
        $out = implode( ' ', $classes );
        if ( $echo ) {
            echo esc_attr( $out );
        } else {
            return $out;
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
    protected static function get_config() {
        $generic_config = self::get_generic_config();
        $config = static::get_module_config();

        // Add in generic config sections if not overridden by module config.
        
        if ( empty( $generic_config ) ) {
            return $config;
        }

        foreach ( $generic_config as $tab => $tab_settings ) {
            if ( ! isset( $config[ $tab ] ) ) {
                $config[ $tab ] = $tab_settings;
            } else {

                // Add in tab title if needed.
                if (
                    isset( $generic_config[ $tab ]['title'] ) &&
                    ! isset( $config[ $tab ]['title'] )
                ) {
                    $config[ $tab ]['title'] = $generic_config[ $tab ]['title'];
                }

            }
        }
 
        return $config;
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
                'title'         =>  __( 'Content', ST_BB_TD ),
            ),
            'background'      => array(
                'title'         =>  __( 'Background', ST_BB_TD ),
                'sections'		=>  array(
                    'row_colour'     =>  array(
                        'title'         =>  __( 'Colour', ST_BB_TD ),
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
                                'label'         => __( 'Opacity', ST_BB_TD ),
                                'default'       =>  100,
                                'description'   =>  '%',
                                'slider'        =>  array(
                                    'min'   =>  0,
                                    'max'   =>  100,
                                    'step'  =>  1,
                                )
                            ),
                        ),
                    ),
                    'row_image'     =>  array(
                        'title'         =>  __( 'Image', ST_BB_TD ),
                        'fields'        =>  array(
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
                    'layout'     =>  array(
                        'title'         =>  __( 'Layout', ST_BB_TD ),
                        'fields'        =>  array(
                            'row_height'  =>  array(
                                'type'          =>  'select',
                                'label'         => __( 'Height', ST_BB_TD ),
                                'default'       =>  'content',
                                'options'       =>  array(
                                    'content'       =>  __( 'Fit content', ST_BB_TD ),
                                    'screen'        =>  __( 'Full screen', ST_BB_TD ),
                                ),
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
        FLBuilder::register_module( static::class, static::get_config() );
    }
    
}
?>