<?php
/**
 * Free Edit module template.
 * @since      1.0.0
 */

global $wp_embed;
if ( isset( $mod_params['free_content'] ) ) {
    echo wp_kses_post( wpautop( $wp_embed->autoembed( $mod_params['free_content'] ) ) );
}
?>