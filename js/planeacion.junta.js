// JavaScript Document
$(function(){
	
	
	$("#Tcoti li").draggable({
			
			helper:'clone'
							
	});
	
	$("#Ncoti").droppable({
			activeClass: "ui-state-default",
			hoverClass: "ui-state-hover",
			accept: "#Tcoti li, #Acoti li",
			helper:'clone',
			drop: function(event, ui) {
	
			$(".ui-selected").each(function(){
					alert($(this).text());
			});
			
			
			
			$('<li></li>').html($(ui.draggable).html()).appendTo(this);
			
			}
			
	});
	
	$(".usTas").droppable({
			activeClass: "ui-state-default",
			hoverClass: "ui-state-hover",
			accept: "#Tcoti li, #Acoti li",
			helper:'clone',
			drop: function(event, ui) {
	
			$(".ui-selected").each(function(){
					alert($(this).text());
			});
			
			
			
			$('<li></li>').html($(ui.draggable).html()).appendTo($(this).children('ul'));
			
			}
		
	
	});
	
	
	$('#asistentes').selectable({});
	
	$('#Acoti li').draggable({
			helper:'clone'
		})
	
	$("h4").click(function(){
				$(this).next(".st").slideToggle();			   
			});

	
});