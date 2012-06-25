<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Agregar Partida Extra</title>
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<script language="javascript">

$(function(){
		 
		 $('#tbuscar').keyup( function (event) {
									
		  $.post("catalago.buscar.php", { buscar: $("#tbuscar").attr("value"), opc: "material", vgo:"idproyecto_material=<?php echo $_GET['idproyecto_material'];?>"},function(data){
																																																									 			$('#resultbusqueda').html(data);
																																																								 		});
		  
		  
		  
        })

		   
	});
</script>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Agregar Partida Extra</h1>
<div id="hbus">

<label>Buscar:</label>
<br />
<input type="text" name="buscar" value=""  size="45" id="tbuscar"/>

</div>
<div id="resultbusqueda">

</div>
</body>
</html>