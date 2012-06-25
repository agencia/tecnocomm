<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

/*TAREAS DE SUBCONTRATISTAS*/
$colname_rsTareas = "-1";
if (isset($_GET['fecharealizar'])) {
  $colname_rsTareas = $_GET['fecharealizar'];
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUusarios = "SELECT * FROM usuarios WHERE activar = 1 ORDER BY username ASC";
$rsUusarios = mysql_query($query_rsUusarios, $tecnocomm) or die(mysql_error());
$row_rsUusarios = mysql_fetch_assoc($rsUusarios);
$totalRows_rsUusarios = mysql_num_rows($rsUusarios);

do{
	$usuarios[$row_rsUusarios['id']] = $row_rsUusarios;
}while($row_rsUusarios = mysql_fetch_assoc($rsUusarios));

?>
<script type="text/javascript">
			$(function(){
					$(".iraip").live("click",function (e){ 
					e.preventDefault();
					//alert($(this).attr("href"));
					window.opener.location.href=$(this).attr("href");
					window.opener.focus();
					return false;
				 })
				 
				 $(".abrircaja").live("click", function (e){
					e.preventDefault();
					var id =$(this).attr("userid");
					 $("#vercaja" + id).append("<input type=\"text\" id=\"cajauser" + id + "\" userid=\"" + id + "\" onkeyup=\"buscarenuser(" + id + ", this.value, $('#fechaplaneacion').val())\"  />");
					 $("#cajauser" + id).focus();
					 });
					 
					 <?php foreach ($usuarios as $usuario): ?>
					buscarenuser(<?php echo $usuario['id']; ?>, '', $('#fechaplaneacion').val());
					<?php endforeach; ?>
			});
			
				function refrescaTarea(idtarea, edo)
				 {
					$("tr.tarea" + idtarea).addClass(edo);
				 }
</script>
<h3>Asignaciones Por Personal</h3>
<table cellpadding="0" cellspacing="0" width="auto" style="border:1px solid #39F;">
<tr>
<?php $j = 0;?>
<?php foreach ($usuarios as $usuario): ?>
	<td class="<?php echo ($j%2)?"fdos":" ";$j++;?>" align="center" style="font-weight:bold;font-size:12px;">
		<?php echo $usuario['username']?><br /><a href="planeacion.user.print.php?idjunta=<?php echo $_GET['idjunta']; ?>&idusuario=<?php echo $usuario['id']?>" class="popup"><img src="images/Imprimir2.png" border="none" align="absmiddle" /></a>
        <a href="planeacion.user.buscar.php?iduser=<?php echo $usuario['id']; ?>" userid="<?php echo $usuario['id']; ?>" class="abrircaja"><img src="images/Search.png" width="24" height="24" border="0" align="absmiddle" /></a><div id="vercaja<?php echo $usuario['id']; ?>"></div>
     </td>
<?php endforeach; ?>
</tr>
<tr>
<?php $j = 0;?>
<?php foreach ($usuarios as $usuario): ?>
	<td class="<?php echo ($j%2)?"fdos":" ";$j++;?>" valign="top" align="center" style="font-weight:bold;font-size:12px;min-width:135px;">
    	<div id="contenidoUser<?php echo $usuario['id']; ?>"></div>
     </td>
<?php endforeach; ?>
</tr>
</table>
<?php
mysql_free_result($rsUusarios);
?>