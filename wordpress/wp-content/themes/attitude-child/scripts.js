(function($){

	// Navigation toggle for mobile
	var navToggle = function(){
		var $primaryNav = $('#access'),
			$wrapper = $('.wrapper');
		if( window.innerWidth < 768 ){
			$primaryNav.unbind().on('click', function(){
				$primaryNav.toggleClass('active');
				$wrapper.toggleClass('menu-open');
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