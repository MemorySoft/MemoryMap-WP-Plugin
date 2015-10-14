(function($, $window, $document){

$('body').on('mpfy_popup_opened', function(e) {
	e.mpfy.popup.on('click', '.mpfy-p-social-small', function(e){
		$(this).toggleClass('mpfy-p-social-small-open');
	});
});

})(jQuery, jQuery(window), jQuery(document));