$(document).ready(function(){
	
				$("form").submit(function() {
						alertar = false;
						$(".requerido").css({'border-color':'#455678'});
						var a = $(".requerido");
						jQuery.each(a, function(i) {
    					if($(this).val() == "" ){
							alertar = true;
							$(this).css({'border-color':'red'});
						}
						});
						
						if(alertar == true){
								alert("Es obligatoria la Informacion, que acontinuacion se marca de rojo");
								return false;
							}
						
					
     				
				})

});
// JavaScript Document