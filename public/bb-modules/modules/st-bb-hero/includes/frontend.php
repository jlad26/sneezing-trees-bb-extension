<?php
/**
 * Hero module template.
 * @since      1.0.0
 */

// Align the block of content.
$content_align_class = '';
if ( 'center' == $mod_params['content_align'] ) {
    $content_align_class = ' offset-md-3';
} elseif ( 'right' == $mod_params['content_align'] ) {
    $content_align_class = ' offset-md-6';
}

// Align text within the block of content.
$text_align_class = '';
if ( 'center' == $mod_params['text_align'] ) {
    $text_align_class = ' st-bb-text-align-center';
} elseif ( 'right' == $mod_params['text_align'] ) {
    $text_align_class = ' st-bb-text-align-right';
}

// Set button classes.
$button_classes = apply_filters( 'st_bb_hero_button_classes', 'st-bb-btn', $module );

?>
<div class="st-bb-hero-content row">
    <div class="col-md-6<?php echo $content_align_class . $text_align_class; ?>">
        <?php if ( isset( $mod_params['title'] ) ) : ?>
        <h1 class="st-bb-hero-title">
            <?php echo esc_html( $mod_params['title'] ); ?>
        </h1>
        <?php endif; ?>

        <?php if ( isset( $mod_params['subtitle'] ) ) : ?>
        <h2 class="st-bb-hero-subtitle">
            <?php echo esc_html( $mod_params['subtitle'] ); ?>
        </h2>
        <?php endif; ?>

        <?php if ( $mod_params['button_url'] && $mod_params['button_text'] ) : ?>
        <a class="<?php esc_attr_e( $button_classes ); ?>" href="<?php echo esc_url( $mod_params['button_url'] ); ?>">
            <?php echo esc_html( $mod_params['button_text'] ); ?>
        </a>
        <?php endif; ?>
    </div>
</div>