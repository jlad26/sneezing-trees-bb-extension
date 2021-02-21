<?php

/**
 * Check that module has been defined, either by BB or ACF.
 * Note that $module is an FLBuilderModule object in either
 * case but the instance has limited data if ACF-defined.
 */
if ( ! isset( $module ) ) {
	return false;
}

// Work out whether we are on back end editing end or not.
$is_edit_mode = is_admin() || isset( $_GET['fl_builder'] );

/**
 * Work out container classes. If one of our modules, add in the the required classes.
 * If a standard BB module, just set as "container" to give backward compatibility for page / post
 * content that has been automatically been placed in a BB classic editor module.
 */
if ( is_subclass_of( $module, 'ST_BB_MODULE' ) ) {
	$rendered_container_classes = $module->container_classes( $echo = false );
} else {
	$container_classes = apply_filters( 'st_bb_all_modules_container_classes',
		array( 'container' => 'container' ),
		$module
	);
	$rendered_container_classes = implode( ' ',  $container_classes );
}

// Only render the container if it has some classes.
$container = array(
	'open'	=>	$rendered_container_classes ? '<div class="' . esc_attr( $rendered_container_classes ) . '">' : '',
	'close'	=>	$rendered_container_classes ? '</div>' : ''
);

// Define module parameters.
$mod_params = get_object_vars( $settings );

$image_attr = array(
	'class'	=>	apply_filters( 'st_bb_background_img_classes', 'st-bb-background-img', $module, $settings ),
	'alt'	=>	isset( $mod_params['row_image_alt'] ) ? $mod_params['row_image_alt'] : '',
	'sizes'	=>	'100vw'
);

// Set to scrolldown to empty values in case this is not one of our modules.
if ( ! isset( $mod_params['row_scrolldown_hover_text'] ) ) {
	$mod_params['row_scrolldown_hover_text'] = '';
}
if ( ! isset( $mod_params['row_scrolldown_target'] ) ) {
	$mod_params['row_scrolldown_target'] = '';
}

// Set section id.
$section_id = ( isset( $mod_params['section_id'] ) && $mod_params['section_id'] ) ? 'id="' . esc_attr( $mod_params['section_id'] ) . '" ' : '';

// Set indent classes.
$pre_post_classes =  ( isset( $mod_params['row_desktop_indent'] ) && 'yes' == $mod_params['row_desktop_indent'] ) ? 'col-xl-10 offset-xl-1' : 'col';

// Global for editors.
global $wp_embed;

/**
 * Add id if necessary, and classes using the filter fl_builder_module_attributes.
 */ ?>
<section <?php echo $section_id; ?><?php ST_BB_Module_Manager::render_section_classes( $module ); ?>>
	
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
		<?php if ( ! empty( $mod_params['row_image'] ) ) : ?>
		<?php echo wp_get_attachment_image( $mod_params['row_image'], 'full', false, $image_attr ); ?>
		<?php endif; ?>
		<?php if ( ! empty( $mod_params['row_bg_color'] ) ) : ?>
		<div class="st-bb-row-overlay"></div>
		<?php endif; ?>

		<?php
		/**
		 * The module content container.
		 */ ?>
		<?php echo $container['open']; ?>
			<?php // Render module content.
			
			// Display any pre content.
			if ( isset( $mod_params['before_content'] ) && $mod_params['before_content'] ) : ?>
				<div class="row st-bb-top-tail">
					<div class="st-bb-before-content <?php echo $pre_post_classes; ?>">
						<div><?php echo wpautop( $wp_embed->autoembed( $mod_params['before_content'] ) ); ?></div>
					</div>
				</div>
			<?php endif; ?>
			
			<?php
			// Display main content.
			ob_start();
			include apply_filters( 'st_bb_module_frontend_file', $module->dir . 'includes/frontend.php', $module );
			$out = ob_get_clean();
			echo apply_filters( 'st_bb_render_module_content', $out, $module );

			// Display any post content.
			if ( isset( $mod_params['after_content'] ) && $mod_params['after_content'] ) : ?>
				<div class="row st-bb-top-tail">
					<div class="st-bb-after-content <?php echo $pre_post_classes; ?>">
						<div><?php echo wpautop( $wp_embed->autoembed( $mod_params['after_content'] ) ); ?></div>
					</div>
				</div>
			<?php endif; ?>

		<?php echo $container['close']; ?>

		<?php if ( $mod_params['row_scrolldown_hover_text'] && $mod_params['row_scrolldown_target'] ) : ?>
				<span class="st-bb-scrolldown-container">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
						<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
					</svg>
					<a class="st-bb-scrolldown" href="<?php echo esc_url( '#' . $mod_params['row_scrolldown_target'] ); ?>">
						<?php esc_html_e( $mod_params['row_scrolldown_hover_text'] );?>
					</a>
				</span>
		<?php endif; ?>
	
	<?php if ( $is_edit_mode ) : ?>
	</div>
	<?php endif; ?>
	
</section>
