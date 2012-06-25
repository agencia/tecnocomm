jQuery.jFastMenu = function(id){

	$(id + ' ul li').mouseover(function(){
		$(this).find('ul:first').css('display', 'block');
	}).mouseout(function(){
		$(this).find('ul:first').css('display', 'none');
	});

}