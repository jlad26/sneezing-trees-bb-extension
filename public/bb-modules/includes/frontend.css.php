<?php
$has_row_image = false;
if ( isset( $settings->image_src ) ) {
    if ( ! empty( $settings->image_src ) ) {
        $has_row_image = true;
    }
}
?>

.fl-node-<?php echo $id; ?> .st-bb-row-<?php echo $has_row_image ? 'overlay' : 'background'; ?> {
    background-color: #<?php echo $settings->background_color; ?>;
    opacity: <?php echo $settings->background_opacity; ?>%;
}