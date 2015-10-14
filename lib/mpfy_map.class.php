<?php
class Mpfy_Map {
	private $post;

	static function get_map_modes() {
		$map_modes = apply_filters('mpfy_map_modes', array(
			'google_maps'=>'Google Maps',
		));
		return $map_modes;
	}

	static function get_all_maps() {
		$map_limit = apply_filters('mpfy_map_limit', 1);
		$raw = get_posts('post_type=map&posts_per_page=' . $map_limit . '&orderby=title&order=asc&post_status=any');

		$maps = array();
		foreach ($raw as $r) {
			$maps[$r->ID] = $r->post_title;
		}
		return $maps;
	}

	static function get_first_map_id() {
		$map = get_posts('post_type=map&posts_per_page=1&post_status=any');
		if (isset($map[0])) {
			return $map[0]->ID;
		}
		return 0;
	}

	function __construct($map_id) {
		$this->post = get_post($map_id);
		if (is_null($this->post)) {
			$this->post = (object) array(
				'ID'=>0,
				'post_title'=>'N/A',
				'post_content'=>'N/A',
			);
		}
	}

	function get_id() {
		return $this->post->ID;
	}

	function get_title() {
		return $this->post->post_title;
	}

	function get_mode() {
		$value = get_post_meta($this->get_id(), '_map_mode', true);
		$value = ($value) ? $value : 'google_maps';

		$supported_modes = self::get_map_modes();
		if (!isset($supported_modes[$value])) {
			$value = 'google_maps';
		}

		return $value;
	}

	function get_center() {
		$center = get_post_meta($this->get_id(), '_map_main_location', true);
		
		if (stristr($center, ',')) {
			$center = explode(',', $center);
		} else {
			$main_location = get_post_meta($this->get_id(), '_map_main_location', true);
			$main_location = new Mpfy_Map_Location($main_location);
			$center = $main_location->get_coordinates();
			if (!array_filter($center)) {
				$center = array(-5, -5);
			}
		}

		return $center;
	}

	function get_zoom_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_enable_zoom', true);
		return $value;
	}

	function get_zoom_level() {
		$value = get_post_meta($this->get_id(), '_map_google_center-zoom', true);
		$value = is_numeric($value) ? $value : 3;
		return $value;
	}

	function get_default_pin_image() {
		$value = get_post_meta($this->get_id(), '_map_pin', true);
		return $value;
	}

	function get_animate_tooltips() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_animate_tooltips', true);
		return $value;
	}

	function get_animate_pinpoints() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_animate_pinpoints', true);
		return $value;
	}

	function get_google_map_mode() {
		$value = get_post_meta($this->get_id(), '_map_google_mode', true);
		if (!$value) {
			$value = 'ROADMAP';
		}
		return $value;
	}

	function get_tags() {
		$value = wp_get_object_terms($this->get_id(), 'location-tag', 'hide_empty=0');
		return $value;
	}

	function get_google_ui_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_google_ui_enabled', true);
		return $value;
	}

	function get_search_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_enable_search', false);
		return $value;
	}

	function get_search_radius() {
		$value = max(1, intval(get_post_meta($this->get_id(), '_map_search_radius', true)));
		return $value;
	}

	function get_search_center_behavior() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_search_center', false);
		return $value;
	}

	function get_filters_center_behavior() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_filters_center', false);
		return $value;
	}

	function get_filters_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_enable_filters', false);
		return $value;
	}

	function get_filters_list_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_enable_filters_list', false);
		return $value;
	}

	// Module: map-clustering
	function get_clustering_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_enable_clustering', false);
		return $value;
	}

	function get_locations($as_objects=true) {
		global $wpdb;

		$supported_post_types = mpfy_get_supported_post_types();

		$posts = $wpdb->get_results('
			SELECT p.`ID`
			FROM ' . $wpdb->posts . ' p
				INNER JOIN ' . $wpdb->postmeta . ' pm ON (pm.`post_id` = p.`ID`)
			WHERE p.`post_status` = "publish"
				AND p.`post_type` IN ("' . implode('", "', array_map('addslashes', $supported_post_types)) . '")
				AND pm.`meta_key` = "_map_location_map"
				AND (
					pm.`meta_value` = "' . $this->get_id() . '"
					OR
					pm.`meta_value` LIKE "' . $this->get_id() . ',%"
					OR
					pm.`meta_value` LIKE "%,' . $this->get_id() . '"
					OR
					pm.`meta_value` LIKE "%,' . $this->get_id() . ',%"
				)
			ORDER BY `menu_order` ASC, `ID` DESC
		');
		$locations = array();

		if ($as_objects) {
			foreach ($posts as $p) {
				$locations[] = new Mpfy_Map_Location($p->ID);
			}
		} else {
			$locations = $posts;
		}

		return $locations;
	}
}