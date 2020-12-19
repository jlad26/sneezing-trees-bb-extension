<?php
/**
 * Defines the Image Gallery module.
 *
 * @since      1.0.0
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */

/**
 * Defines the  Image Gallery module.
 *
 * Defines the  Image Gallery module class and settings.
 *
 * @package    ST_BB
 * @subpackage ST_BB/public
 */
class ST_BB_Image_Gallery_Module extends ST_BB_Module {
    
    /**
	 * Constructor.
	 *
	 * @since    1.0.0
	 */
    public function __construct() {
        parent::__construct( array(
            'name'              =>  __( 'Image Gallery', ST_BB_TD ),
            'description'       =>  __( 'Image Gallery', ST_BB_TD ),
            'icon'              =>  'format-image.svg',
            'partial_refresh'   =>  true,
            'config'            =>  array(
                'acf_version'       =>  false
            )
        ) );

    }

    /**
	 * Enqueue LightGallery JS and CSS.
	 *
	 * @since    1.0.0
     * 
     * @return  array
	 */
    public function enqueue_scripts() {
        
        $this->add_js( 'lightgallery', ST_BB_URL . 'public/lightgallery/lightgallery/dist/js/lightgallery.min.js', array(), false );
        $lightgallery_plugins = array( 'thumbnail', 'share', 'fullscreen' );
        foreach ( $lightgallery_plugins as $lightgallery_plugin ) {
            $this->add_js( 'lg-' . $lightgallery_plugin, ST_BB_URL . 'public/lightgallery/lg-' . $lightgallery_plugin .'/dist/lg-' . $lightgallery_plugin . '.min.js', array( 'lightgallery' ), false );
        }


        $this->add_css( 'lightgallery', ST_BB_URL . 'public/lightgallery/lightgallery/dist/css/lightgallery.min.css', array(), false );
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
            'module'    =>  array(
                'title' =>  __( 'Images', ST_BB_TD ),
                'sections'		=>  array(
                    'images'     =>  array(
                        'title'         =>  __( 'Images', ST_BB_TD ),
                        'fields'        =>  array(
                            'gallery_image_ids'  => array(
                                'type'          =>  'multiple-photos',
                                'label'         => __( 'Images', ST_BB_TD ),
                            ),
                        ),
                    ),
                ),
            ),
            'gallery_options'   =>  array(
                'title'     =>  __( 'Gallery options', ST_BB_TD ),
                'sections'   =>  array(
                    'gallery_options'     =>  array(
                        'title'         =>  __( 'Options', ST_BB_TD ),
                        'fields'        =>  array(
                            'show_captions' => array(
                                'type'      =>  'select',
                                'label'     => __( 'Show image captions', ST_BB_TD ),
                                'default'   =>  'no',
                                'options'   =>  array(
                                    'no'    =>  'No',
                                    'yes'   =>  'Yes'
                                ),
                                'help'      =>  __( 'Selecting this option will enable image captions when images are displayed at full size.', ST_BB_TD ),
                                'sanitize'  =>  'sanitize_text_field'
                            ),
                            'enable_thumbnail' => array(
                                'type'      =>  'select',
                                'label'     => __( 'Show thumbnails', ST_BB_TD ),
                                'default'   =>  'yes',
                                'options'   =>  array(
                                    'no'    =>  'No',
                                    'yes'   =>  'Yes'
                                ),
                                'help'      =>  __( 'Selecting this option will display all gallery images as thumbnails beneath the image being viewed.', ST_BB_TD ),
                                'sanitize'  =>  'sanitize_text_field'
                            ),
                            'enable_share' => array(
                                'type'      =>  'select',
                                'label'     => __( 'Enable share icon', ST_BB_TD ),
                                'default'   =>  'yes',
                                'options'   =>  array(
                                    'no'    =>  'No',
                                    'yes'   =>  'Yes'
                                ),
                                'help'      =>  __( 'Selecting this option will allow viewers to share your images through Facebook, Twitter, Google Plus or Pinterest.', ST_BB_TD ),
                                'sanitize'  =>  'sanitize_text_field'
                            ),
                            'enable_fullscreen' => array(
                                'type'      =>  'select',
                                'label'     => __( 'Allow fullscreen viewing', ST_BB_TD ),
                                'default'   =>  'yes',
                                'options'   =>  array(
                                    'no'    =>  'No',
                                    'yes'   =>  'Yes'
                                ),
                                'help'      =>  __( 'Selecting this option will allow viewers to view your images in total fullscreen (i.e., hiding any browser toolbars).', ST_BB_TD ),
                                'sanitize'  =>  'sanitize_text_field'
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
    
}
?>