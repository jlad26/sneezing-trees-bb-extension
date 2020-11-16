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
class ST_BB_Module extends FLBuilderModule {

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

    // Constructor.
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
    }
    
}
?>