<?php
// Enqueue admin assets
function mpfy_pc_admin_behaviors($hook) {
    if ($hook !== 'post.php') {
        return;
    }

    wp_enqueue_script('mpfy-popup-colors-admin', plugins_url('modules/popup-colors/admin.js', MAPIFY_PLUGIN_FILE), array('jquery'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'mpfy_pc_admin_behaviors');

// Load values in front-end
function mpfy_pc_map_background_color($value, $map_id) {
    $background = get_post_meta($map_id, '_map_background_color', true);
	if (!$background) {
		$background = $value;
	}

	return $background;
}
add_filter('mpfy_map_background_color', 'mpfy_pc_map_background_color', 10, 2);

function mpfy_pc_map_tooltip_background_color($value, $map_id) {
	$tooltip_background = get_post_meta($map_id, '_map_tooltip_background_color', true);
	if (!$tooltip_background) {
		$tooltip_background = '#ffffff';
	}
	$tooltip_background = mpfy_hex2rgb($tooltip_background); // array(r,g,b)
	$tooltip_background[] = 1; // add alpha

	return $tooltip_background;
}
add_filter('mpfy_map_tooltip_background_color', 'mpfy_pc_map_tooltip_background_color', 10, 2);

function mpfy_pc_apply_colors($map_location_id, $map_id) {
	$colors = array(
		'_map_popup_background_color'=>'#FFFFFF',
		'_map_popup_header_background_color'=>'#FED130',
		'_map_popup_date_background_color'=>'#333333',
		'_map_popup_accent_color'=>'#999999',
	);
	foreach ($colors as $key => $default) {
		$v = get_post_meta($map_id, $key, true);
		if ($v) {
			$colors[$key] = $v;
		}
	}

	$styles = '
	<style type="text/css">
		.mpfy-p-color-popup-background { background-color: ' . $colors['_map_popup_background_color'] . ' !important; }
		.mpfy-p-color-header-background { background-color: ' . $colors['_map_popup_header_background_color'] . ' !important; }
		.mpfy-p-color-header-date-background { background-color: ' . $colors['_map_popup_date_background_color'] . ' !important; }
		.mpfy-p-color-accent-background { background-color: <?php ' . $colors['_map_popup_accent_color'] . ' !important; }
		.mpfy-p-color-accent-color { color: ' . $colors['_map_popup_accent_color'] . ' !important; }
	</style>
	';

	echo $styles;
}
add_action('mpfy_popup_before_section', 'mpfy_pc_apply_colors', 10, 2);
