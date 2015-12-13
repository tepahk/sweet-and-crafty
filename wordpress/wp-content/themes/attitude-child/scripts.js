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
	}

	navToggle();

	$(window).resize(function(){
		navToggle();
	})


})(jQuery);