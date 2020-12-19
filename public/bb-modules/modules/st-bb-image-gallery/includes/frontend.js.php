lightGallery(document.getElementById('lightgallery-<?php echo $id; ?>'), {
    subHtmlSelectorRelative : true,
    download : false,
    hideBarsDelay : 1000,
    thumbnail : <?php echo 'yes' == $settings->enable_thumbnail ? 'true' : 'false'; ?>,
    fullScreen : <?php echo 'yes' == $settings->enable_fullscreen ? 'true' : 'false'; ?>,
    share : <?php echo 'yes' == $settings->enable_share ? 'true' : 'false'; ?>,
});