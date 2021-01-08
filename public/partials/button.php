<?php
/**
 * Button template.
 * @since      1.0.0
 */

// Set field prefix if required.
if ( ! isset( $field_prefix ) ) {
    $field_prefix = '';
}

// Set button classes.
$button_classes = array( 'st-bb-btn' );
if ( $field_prefix ) {
    $field_prefix .= '_';
    $button_classes[] = $field_prefix . 'st-bb-btn';
}
$button_classes = apply_filters( 'st_bb_button_classes', $button_classes , $module );

// Get the button url, plus target and attributes if necessary.
$target = $rel = '';
if ( $mod_params[ $field_prefix . 'button_text' ] ) {
    if ( in_array( $mod_params[ $field_prefix . 'button_url_type' ], array( 'url', 'anchor' ) ) ) {
        
        $button_url = esc_url( 'url' == $mod_params[ $field_prefix . 'button_url_type' ] ? $mod_params[ $field_prefix . 'button_url' ] : '#' . $mod_params[ $field_prefix . 'button_anchor_target' ] );
        
        // If no anchor has been specified, make sure the url value is empty.
        if ( '#' == $button_url && 'anchor' == $mod_params[ $field_prefix . 'button_url_type' ] ) {
            $button_url = '';
        }

        if ( 'url' == $mod_params[ $field_prefix . 'button_url_type' ] ) {
            if ( 'yes' == $mod_params[ $field_prefix . 'button_new_window' ] ) {
                $target = ' target="_blank"';
            }
            $rel = $module->get_rel( $field_prefix );
        }
    } else {
        $post_type = $mod_params[ $field_prefix . 'button_url_type' ];
        $post_id = intval( str_replace( 'id_', '', $mod_params[ $field_prefix . 'select_' . $post_type . '_id' ] ) );
        $button_url = get_permalink( $post_id );
    }
}
?>
<?php if ( $mod_params[ $field_prefix . 'button_text' ] && $button_url ) : ?>
    <p>
        <a class="<?php esc_attr_e( implode( " ", $button_classes ) ); ?>" href="<?php echo $button_url; ?>"<?php echo $target . $rel; ?>>
            <?php esc_html_e( $mod_params[ $field_prefix . 'button_text' ] ); ?>
        </a>
    </p>
<?php endif; ?>