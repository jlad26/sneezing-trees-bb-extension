<?php
/**
 * Image and Text module template.
 * @since      1.0.0
 */

// Set button classes.
$button_classes = apply_filters( 'st_bb_image_text_button_classes', 'st-bb-btn', $module );

// Enable embedding.
global $wp_embed;
$mod_params['text_content'] = wpautop( $wp_embed->autoembed( $mod_params['text_content'] ) );

/**
 * Define image attributes.
 * Note that sizes are defined for default bootstrap grid breakpoints and column padding of 15px.
 */
$mod_params['image_attr'] = array(
	'alt'	=>	isset( $mod_params['row_image_alt'] ) ? $mod_params['row_image_alt'] : '',
	'sizes'	=>	'(min-width: 1200px) 445px, (min-width: 992px) 450px, (min-width: 768px) 330px, (min-width: 576px) 510px, calc(100vw - 30px)'
);

// Set column ordering classes.
$order_classes = array(
    'first_col'     =>  array(),
    'second_col'    =>  array()
);

if ( 'text_first' == $mod_params['mobile_order'] ) {
    $order_classes = array(
        'first_col'     =>  array( 'order-last' ),
        'second_col'    =>  array( 'order-first' )
    );
}

if ( 'right' == $mod_params['image_placement'] ) {
    if ( empty( $order_classes['first_col'] ) ) {
        $order_classes['first_col'][] = 'order-md-last';
        $order_classes['second_col'][] = 'order-md-first';
    }
} else {
    if ( ! empty ( $order_classes['first_col'] ) ) {
        $order_classes['first_col'][] = 'order-md-first';
        $order_classes['second_col'][] = 'order-md-last';
    }
}

$order_classes['first_col'] = implode( ' ', $order_classes['first_col'] );
$order_classes['second_col'] = implode( ' ', $order_classes['second_col'] );

?>
<div class="row">
    <div class="st-bb-img-col col-md-6 col-xl-5 offset-xl-1 <?php echo $order_classes['first_col']; ?>">
        <?php include ST_BB_DIR . 'public/partials/figure.php'; ?>
    </div>
    <div class="st-bb-text-col col-md-6 col-xl-5 <?php echo $order_classes['second_col']; ?>">
    <?php if ( $mod_params['text_content'] ) : ?>
        <div class="st-bb-text"><?php echo $mod_params['text_content']; ?></div>
    <?php endif; ?>
    <?php if ( $mod_params['button_text'] && $mod_params['button_url'] ) : ?>
        <a class="<?php esc_attr_e( $button_classes ); ?>" href="<?php echo esc_url( $mod_params['button_url'] ); ?>">
            <?php esc_html_e( $mod_params['button_text'] ); ?>
        </a>
    <?php endif; ?>
    </div>
</div>