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
            <?php if ( 'yes' == $mod_params['include_nav'] ) : ?>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            <?php endif; ?>
        </div>
    </div>
</div>