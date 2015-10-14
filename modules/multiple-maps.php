<?php
function mpfy_mm_map_limit($limit) {
	return -1;
}
add_filter('mpfy_map_limit', 'mpfy_mm_map_limit');

function mpfy_mm_map_location_custom_fields($custom_fields) {

	$custom_fields['map_location_map'] = Carbon_Field::factory('relationship_mpfy', 'map_location_map', 'Selecciona un mapa')
		->set_post_type('map')
		->help_text('Selecciona el mapa donde aparecerá la localización actual. Las etiquetas asociadas aparecerán abajo.');

	return $custom_fields;
}
add_filter('mpfy_map_location_custom_fields', 'mpfy_mm_map_location_custom_fields');

function mpfy_mm_show_map_ui($show) {
	return true;
}
add_filter('mpfy_show_map_ui', 'mpfy_mm_show_map_ui');

// Remove default menu items & behavior
remove_action('admin_menu', 'mpfy_amg_map_settings_menu', 9);
remove_action('admin_enqueue_scripts', 'mpfy_ms_admin_behaviors');
remove_action('admin_menu', 'mpfy_ms_map_settings_limit', 9);
remove_filter('mpfy_map_custom_fields', 'mpfy_ms_map_custom_fields', 11);
remove_action('init', 'mpfy_ms_guarantee_map_post');
