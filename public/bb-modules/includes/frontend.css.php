<?php if ( ! empty( $settings->row_bg_color ) ) : ?>
.fl-node-<?php echo $id; ?> .st-bb-row-overlay {
    background-color: #<?php echo $settings->row_bg_color; ?>;
    opacity: <?php echo $settings->row_bg_opacity; ?>%;
}
<?php endif; ?>