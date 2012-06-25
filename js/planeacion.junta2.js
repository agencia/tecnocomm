// JavaScript Document
$(function(){
		  
	$('.tabs').tabs();
	$('#tabsTareas').tabs();
	
	 $("#tareas > div, #diaanterior > div, #diahoy > div").addClass("ui-accordion ui-widget ui-helper-reset")
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

	
	$(".asignNow").live('click',function(e){
		e.preventDefault();
		$("#idip").val($(this).attr('idip'));
		$("#valorreferencia").val($(this).attr('valorreferencia'));
		$("#tipoelemento").val($(this).attr('tipoelemento'));
		
		$(':checkbox').attr('checked',false);
		
		$("#selUsuario").dialog('open');
	
	});
	
	$("#selUsuario").dialog({
			autoOpen: false,
			
					height: $(window).height() - 100,
					width: $(window).width() - 100,
			modal: true,
			buttons: {
				'Aceptar': function() {
						 $.ajax({
								url:'planeacion.addEvent.php',
								type:'POST',
								data:$('#addEvento').serialize(),
								cache:false,
								success:function(data1){
										loada(0);
										loada(2);
										loada(4);
										loada(6);
										loada(8);
										loada(12);
										loada(13);
										concentrado();
										$(this).dialog('close');
								}
								
								
						 });
						$('#buscoti').attr("value", "");
						$('#busleva').attr("value", "");
						$('#buscuen').attr("value", "");
						$('#busfact').attr("value", "");
						$('#busorde').attr("value", "");	
						$(this).dialog('close');
					},
				Cancel: function() {
					$(this).dialog('close');
				}
			},
			close: function() {
				
			},
			open: function(e, ui){
					$('#calendario').datepicker('setDate' , new Date());
					
					$('#comentarioadd').val('');
				
			}

		});
	
	
	$('#addBitacora').dialog({
						autoOpen: false,
						
					height: $(window).height() - 200,
					width: $(window).width() - 200,
						modal: true,
						buttons:{
								'Aceptar':function(){
										$('#formbitacora').submit(function(e){
																		 
																$.ajax({
																	   url:$(this).attr('action'),
																	   data:$(this).serialize(),
																	   type:'POST',
																	   dataType:'html',
																	   success:function(data){
																		   	 $('#addBitacora').dialog('close');
																		   }
																	   
																	   });					 
																		 
																		 
																		  e.preventDefault();			   
															});
										
										$('#formbitacora').submit();
								},
								'Cancelar':function(){
									$(this).dialog('close');	
								}								
						},
						
						  
					});



	$("#comentario").dialog({
					autoOpen: false,
					height: $(window).height() - 100,
					width: $(window).width() - 100,
					modal: true,
					buttons:{
						'Guardar':function(){
							
							$.ajax({
									url:'planeacion.updateEvent.php',
									data:$('#realizado').serialize(),
									cache:false,
									type:'POST',
									success:function(){
											loada(1);
											loada(0);
											loada(2);
											loada(3);
											loada(4);
											loada(5);
											loada(6);
											loada(7);
											loada(8);
											loada(9);
											loada(10);
											loada(11);
											loada(12);
											loada(13);
											concentrado();
											$('#realizado .comment').val('');
											$("#comentario").dialog('close');
											
										}
							});
							
								
							},
						Cancel: function(){
							$(this).dialog('close');
							}
						}
				});
	
	$("#historial").dialog({
					autoOpen: false,
					height:450,
					width:700,
					modal:true,
					buttons:{
						   'Aceptar':function(){$(this).dialog('close');}
						   }
						   
					});
	
	$(".guardarComentario").live('click',function(e){
					$('#realizado  .idtarea').val($(this).attr('idtarea'));
					$("#comentario").dialog('open');
					e.preventDefault();
					});
	
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
	

	$("#calendario, #calendario2").datepicker({
   									 			onSelect: function(dateText, inst) {
   		  				             			$(".fechadest").val(dateText);
												alert(dateText);
												},dateFormat: 'yy/mm/dd'});
	
	//cotizaciones
	loada(0);
	loada(1);
	
	//levantamientos
	loada(2);
	loada(3);
	
	//ordenes
	loada(4);
	loada(5);
	
	/*Facturas*/
	loada(6);
	loada(7);
	
	/*cxc*/
	loada(8);
	loada(9);
	
	//CXP
	loada(10);
	loada(11);
	
	//ADMIN
	loada(12);
	loada(13);
	concentrado();
	
	
	$(".ampliar").click(function(e){
				$("#tareas").css('width','98%');
				e.preventDefault();
				});
});


