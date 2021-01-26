<?php
/**
 * Free Edit module template.
 * @since      1.0.0
 */

global $wp_embed;
?>
<div class="row">
    <div class="st-bb-rich-text col<?php if ( 'yes' == $mod_params['row_desktop_indent'] ) : ?> col-xl-10 offset-xl-1<?php endif; ?>">
    <?php if ( isset( $mod_params['free_content'] ) ) echo wpautop( $wp_embed->autoembed( $mod_params['free_content'] ) ); ?>
    </div>
</div>