<?php
function mpfy_wpt_load_wpthumb() {
	if (!function_exists('wpthumb')) {
		include_once(MAPIFY_PLUGIN_DIR . '/lib/wpthumb/wpthumb.php');
	}
}
add_action('wp', 'mpfy_wpt_load_wpthumb');

function mpfy_wpt_get_thumb($thumbnail, $src, $w, $h) {
	$thumbnail = wpthumb($src, array(
		'width'=>$w,
		'height'=>$h,
		'crop'=>true,
	));
	return $thumbnail;
}
add_filter('mpfy_get_thumb', 'mpfy_wpt_get_thumb', 10, 4);
