<?php
$map_choices = array(0=>'None') + Mpfy_Map::get_all_maps();


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// 	CAMPOS PERSONALIZADOS DE LAS LOACALIZACIONES
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$map_location_custom_fields = array(
	'position_start'=>'',

	'map_location_separator_1'=>Carbon_Field::factory('separator', 'map_location_separator_1', 'Dirección y contacto'),

	// LOCALIZACIÓN
	'map_location_google_location'=>Carbon_Field::factory('map_with_address', 'map_location_google_location', 'Localización'),

	// DIRECCIÓN Y CONTACTO
	'map_location_address'=>Carbon_Field::factory('text', 'map_location_address', 'Dirección'),
	'map_location_address_2'=>Carbon_Field::factory('text', 'map_location_address_2', 'Zona/Barrio'),
	'map_location_city'=>Carbon_Field::factory('text', 'map_location_city', 'Ciudad'),
	'map_location_phone'=>Carbon_Field::factory('text', 'map_location_phone', 'Teléfono'),
	// Guardado para futuras implementaciones
	// ==============================================================================================================
	// 'map_location_state'=>Carbon_Field::factory('text', 'map_location_state', 'Provincia'),
	// Revisar /modules/image-mode/image-mode.php cuando descomentemos esto...
	// 'map_location_zip'=>Carbon_Field::factory('text', 'map_location_zip', 'CP'),
	// 'map_location_country'=>Carbon_Field::factory('text', 'map_location_country', 'País'),
	'map_location_links'=>Carbon_Field::factory('complex', 'map_location_links', 'Enlaces')
			->add_fields(array(
				Carbon_Field::factory('text', 'url', 'URL'),
				Carbon_Field::factory('text', 'text', 'Text'),
				Carbon_Field::factory('select', 'target', 'Abrir en una nueva ventana o pestaña')
					->add_options(array(
						'_top'=>'No',
						'_blank'=>'Sí',
					)),
			)),
	'map_location_popup_location_information'=>Carbon_Field::factory('select', 'map_location_popup_location_information', '¿Incluir la información de contacto de la localización?')
		->add_options(array( 'yes' => 'Sí', 'no' => 'No' ))
		->help_text('Selecciona si quieres mostrar o no la información de contacto de la localización.'),

	// MAPA
	'map_location_separator_2'=>Carbon_Field::factory('separator', 'map_location_separator_2', 'Asignar mapa'),
	'map_location_map'=>Carbon_Field::factory('select', 'map_location_map', 'Mapas')
		->add_options($map_choices)
		->help_text('Need more than one map? <a href="http://www.mapifypro.com/" target="_blank">Upgrade to MapifyPro!</a>'),
	'map_location_tags'=>Carbon_Field::factory('map_tags', 'map_location_tags', 'Filtros del mapa'),

	// POPUP
	'map_location_separator_4'=>Carbon_Field::factory('separator', 'map_location_separator_4', 'Visualización'),
	'map_location_tooltip_enabled'=>Carbon_Field::factory('select', 'map_location_tooltip_enabled', '¿Usar Popup?') // mislabeled - this refers to the popup, not tooltip
		->add_options(array( 'yes' => 'Sí', 'no' => 'No' )),
	'map_location_popup_directions'=>Carbon_Field::factory('select', 'map_location_popup_directions', 'Incluir el botón "Ver en Google Maps"')
		->add_options(array( 'yes' => 'Sí', 'no' => 'No' ))
		->help_text('Selecciona si quieres mostrar o no el botón de Google Maps en el popup.'),

	// TOOLTIP
	'map_location_tooltip_show'=>Carbon_Field::factory('select', 'map_location_tooltip_show', '¿Usar Tooltip?')
		->add_options(array( 'yes' => 'Sí', 'no' => 'No' )),
	'map_location_tooltip_close'=>Carbon_Field::factory('select', 'map_location_tooltip_close', 'Cierre del Tooltip')
		->add_options(array( 'manual' => 'Manual', 'auto' => 'Automático' ))
		->help_text('<strong>Automático:</strong> El tooltip se cerrará automáticamente cuando el usuario mueva el ratón.<br /><strong>Manual:</strong> Elige este si el tooltip contiene botones o enlaces.'),
	'map_location_tooltip'=>Carbon_Field::factory('textarea', 'map_location_tooltip', 'Tooltip')
		->help_text('Este es el texto que aparece en el tootltip encima del botón que dispara el popup con los detalles de la localización.'),

	// LISTA INTERACTIVA 
	// Guardado para futuras implementaciones
	// ==============================================================================================================
	// 'map_location_mll_include'=>Carbon_Field::factory('select', 'map_location_mll_include', '¿Incluir en la lista de localizaciones del mapa seleccionado?')
	// 	->add_options(array('y'=>'Sí', 'n'=>'No')),
	// 'map_location_mll_description'=>Carbon_Field::factory('textarea', 'map_location_mll_description', 'Descripción corta')
	// 	->help_text('La descripción corta de la localización aparecerá en la Lista Interactiva. Esta descripción es independiente del Tooltip y la descripción del Popup.'),

	// PIN
	'map_location_pin'=>Carbon_Field::factory('image_pin', 'map_location_pin', 'Imagen del puntero')
		->attach_to_map('map_location_google_location')
		->help_text('Puedes agregarle un puntero personalizado a esta localización, si lo dejas en blanco se usará el puntero por defecto.'),
	'position_after_pin'=>'',

	// MULTIMEDIA
	'map_location_separator_5'=>Carbon_Field::factory('separator', 'map_location_separator_5', 'Multimedia'),	
	'map_location_gallery_images'=>Carbon_Field::factory('image_list', 'map_location_gallery_images', 'Galeria de imágenes')
		->setup_labels(array(
			'singular_name'=>'Imagen',
			'plural_name'=>'Imagenes',
		))
		->add_fields(array(
			Carbon_Field::factory('image', 'image', 'Imagen'),
		)),
	'map_location_video_embed'=>Carbon_Field::factory('textarea', 'map_location_video_embed', 'Incluir video')
		->help_text('Inserta el código de Vimeo o Youtube para incluir un video en la galeria, si lo dejas en blanco solo mostrará las imágenes que selecciones más abajo.'),

	'position_end'=>'',
);

