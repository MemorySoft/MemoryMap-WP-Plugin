<?php
function mpfy_spp_add_post($post_types) {
	$post_types[] = 'post';
	return $post_types;
}
add_filter('mpfy_supported_post_types', 'mpfy_spp_add_post');
