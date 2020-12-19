<?php
/**
 * Image Gallery module template.
 * @since      1.0.0
 */

/**
 * Define image attributes.
 * Note that sizes are defined for default bootstrap grid breakpoints and column padding of 15px.
 */
$mod_params['image_attr'] = array(
	'sizes'	=>	'150px'
);

// Set image size to thumbnail for the display.
$mod_params['image_size'] = 'thumbnail';

?>
<div class="row">
    <div class="col">
        <div id="lightgallery-<?php echo $module->node; ?>" class="st-bb-gallery"><?php

            if ( is_array( $mod_params['gallery_image_ids'] ) ) {
                foreach ( $mod_params['gallery_image_ids'] as $image_id ) {
                    
                    $full_image_size = apply_filters( 'st_bb_default_img_size', '2048x2048' );
                    $full_image_data = wp_get_attachment_image_src( $image_id, $full_image_size );
                    
                    // Don't include this image if we have no data (e.g., is a video.)
                    if ( ! $full_image_data ) {
                        continue;
                    }
                    
                    $mod_params['image_id'] = $image_id;
                    $caption = wp_get_attachment_caption( $image_id );
                    $mod_params['image_attr']['alt'] = $caption ? $caption : '';

                    $full_image_srcset = wp_get_attachment_image_srcset( $image_id, $full_image_size );
                    $caption_attr = 'yes' == $mod_params['show_captions'] ? ' data-sub-html=".st-bb-gallery-caption"' : '';
                    
                ?><div class="st-bb-gallery-thumbnail" data-src="<?php echo $full_image_data[0]; ?>" data-srcset="<?php echo $full_image_srcset; ?>" data-sizes="100vw"<?php echo $caption_attr; ?>>
                    <?php
                        include ST_BB_DIR . 'public/partials/figure.php';
                        if ( 'yes' == $mod_params['show_captions'] ) :
                            $caption = wp_kses_post( nl2br( wp_get_attachment_caption( $image_id ) ) ); ?>
                            <div class="st-bb-gallery-caption"><?php echo $caption; ?></div>
                        <?php endif; ?>
                </div><?php 

                }
            }
        ?>
        </div>
    </div>
</div>