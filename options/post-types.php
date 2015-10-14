<?php  
register_post_type('map-location', array(
	'labels' => array(
		'name'	 => 'Localizaciones',
		'singular_name' => 'Localización',
		'add_new' => __( 'Añadir nueva' ),
		'add_new_item' => __( 'Añadir una nueva localización' ),
		'view_item' => 'Ver localización',
		'edit_item' => 'Editar localización',
	    'new_item' => __('Nueva localización'),
	    'view_item' => __('Ver localización'),
	    'search_items' => __('Buscar localizaciones'),
	    'not_found' =>  __('No se han encontrado localizaciones'),
	    'not_found_in_trash' => __('No se han encontrado localizaciones en la papelera'),
	),
	'public' => true,
	'exclude_from_search' => true,
	'show_ui' => true,
	'capability_type' => 'post',
	'hierarchical' => false,
	'rewrite' => array(
		"slug" => "map-locations",
		"with_front" => false,
	),
	'query_var' => true,
	'has_archive' => 'map-locations',
	'supports' => array('title', 'editor'),
	'show_in_menu' => 'mapify.php',
));

register_post_type('map', array(
	'labels' => array(
		'name'	 => 'Mapas',
		'singular_name' => 'Mapa',
		'add_new' => __( 'Añadir nuevo' ),
		'add_new_item' => __( 'Añadir nuevo mapa' ),
		'view_item' => 'Ver mapa',
		'edit_item' => 'Editar mapa',
	    'new_item' => __('Nuevo mapa'),
	    'view_item' => __('Ver mapa'),
	    'search_items' => __('Buscar mapas'),
	    'not_found' =>  __('No se han encontrado mapas'),
	    'not_found_in_trash' => __('No se han encontrado mapas en la papelera'),

	    // 'menu_name' => MAPIFY_PLUGIN_NAME,
	),
	'public' => false,
	'exclude_from_search' => true,
	'show_ui' => apply_filters('mpfy_show_map_ui', false),
	'capability_type' => 'post',
	'hierarchical' => false,
	'rewrite' => false,
	'query_var' => true,
	'supports' => array('title'),
	'show_in_menu' => 'mapify.php',
));

do_action('mpfy_post_types_registered');
