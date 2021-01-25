<?php
/**
 * Central Column Free Edit module template.
 * @since      1.0.0
 */

global $wp_embed;
?>
<div class="row">
    <div class="st-bb-rich-text col-lg-6 offset-lg-3">
        <?php
        if ( isset( $mod_params['free_content'] ) ) {
            echo wpautop( $wp_embed->autoembed( $mod_params['free_content'] ) );
        }
        ?>
    </div>
</div>