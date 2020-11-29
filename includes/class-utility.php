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
    public static function get_options( $option = '' ) {

        $options = get_option( 'st_bb_options', '' );

        if ( ! empty( $option) ) {
            if ( isset( $options[ $option ] ) ) {
                $options = $option[ $option ];
            }
        }
        
        return $options;

    }

}
