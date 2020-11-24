<?php
$has_row_image = false;
if ( isset( $settings->row_image_src ) ) {
    if ( ! empty( $settings->row_image_src ) ) {
        $has_row_image = true;
    }
}
?>

.fl-node-<?php echo $id; ?> .st-bb-row-<?php echo $has_row_image ? 'overlay' : 'background'; ?> {
    background-color: #<?php echo $settings->row_bg_color; ?>;
    opacity: <?php echo $settings->row_bg_opacity; ?>%;
}