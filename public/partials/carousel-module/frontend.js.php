var stBBSwiper = new Swiper('.fl-node-<?php echo $id; ?> .swiper-container', {
    <?php
    $carousel_settings = 'grabCursor  :   true,
slidesPerView   :  1,
';
    if ( 'yes' == $settings->include_nav ) {
        $carousel_settings .= 'navigation  : {
    nextEl  : \'.fl-node-' . $id . ' .st-bb-swiper-button-next\',
    prevEl  : \'.fl-node-' . $id . ' .st-bb-swiper-button-prev\',
},
';
    }
    echo apply_filters( 'st_bb_carousel_params-' . $settings->instance_id, $carousel_settings, $id, $settings, $module );
    ?>
} );