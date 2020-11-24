<?php

/**
 * Check that module has been defined, either by BB or ACF.
 * Note that $module is either an FLBuilderModule object or an
 * ST_ACF_Module object
 */
if ( ! isset( $module ) ) {
	return false;
}

// Make sure that image src is set.
$mod_params = array(
	'row_image_src'		=>	''
);

// If this is a BB module...
if ( is_subclass_of( $module, 'FLBuilderModule' ) ) {

	// Define module parameters.
	$mod_params = get_object_vars( $settings );

	// Get BB section classes.
	ob_start();
	FLBuilder::render_module_attributes( $module );
	$mod_params['section_attributes'] = ltrim( ob_get_clean() );

} else { // ...this is an ACF version.
	// $mod_params = ? some ACF module property

	$acf_section_classes = apply_filters( 'st_bb_section_classes', array( 'st-bb-section' ), $module );
	$mod_params['section_attributes'] = 'class="' . implode( ' ', $acf_section_classes ) . '"';

}

// Set defaults.
$defaults = array(
    'row_classes'     =>  '',
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
		 * The .st-bb-row element is always 100% width of the overall section as it is used
		 * to position full width background and images.
		 */ ?>
		<div class="st-bb-row<?php echo esc_attr( $mod_params['row_classes'] ); ?>">

			<?php
			/**
			 * The .st-bb-row-background and .st-bb-row-overlay elements together set the
			 * row background color and image.
			 * If an image is used, then the background colour and opacity is set on the
			 * .st-bb-row-overlay element and elements are positioned absolutely.
			 * Otherwise background colour and opacity are set on the .st-bb-row-background element and
			 * positioned statically.
			 */ ?>
			<div class="st-bb-row-background<?php echo ! empty( $mod_params['row_image_src'] ) ? ' --st-bb-row-img' : ''; ?>">
				
				<?php
				// Only include the overlay and image if an image is set.
				if ( ! empty( $mod_params['row_image_src'] ) ) :
				?>
				<div class="st-bb-row-overlay"></div>
				<img src="<?php echo esc_url( $mod_params['row_image_src'] ); ?>" alt="" />
				<?php endif; ?>

				<?php
				/**
				 * The module content container.
				 */ ?>
				<div class="st_bb_module">
					<?php // Render module content.
					ob_start();
					include apply_filters( 'st_bb_module_frontend_file', $module->dir . 'includes/frontend.php', $module );
					$out = ob_get_clean();
					echo apply_filters( 'st_bb_render_module_content', $out, $module );
					?>
				</div>

			</div>

		</div>
	
	<?php if ( $on_backend ) : ?>

	</div>
	<?php endif; ?>
</section>
