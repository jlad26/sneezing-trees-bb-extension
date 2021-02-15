<?php
$carousel_css = '';
if ( 'outside' == $settings->nav_layout ) {
    $carousel_css .= '.fl-node-' . $id . ' .row div.swiper-container {
    width: calc(100% - 8rem);
},
';
}
echo apply_filters( 'st_bb_carousel_css-' . $settings->instance_id, $carousel_css, $id, $settings, $module );
?>