<?php

/**
 * Define the utility functions for the plugin.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/includes
 */

/**
 * Define the utility functions for the plugin.
 *
 * @since      1.0.0
 * @package    ST_BB
 * @subpackage ST_BB/includes
 */
class ST_BB_Utility {

	/**
	 * Get an option or all options if none specified.
	 *
	 * @since    1.0.0
     * 
     * @param   string  $option     Key of option to return.
     * @param   mixed   Option or options.
	 */
    public function get_options( $option = '' ) {

        $options = get_option( 'st_bb_options', '' );

        if ( ! empty( $option) ) {
            if ( isset( $options[ $option ] ) ) {
                $options = $option[ $option ];
            }
        }
        
        return $options;

    }

    /**
	 * Adds in any ACF field groups found that are ST BB modules.
	 *
	 * @since    1.0.0
	 */
    public function add_acf_bb_modules() {
        if ( is_singular() ) {
            
            $post_id = get_the_ID();
            
            // Get all ST BB groups.
            $field_groups = get_field_objects( $post_id );
            $st_bb_field_groups = $this->extract_acf_bb_modules( $field_groups );
            
            // Go no further if we don't have anything to work with.
            if ( empty( $st_bb_field_groups ) ) {
                return false;
            }
            
            // Order the field groups.
            $st_bb_field_groups = $this->order_field_groups( $st_bb_field_groups );
            
            // Create the modules.
            $this->create_modules( $st_bb_field_groups );

        }
    }

    /**
	 * Get field groups that are ST BB modules.
	 *
	 * @since    1.0.0
     * 
     * @param   array  $field_groups     Array of field groups.
     * @return  array
	 */
    private function extract_acf_bb_modules( $field_groups ) {

        if ( ! is_array( $field_groups) ) {
            return array();
        }
        
        // Get class names of all ST BB Modules
        $st_bb_module_names = array();
        foreach ( FLBuilderModel::$modules as $module ) {
            if ( is_subclass_of( $module, 'ST_BB_Module' ) ) {
                $st_bb_module_names[] = get_class( $module );
            }
        }

        // Select field groups that are ST BB modules.
        foreach ( $field_groups as $key => $value ) {
            
            // Convert name to format for matching with ST BB Module names.
            $group_name = strtolower( $key );

            $st_bb_module_class = false;
            foreach ( $st_bb_module_names as $st_bb_module_name ) {
                if ( 0 === strpos( $group_name, strtolower( $st_bb_module_name ) ) ) {
                    $st_bb_module_class = $st_bb_module_name;
                    break;
                }
            }

            // Remove from groups if not one of our modules and add class name if it is.
            if ( $st_bb_module_class ) {
                $field_groups[ $key ]['st_bb_class'] = $st_bb_module_class;
            } else {
                unset( $field_groups[ $key ] );
            }

        }

        return $field_groups;

    }

    /**
	 * Order ST BB modules according to order.
	 *
	 * @since    1.0.0
     * 
     * @param   array  $field_groups     Array of field groups.
     * @return  array
	 */
    private function order_field_groups( $field_groups ) {
        $ordered = array();
        foreach( $field_groups as $group ) {
            $order = 0;
            if ( isset( $group['parent'] ) ) {
                if ( $group['parent'] > 0 ) {
                    $parent_post = get_post( $group['parent'] );
                    $order = $parent_post->menu_order;
                }
            }
            $ordered[ $order ] = $group;
        }
        ksort( $ordered );
        return $ordered;
    }

    /**
	 * Create BB modules from field groups.
	 *
	 * @since    1.0.0
     * 
     * @param   array  $field_groups     Array of field groups.
     * @return  array
	 */
    private function create_modules( $field_groups ) {
        
    }

}
