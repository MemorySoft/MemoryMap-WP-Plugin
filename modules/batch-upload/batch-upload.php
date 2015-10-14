<?php
// function mpfy_batch_add_admin_page() {
// 	add_submenu_page('mapify.php', 'Batch Upload', 'Batch Upload', 'manage_options', 'mpfy-import', 'mpfy_batch_upload_admin_page');
// }
// add_action('admin_menu', 'mpfy_batch_add_admin_page');

function mpfy_batch_upload_admin_page() {
	include_once('admin/batch-upload.php');
}

function mpfy_batch_trigger_hook() {
	$new_post = array(
		'post_type'=>'map-location',
		'post_status'=>'publish',
		'post_title'=>$_POST['row'][0],
		'post_content'=>$_POST['row'][1],
	);
	$new_post_id = wp_insert_post($new_post);
	$tooltip_enabled = (strtolower($_POST['row'][6]) == 'y') ? 'yes' : 'no';

	update_post_meta($new_post_id, '_map_location_map', $_POST['map_id']);
	update_post_meta($new_post_id, '_map_location_tooltip', $_POST['row'][2]);
	update_post_meta($new_post_id, '_map_location_address', $_POST['row'][3]);
	update_post_meta($new_post_id, '_map_location_city', $_POST['row'][4]);
	update_post_meta($new_post_id, '_map_location_zip', $_POST['row'][5]);
	update_post_meta($new_post_id, '_map_location_tooltip_enabled', $tooltip_enabled);

	$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($_POST['row'][3]) . '&sensor=false';
	$curl = curl_init($url);
	// curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($curl);
	$data = json_decode($response);
	$found_address = false;

	if (@isset($data->results[0])) {
		$found_address = true;
		$latlng = $data->results[0]->geometry->location->lat . ',' . $data->results[0]->geometry->location->lng;
		update_post_meta($new_post_id, '_map_location_google_location', $latlng);
	}

	if ($found_address) {
		echo 'Imported <em>' . $_POST['row'][0] . ' (' . $_POST['row'][3] . ')</em>';
	} else {
		echo 'Imported <em>' . $_POST['row'][0] . ' (' . $_POST['row'][3] . ')</em>. <span style="color: red;">Google Maps failed to retrieve exact location (manual entry is required).</span>';
	}
	exit;
}
add_action('wp_ajax_mpfy_batch_upload', 'mpfy_batch_trigger_hook', 1000);
