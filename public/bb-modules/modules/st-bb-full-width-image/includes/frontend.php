<?php
/**
 * Text module template.
 * @since      1.0.0
 */

$mod_params['image_attr'] = array(
	'alt'	=>	isset( $mod_params['row_image_alt'] ) ? $mod_params['row_image_alt'] : '',
	'sizes'	=>	'100vw'
);
$mod_params['full_width_img'] = true;
include ST_BB_DIR . 'public/partials/figure.php';
?>