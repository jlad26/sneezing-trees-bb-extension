<?php
// $groups received as a magic variable from template loading.
foreach ( $groups as $group ) {
	$cols = FLBuilderModel::get_nodes( 'column', $group );
	foreach ( $cols as $col ) {
		$nodes = FLBuilderModel::get_nodes( null, $col );
		foreach ( $nodes as $node ) {
			if ( 'module' == $node->type && FLBuilderModel::is_module_registered( $node->settings->type ) ) {
				self::render_module( $node );
			}
		}
	}
}
?>