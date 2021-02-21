<?php
/**
 * Full width image module template.
 * @since      1.0.0
 */

$mod_params['image_attr'] = array(
	'alt'	=>	isset( $mod_params['row_image_alt'] ) ? $mod_params['row_image_alt'] : '',
	'sizes'	=>	'100vw'
);
$mod_params['full_width_img'] = true;
$desktop_indent = 'no' == $mod_params['full_screen_stretch'] && 'yes' == $mod_params['row_desktop_indent'];
if ( $desktop_indent ) : ?>
<div class="row">
	<div class="col-xl-10 offset-xl-1">
<?php endif;
		include ST_BB_DIR . 'public/partials/figure.php';
if ( $desktop_indent ) : ?>
	</div>
</div>
<?php endif; ?>
