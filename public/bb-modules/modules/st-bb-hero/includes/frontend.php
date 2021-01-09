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

// Format content.
global $wp_embed;
$content = '';
if ( isset( $mod_params['free_content'] ) ) {
    $content = wp_kses_post( wpautop( $wp_embed->autoembed( $mod_params['free_content'] ) ) );
}

?>
<div class="st-bb-hero-content row">
    <div class="col-md-6 st-bb-rich-text<?php echo $content_align_class . $text_align_class; ?>">
        <?php echo $content; ?>
        <?php include ST_BB_DIR . 'public/partials/button.php'; ?>
    </div>
</div>