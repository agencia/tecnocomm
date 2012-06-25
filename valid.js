$(document).ready(function(){
				$("form").submit(function() {
						
     				 if ($(".requerido[value='']")) {
						$(".requerido[value!='']").css({'border-color' : '#455678'});
						$(".requerido[value='']").css({'border-color' : 'red'});	
						alert("Algunos Campos no se han llenado");
     	   				return false;
						} 
				})

});
// JavaScript Document