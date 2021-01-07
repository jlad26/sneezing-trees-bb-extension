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
        $config = array(
            'default_padding'   =>  'none',
            'acf_version'       =>  false,
            'js'                =>  array(),
            'css'               =>  array()
        );
        
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
	 * Add in any js or css.
	 *
	 * @since    1.0.0
	 */
    public function enqueue_scripts() {
        if ( $this->config['js'] ) {
            foreach ( $this->config['js'] as $script_params ) {
                $script_params = $this->parse_enqueue_params( $script_params, 'js' );
                extract( $script_params );
                if ( $handle ) {
                    $this->add_js( $handle, $src, $deps, $ver, $in_footer );
                }
            }
        }

        if ( $this->config['css'] ) {
            foreach ( $this->config['css'] as $script_params ) {
                $script_params = $this->parse_enqueue_params( $script_params, 'css' );
                extract( $script_params );
                if ( $handle ) {
                    $this->add_css( $handle, $src, $deps, $ver, $media );
                }
            }
        }

    }

    /**
	 * Parse parameters for enqueuing.
	 *
	 * @since    1.0.0
     * 
     * @param   array   $params     parameters to be parsed
     * @param   string  $type       either 'css' or 'js'
	 */
    public function parse_enqueue_params( $params, $type ) {
        
        $param_defaults = array(
            'handle'    =>  '',
            'src'       =>  '',
            'deps'      =>  array(),
            'ver'       =>  false,
        );
        if ( 'js' == $type ) {
            $param_defaults['in_footer'] = false;
        } else {
            $param_defaults['media'] = 'all';
        }

        return wp_parse_args( $params, $param_defaults );

    }

    /**
	 * Get module section classes. Used only for ACF versions of modules.
	 *
	 * @since    1.0.0
	 */
    public function get_section_classes() {
        
        // Get classes generic to this type of module.
        if ( isset ( $this->config['section_classes'] ) ) {
            $classes = $this->config['section_classes'];
        }

        // Get any classes specific to this module.
        if ( isset ( $this->settings->section_classes ) ) {
            $classes = array_merge( $classes, explode( ' ', $this->settings->section_classes ) );
        }

        // Get the padding class.
        if ( isset ( $this->settings->vspace ) ) {
            if ( 'none' != $this->settings->vspace ) {
                $classes = array_merge( $classes, array( $this->settings->vspace ) );
            }
        }

        // Add in vertical centering if set to full screen.
		if ( isset( $this->settings->row_height ) ) {
			if ( 'screen' == $this->settings->row_height ) {
				$classes = array_merge( $classes, array( 'd-flex', 'align-items-center' ) );
			}
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
	 * Returns button link rel based on settings
     * 
	 * @since 1.0.0
	 */
	public function get_rel() {
        $rel = array();
        if ( 'yes' == $this->settings->button_new_window ) {
            $rel[] = 'noopener';
        }
		if ( 'yes' == $this->settings->button_nofollow ) {
			$rel[] = 'nofollow';
		}
		$rel = implode( ' ', $rel );
		if ( $rel ) {
			$rel = ' rel="' . $rel . '" ';
		}
		return $rel;
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

        // Add in generic config sections if not already set by module config.
        
        if ( empty( $generic_config ) ) {
            return $config;
        }

        foreach ( $generic_config as $tab_key => $generic_tab_contents ) {
            
            // If tab only exists in generic config...
            if ( ! isset( $config[ $tab_key ] ) ) {
                
                // Set config to generic version.
                $config[ $tab_key ] = $generic_tab_contents;
            
            // ...but if tab exists in both generic and module...
            } else {

                $default_title = apply_filters( 'st_bb_module_settings_default_title', __( 'General', ST_BB_TD ) );
                
                // Use module tab title as priority, fallback to generic.
                if ( ! isset( $config[ $tab_key ]['title'] ) ) {
                    if ( isset( $generic_config[ $tab_key ]['title'] ) ) {
                        $config[ $tab_key ]['title'] = $generic_config[ $tab_key ]['title'];
                    } else {
                        $config[ $tab_key ]['title'] = $default_title;
                    }
                }

                // Cycle through sections.
                $sections = isset( $generic_tab_contents['sections'] ) ? $generic_tab_contents['sections'] : array();
                foreach ( $sections as $section_key => $section_contents ) {

                    // Set whole section if not set by module.
                    if ( ! isset( $config[ $tab_key ]['sections'][ $section_key ] ) ) {
                        $config[ $tab_key ]['sections'][ $section_key ] = $section_contents;
                    } else {

                        // Use module section title as priority, fallback to generic.
                        if ( ! isset( $config[ $tab_key ]['sections'][ $section_key ]['title'] ) ) {
                            if ( isset( $section_contents['title'] ) ) {
                                $config[ $tab_key ]['sections'][ $section_key ]['title'] = $section_contents['title'];
                            } else {
                                $config[ $tab_key ]['sections'][ $section_key ]['title'];
                            }
                        }

                        // Cycle through the fields.
                        $fields = isset( $section_contents['fields'] ) ? $section_contents['fields'] : array();
                        foreach ( $fields as $field_key => $field_contents ) {

                            // Set whole field if not set by section.
                            if ( ! isset( $config[ $tab_key ]['sections'][ $section_key ]['fields'][ $field_key ] ) ) {
                                $config[ $tab_key ]['sections'][ $section_key ]['fields'][ $field_key ] = $field_contents;
                            }

                        }

                    }

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
            'top_and_tail'      => array(
                'title'         =>  __( 'Top and Tail', ST_BB_TD ),
                'sections'		=>  array(
                    'before_content'     =>  array(
                        'title'         =>  __( 'Before main content', ST_BB_TD ),
                        'fields'        =>  array(
                            'before_content'    =>  array(
                                'type'          =>  'editor',
                                'rows'			=>  13,
                                'wpautop'		=>  false,
                                'media_buttons'	=>  true,
                                'preview'		=>  array(
                                    'type'		=> 'text',
                                    'selector'	=> '.st-bb-before-content',
                                ),
                                'sanitize'		=>	'wp_kses_post',
                            ),
                        ),
                    ),
                    'after_content'     =>  array(
                        'title'         =>  __( 'After main content', ST_BB_TD ),
                        'fields'        =>  array(
                            'after_content'    =>  array(
                                'type'          =>  'editor',
                                'rows'			=>  13,
                                'wpautop'		=>  false,
                                'media_buttons'	=>  true,
                                'preview'		=>  array(
                                    'type'		=> 'text',
                                    'selector'	=> '.st-bb-after-content',
                                ),
                                'sanitize'		=>	'wp_kses_post',
                            ),
                        ),
                    ),
                ),
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
                            'row_image_xpos'  =>  array(
                                'type'          =>  'select',
                                'label'         => __( 'Image horizontal position', ST_BB_TD ),
                                'default'       =>  'center',
                                'options'       =>  array(
                                    'center'    =>  __( 'Center', ST_BB_TD ),
                                    'left'      =>  __( 'Left', ST_BB_TD ),
                                    'right'     =>  __( 'Right', ST_BB_TD ),
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                            'row_image_ypos'  =>  array(
                                'type'          =>  'select',
                                'label'         => __( 'Image vertical position', ST_BB_TD ),
                                'default'       =>  'center',
                                'options'       =>  array(
                                    'center'    =>  __( 'Center', ST_BB_TD ),
                                    'top'      =>  __( 'Top', ST_BB_TD ),
                                    'bottom'     =>  __( 'Bottom', ST_BB_TD ),
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    ),
                    'row_scrolldown'     =>  array(
                        'title'         =>  __( 'Scrolldown link', ST_BB_TD ),
                        'fields'        =>  array(
                            'row_scrolldown_hover_text' =>  array(
                                'type'          =>  'text',
                                'label'         =>  __( 'Text on hover', ST_BB_TD ),
                                'preview'       =>  false,
                                'help'          =>  __( 'Text that displays on hover over the icon', ST_BB_TD ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                            'row_scrolldown_target' =>    array(
                                'type'          =>  'text',
                                'label'         =>  __( 'Target', ST_BB_TD ),
                                'preview'       =>  false,
                                'help'          =>  __( 'Enter the id of the element you wish to scroll to (excluding the # symbol).', ST_BB_TD ),
                                'sanitize'		=>	'ST_BB_Module_Manager::sanitize_anchor_target',
                            ),
                        ),
                    ),
                ),
            ),
            'styling'      => array(
                'title'         =>  __( 'Styling', ST_BB_TD ),
                'sections'		=>  array(
                    'layout'     =>  array(
                        'title'         =>  __( 'Layout', ST_BB_TD ),
                        'fields'        =>  array(
                            'row_height'  =>  array(
                                'type'          =>  'select',
                                'label'         => __( 'Minimum Height', ST_BB_TD ),
                                'default'       =>  'content',
                                'options'       =>  array(
                                    'content'       =>  __( 'Fit content', ST_BB_TD ),
                                    'screen'        =>  __( 'Full screen', ST_BB_TD ),
                                ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                            'vspace'    =>  array(
                                'type'		=> 'select',
                                'label'		=> __( 'Vertical Spacing', ST_BB_TD ),
                                'options'	=> array(
                                    'none'          =>  __( 'None', ST_BB_TD ),
                                    'st-bb-pad-y-1' =>	__( 'Small', ST_BB_TD ),
                                    'st-bb-pad-y-2'	=>	__( 'Medium', ST_BB_TD ),
                                    'st-bb-pad-y-3'	=>	__( 'Large', ST_BB_TD ),
                                    'st-bb-pad-y'   =>	__( 'X-Large', ST_BB_TD ),
                                ),
                            ),
                        ),
                    ),
                    'css_attributes'     =>  array(
                        'title'         =>  __( 'CSS attributes', ST_BB_TD ),
                        'fields'        =>  array(
                            'section_classes'  =>  array(
                                'type'          =>  'text',
                                'label'         => __( 'Section classes', ST_BB_TD ),
                                'default'       =>  '',
                                'preview'       =>  false,
                                'sanitize'		=>	'sanitize_text_field',
                                'help'          =>  __( 'Classes to add to the section element. Separate with spaces.', ST_BB_TD ),
                            ),
                            'section_id'  =>  array(
                                'type'          =>  'text',
                                'label'         => __( 'Section ID', ST_BB_TD ),
                                'default'       =>  '',
                                'preview'       =>  false,
                                'sanitize'		=>	'sanitize_text_field',
                                'help'          =>  __( 'ID to add to the section element. Do not include the # symbol.', ST_BB_TD ),
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