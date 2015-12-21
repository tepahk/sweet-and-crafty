(function($){

	// Navigation toggle for mobile
	var navToggle = function(){
		var $primaryNav = $('#access'),
			$body = $('body');
		if( window.innerWidth < 768 ){
			$primaryNav.unbind().on('click', function(){
				$primaryNav.toggleClass('active');
				$body.toggleClass('menu-open');
			})
		}else{
			$primaryNav.unbind();
		}
	}();

	var addPinIt = function(){
		$('body').append('<script async defer src="//assets.pinterest.com/js/pinit.js"></script>');
	}();

})(jQuery);