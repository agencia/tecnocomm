// JavaScript Document
	function buscar(_url, q, clasedestino)
	{
		$.ajax({
			url:_url,
			data:{'q':$(q).val()},
			success:function(data){
				$(clasedestino).html(data);
				
				$('.too').qtip({ style: {ame: 'blue',tip: true},   position: {
					  corner: {
						 target: 'topMiddle',
						 tooltip: 'bottomMiddle',
						 adjust: { mouse: true }
					  }
				   }
				 });
			}
	   });
		
	}
	
	var d = new Date();
	var fec = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
	
	function refrescaTarea(idtarea, edo)
	{
		$("tr.tarea" + idtarea).addClass(edo);
	}

$(function(){
		$("#historial").dialog({
					autoOpen: false,
					height:450,
					width:700,
					modal:true,
					buttons:{
						   'Aceptar':function(){$(this).dialog('close');}
						   }
						   
					});
	
	$("#statusgeneral > div").addClass("ui-accordion ui-widget ui-helper-reset")
        .find("h3")
                .addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-top ui-corner-bottom")
                .prepend('<span class="ui-icon ui-icon-triangle-1-e"/>')
                .click(function() {
                        $(this).toggleClass("ui-accordion-header-active").toggleClass("ui-state-active")
                                .toggleClass("ui-state-default").toggleClass("ui-corner-bottom")
                        .find("> .ui-icon").toggleClass("ui-icon-triangle-1-e").toggleClass("ui-icon-triangle-1-s")
                        .end().next().toggleClass("ui-accordion-content-active").toggle();
                        return false;
                })
                .next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom").hide();
				

	buscar('planeacion.cotizaciones.php?type=2&fecha=' +fec,'','.ayercotizaciones')
	buscar('planeacion.levantamientos.php?type=2&fecha=' + fec,'','.ayerlevantamientos');
	buscar('planeacion.ordenservicio.php?type=2&fecha=' + fec,'','.ayerordenservicio');
	buscar('planeacion.admin.php?type=2&fecha=' + fec,'', '.ayeradmin');
	buscar('planeacion.admin.facturas.php?type=2&fecha=' + fec,'','.ayerfacturas');
	buscar('planeacion.admin.cxp.php?type=2&fecha=' + fec,'','.ayercxo');
	
		$(".verHistorial").live('click',function(e){
					$.ajax({
						   url:$(this).attr('href'),
						   success:function(data){
							   $('#contHistorial').html(data);
							   $("#historial").dialog('open');
							   },
							error:function(data){
								  $('#contHistorial').html('Error Al Intentar Ver El Historial');							   
							   
							   $("#historial").dialog('open');
								}
						   });
					e.preventDefault();
					});
});
