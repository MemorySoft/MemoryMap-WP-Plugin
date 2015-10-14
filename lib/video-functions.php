<?php
# This file contains functions that are related with videos

#
# Filters/resizes video embed codes.
#
function mpfy_filter_video($html, $wmode = false, $width = false, $height = false) {
	$final_html = $html;
	if ($wmode && strpos('<embed') !== FALSE ) {
		$final_html = str_replace('<embed', '<param name="wmode" value="transparent"></param><embed wmode="transparent" ', $final_html);
	}
	if (is_numeric($width)) {
		$final_html = preg_replace('~width="[\d]+"~', 'width="'.$width.'"', $final_html);
	}
	if (is_numeric($height)) {
		$final_html = preg_replace('~height="[\d]+"~', 'height="'.$height.'"', $final_html);
	}
	
	return $final_html;
}

#
# Return the thumbnail src for Youtube and Vimeo videos
# $embed_code = the full video embed code ( or YouTube video url )
#
function mpfy_get_video_thumb($embed_code) {
	$return = '';
	if ( stristr($embed_code, '<iframe') ) {
		if ( stristr($embed_code, 'youtube') ) {
			preg_match('~(v/|v=|embed/)(.*?)(\?|&|\z|\'|\")~', $embed_code, $video_id);
			$return = "http://img.youtube.com/vi/".$video_id[2]."/0.jpg";
		} elseif ( stristr($embed_code, 'vimeo') ) {
			preg_match('~video/(\d+)~', $embed_code, $video_id);
			$thumb = mpfy_get_vimeo_thumb($video_id[1]);
			$return = $thumb[0]['thumbnail_large'];
		}
	} else {
		if ( stristr($embed_code, 'youtube') ) {
			preg_match('~(v/|v=)(.*?)(\?|&|\z)~', $embed_code, $video_id);
			$return = "http://img.youtube.com/vi/".$video_id[2]."/0.jpg";
		} elseif ( stristr($embed_code, 'vimeo') ) {
			preg_match('~clip_id=(.*?)&~', $embed_code, $video_id);
			$thumb = mpfy_get_vimeo_thumb($video_id[1]);
			$return = $thumb[0]['thumbnail_large'];
		}
	}
	
	return $return;
}


#
# Return the thumbnail src for Vimeo videos
# $video_id = the Vimeo video id
#
function mpfy_get_vimeo_thumb($videoid) {
	$url = "http://vimeo.com/api/v2/video/".$videoid.".php";
	$cache_id = 'vimeocache::' . md5($url);
	$cache_lifetime = 300;
	
	$cached = get_option($cache_id, -1);
	$has_cache = $cached !== -1;
	
	$is_expired = isset($cached['expires']) && time() > $cached['expires'];
	
	if (!$has_cache || $is_expired) {
		$data = wp_remote_get($url);
		$data = $data['body'];
		
		$video_cache = array(
			'data'=>$data,
			'expires'=>time() + $cache_lifetime,
		);
		update_option($cache_id, $video_cache);
	} else {
		$data = $cached['data'];
	}
	
	$finaldata = unserialize($data);
	
	return $finaldata;
}

#
# Return a URL to an embedabble YouTube Video (the actual video file URL)
# $video_url = the YouTube video URL
#
function mpfy_get_youtube_video($video_url) {
	return preg_replace('~(http:\/\/www\.youtube\.com\/watch\?v=)(.*)~', 'http://www.youtube.com/embed/$2', $video_url);
}

#
# Return an embedcode of a YouTube Video.
# $video_url = URL of the playable YouTube Video (for example: http://www.youtube.com/watch?v=emMDmRtdP7w0)
# $width = width of embedded video (optional)
# $height = height of embedded video (optional)
# $old_embed_code = whether to use the old embedcode (optional). Uses @mpfy_get_youtube_video to grab embeddable video URL
#
function mpfy_create_embedcode($video_url, $width = 440, $height = 350, $old_embed_code = false) {
	if (!$width || !is_numeric($width)) {
		$width = 440;
	}
	if (!$height || !is_numeric($height)) {
		$height = 350;
	}

	if ( $old_embed_code ) {
		$actual_file = mpfy_get_youtube_video($video_url);
		return '<object width="' . $width . '" height="' . $height . '"><param name="movie" value="' . $actual_file . '"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="wmode" value="transparent"><embed src="' . $actual_file . '" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="' . $width . '" height="' . $height . '" wmode="transparent"></embed></object>';
	} else {
		return '<iframe title="YouTube video player" width="' . $width . '" height="' . $height . '" src="' . str_replace('watch?v=', 'embed/', $video_url) . '" frameborder="0" allowfullscreen></iframe>';
	}
}

?>