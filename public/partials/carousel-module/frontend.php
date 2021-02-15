<?php
/**
 * Custom Carousel module template.
 * @since      1.0.0
 */

$is_full_width = ( isset( $mod_params['full_width'] ) && 'yes' == $mod_params['full_width'] );
$desktop_indent = isset( $mod_params['row_desktop_indent'] ) && 'yes' == $mod_params['row_desktop_indent'];
$col_classes = 'col';
if ( ! $is_full_width && $desktop_indent ) {
    $col_classes = 'col-xl-10 offset-xl-1';
}
?>
<div class="row">
    <div class="<?php echo $col_classes; ?>">
        <div class="st-bb-swiper">
            <div class="swiper-container">
                <div class="swiper-wrapper">
            <?php
                if ( is_array( $mod_params['slides'] ) ) {
                    foreach ( $mod_params['slides'] as $slide ) {
            ?>
                    <div class="swiper-slide">
                        <?php echo $slide; ?>
                    </div>
            <?php
                    }
                }
            ?>
                </div>
            </div>
            <?php if ( 'yes' == $mod_params['include_nav'] ) : ?>
                <div class="st-bb-swiper-button st-bb-swiper-button-prev">
                <svg xmlns="http://www.w3.org/2000/svg" width="4rem" height="4rem" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
                </div>
                <div class="st-bb-swiper-button st-bb-swiper-button-next">
                <svg xmlns="http://www.w3.org/2000/svg" width="4rem" height="4rem" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                </svg>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>