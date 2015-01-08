// Change the header onscroll
$(function(){
	$(window).scroll(function() {
		if(getCurrentScroll() >= 10){
			$('header.main').addClass('small');
			$('.menu-img').addClass('hidden');
		}
		else{
			$('header.main').removeClass('small');
			$('.menu-img').removeClass('hidden');
		}
	});
	function getCurrentScroll() {
		return window.pageYOffset;
	}
});