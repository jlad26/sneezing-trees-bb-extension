var stBBSwiper = new Swiper('.fl-node-<?php echo $id; ?> .swiper-container', {
    <?php
    $carousel_settings = 'navigation  : {
        nextEl  : \'.fl-node-' . $id . ' .swiper-button-next\',
        prevEl  : \'.fl-node-' . $id . ' .swiper-button-prev\',
    },
    grabCursor  :   true,
    ';
    echo apply_filters( 'st-bb-custom-carousel-params-' . $settings->custom_carousel_id, $carousel_settings, $id, $settings, $module );
    ?>
} );