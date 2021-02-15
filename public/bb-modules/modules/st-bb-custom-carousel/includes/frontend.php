<?php
/**
 * Custom Carousel module template.
 * @since      1.0.0
 */

$mod_params['slides'] = apply_filters( 'st_bb_custom_carousel_slides-' . $mod_params['instance_id'], array(), $mod_params );
include ST_BB_DIR . 'public/partials/carousel-module/frontend.php';