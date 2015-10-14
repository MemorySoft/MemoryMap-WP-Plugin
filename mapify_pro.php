<?php
/*
Plugin Name: MapifyPro
Plugin URI: http://www.mapifypro.com
Description: Implementación de mapa interactivo para Código Nuevo.
Version: 2.1.1
Author: Josh Sears
Author URI: http://www.mapifypro.com
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Mapify Plugin-specific */
$plugin_data = get_file_data(__FILE__, array('Plugin Name'=>'Plugin Name', 'Version'=>'Version'));

function mpfy_mp_plugin_conflict() {
	require_once(ABSPATH . '/wp-admin/includes/plugin.php');

	$plugin_data = get_file_data(__FILE__, array('Plugin Name'=>'Plugin Name', 'Version'=>'Version'));

	$incompatible_plugins = array(
		'mapify_pro'=>'MapifyPro',
		'mapify_store_locator'=>'MapifyStoreLocator',
	);

	$incompatible_plugins_detected = array();

	$me = str_replace('.php', '', basename(__FILE__));
	foreach ($incompatible_plugins as $k => $name) {
		if ($me != $k && is_plugin_active($k . '/' . $k . '.php')) {
			$incompatible_plugins_detected[] = $name;
		}
	}

	$incompatible_plugins_detected = implode(', ', $incompatible_plugins_detected);
	if ($incompatible_plugins_detected) {
		$message = sprintf(__('El plugin %s permanecerá desactivado hasta que desactives %s.'), $plugin_data['Plugin Name'], $incompatible_plugins_detected);
	} else {
		$message = sprintf(__('El plugin %s permanecerá desactivado debido a que genera un conflito.'), $plugin_data['Plugin Name']);
	}
	?>
	<div class="error">
		<p><?php echo $message; ?></p>
	</div>
	<?php
}

if (defined('MAPIFY_PLUGIN_NAME')) {
	add_action('admin_notices', 'mpfy_mp_plugin_conflict');
	return; // prevent plugin from loading
}

if ( ! defined( 'MAPIFY_PLUGIN_NAME' ) ) {
	define( 'MAPIFY_PLUGIN_NAME', $plugin_data['Plugin Name'] );
}

if ( ! defined( 'MAPIFY_PLUGIN_VERSION' ) ) {
	define( 'MAPIFY_PLUGIN_VERSION', $plugin_data['Version'] );
}

if ( ! defined( 'MAPIFY_PLUGIN_FILE' ) ) {
	define( 'MAPIFY_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'MAPIFY_PLUGIN_DIR' ) ) {
	define( 'MAPIFY_PLUGIN_DIR', dirname(__FILE__) );
}

if ( ! defined('MPFY_IS_AJAX') ) {
	$is_ajax = (bool) ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' );
	define('MPFY_IS_AJAX', $is_ajax);
}

include_once('am-integration.php');
/* /Mapify Plugin-specific */


/* Mapify General */
include_once('lib/utils.php');
include_once('lib/video-functions.php');

include_once('core.php');
include_once('modules/plugin.php');
