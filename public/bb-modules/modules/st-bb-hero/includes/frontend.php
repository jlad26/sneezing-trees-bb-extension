<?php
/**
 * Hero module template.
 * @since      1.0.0
 */

// Align the block of content.
$content_align_class = '';
$desktop_indent = 'yes' == $mod_params['row_desktop_indent'] ? true : false;
if ( 'left' == $mod_params['content_align'] ) {
    if ( $desktop_indent ) {
        $content_align_class = ' col-xl-5 offset-xl-1';
    }
} elseif ( 'center' == $mod_params['content_align'] ) {
    $content_align_class = ' offset-lg-3';
} elseif ( 'right' == $mod_params['content_align'] ) {
    $content_align_class = ' offset-lg-6';
    if ( $desktop_indent ) {
        $content_align_class .= ' col-xl-5';
    }
}

// Format content.
global $wp_embed;
$content = '';
if ( isset( $mod_params['free_content'] ) ) {
    $content = wpautop( $wp_embed->autoembed( $mod_params['free_content'] ) );
}

?>
<div class="st-bb-hero-content row">
    <div class="col-lg-6 st-bb-rich-text<?php echo $content_align_class; ?>">
        <?php echo $content; ?>
        <?php include ST_BB_DIR . 'public/partials/button.php'; ?>
    </div>
</div>