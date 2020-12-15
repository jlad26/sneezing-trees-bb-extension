(function(){
    var i, e, d = document, s = "script";i = d.createElement("script");i.async = 1;
    i.src = "https://cdn.curator.io/published/<?php echo $settings->curator_feed_id; ?>.js";
    e = d.getElementsByTagName(s)[0];e.parentNode.insertBefore(i, e);
})();