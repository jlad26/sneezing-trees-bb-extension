<?php
/**
 * Custom Carousel module template.
 * @since      1.0.0
 */

$mod_params['slides'] = apply_filters( 'st-bb-custom-carousel-slides-' . $mod_params['custom_carousel_id'], array() );

?>
<div class="row">
    <div class="col-lg-10 offset-lg-1">
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