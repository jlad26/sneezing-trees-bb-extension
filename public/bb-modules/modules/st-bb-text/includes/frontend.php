<?php
/**
 * Text module template.
 * @since      1.0.0
 */

global $wp_embed;
if ( isset( $mod_params['text_content'] ) ) {
    echo wpautop( $wp_embed->autoembed( $mod_params['text_content'] ) );
}
?>