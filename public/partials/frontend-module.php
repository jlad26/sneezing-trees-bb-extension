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

// Work out whether we are on back end editing end or not.
$is_edit_mode = is_admin() || isset( $_GET['fl_builder'] );

/**
 * Add classes to the section using the filter fl_builder_module_attributes.
 */ ?>
<section <?php ST_BB_Module_Manager::render_section_classes( $module ); ?>>
	
	<?php
	// If we are on the backend we need to include the BB wrapper to support editing functionality.
	if ( $is_edit_mode ) :
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
		<div class="<?php if ( is_subclass_of( $module, 'ST_BB_MODULE' ) ) $module->container_classes(); ?>">
			<?php // Render module content.
			ob_start();
			include apply_filters( 'st_bb_module_frontend_file', $module->dir . 'includes/frontend.php', $module );
			$out = ob_get_clean();
			echo apply_filters( 'st_bb_render_module_content', $out, $module );
			?>
		</div>
	
	<?php if ( $is_edit_mode ) : ?>
	</div>
	<?php endif; ?>
	
</section>
