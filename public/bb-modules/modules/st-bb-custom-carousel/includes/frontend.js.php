var stBBSwiper = new Swiper('.fl-node-<?php echo $id; ?> .swiper-container', {
    <?php
    $carousel_settings = 'navigation  : {
        nextEl  : \'.fl-node-' . $id . ' .swiper-button-next\',
        prevEl  : \'.fl-node-' . $id . ' .swiper-button-prev\',
    },
    grabCursor  :   true,
    ';
    echo apply_filters( 'st_bb_custom_carousel_params-' . $settings->instance_id, $carousel_settings, $id, $settings, $module );
    ?>
} );