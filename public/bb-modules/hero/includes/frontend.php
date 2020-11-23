<?php
/**
 * Hero module template.
 *
 * @params array    $mod_params   Array of settings.
 * 
 * @since      1.0.0
 */

// If this is a BB module...
if ( isset( $settings) && isset( $module ) ) {

    $mod_params = get_object_vars( $settings );

} else { // ...this is an ACF version.

}

// Set defaults.
$defaults = array(
    'container_classes'     =>  '',
    'button_classes'        =>  '',
    'button_url'            =>  '#',
);
foreach ( $defaults as $param => $default ) {
    if ( ! isset( $mod_params[ $param ] ) ) {
        $mod_params[ $param ] = $default;
    }
}
?>
<div class="st-bb-hero st-bb-hero-outer<?php echo esc_attr( $mod_params['container_classes'] ); ?>">
    <div class="st-bb-hero-inner">
        <div class="st-bb-hero-overlay"></div>
        <?php if ( isset( $mod_params['image_src'] ) ) : ?>
        <img src="<?php echo esc_url( $mod_params['image_src'] ); ?>" alt="" />
        <?php endif; ?>
        <div class="st-bb-hero-content <?php $module->module_classes( 'inner_content_classes' ); ?>">
            
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
</div>