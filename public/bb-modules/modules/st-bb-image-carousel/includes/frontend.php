<?php
/**
 * Image Carousel module template.
 * @since      1.0.0
 */

/**
 * Define image attributes.
 * Note that sizes are defined for default bootstrap grid breakpoints and column padding of 15px.
 */
$mod_params['image_attr'] = array(
	'sizes'	=>	'(min-width: 1200px) 1110px, (min-width: 992px) 930px, (min-width: 768px) 690px, (min-width: 576px) 510px, calc(100vw - 30px)'
);

// Set image size to 1536 x 1536 as we won't be needing wider or taller than that.
$mod_params['image_size'] = '1536x1536';

// Create slides.
$mod_params['slides'] = array();
if ( is_array( $mod_params['carousel_image_ids'] ) ) {
    foreach ( $mod_params['carousel_image_ids'] as $image_id ) {
        $mod_params['image_id'] = $image_id;
        $mod_params['caption'] = wp_get_attachment_caption( $image_id );
        $mod_params['image_attr']['alt'] = $mod_params['caption'] ? $mod_params['caption'] : '';
        ob_start();
        include ST_BB_DIR . 'public/partials/figure.php';
        $mod_params['slides'][] = ob_get_clean();
    }
}

include ST_BB_DIR . 'public/partials/frontend-carousel-module.php'; 
?>