function loada(tipo){
			  var class = '.cotizaciones';
			  var type = 0;
			  
			  switch(tipo){
				  case 0: class = '.cotizaciones'; lurl = 'planeacion.cotizaciones.php'; type = 1;
				  break;
				  case 1: class = '.ayercotizaciones'; lurl = 'planeacion.cotizaciones.php'; type = 2;
				  break;
				  case 2: class = '.levantamientos'; lurl = 'planeacion.levantamientos.php'; type = 1;
				  break;
				  case 3: class = '.ayerlevantamientos'; lurl = 'planeacion.levantamientos.php'; type = 2;
				  break;
				  
				  case 4: class = '.ordenservicio'; lurl = 'planeacion.ordenservicio.php'; type = 1;
				  break;
				  case 5: class = '.ayerordenservicio'; lurl = 'planeacion.ordenservicio.php'; type = 2;
				  break;
				  
				  case 6: class = '.facturas'; lurl = 'planeacion.admin.facturas.php'; type = 1;
				  break;
				  
				  case 7: class = '.ayerfacturas'; lurl = 'planeacion.admin.facturas.php'; type = 2;
				  break;
				  
				  case 8: class = '.cxc'; lurl = 'planeacion.admin.cxc.php'; type = 1;
				  break;
				  
				  case 9: class = '.ayercxc'; lurl = 'planeacion.admin.cxc.php'; type = 2;
				  break;
				  
				  case 10: class = '.cxp'; lurl = 'planeacion.admin.cxp.php'; type = 1;
				  break;
				  case 11: class = '.ayercxp'; lurl = 'planeacion.admin.cxp.php'; type = 2;
				  break
				  
				  case 12: class = '.admin'; lurl = 'planeacion.admin.php'; type = 1;
				  break;
				  
				  case 13: class = '.ayeradmin'; lurl = 'planeacion.admin.php'; type = 2;
				  break;
				   
			  }


	$.ajax({
		  url:lurl,
		  data:{'type':type,'fecha':$('#fechaplaneacion').val()},
		  type:'get',
		  cache:false,
		  success:function(data){
	  
			  $(class).html(data);
			  $('.too').qtip({ style: {ame: 'blue',tip: true},   position: {
					  corner: {
						 target: 'topMiddle',
						 tooltip: 'bottomMiddle',
						 adjust: { mouse: true }
					  }
				   }
				 });
			  //$(class).prev().toggleClass("ui-accordion-header-active").toggleClass("ui-state-active")
				//					.toggleClass("ui-state-default").toggleClass("ui-corner-bottom")
					//				.find("> .ui-icon").toggleClass("ui-icon-triangle-1-e").toggleClass("ui-icon-triangle-1-s")
						//			.end().next().toggleClass("ui-accordion-content-active").toggle();
		  },
		  error:function(){alert("No es posible, leer algunos eventos, precione F5");}
		 
	});
	
	
	
}


function concentrado(){
		$.ajax({
			   url:'planeacion.concentrado.php',
			   type:'GET',
			   data:{'fecharealizar':$('#fechaplaneacion').val(),'idjunta':$('#idjunta').val()},
			   cache:false,
			   success:function(data){
				 	$('#concentrado').html(data);  
				 }
			   });
}

function buscarenuser(id, b, f){
	$.ajax({
		  url: 'planeacion.buscar.usuario.php?iduser=' + id + '&fecharealizo=' + f + '&b=' + b,
		  cahce: false,
		  success: function(data) {
			$("#contenidoUser" + id).html(data);
		  }
		});
	}

function buscar(_url, q, clasedestino)
{
	$.ajax({
		url:_url,
		data:{'q':$(q).val()},
		success:function(data){
			$(clasedestino).html(data);
		}
	});
	
}

function addBitacora(idTarea,idusuario)
{
	$.ajax({
		   url:'planeacion.addBitacora.php',
		   data:{'idtarea':idTarea,'idusuario':idusuario},
		   type:'GET',
		   success:function(data) {
				$('#contentBitacora').html(data);
				$('#addBitacora').dialog('open');
			   }
		   });
}
	
function delTarea(objeto){
	if($('#deltarea').length == 0){
		$('<div />').attr('id','deltarea').attr('title','Eliminar Tarea').appendTo('body');
		$('#deltarea').html('<h3>Realimente Desea Eliminar La Tarea</h3>');
	}
	$('#deltarea').dialog({
						modal:true,
						buttons:{
								'Si':function(){
										$.ajax({
											   url:$(objeto).attr('href'),
											   type:'GET',
											   success:function(){
												   $('#deltarea').dialog('close').remove();
												  		
											loada(0);
											loada(1);
											loada(2);
											loada(3);
											loada(4);
											loada(5);
											loada(6);
											loada(7);
											loada(8);
											loada(9);
											loada(10);
											loada(11);
											loada(12);
											loada(13);
												 	}
											   });
									},
								'No':function(){
										$('#deltarea').dialog('close').remove();
									}
							}
				});
}

function addComent(idTarea)
{
	
	$.get('planeacion.comentar.php',{'idtarea':idTarea},function(data){$('#comentar').html(data)});
	
	
	$('#comentar').dialog({
			modal : true,
			autoOpen : false,
			width : $(window).width() - 100,
			height : $(window).height() - 100,
			buttons :  {
					
					'Aceptar' : function(){
							$.ajax({
								   	url : 'planeacion.comentar.php',
									data : $('#com').serialize(),
									type : 'POST',
									success : function(data){
										//@todo comentar
										$('#comentar').dialog('close');
										}
								   
								   });
						},
					'Cancelar' : function(){
							$(this).dialog('close');
						}
				}
		});
	
	$('#comentar').dialog('open');
}
