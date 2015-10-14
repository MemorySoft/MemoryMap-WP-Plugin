<?php
// Enqueue front-end assets
function mpfy_mll_enqueue_assets() {
	if (!defined('MPFY_LOAD_ASSETS')) {
		return;
	}

	// Load popup styles
	wp_enqueue_style('mpfy-map-location-list', plugins_url('modules/map-location-list/style.css', MAPIFY_PLUGIN_FILE), array(), '1.0.0');

	// Load popup behaviors
	wp_enqueue_script('mpfy-map-location-list', plugins_url('modules/map-location-list/functions.js', MAPIFY_PLUGIN_FILE), array('jquery'), '1.0.0', true);
}
add_action('wp_footer', 'mpfy_mll_enqueue_assets');

function mpfy_mll_template_after_map($map_id) {
	$enabled = mpfy_meta_to_bool($map_id, '_map_mll_include', false);
	if (!$enabled) {
		return;
	}

	$map = new Mpfy_Map($map_id);
	$locations = $map->get_locations();

	include('templates/list.php');
}
add_action('mpfy_template_after_map', 'mpfy_mll_template_after_map');
