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

    /**
	 * Get all post types.
	 *
	 * @since    1.0.0
     * 
     * @param   bool    $public_only     Whether to return only public post types.
     * @return  array
     */
    public static function get_post_types( $public_only = true ) {
        $args = array(
			'public'	=>	$public_only,
			'_builtin'	=>	false
		);
        $post_types = array(
            'page'	=>	get_post_type_object( 'page' ),
            'post'	=>	get_post_type_object( 'post' ),
        );
        return array_merge( $post_types, get_post_types( $args, 'objects' ) );
    }

}
