(function($, $window, $document){

$('body').on('mpfy_admin_map_updated', function(e) {
	var style = $('select[name="_map_google_style"]').val();
	style = (style) ? style : 'default';

	e.mpfy.settings.style = style;
});

$('select[name="_map_google_style"]').change(function() {
	$('body').trigger($.Event('mpfy_trigger_map_reload'));
});

})(jQuery, jQuery(window), jQuery(document));