<?php
/**
 * Hero module template.
 * @since      1.0.0
 */
?>
<div class="st-bb-hero-content row">
    <div class="col-md-6">
        <?php if ( isset( $mod_params['title'] ) ) : ?>
        <h1 class="st-bb-hero-title">
            <?php echo esc_html( $mod_params['title'] ); ?>
        </h1>
        <?php endif; ?>

        <?php if ( isset( $mod_params['subtitle'] ) ) : ?>
        <h2 class="st-bb-hero-subtitle">
            <?php echo esc_html( $mod_params['subtitle'] ); ?>
        </h2>
        <?php endif; ?>

        <?php if ( isset( $mod_params['button'] ) ) : ?>
        <a type="button" class="st-bb-button<?php esc_attr_e( $mod_params['button_classes'] ); ?>" href="<?php echo esc_url( $mod_params['button_url'] ); ?>">
            <?php echo esc_html( $mod_params_content['button_text'] ); ?>
        </button>
        <?php endif; ?>
    </div>
</div>