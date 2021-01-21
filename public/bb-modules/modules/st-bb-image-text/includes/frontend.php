<?php
/**
 * Image and Text module template.
 * @since      1.0.0
 */

// Enable embedding.
global $wp_embed;
$mod_params['text_content'] = wpautop( $wp_embed->autoembed( $mod_params['text_content'] ) );

/**
 * Define image attributes.
 * Note that sizes are defined for default bootstrap grid breakpoints and column padding of 15px.
 */
$mod_params['image_attr'] = array(
	'alt'	=>	isset( $mod_params['row_image_alt'] ) ? $mod_params['row_image_alt'] : '',
	'sizes'	=>	'(min-width: 1200px) 445px, (min-width: 992px) 450px, (min-width: 768px) 690px, (min-width: 576px) 510px, calc(100vw - 30px)'
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
        $order_classes['first_col'][] = 'order-lg-last';
        $order_classes['second_col'][] = 'order-lg-first';
    }
    $col_classes = array(
        'first_col'     =>  'col-lg-6 col-xl-5',
        'second_col'    =>  'col-lg-6 col-xl-5 offset-xl-1'
    );
} else {
    if ( ! empty ( $order_classes['first_col'] ) ) {
        $order_classes['first_col'][] = 'order-lg-first';
        $order_classes['second_col'][] = 'order-lg-last';
    }
    $col_classes = array(
        'first_col'     =>  'col-lg-6 col-xl-5 offset-xl-1',
        'second_col'    =>  'col-lg-6 col-xl-5'
    );
}

$order_classes['first_col'] = implode( ' ', $order_classes['first_col'] );
$order_classes['second_col'] = implode( ' ', $order_classes['second_col'] );

?>
<div class="row">
    <div class="st-bb-img-col <?php echo $col_classes['first_col'] . ' ' . $order_classes['first_col']; ?>">
        <?php include ST_BB_DIR . 'public/partials/figure.php'; ?>
    </div>
    <div class="st-bb-text-col <?php echo $col_classes['second_col'] . ' ' . $order_classes['second_col']; ?>">
    <div class="st-bb-col-content">
        <?php if ( $mod_params['text_content'] ) : ?>
            <div class="st-bb-text"><?php echo $mod_params['text_content']; ?></div>
        <?php endif; ?>
        <?php include ST_BB_DIR . 'public/partials/button.php'; ?>
        </div>
    </div>
</div>