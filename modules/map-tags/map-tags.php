<?php
// Register the map tag taxonomy
function mpfy_mt_register_taxonomy() {
	$tag_post_types = mpfy_get_supported_post_types();
	$tag_post_types[] = 'map';
	register_taxonomy('location-tag', $tag_post_types, array(
		'hierarchical'            => false,
		'labels'                  => array(
			'name'                         => _x( 'Filtros del mapa', 'taxonomy general name' ),
			'singular_name'                => _x( 'Filtro', 'taxonomy singular name' ),
			'search_items'                 => __( 'Buscar filtros' ),
			'popular_items'                => __( 'Filtros populares' ),
			'all_items'                    => __( 'Todos los filtros' ),
			'parent_item'                  => null,
			'parent_item_colon'            => null,
			'edit_item'                    => __( 'Editar filtro' ), 
			'update_item'                  => __( 'Actualizar filtro' ),
			'add_new_item'                 => __( 'Nuevo filtro' ),
			'new_item_name'                => __( 'Nombre del nuevo filtro' ),
			'separate_items_with_commas'   => __( 'Separa los filtros con comas' ),
			'add_or_remove_items'          => __( 'Añadir o quitar filtros' ),
			'choose_from_most_used'        => __( 'Selecciona entre los filtros más usados' ),
			'not_found'                    => __( 'No se han encontrado filtros.' ),
			'menu_name'                    => __( 'Filtros' )
		),
		'show_ui'                 => true,
		'show_admin_column'       => true,
		'update_count_callback'   => '_update_post_term_count',
		'query_var'               => true,
		'rewrite'                 => array( 'slug' => 'Filtro' )
	));
}
add_action('mpfy_post_types_registered', 'mpfy_mt_register_taxonomy');

// Provide an ajax service which reports map tags
function mpfy_ajax_mpfy_get_map_tags() {
	$pids = array_filter(array_map('intval', explode(',', $_GET['mids'])));

	$response = array();
	foreach ($pids as $pid) {
		$r = array(
			'map'=>array(
				'name'=>get_the_title($pid),
			),
			'tags'=>wp_get_object_terms($pid, 'location-tag'),
		);
		$response[$pid] = $r;
	}
	
	echo json_encode($response);
	exit;
}
add_action('wp_ajax_mpfy_get_map_tags', 'mpfy_ajax_mpfy_get_map_tags');

// Load custom carbon field
function mpfy_mt_attach_carbon_custom_classes() {
	include_once('carbon.php');
}
add_action('carbon_register_fields', 'mpfy_mt_attach_carbon_custom_classes', 11);