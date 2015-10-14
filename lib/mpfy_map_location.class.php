<?php
class Mpfy_Map_Location {
	private $post;
	
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

	function get_content() {
		return $this->post->post_content;
	}

	function get_maps() {
		$maps = get_post_meta($this->get_id(), '_map_location_map', true);
		$maps = array_filter(array_map('intval', explode(',', $maps)));
		return $maps;
	}

	function get_coordinates() {
		$coordinates = explode(',', get_post_meta($this->get_id(), '_map_location_google_location', true));
		return $coordinates;
	}

	function get_directions_url() {
		$coordinates = $this->get_coordinates();
		$directions_url = 'http://www.google.com/maps/?saddr=&daddr=' . $coordinates[0] . ',' . $coordinates[1];
		return $directions_url;
	}

	function get_popup_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_location_tooltip_enabled', true);
		return $value;
	}

	function get_tooltip_enabled() {
		$value = mpfy_meta_to_bool($this->get_id(), '_map_location_tooltip_show', true);
		return $value;
	}

	function get_tooltip_close_behavior() {
		$value = get_post_meta($this->get_id(), '_map_location_tooltip_close', true);
		$value = ($value) ? $value : 'manual';
		return $value;
	}

	function get_tooltip_text() {
		$value = get_post_meta($this->get_id(), '_map_location_tooltip', true);
		return $value;
	}

	function get_tooltip_content() {
		ob_start();
		
		do_action('mpfy_tooltip_content_before', $this->get_id());

		$addr = $this->get_city() . ' ' . $this->get_zip();
		$directions_url = $this->get_directions_url();
		$text = $this->get_tooltip_text();
		//$text = str_replace('[directions]', '<a href="' . $directions_url . '" target="_blank" class="mpfy-directions-button">Ver en Google Maps</a>', $text);
		$botonPopup = '<a href="#" class="mpfy-directions-button" data-mpfy-action="open_popup" data-mpfy-value="' . $this->get_id() . '">Ver detalles</a>';

		echo wpautop('<h4><strong>' . $this->get_title() . '</strong></h4>' . $text . '<br />' . $botonPopup);

		do_action('mpfy_tooltip_content_after', $this->get_id());

		$html = ob_get_clean();

		return $html;
	}

	function get_tags() {
		$value = wp_get_object_terms($this->get_id(), 'location-tag');
		return $value;
	}

	function get_pin_image($map_id = 0) {
		$result = array(
			'url'=>'',
			'size'=>false,
			'anchor'=>array(0, 0),
		);

		if (!$map_id) {
			$map_id = $this->get_maps();
			$map_id = isset($map_id[0]) ? $map_id[0] : 0;
		}
		$map = new Mpfy_Map($map_id);

		$pin_image = get_post_meta($this->get_id(), '_map_location_pin', true);
		if (!$pin_image) {
			$pin_image = $map->get_default_pin_image();
		}

		if ($pin_image) {
			$result['url'] = mpfy_get_file_real_url($pin_image);

			$result['size'] = @getimagesize(mpfy_get_file_real_path($pin_image));
			if ($result['size']) {
				$result['anchor'] = array(
					round($result['size'][0] / 2),
					$result['size'][1],
				);
			}
		}

		return $result;
	}

	function get_video_embed() {
		$value = get_post_meta($this->get_id(), '_map_location_video_embed', true);
		if (preg_match('/^http/', $value)) {
			// dirty solution
			$value = mpfy_create_embedcode($value);
		}
		return $value;
	}

	function get_video_thumb() {
		$value = mpfy_get_video_thumb($this->get_video_embed());
		return $value;
	}

	function get_gallery_images() {
		$value = carbon_get_post_meta($this->get_id(), 'map_location_gallery_images', 'complex');
		return $value;
	}

	function get_address() {
		$value = get_post_meta($this->get_id(), '_map_location_address', true);
		return $value;
	}

	function get_address_line_2() {
		$value = get_post_meta($this->get_id(), '_map_location_address_2', true);
		return $value;
	}

	function get_city() {
		$value = get_post_meta($this->get_id(), '_map_location_city', true);
		return $value;
	}

	function get_state() {
		$value = get_post_meta($this->get_id(), '_map_location_state', true);
		return $value;
	}

	function get_zip() {
		$value = get_post_meta($this->get_id(), '_map_location_zip', true);
		return $value;
	}

	function get_country() {
		$value = get_post_meta($this->get_id(), '_map_location_country', true);
		return $value;
	}

	function get_formatted_address($format, $major_separator=' | ', $minor_separator=', ') {
		$data = $format;

		foreach ($data as $gid => $group) {
			foreach ($group as $fid => $field) {
				$m = 'get_' . $field;
				if (method_exists($this, $m)) {
					$data[$gid][$fid] = $this->{$m}();
				}
			}
			$data[$gid] = array_filter($data[$gid]);
			$data[$gid] = implode($minor_separator, $data[$gid]);
		}
		$data = array_filter($data);
		$data = implode($major_separator, $data);
		return $data;
	}
}