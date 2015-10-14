<?php
class Carbon_Field_Map_Tags extends Carbon_Field {
	static $attached_scripts = false;

	function admin_init() {
		if ( !self::$attached_scripts ) {
			self::$attached_scripts = true;

			add_action('admin_footer', array($this, 'admin_enqueue_scripts'));
		}
	}

	function admin_enqueue_scripts() {
		?>
		<script type="text/javascript">
		(function($){
			var slot = $('.tags-slot');

			var current_tags = '';
			function mpfy_check_map_tags() {
				var new_tags = [];
				$('select[name="_map_location_map"], input[name="_map_location_map[]"]').each(function() {
					new_tags.push($(this).val());
				});
				new_tags = new_tags.join(',');

				if (new_tags == current_tags) {
					return false;
				}

				current_tags = new_tags;
				if (current_tags.length == 0) {
					slot.html('Selecciona un mapa de la lista.');
				} else {
					slot.html('<em>Espera...</em>');

					$.get("<?php echo admin_url('admin-ajax.php'); ?>", {
						'action': 'mpfy_get_map_tags',
						'mids': current_tags
					}, function(response) {
						var html = '';

						for (var mid in response) {
							var m = response[mid];
							html += '<em>' + m.map.name + ':</em><br />';
							if (m.tags.length) {
								for (var i = 0; i < m.tags.length; i++) {
									var t = m.tags[i];
									html += '<a href="#" class="map-tag-link" data-val="' + t.name + '">' + t.name + '</a>' + '&nbsp;';
								}
								html += '<br />';
							} else {
								html += 'El mapa seleccionado no tiene filtros.' + '<br />';
							}
							html += '<br />';
						}

						$('.tags-slot').html(html + '<p class="ecf-description" style="margin-top: 0px;"><em>Estos son las filtros asociados con el mapa seleccionado. Asígnalos a la localización actual clicando sobre ellos o crea nuevos en "Filtros del mapa"</em></p>');
					}, 'json');
				}
			}

			if (slot.length > 0) {
				setInterval(mpfy_check_map_tags, 2000);
				mpfy_check_map_tags();

				$(document).on('click', '.map-tag-link', function() {
					$('input[name="newtag[location-tag]"]').val($(this).attr('data-val')).next().click();
					return false;
				});
			}
		})(jQuery);
		</script>
		<?php
	}

	function render() {
		echo '<div class="tags-slot"></div>';
	}

	function load() {
		// skip;
	}

	function save() {
		// skip;
	}

	function delete() {
		// skip;
	}
}