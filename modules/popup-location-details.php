<?php
// Add directions button to popup
function mpfy_pld_display_button($location_id, $map_id) {
	$map = new Mpfy_Map($map_id);
	$map_location = new Mpfy_Map_Location($location_id);
	
	$map_mode = $map->get_mode();

	$popup_directions = mpfy_meta_to_bool($location_id, '_map_location_popup_directions', true);
	$popup_location_information = mpfy_meta_to_bool(get_the_ID(), '_map_location_popup_location_information', true);
	
	// directions
	$directions_url = $map_location->get_directions_url();

	// location information
	$address_lines = array(
		array(
			$map_location->get_address(),
		),
		array(
			$map_location->get_address_line_2(),
		),
		array(
			$map_location->get_city(),
			// Guardado para futuras implementaciones
			// ==============================================================================================================
			//$map_location->get_state(),
			//$map_location->get_zip(),
			//$map_location->get_country(),
		),
		array(
			get_post_meta($map_location->get_id(), '_map_location_phone', true),
		),
	);

	$address_lines_formatted = '';
	foreach ($address_lines as $line) {
		$contents = implode(' ', array_filter(array_values($line)));
		if (!$contents) { continue; }
		$address_lines_formatted .= $contents . '<br />';
	}

	$links = carbon_get_post_meta(get_the_ID(), 'map_location_links', 'complex');

	$tags = wp_get_object_terms(get_the_ID(), 'location-tag');
	?>
	
	<?php if ($popup_location_information) : ?>
		<aside class="mpfy-p-widget mpfy-p-widget-location">
			<div class="mpfy-p-holder">
				<!-- <h5 class="mpfy-p-widget-title">Información de contacto</h5> -->
				<?php if ($address_lines_formatted) : ?>
					<div class="mpfy-p-entry">
						<p>
							<strong><?php echo $address_lines_formatted; ?></strong> 
						</p>
					</div>
				<?php endif; ?>
				<?php if ($links) : ?>
					<div class="mpfy-p-links">
						<ul>
							<?php foreach ($links as $o) : ?>
								<li>
									<a href="<?php echo esc_attr($o['url']); ?>" target="<?php echo $o['target']; ?>" class=" mpfy-p-color-accent-color"><?php echo esc_attr($o['text']); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
			<?php if ($tags) : ?>
				<div class="mpfy-p-tags">
					<?php foreach ($tags as $t) : ?>
						<a href="#" data-mpfy-action="set_map_tag" data-mpfy-value="<?php echo $t->term_id; ?>"><?php echo esc_attr($t->name); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="cl">&nbsp;</div>
		</aside>
	<?php endif; ?>
	<?php if ($popup_directions && $map_mode === 'google_maps') : ?>
		<aside class="mpfy-p-widget mpfy-p-widget-direction <?php echo ($popup_location_information) ? 'mpfy-p-widget-direction-with-location' : 'mpfy-p-widget-direction-without-location'; ?>">
			<a href="<?php echo $directions_url; ?>" target="_blank" class=""><i class="fa fa-map-marker"></i> Ver en Google Maps</a>
		</aside>
		<hr>
	<?php endif; ?>
	<?php
}
add_action('mpfy_popup_location_information', 'mpfy_pld_display_button', 10, 2);
