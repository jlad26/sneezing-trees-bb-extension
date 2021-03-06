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
            'css'               =>  array(),
        );
        
        if ( isset( $args['config'] ) ) {
            $config = array_merge(  $config, $args['config'] );
        }

        // Add additional required classes, using matched key and value for easy unsetting using filters.
        $config['section_classes'] = array( 
            'st-bb-section' =>  'st-bb-section',
            $this->slug     =>  $this->slug
        );
        $this->config = apply_filters( 'st_bb_module_config', $config, $this );
        
    }

    /**
	 * Add in any js or css.
	 *
	 * @since    1.0.0
	 */
    public function enqueue_scripts() {

        $instance_id = isset( $this->settings->instance_id ) ? $this->settings->instance_id : '';
        
        $js = apply_filters( 'st_bb_js_scripts', $this->config['js'], $instance_id );
        
        if ( $js ) {
            foreach ( $js as $script_params ) {
                $script_params = $this->parse_enqueue_params( $script_params, 'js' );
                extract( $script_params );
                if ( $handle ) {
                    $this->add_js( $handle, $src, $deps, $ver, $in_footer );
                }
            }
        }

        $css = apply_filters( 'st_bb_css_stylesheets', $this->config['css'], $instance_id );

        if ( $css ) {
            foreach ( $css as $script_params ) {
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
        
        // Allow amendment of section classes.
		$classes = apply_filters( 'st_bb_section_classes', $classes, $this );

        return $classes;
    }

    /**
	 * Get module container classes.
	 *
	 * @since    1.0.0
	 */
    public function container_classes( $echo = true ) {
        $out = '';

        // Set default classes.
        $classes = array(
            $this->slug . '-container'  =>  $this->slug . '-container',
            'st-bb-module-container'    =>  'st-bb-module-container',
            'container'                 =>  'container'
        );

        /**
         * Give child modules opppotunity to amend container classes, by overriding the
         * customise_container_classes function.
         */
        $classes = $this->customise_container_classes( $classes );

        // This filter is applied only to this plugin's modules.
        $classes = apply_filters( 'st_bb_module_container_classes', $classes, $this );
        
        // This filter is applied to ALL Beaver Builder modules, not just this plugin's modules (@see partials/frontend-module.php)
        $classes = apply_filters( 'st_bb_all_modules_container_classes', $classes, $this );
        
        $out = implode( ' ', $classes );
        if ( $echo ) {
            echo esc_attr( $out );
        } else {
            return $out;
        }

    }

    /**
	 * Customise container classes. Available for child modules to override.
	 *
	 * @since    1.0.0
	 */
    protected function customise_container_classes( $classes ) {
        return $classes;
    }

    /**
	 * Get the settings to be applied on module registration.
     * Adds to or overrides the generic module settings with the child module settings.
	 *
	 * @since    1.0.0
     * 
     * @param   string  $child_class_name     name of child class
     * @return  array
	 */
    protected static function get_init_settings( $child_class_name ) {
        $generic_config = self::get_generic_init_settings();
        $config = static::get_module_init_settings();

        /**
         * Amend Desktop Indent setting on modules where setting is available to content, not just top and tail,
         * by moving from Top and Tail tab to Styling tab, and amending the Help text.
         */
        $no_desktop_indent_modules = array(
            'ST_BB_Central_Col_Free_Edit_Module',
            'ST_BB_Curator_Feed_Module',
            'ST_BB_Html_Module'
        );

        if ( ! in_array( $child_class_name, $no_desktop_indent_modules ) ) {
            $desktop_indent_field = $generic_config['top_and_tail']['sections']['top_and_tail_layout']['fields']['row_desktop_indent'];
            $desktop_indent_field['help'] = __( 'Whether when viewed on desktop the content should be indented on both sides, i.e., displayed in a column narrower than the width of the header', ST_BB_TD );
            unset( $generic_config['top_and_tail']['sections']['top_and_tail_layout'] );
            $generic_config['styling']['sections']['layout']['fields']['row_desktop_indent'] = $desktop_indent_field;
        }
        
        // Do nothing if we have no generic config.
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

        return apply_filters( 'st_bb_module_settings', $config, $child_class_name );

    }

    /**
	 * Get the generic module settings.
     * Can be added to or overriden by the child module settings.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    private static function get_generic_init_settings() {
        return array(
            'module'      => array(
                'title'         =>  __( 'Content', ST_BB_TD ),
            ),
            'top_and_tail'      => array(
                'title'         =>  __( 'Top and Tail', ST_BB_TD ),
                'sections'		=>  array(
                    'top_and_tail_layout'   =>  array(
                        'title'     =>  __( 'Layout', ST_BB_TD ),
                        'fields'    =>  array(
                            'row_desktop_indent'    =>  array(
                                'type'          =>  'select',
                                'label'         => __( 'Indent on desktop', ST_BB_TD ),
                                'default'       =>  'yes',
                                'options'       =>  array(
                                    'yes'       =>  __( 'Yes', ST_BB_TD ),
                                    'no'        =>  __( 'No', ST_BB_TD ),
                                ),
                                'help'          =>  __( 'Whether when viewed on desktop the top and tail content should be indented on both sides, i.e., displayed in a column narrower than the width of the header', ST_BB_TD ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        )
                    ),
                    'before_content'     =>  array(
                        'title'         =>  __( 'Before main content', ST_BB_TD ),
                        'fields'        =>  array(
                            'before_content'    =>  array(
                                'type'          =>  'editor',
                                'wpautop'		=>  false,
                                'media_buttons'	=>  true,
                                'preview'		=>  array(
                                    'type'		=> 'text',
                                    'selector'	=> '.st-bb-before-content div',
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
                                'wpautop'		=>  false,
                                'media_buttons'	=>  true,
                                'preview'		=>  array(
                                    'type'		=> 'text',
                                    'selector'	=> '.st-bb-after-content div',
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
                    'developer'     =>  array(
                        'title'     =>  __( 'Developer', ST_BB_TD ),
                        'fields'        =>  array(
                            'instance_id'    =>  array(
                                'type'          =>  'text',
                                'label'         =>  __( 'Module ID', ST_BB_TD ),
                                'help'          =>  __( 'Give the module a unique ID. This is not displayed but can then be used by developers in filters to add custom CSS for example.', ST_BB_TD ),
                                'sanitize'		=>	'sanitize_text_field',
                            ),
                        ),
                    )
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
    abstract protected static function get_module_init_settings();
    
    /**
	 * Register the module using intial config settings.
	 *
	 * @param   string  $child_class_name     name of child class
     * @since    1.0.0
	 */
    public static function init( $child_class_name ) {
        FLBuilder::register_module( static::class, static::get_init_settings( $child_class_name ) );
    }
    
}
?>