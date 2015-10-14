<?php
include_once('mpfy_map.class.php');
include_once('mpfy_map_location.class.php');

// Returns the plugin files version
function mpfy_get_version() {
	$version = get_option('mpfy_plugin_version');
	if (!$version) {
		update_option('mpfy_flush_required', 'y');
		update_option('mpfy_plugin_version', MAPIFY_PLUGIN_VERSION);
		return MAPIFY_PLUGIN_VERSION;
	}
	return $version;
}

// Returns a url to a thumbnail of the provided src
function mpfy_get_thumb($src, $w, $h) {
	$thumb = apply_filters('mpfy_get_thumb', $src, $src, $w, $h);
	return $thumb;
}

// Converts a url into a local path
function mpfy_get_file_real_path($path) {
	$dirs = wp_upload_dir();
	$real_path = $path;
	$real_path = str_replace('www.', '', $real_path);
	$home_url = str_replace('www.', '', home_url());
	if (!stristr($path, $home_url)) {
		$real_path = $home_url . $path;
	}
	if (!stristr($dirs['baseurl'], $home_url)) {
		$dirs['baseurl'] = $home_url . $dirs['baseurl'];
	}
	$real_path = str_replace($dirs['baseurl'], $dirs['basedir'], $real_path);
	$real_path = str_replace('\\', DIRECTORY_SEPARATOR, $real_path);
	$real_path = str_replace('/', DIRECTORY_SEPARATOR, $real_path);
	return $real_path;
}

// Converts a local path to a url
function mpfy_get_file_real_url($url) {
	if (!stristr($url, home_url())) {
		$url = home_url() . $url;
	}
	return $url;
}

// Converts HEX color code to an array of RGB values
function mpfy_hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);

   return $rgb;
}

// Unshifts values before the entered key
function mpfy_array_unshift_key($array, $key, $new_keyvalues) {
	$new_array = array();

	$indexes = array_flip(array_keys($array));
	if (!isset($indexes[$key])) {
		return $array; // key not found at all; return original array; maybe raise an error?
	}

	$index = $indexes[$key];
	// append first part
	$new_array += array_slice($array, 0, $index, true);

	// append $new_keyvalues
	$new_array += $new_keyvalues;

	// append second part
	$new_array += array_slice($array, $index, NULL, true);
	return $new_array;
}

// Push values after the entered key
function mpfy_array_push_key($array, $key, $new_keyvalues) {
	$new_array = array();

	$indexes = array_flip(array_keys($array));
	if (!isset($indexes[$key])) {
		return $array; // key not found at all; return original array; maybe raise an error?
	}

	$index = $indexes[$key];
	// append first part
	$new_array += array_slice($array, 0, $index + 1, true);

	// append $new_keyvalues
	$new_array += $new_keyvalues;

	// append second part
	$new_array += array_slice($array, $index + 1, NULL, true);
	return $new_array;
}

// Gets a single meta and makes sure the return value is boolean
function mpfy_meta_to_bool($post_id, $meta_key, $default) {
	$value = get_post_meta($post_id, $meta_key, true);

	// return if value is already boolean
	if (is_bool($value)) {
		return $value;
	}

	// the default should be returned if a blank string value (i.e. when post meta does not exist)
	if ($value === '') {
		return $default;
	}

	// consider only the listed values as true
	$true_values = array(1, '1', 'true', 'y', 'yes');
	if (in_array($value, $true_values)) {
		return true;
	}

	// all other cases are considered false
	return false;
}

// Returns the post types which are considered as locations
function mpfy_get_supported_post_types() {
	$post_types = array('map-location');
	$post_types = apply_filters('mpfy_supported_post_types', $post_types);
	return $post_types;
}
