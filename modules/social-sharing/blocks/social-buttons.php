<?php
$services = array(
	'facebook'=>'st_facebook',
	'pinterest'=>'st_pinterest',
	'twitter'=>'st_twitter',
	'googleplus'=>'st_googleplus',
	'email'=>'st_email',
);
$share_url = add_query_arg('mpfy-pin', $post_id, $_SERVER['HTTP_REFERER']);

$enabled_services = get_post_meta($post_id, '_map_location_share', true);
$enabled_services = $enabled_services ? $enabled_services : array();
?>
<div class="mpfy-p-social-small">
	<?php do_action('mpfy_share_links_before', $post_id); ?>

	<?php foreach ($services as $s => $st) : ?>
		<?php if (!in_array($s, $enabled_services)) { continue; } ?>
		<span class="mpfy-social-btn">
			<span class="<?php echo $st; ?>" st_url="<?php echo esc_attr($share_url); ?>"></span>
		</span>
	<?php endforeach; ?>

	<?php do_action('mpfy_share_links_after', $post_id); ?>
</div>