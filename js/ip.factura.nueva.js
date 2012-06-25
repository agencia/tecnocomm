// JavaScript Document
$(function(){
	
		$(".fecha").datepicker({dateFormat: 'yy-mm-dd' ,dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'], dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'], monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']});


	
	
	});


$(function(){
		   
		  $('form').submit(function(e){
			//validad que haya seleccionado una cotizacion u orden de servicio
			
			var v = $('#idcotizacion').val();
	
			
			if( v == ""){
			
				if(confirm('No selecciono ninguna cotizacion, desea continuar	?')){
					return true;
				}else{
					return false;
					}
				
			}
			
			
									
			}); 
		   
		   });