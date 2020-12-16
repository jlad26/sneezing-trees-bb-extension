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

?>
<figure class="<?php echo $figure_classes; ?>">
    <?php echo wp_get_attachment_image( $mod_params['image_id'], $image_size, false, $image_attr ); ?>
	<?php if ( isset( $mod_params['caption_header'] ) || isset( $mod_params['caption'] ) ) : ?>
        <figcaption class="<?php echo $figure_classes; ?>">
            <?php if ( isset( $mod_params['caption_header'] ) ) : ?>
            <span class="caption__header"><?php esc_html_e( $mod_params['caption_header'] ); ?></span>
            <?php endif; ?>
            <?php if ( isset( $mod_params['caption'] ) ) : ?>
            <span class="caption__content"><?php esc_html_e( $mod_params['caption'] ); ?></span>
            <?php endif; ?>
        </figcaption>
    <?php endif; ?>
</figure>