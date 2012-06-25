<?php session_start(); ?>
<?php require_once('Connections/tecnocomm.php'); ?>
<?php

$asignados = array();

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTarea = sprintf("SELECT * FROM tarea WHERE idtarea = %s", $_GET['idtarea']);
$rsTarea = mysql_query($query_rsTarea, $tecnocomm) or die(mysql_error());
$row_rsTarea = mysql_fetch_assoc($rsTarea);
$totalRows_rsTarea = mysql_num_rows($rsTarea);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuarios = "SELECT u.id, u.username FROM usuarios u WHERE u.activar = 1 ORDER BY u.username ASC";
$rsUsuarios = mysql_query($query_rsUsuarios, $tecnocomm) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuariosM = sprintf("SELECT u.id, u.username FROM usuarios u, tarea_usuario tu WHERE u.activar = 1 AND u.id = tu.idusuario AND tu.idtarea = %s ORDER BY u.username ASC", $_GET['idtarea']);
$rsUsuariosM = mysql_query($query_rsUsuariosM, $tecnocomm) or die(mysql_error());
$row_rsUsuariosM = mysql_fetch_assoc($rsUsuariosM);
$totalRows_rsUsuariosM = mysql_num_rows($rsUsuariosM);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Tarea</title>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jqueryui.js"></script>
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css" />
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
window.moveTo(0,0);
window.resizeTo(800,600); 
$(document).ready(function (){
		$("#calendario, #calendario2").datepicker({
   									 			onSelect: function(dateText, inst) {
   		  				             			$(".fechadest").val(dateText);
												alert(dateText);
												},dateFormat: 'yy/mm/dd'});
	$("#reas").hide();
	$("#usuarios").dialog({ 
		autoOpen: false, 
		buttons: {
			"Cancelar": function () {
					$( this ).dialog( "close" );
				},
			"Agregar": function () {
					$("#asignados").empty();
					var fields = $("#usuariosL").serializeArray();
					jQuery.each(fields, function(i, field){
						//alert($("#catUser" + field.value).attr("usuario"));
						$("#asignados").append(
						"<li><label><input type=\"checkbox\" checked=\"checked\"  name=\"usuariosm[]\" value=\"" + field.value + "\"/>" + ($("#catUser" + field.value).attr("usuario")) + "</label></li>");
					  });
					$( this ).dialog( "close" );
				}
			}
		});
	$("#estado").change(function(){
		if ($(this).val() == 3)
            $("#reas").fadeIn(1000);
		else 
            $("#reas").fadeOut(1000);
	});
	$("#agregarMas").click(function () {
		$("#usuarios").dialog("open");
	});
});
</script>
</head>

<body>
<div id="wrapper">
<form method="post" name="realizado" id="realizado" action="planeacion.updateEvent.php">
<h1>Actualizar Tarea</h1>
<div style="float:left">
<label>
Estado:
</label>
<select id="estado" name="estado">
<?php if (($row_rsTarea['estado'] == 0) || ($row_rsTarea['estado'] == 3)) { ?>
<option value="1"> Realizado </option>
<?php } ?>
<?php if ($row_rsTarea['estado'] == 1) { ?>
<option value="2"> Verificado </option>
<?php } ?>
<?php if ($row_rsTarea['estado'] != 2) { ?>
<option value="3"> Reasignado </option>
<?php } ?>
<option value="0"> Pendiente</option>
</select>
<br />
<br />
<label>Comentario:</label>
<br />
<textarea style="width:500px;height:350px" name="comentario" class="comment" tabindex="1">
</textarea>
<input type="hidden" name="idtarea" value="<?php echo $_GET['idtarea']; ?>" class='idtarea'/>
<input type="hidden" name="fecha" value="<?php echo date('Y/m/d');?>" id="fechadest"/>
<input type="hidden" name="idusuario" value="<?php echo $_SESSION['MM_Userid'];?>" id="idusuario"/>
</div>
<div style="float:right">
<div id="reas">
	<div id="calendario2"></div>
<h3>Involucrados <a href="#" id="agregarMas"><img src="images/Agregar.png" border="0" title="Agregar más" /></a></h3>
<ul style="list-style:none;" id="asignados">
  <?php do { ?>
    <li><label><input type="checkbox" checked="checked"  name="usuariosm[]" value="<?php echo $row_rsUsuariosM['id']; ?>"/><?php echo $row_rsUsuariosM['username']; ?></label></li><?php $asignados[] = $row_rsUsuariosM['id']; ?>
    <?php } while ($row_rsUsuariosM = mysql_fetch_assoc($rsUsuariosM)); ?>
</ul>
</div>
<br />
<input type="submit" value="Actualizar Tarea" />
</div>
</div>
<!-- FIN DE MARCAR COMO REALIZADO-->

<div id="comentar" style="display:none" title="Actualizar Tarea">

</form>
</div>
<div id="usuarios">
<form id="usuariosL">
<ul style="list-style:none;">
  <?php do { ?>
    <li><label><input type="checkbox"  name="usuarios[]" <?php echo (in_array($row_rsUsuarios['id'],$asignados)) ? "checked=\"checked\"": ""; ?> id="catUser<?php echo $row_rsUsuarios['id']; ?>" value="<?php echo $row_rsUsuarios['id']; ?>" usuario="<?php echo $row_rsUsuarios['username']; ?>" /><?php echo $row_rsUsuarios['username']; ?></label></li>
    <?php } while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios)); ?>
</ul>
</form>
</div>
</body>
</html>