$map_location_custom_fields = apply_filters('mpfy_map_location_custom_fields', $map_location_custom_fields);
$map_location_custom_fields = array_filter($map_location_custom_fields); // clear positions

Carbon_Container::factory('custom_fields', /*MAPIFY_PLUGIN_NAME .*/ ' Opciones de la localización')
	->show_on_post_type(mpfy_get_supported_post_types())
	->add_fields(array_values($map_location_custom_fields));

$raw = get_posts('post_type=map-location&posts_per_page=-1&orderby=title&order=asc');
$locations = array('0'=>'Selecciona una');
foreach ($raw as $r) {
	$ml = new Mpfy_Map_Location($r->ID);
	$coords = $ml->get_coordinates();

	if ($coords) {
		$locations[$ml->get_id()] = $ml->get_title();
	} else {
		// Dynamically update coordinates meta field
		$lat = carbon_get_post_meta($r->ID, 'map_location_google_location_lat');
		$lng = carbon_get_post_meta($r->ID, 'map_location_google_location_lng');

		$coords = array( $lat, $lng );
		$coords = array_filter( $coords );

		if ( ! empty( $coords ) ) {
			$coords = implode( ',', $coords );
			update_post_meta( $r->ID, '_map_location_google_location', $coords );

			$locations[$r->ID] = $r->post_title;
		}
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
// 	CAMPOS PERSONALIZADOS DE LOS MAPAS
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$map_custom_fields = array(
	'position_start'=>'',

	'map_id'=>Carbon_Field::factory('text', 'map_id', 'ID del mapa')
		->help_text( (isset($_GET['post']) ? $_GET['post'] : '') ),

	// AJUSTES DEL MAPA
	'map_enable_search'=>Carbon_Field::factory('select', 'map_enable_search', 'Activar búsqueda')
		->add_options(array( 'no' => 'No', 'yes' => 'Sí' )),
	'map_pin'=>Carbon_Field::factory('image_pin', 'map_pin', 'Puntero por defecto')
		->attach_to_map('map_google_center')
		->help_text('Esta será la imagen por defecto de los punteros de este mapa. Se recomiendan archivos PNG con transparencia.'),
	'map_enable_filters'=>Carbon_Field::factory('select', 'map_enable_filters', 'Activar lista desplegable de filtros')
		->add_options(array( 'no' => 'No', 'yes' => 'Sí' )),
	'map_enable_filters_list'=>Carbon_Field::factory('select', 'map_enable_filters_list', 'Activar lista de filtros')
		->add_options(array( 'yes' => 'Sí', 'no' => 'No')),
	// Guardado para futuras implementaciones
	// ==============================================================================================================
	// 'map_mll_include'=>Carbon_Field::factory('select', 'map_mll_include', 'Activar la Lista Interactiva de localizaciones')
	// 		->add_options(array('n'=>'No', 'y'=>'Sí'))
	// 		->help_text('Añade las localizaciones en una lista interactiva debajo del mapa.'),

	// AJUSTES EXTRA
	// Guardado para futuras implementaciones
	// ==============================================================================================================
	// 'map_enable_zoom'=>Carbon_Field::factory('select', 'map_enable_zoom', 'Activar zoom')
	// 	->add_options(array( 'no' => 'No', 'yes' => 'Sí' )),
	// 'map_google_ui_enabled'=>Carbon_Field::factory('select', 'map_google_ui_enabled', 'Activar controles de Google Maps')
	// 	->add_options(array( 'no' => 'No', 'yes' => 'Sí' )),
	// 'map_animate_tooltips'=>Carbon_Field::factory('select', 'map_animate_tooltips', '¿Tooltips animados?')
	// 	->add_options(array( 'yes' => 'Sí', 'no' => 'No' ))
	// 	->help_text('Añade una sutil animación a los tootltips.'),
	// 'map_animate_pinpoints'=>Carbon_Field::factory('select', 'map_animate_pinpoints', '¿Punteros animados?')
	// 	->add_options(array( 'yes' => 'Sí', 'no' => 'No' ))
	// 	->help_text('Añade una sutil animación a los`punteros.'),
	// 'map_search_center'=>Carbon_Field::factory('select', 'map_search_center', 'Centrar mapa en los resultados de la búsqueda')
	// 	->add_options(array( 'no' => 'No', 'yes' => 'Sí' )),
	// 'map_filters_center'=>Carbon_Field::factory('select', 'map_filters_center', 'Centrar mapa en los resultados de los filtros')
	// 	->add_options(array( 'no' => 'No', 'yes' => 'Sí' )),
	// 'position_after_ui'=>'',

	// GOOGLE MAP
	'map_separator_3'=>Carbon_Field::factory('separator', 'map_separator_3', 'Ajustes de Google Maps'),
	'map_google_mode'=>Carbon_Field::factory('select', 'map_google_mode', 'Modo del mapa')
		->add_options(array( 'ROADMAP' => 'Carretera', 'SATELLITE' => 'Satelite', 'HYBRID' => 'Híbrido', 'TERRAIN' => 'Terreno' )),
	'map_google_style'=>Carbon_Field::factory('select', 'map_google_style', 'Estilo del mapa')
		->add_options(mpfy_sm_get_snazzymaps())
		->help_text('Selecciona un estilo para Google Maps, que puedes visualizar abajo.<br />Este ajuste sobreescribirá "Modo del mapa".'),
	'map_search_radius'=>Carbon_Field::factory('text', 'map_search_radius', 'Radio de búsqueda')
		->set_default_value(5)
		->help_text('En kilometros'),
	'map_main_location'=>Carbon_Field::factory('select_location', 'map_main_location', 'Localización principal')
		->add_options($locations)
		->help_text('El mapa se centrará en esa localización'),
	'map_google_center'=>Carbon_Field::factory('map_mpfy', 'map_google_center', 'Zoom inicial y estilo')
		->help_text('Esta será en nivel inicial de zoom'),

	// COLORES
	// Guardado para futuras implementaciones
	// ==============================================================================================================
	// 'map_separator_colors'=>Carbon_Field::factory('separator', 'map_separator_colors', 'Colores'),
	// 'map_background_color'=>Carbon_Field::factory('color', 'map_background_color', 'Fondo del mapa')
	// 	->set_default_value('#ffffff'),
	// 'map_tooltip_background_color'=>Carbon_Field::factory('color', 'map_tooltip_background_color', 'Fondo del Tooltip')
	// 	->set_default_value('#ffffff'),
	// 'map_popup_background_color'=>Carbon_Field::factory('color', 'map_popup_background_color', 'Fondo del Popup')
	// 	->set_default_value('#ffffff'),
	// 'map_popup_header_background_color'=>Carbon_Field::factory('color', 'map_popup_header_background_color', 'Cabecera del Popup')
	// 	->set_default_value('#FED130'),
	// 'map_popup_date_background_color'=>Carbon_Field::factory('color', 'map_popup_date_background_color', 'Fecha del Popup')
	// 	->set_default_value('#333333'),
	// 'map_popup_accent_color'=>Carbon_Field::factory('color', 'map_popup_accent_color', 'Acento del Popup')
	// 	->set_default_value('#999999'),

	'position_end'=>'',
);

$map_modes = Mpfy_Map::get_map_modes();
if (count($map_modes) > 1) {
	$map_custom_fields = mpfy_array_push_key($map_custom_fields, 'position_after_map_id', array(
		'map_mode'=>Carbon_Field::factory('map_mode', 'map_mode', 'Modo')
			->add_options($map_modes),
	));
}

$map_custom_fields = apply_filters('mpfy_map_custom_fields', $map_custom_fields);
$map_custom_fields = array_filter($map_custom_fields); // clear positions

Carbon_Container::factory('custom_fields', 'Opciones del mapa')
	->show_on_post_type('map')
	->add_fields(array_values($map_custom_fields));

Carbon_Container::factory('term_meta', 'Category Options')
	->show_on_taxonomy('location-tag')
	->add_fields(array(
		Carbon_Field::factory('attachment', 'mpfy_location_tag_image', 'Image')
	));

$plugin_settings = array();

$plugin_settings = apply_filters('mpfy_plugin_settings', $plugin_settings);

// SOCIAL SHARE -> Entrada en menu admin
// Guardado para futuras implementaciones
// ==============================================================================================================
// if ($plugin_settings) {
// 	Carbon_Container::factory('theme_options', MAPIFY_PLUGIN_NAME . ' Settings')
// 		->set_page_parent('mapify.php')
// 		->add_fields($plugin_settings);
// }

