<?php
ob_start();

// Add classes to the section using the filter fl_builder_module_attributes ?>
<section<?php FLBuilder::render_module_attributes( $module ); ?>>

<?php
if ( has_filter( 'fl_builder_module_frontend_custom_' . $module->slug ) ) {
	echo apply_filters( 'fl_builder_module_frontend_custom_' . $module->slug, (array) $module->settings, $module );
} else {
	include apply_filters( 'fl_builder_module_frontend_file', $module->dir . 'includes/frontend.php', $module );
}
?>

</section>

<?php
$out = ob_get_clean();

echo apply_filters( 'fl_builder_render_module_content', $out, $module );

?>
