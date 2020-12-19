<?php
/**
 * Image Carousel module template.
 * @since      1.0.0
 */

/**
 * Define image attributes.
 * Note that sizes are defined for default bootstrap grid breakpoints and column padding of 15px.
 */
$mod_params['image_attr'] = array(
	'sizes'	=>	'(min-width: 1200px) 950px, (min-width: 992px) 770px, (min-width: 768px) 690px, (min-width: 576px) 510px, calc(100vw - 30px)'
);

// Set image size to 1536 x 1536 as we won't be needing wider or taller than that.
$mod_params['image_size'] = '1536x1536';

?>
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="swiper-container">
            <div class="swiper-wrapper">
        <?php
            if ( is_array( $mod_params['carousel_image_ids'] ) ) {
                foreach ( $mod_params['carousel_image_ids'] as $image_id ) {
                    $mod_params['image_id'] = $image_id;
                    $mod_params['caption'] = wp_get_attachment_caption( $image_id );
                    $mod_params['image_attr']['alt'] = $mod_params['caption'] ? $mod_params['caption'] : '';
        ?>
                <div class="swiper-slide">
                    <?php include ST_BB_DIR . 'public/partials/figure.php'; ?>
                </div>
        <?php
                }
            }
        ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</div>