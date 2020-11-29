<?php if ( 'screen' == $settings->row_height ) : ?>
.fl-node-<?php echo $id; ?> {
    min-height: 100vh;
}
<?php endif; ?>
<?php if ( ! empty( $settings->row_bg_color ) ) : ?>
.fl-node-<?php echo $id; ?> .st-bb-row-overlay {
    background-color: #<?php echo $settings->row_bg_color; ?>;
    <?php if ( $settings->row_bg_opacity < 100 ) : ?>
    opacity: <?php echo $settings->row_bg_opacity; ?>%;
    <?php endif; ?>
}
<?php endif; ?>