<?php
/**
 * Button template.
 * @since      1.0.0
 */

// Set button classes.
$button_classes = apply_filters( 'st_bb_button_classes', 'st-bb-btn', $module );

// Get the button url, plus target and attributes if necessary.
$target = $rel = '';
if ( $mod_params['button_text'] ) {
    if ( in_array( $mod_params['button_url_type'], array( 'url', 'anchor' ) ) ) {
        
        $button_url = esc_url( $mod_params['button_url'] );
        
        if ( 'url' == $mod_params['button_url_type'] ) {
            if ( 'yes' == $mod_params['button_new_window'] ) {
                $target = ' target="_blank"';
            }
            $rel = $module->get_rel();
        }
    } else {
        $post_type = $mod_params['button_url_type'];
        $post_id = intval( str_replace( 'id_', '', $mod_params['select_' . $post_type . '_id' ] ) );
        $button_url = get_permalink( $post_id );
    }
}
?>
<?php if ( $mod_params['button_text'] && $button_url ) : ?>
    <p>
        <a class="<?php esc_attr_e( $button_classes ); ?>" href="<?php echo $button_url; ?>"<?php echo $target . $rel; ?>>
            <?php esc_html_e( $mod_params['button_text'] ); ?>
        </a>
    </p>
<?php endif; ?>