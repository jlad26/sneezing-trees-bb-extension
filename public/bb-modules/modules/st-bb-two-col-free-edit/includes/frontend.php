<?php
/**
 * Two Column Free Edit module template.
 * @since      1.0.0
 */

global $wp_embed;
?>
<div class="row">
    <div class="st-bb-free-edit-1-container col-lg-6 col-xl-5 offset-xl-1 st-bb-text-col-1">
        <?php
        if ( isset( $mod_params['free_content_1'] ) ) {
            echo wp_kses_post( wpautop( $wp_embed->autoembed( $mod_params['free_content_1'] ) ) );
        }
        $field_prefix = 'col_1';
        include ST_BB_DIR . 'public/partials/button.php';
        ?>
    </div>
    <div class="st-bb-free-edit-2-container col-lg-6 col-xl-5 st-bb-text-col-2">
        <?php
        if ( isset( $mod_params['free_content_2'] ) ) {
            echo wp_kses_post( wpautop( $wp_embed->autoembed( $mod_params['free_content_2'] ) ) );
        }
        $field_prefix = 'col_2';
        include ST_BB_DIR . 'public/partials/button.php';
        ?>
    </div>
</div>