<?php
global $wpdb;

$errors = array();

$mode = $map->get_mode();
$zoom_level = $map->get_zoom_level();
$zoom_enabled = $map->get_zoom_enabled();
$tileset = apply_filters('mpfy_map_get_tileset', array('url'=>'', 'message'=>''), $map->get_id());
if (!$tileset['url'] && $tileset['message']) {
	$errors[] = $tileset['message'];
}

$animate_tooltips = $map->get_animate_tooltips();
$animate_pinpoints = $map->get_animate_pinpoints();

$pins = $map->get_locations(false);

foreach ($pins as $index => $p) {
	$p = get_post($p->ID);
	$map_location = new Mpfy_Map_Location($p->ID);

	$p->animate_tooltips = $animate_tooltips;
	$p->animate_pinpoints = false;//$animate_pinpoints;

	$p->google_coords = $map_location->get_coordinates();

	$p->pin_image = $map_location->get_pin_image();

	$p->pin_city = $map_location->get_city();
	$p->pin_zip = $map_location->get_zip();
	
	$p->data_tags = array();
	$tags = $map_location->get_tags();
	foreach ($tags as $t) {
		$p->data_tags[$t->term_id] = $t->term_id;
	}

	$p->popup_enabled = $map_location->get_popup_enabled();

	$p->tooltip_enabled = $map_location->get_tooltip_enabled();
	$p->tooltip_close = $map_location->get_tooltip_close_behavior();
	$p->tooltip_content = $map_location->get_tooltip_content();

	$pins[$index] = $p;
}

$map_background_color = apply_filters('mpfy_map_background_color', '', $map->get_id());
$tooltip_background = apply_filters('mpfy_map_tooltip_background_color', array(0, 0, 0, 0.71), $map->get_id());

$google_map_mode = $map->get_google_map_mode();

$google_map_style = apply_filters('mpfy_google_map_style', 'default', $map->get_id());

$map_tags = $map->get_tags();
$search_enabled = $map->get_search_enabled();
$search_radius = $map->get_search_radius();
$search_center = $map->get_search_center_behavior();
$filters_center = $map->get_filters_center_behavior();
$clustering_enabled = apply_filters('mpfy_clustering_enabled', false, $map->get_id());

$map_google_ui_enabled = $map->get_google_ui_enabled();

$filters_enabled = $map->get_filters_enabled();
$filters_list_enabled = $map->get_filters_list_enabled();

$center = implode(',', $map->get_center());

