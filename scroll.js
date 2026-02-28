/*
.............................
.... scroll ...............
*/
$(document).ready(function(){
	
	/* scroll */
		$(window).scroll(function(){
		var element = '.header';
		var scrPosition = $(window).scrollTop();
		if(scrPosition > 250){
			$(element).addClass("scroll");
		} else {
			$(element).removeClass("scroll");
		}    
   
  });
		
		$(window).scroll(function(){
		var element = '.main_logo';
		var scrPosition = $(window).scrollTop();
		if(scrPosition > 10){
			$(element).addClass("scroll");
		} else {
			$(element).removeClass("scroll");
		}    
   
  });
		
		$(window).scroll(function(){
		var element = '.navigation';
		var scrPosition = $(window).scrollTop();
		if(scrPosition > 10){
			$(element).addClass("scroll");
		} else {
			$(element).removeClass("scroll");
		}    
   
  });
		
});



	
	
	
	
	
