(function($) {

var startJMenu = {
	init : function(options){
		var settings = {
			
		};
		settings = $.extend(settings, options);	

		// define
		var menu = $(this);
		menu.addClass('jmenu');
		menu.find("li:first-child").addClass("first");
		menu.find("li:last-child").addClass("last");
		menu.find("li").has("ul").addClass("parent");

		menu.find(" > li.parent").each(function(){
			if($(this).offset().left > $(window).width()/2 - 100){
				$(this).addClass('parentRight');
			}
		})

		menu.find("li.parent > a").attr("href","javascript:void(null)");
		menu.find("li.parent").click(function(){
			$(this).toggleClass('selected');
		})
		menu.find(" > li").hover(
			function() {
		        $(this).find(" > ul").stop(true,true).slideDown();
		    }, 
		    function() {
		        $(this).find(" > ul").stop(true,true).slideUp();
		    }
		)
		menu.find(" > li li").hover(
			function() {
		        $(this).find(" > ul").stop(true,true).animate({width: 'show'});
		    }, 
		    function() {
		        $(this).find(" > ul").stop(true,true).animate({width: 'hide'});
		    }
		)
	}
};	


$.fn.startJMenu = function(method) {
	if (!startJMenu[method]) {
    	$(this).each(function() {
        	return startJMenu.init.apply(this, [method]);
    	});
    }
};
})(jQuery);