$routes = apply_filters('pretty_routes_load_routes', array(), $map->get_id());
?>
<div id="mpfy-map-<?php echo $mpfy_instances; ?>" class="mpfy-fullwrap">
	<?php if ($errors) : ?>
		<p>
			<?php foreach ($errors as $e) : ?>
				<?php echo $e; ?><br />
			<?php endforeach; ?>
		</p>
	<?php else : ?>
		
		<div class="mpfy-controls-wrap">
			<div class="mpfy-controls <?php echo ( ($filters_enabled && $map_tags) && $search_enabled) ? 'mpfy-controls-all' : ''; ?>" style="<?php echo ( (!$filters_enabled || !$map_tags) && !$search_enabled) ? 'display: none;' : ''; ?>">
				<div class="mpfy-controls-bar">
					<div class="row">
						<div class="large-8 columns">
							<?php if ($filters_list_enabled) : ?>
								<div class="mpfy-tags-list">
									<div class="cl">&nbsp;</div>
									<a href="#" class="mpfy-tl-item" data-tag-id="0">
										<span class="mpfy-tl-i-icon"></span>
										TODO
									</a>
									<?php foreach ($map_tags as $t) : ?>
										<?php
										$image = wp_get_attachment_image_src(carbon_get_term_meta($t->term_id, 'mpfy_location_tag_image'), 'mpfy_location_tag');
										?>
										<a href="#" class="mpfy-tl-item" data-tag-id="<?php echo $t->term_id; ?>">
											<?php if ($image) : ?>
												<span class="mpfy-tl-i-icon" style="background-image: url('<?php echo $image[0]; ?>');"></span>
											<?php endif; ?>
											<?php echo $t->name; ?>
										</a>
									<?php endforeach; ?>
									<div class="cl">&nbsp;</div>
								</div>
							<?php endif; ?>
						</div>

						<div class="large-4 columns">
							<form class="mpfy-search-form" method="post" action="" style="<?php echo (!$search_enabled) ? 'display: none;' : ''; ?>">
								<div class="mpfy-search-wrap">
									<input type="text" name="mpfy_search" class="mpfy_search" value="" placeholder="Nombre de la ciudad" />
									<a href="#" class="mpfy-clear-search">&nbsp;</a>
									<!-- <input type="submit" name="" value="BUSCAR" class="mpfy_search_button" /> -->
									<button type="submit" class="mpfy_search_button">BUSCAR<a/>
								</div>
							</form>
			
							<div class="mpfy-filter" style="<?php echo (!$filters_enabled || !$map_tags) ? 'display: none;' : ''; ?>">
								<label>Filtrar resultados por</label>
								<div class="select">
									<span class="select-value"></span>
									<select name="mpfy_tag" class="mpfy_tag_select">
										<option value="0">Vista inicial</option>
										<?php foreach ($map_tags as $t) : ?>
											<option value="<?php echo $t->term_id; ?>"><?php echo $t->name; ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>	

			<?php if ($zoom_enabled || $map_google_ui_enabled) : ?>
				<a href="#" class="mpfy-zoom-in"></a>
				<a href="#" class="mpfy-zoom-out"></a>
			<?php endif; ?>

            <a href="#" class="button mpfy-sugerir" data-reveal-id="modal-form">Sugiere un sitio</a>
            


		</div>

		<div class="mpfy-map-canvas mpfy-mode-<?php echo $mode ?> <?php echo ($map_tags || $search_enabled) ? 'with-controls' : ''; ?>">
			<div style="display: none;">
				<?php foreach ($pins as $p) : ?>
					<?php if (!$p->popup_enabled) { continue; } ?>
					<a href="<?php echo add_query_arg('mpfy_map', $map->get_id(), get_permalink($p->ID)); ?>" data-id="<?php echo $p->ID; ?>" class="mpfy-pin mpfy-pin-id-<?php echo $p->ID; ?>">&nbsp;</a>
				<?php endforeach; ?>
			</div>

			<div id="custom-mapping-google-map-<?php echo $mpfy_instances; ?>" style="height: <?php echo $height; ?>px; overflow: hidden;"></div>
			
			<!-- 	

			LISTA DE LOCALIZACIONES
			Guardado futuras implementaciones.
			Para activar, descomentar el campo 'Activar la Lista Interactiva de localizaciones' en /options/custom-fields.php
			=========================================================================================================================

			<?php do_action('mpfy_template_after_map', $map->get_id()); ?> 
			-->
		</div>
	<?php endif; ?>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	var map_instance = MapifyPro.Instance.create(<?php echo $mpfy_instances; ?>);
	map_instance.routes = <?php echo json_encode($routes); ?>;
	map_instance.tooltip_background = <?php echo json_encode($tooltip_background); ?>;
	
	var center = <?php echo json_encode($center); ?>;
	var zoom = {
		'zoom': <?php echo $zoom_level; ?>,
		'enabled': <?php echo ($zoom_enabled) ? 'true' : 'false'; ?>
	};
	var search_radius = <?php echo $search_radius; ?>;
	var settings = {
		'mapTypeId': <?php echo json_encode($google_map_mode); ?>,
		'map_mode': <?php echo json_encode($mode); ?>,
		'search_center': <?php echo json_encode($search_center); ?>,
		'filters_center': <?php echo json_encode($filters_center); ?>,
		'style': <?php echo json_encode($google_map_style); ?>,
		'clustering_enabled': <?php echo json_encode($clustering_enabled); ?>,
		'background': <?php echo json_encode($map_background_color); ?>,
		'ui_enabled': <?php echo json_encode($map_google_ui_enabled); ?>,
		'image_source': <?php echo json_encode($tileset['url']); ?>
	};
	var inst = new MapifyPro.Google(center, zoom, search_radius, <?php echo json_encode($pins); ?>, map_instance, settings);

	<?php if (isset($_GET['mpfy-pin'])) : ?>
		var open_pin = <?php echo $_GET['mpfy-pin']; ?>;
		var open_tooltip = <?php echo json_encode(isset($_GET['mpfy-tooltip'])); ?>;
		(function(instance) {
			
			setTimeout(function() {
				var a = jQuery(instance.container).find('a.mpfy-pin[data-id="' + open_pin + '"]');
				if (a.length) {
					if (open_tooltip) {

						var marker_found = function(m) {
							instance.uncluster(m);
							google.maps.event.addListenerOnce(instance.map, 'center_changed', function() {
								for (var i = 0; i < instance.markers.length; i++) {
									instance.markers[i].setVisible(false);
								}
								m.setVisible(true);
								google.maps.event.trigger(m, 'mouseover');

								m._mpfy.tooltip_object.node().on('tooltip_closed', function(e) {
									for (var i = 0; i < instance.markers.length; i++) {
										instance.markers[i]._mpfy.refreshVisibility();
									}
								});
							});
							if (m.getMap()) {
								m.getMap().setCenter(m.getPosition());
							}
						}

					} else {

						var marker_found = function(m) {
							if (m.getMap()) {
								m.getMap().setCenter(m.getPosition());
							}
						}
						a.trigger('click');

					}

					google.maps.event.addListenerOnce(instance.map, 'idle', function() {
						for (var i = 0; i < instance.markers.length; i++) {
							var m = instance.markers[i];
							if (m._mpfy.pin_id == open_pin) {
								marker_found(m);
								break;
							}
						}
					});
				}
			}, 1);

		})(map_instance);
	<?php endif; ?>
});
</script>