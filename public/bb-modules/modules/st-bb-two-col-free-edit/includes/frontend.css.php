<?php if ( 'stretch' != $settings->text_valign_1 ) : ?>
.fl-node-<?php echo $id; ?> .st-bb-text-col-1 {
    align-items: <?php echo $settings->text_valign; ?>
}
<?php endif; ?>
<?php if ( 'stretch' != $settings->text_valign_2 ) : ?>
.fl-node-<?php echo $id; ?> .st-bb-text-col-2 {
    align-items: <?php echo $settings->text_valign_2; ?>
}
<?php endif; ?>