<?php

/**
 * Check that module has been defined, either by BB or ACF.
 * Note that $module is an FLBuilderModule object in either
 * case but the instance has limited data if ACF-defined.
 */
if ( ! isset( $module ) ) {
	return false;
}

// Define module parameters.
$mod_params = get_object_vars( $settings );

// If this is a full BB module...
if ( ! empty( $module->form ) ) {

	// Get BB section classes.
	ob_start();
	FLBuilder::render_module_attributes( $module );
	$mod_params['section_attributes'] = ltrim( ob_get_clean() );

} else { // ...this is an ACF version.

	$acf_section_classes = apply_filters( 'st_bb_section_classes', array( 'st-bb-section', 'fl-node-' . $module->node ), $module );
	$mod_params['section_attributes'] = 'class="' . implode( ' ', $acf_section_classes ) . '"';

}

// Set defaults.
$defaults = array(
    'button_classes'        =>  '',
    'button_url'            =>  '#',
);
foreach ( $defaults as $param => $default ) {
    if ( ! isset( $mod_params[ $param ] ) ) {
        $mod_params[ $param ] = $default;
    }
}

// Work out whether we are on back end editing end or not.
$on_backend = is_admin() || isset( $_GET['fl_builder'] );

/**
 * Add classes to the section using the filter fl_builder_module_attributes.
 * By default the class st-bb-section is added.
 */ ?>
<section <?php echo $mod_params['section_attributes']; ?>>
	
	<?php
	// If we are on the backend we need to include the BB wrapper to support editing functionality.
	if ( $on_backend ) :
	?>
	<div class="fl-module-content fl-node-content">
	<?php endif; ?>

		<?php
		/**
		 * The row background image and overlay.
		 */ ?>
		<?php if ( ! empty( $mod_params['row_image_src'] ) ) : ?>
		<img class="st-bb-background-img" src="<?php echo esc_url( $mod_params['row_image_src'] ); ?>" alt="<?php esc_attr_e( $mod_params['row_image_alt'] ); ?>" />
		<?php endif; ?>
		<div class="st-bb-row-overlay"></div>
		
		<?php
		/**
		 * The module content container.
		 */ ?>
		<div class="<?php $module->module_classes( 'module' ); ?>">
			<?php // Render module content.
			ob_start();
			include apply_filters( 'st_bb_module_frontend_file', $module->dir . 'includes/frontend.php', $module );
			$out = ob_get_clean();
			echo apply_filters( 'st_bb_render_module_content', $out, $module );
			?>
		</div>
	
	<?php if ( $on_backend ) : ?>
	</div>
	<?php endif; ?>
	
</section>
