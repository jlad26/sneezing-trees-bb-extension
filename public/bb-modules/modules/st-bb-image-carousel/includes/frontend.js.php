var stBBSwiper = new Swiper('.fl-node-<?php echo $id; ?> .swiper-container', {
    navigation  : {
        nextEl  : '.fl-node-<?php echo $id; ?> .swiper-button-next',
        prevEl  : '.fl-node-<?php echo $id; ?> .swiper-button-prev',
    },
    slidesPerView:  1,
} );