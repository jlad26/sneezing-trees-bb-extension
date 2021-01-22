<?php
/**
 * Custom Carousel module template.
 * @since      1.0.0
 */

$mod_params['slides'] = apply_filters( 'st_bb_custom_carousel_slides-' . $mod_params['instance_id'], array(), $mod_params );
$is_full_width = ( isset( $mod_params['full_width'] ) && 'yes' == $mod_params['full_width'] );
?>
<div class="row">
    <div class="<?php echo $is_full_width ? 'col' : 'col-lg-10 offset-lg-1'; ?>">
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