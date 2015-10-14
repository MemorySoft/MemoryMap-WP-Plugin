<?php
// Dequeue google maps from carbon in order to load geometry library
function mpfy_admin_dequeue_maps() {
	wp_dequeue_script('carbon-google-maps');
}
add_action('admin_init', 'mpfy_admin_dequeue_maps', 11);

function mpfy_plugin_init() {
	// Load the mapify post types
	include_once(MAPIFY_PLUGIN_DIR . '/options/post-types.php');

	// Flush rewrite rules after an update to make sure that the custom post types are included in rewrite rules
	if (get_option('mpfy_flush_required') === 'y') {
		update_option('mpfy_flush_required', 'n');

		if (function_exists('flush_rewrite_rules')) {
			flush_rewrite_rules();
		} else {
			add_action('wp', 'flush_rewrite_rules');
		}
	}

	// enqueue generic dependencies
	wp_enqueue_script('jquery');
}
add_action('init', 'mpfy_plugin_init', 100);

// Enqueue front-end assets
function mpfy_enqueue_assets() {
	if (!defined('MPFY_LOAD_ASSETS') || is_admin()) {
		return false;
	}

	wp_enqueue_style('mpfy-fonts', plugins_url('assets/fonts.css', MAPIFY_PLUGIN_FILE));
	wp_enqueue_style('mpfy-map', plugins_url('assets/map.css', MAPIFY_PLUGIN_FILE), array(), '2.0.5.1');
	wp_enqueue_style('mpfy-popup', plugins_url('assets/popup.css', MAPIFY_PLUGIN_FILE), array(), '2.0.5.1');

	wp_enqueue_script('gmaps', 'http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry', array(), false, true);
	
	wp_enqueue_script('carouFredSel', plugins_url('assets/js/jquery.carouFredSel-6.2.1-packed.js', MAPIFY_PLUGIN_FILE), array(), false, true);
	wp_enqueue_script('mousewheel', plugins_url('assets/js/jquery.mousewheel.js', MAPIFY_PLUGIN_FILE), array(), false, true);
	wp_enqueue_script('jscrollpane', plugins_url('assets/js/jquery.jscrollpane.min.js', MAPIFY_PLUGIN_FILE), array(), false, true);
	wp_enqueue_script('touchSwipe', plugins_url('assets/js/jquery.touchSwipe.min.js', MAPIFY_PLUGIN_FILE), array(), false, true);
	wp_enqueue_script('fullscreener', plugins_url('assets/js/jquery.fullscreener.js', MAPIFY_PLUGIN_FILE), array(), false, true);

	wp_enqueue_script('mpfy-map', plugins_url('assets/js/map-instance.js', MAPIFY_PLUGIN_FILE), array(), '2.0.5.1', true);
	wp_enqueue_script('mpfy-tooltip', plugins_url('assets/js/tooltip.js', MAPIFY_PLUGIN_FILE), array(), false, true);

	// load popup html
	include_once(MAPIFY_PLUGIN_DIR . '/templates/popup.php');

	// Add WP ajax url to the global JS scope for easy access
	?>
	<script type="text/javascript">
	window.wp_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
	</script>
	<?php
}
add_action('wp_footer', 'mpfy_enqueue_assets');

// Enqueue admin assets
function mpfy_plugin_admin_assets() {
	wp_enqueue_style('mpfy-admin-css', plugins_url('assets/admin.css' , MAPIFY_PLUGIN_FILE));

	wp_enqueue_script('gmaps', 'http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry');
	wp_register_script('mpfy-admin', plugins_url('assets/js/admin.js', MAPIFY_PLUGIN_FILE));
	wp_localize_script('mpfy-admin', 'script_settings', array(
		'carbon_container'=>str_replace(' ', '', MAPIFY_PLUGIN_NAME) . 'Options',
	));
	wp_enqueue_script('mpfy-admin');
}
add_action('admin_menu', 'mpfy_plugin_admin_assets');

// Add general Mapify shortcode
function mpfy_shortcode_custom_mapping($atts, $content) {
	static $mpfy_instances = -1;
	$mpfy_instances ++;

	if (!defined('MPFY_LOAD_ASSETS')) {
		define('MPFY_LOAD_ASSETS', true);
	}

	extract( shortcode_atts( array(
		'width'=>500,
		'height'=>300,
		'map_id'=>0,
	), $atts));

	$width = intval($width);
	$width = ($width < 1) ? 500 : $width;
	$height = intval($height);
	$height = ($height < 1) ? 300 : $height;

	if ($map_id == 0) {
		$map_id = Mpfy_Map::get_first_map_id();
	}

	$map = get_post(intval($map_id));
	if (!$map || is_wp_error($map) || $map->post_type != 'map') {
		return 'Es invalido o no has especifidcado un ID.';
	}

	$map = new Mpfy_Map($map->ID);

	ob_start();
	include('templates/map.php');
	$cnt = ob_get_clean();
	$cnt = preg_replace('~>\s*<~s', '><', $cnt);
	$cnt = preg_replace('~\s*<br \/>\s*~i', '<br />', $cnt);
	return $cnt;
}
add_shortcode('custom-mapping', 'mpfy_shortcode_custom_mapping');

// Add compatibility with previous version of the plugin
if ( !function_exists('cm_shortcode_custom_mapping') ) {
	function cm_shortcode_custom_mapping() {
		return call_user_func_array('mpfy_shortcode_custom_mapping', func_get_args() );
	}
}

// Apply proper template to map post type so that WP does not take a generic template from the theme
function mpfy_filter_single_template($template) {
	global $post;
	
	if ($post->post_type == 'map-location') {
		return MAPIFY_PLUGIN_DIR . '/templates/single-map-location.php';
	}

	if (MPFY_IS_AJAX && in_array($post->post_type, mpfy_get_supported_post_types())) {
		$map_location = new Mpfy_Map_Location($post->ID);
		if ($map_location->get_maps()) {
			return MAPIFY_PLUGIN_DIR . '/templates/single-map-location.php';
		}
	}

	return $template;
}
add_filter('single_template', 'mpfy_filter_single_template', 1000);
