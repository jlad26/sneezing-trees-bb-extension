<?php
// Set figure classes.
$figure_classes = 'figure';
if ( isset( $mod_params['figure_classes'] ) ) {
    $figure_classes .= ' ' . $mod_params['figure_classes'];
}

// Set image classes.
$image_attr = $mod_params['image_attr'];
$image_classes = array( 'st-bb-img' );
if ( isset( $mod_params['image_classes'] ) ) {
    $image_classes = array_merge( $image_classes, $mod_params['image_classes'] );
}
$image_attr['classes'] = implode( ' ', $image_classes );
$image_attr = apply_filters( 'st_bb_image_attributes', $image_attr, $module, $settings );

// Set image size.
$image_size = isset( $mod_params['image_size'] ) ? $mod_params['image_size'] : apply_filters( 'st_bb_default_img_size', '2048x2048' );

// Set caption.
$caption = '';
if ( 'saved' == $mod_params['image_caption_type'] ) {
    $caption = wp_get_attachment_caption( $mod_params['image_id'] );
} elseif ( 'custom' == $mod_params['image_caption_type'] ) {
    $caption = $mod_params['image_caption'];
}

// Work out whether we are indenting on desktop view
$desktop_indent = $mod_params['full_width_img'] && isset( $mod_params['row_desktop_indent'] ) && 'yes' == $mod_params['row_desktop_indent'];
?>
<figure class="<?php echo $figure_classes; ?>">
    <?php echo wp_get_attachment_image( $mod_params['image_id'], $image_size, false, $image_attr ); ?>
	<?php if ( $caption ) : ?>
        <figcaption class="st-bb-caption<?php if ( $mod_params['full_width_img'] ) : ?> container<?php endif; ?>">
            <?php if ( $desktop_indent ) : ?>
                <div class="row">
                    <div class="col-xl-11 offset-xl-1">
            <?php endif; ?>
                <?php esc_html_e( $caption ); ?>
            <?php if ( $desktop_indent ) : ?>
                    </div>
                </div>
            <?php endif; ?>
        </figcaption>
    <?php endif; ?>
</figure>