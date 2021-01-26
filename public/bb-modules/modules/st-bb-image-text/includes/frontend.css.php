<?php if ( 'stretch' != $settings->text_valign ) : ?>
.fl-node-<?php echo $id; ?> .st-bb-text-col {
    align-items: <?php echo $settings->text_valign; ?>
}
<?php endif; ?>
<?php if ( 'stretch' != $settings->image_valign ) : ?>
.fl-node-<?php echo $id; ?> .st-bb-img-col {
    align-items: <?php echo $settings->image_valign; ?>
}
<?php endif; ?>
<?php if ( 'no' == $settings->image_margin_bottom ) : ?>
@media (min-width: 992px) {
    .fl-node-<?php echo $id; ?> .st-bb-img-col .figure {
        margin-bottom: 0
    }
}
<?php endif; ?>