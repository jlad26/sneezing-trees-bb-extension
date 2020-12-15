<?php
/**
 * Defines the Shortcode module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the Curator Feed module.
 *
 * Defines the Curator Feed module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Curator_Feed_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( 'Curator Feed', ST_BB_TD ),
            'description'       =>  __( 'Displays a feed from Curator', ST_BB_TD ),
            'icon'              =>  'text.svg',
            'partial_refresh'   =>  true,
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
                    'curator_feed'		=>  array(
                        'title'         =>  __( 'Curator Feed', ST_BB_TD ),
                        'fields'        =>  array(
                            'curator_feed_id'		=>  array(
                                'type'          =>  'text',
                                'label'         =>  __( 'Feed ID', ST_BB_TD ),
                                'sanitize'      =>  'sanitize_text_field'
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    
}
?>