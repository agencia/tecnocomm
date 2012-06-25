<?php require_once('Connections/tecnocomm.php'); ?>
<?php
$ide_Nivel = "-1";
if (isset($_GET['id'])) {
  $ide_Nivel = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Nivel = sprintf("select * from nombres_accesos where id=%s", $ide_Nivel);
$Nivel = mysql_query($query_Nivel, $tecnocomm) or die(mysql_error());
$row_Nivel = mysql_fetch_assoc($Nivel);
$totalRows_Nivel = mysql_num_rows($Nivel);

$colname_Permisos = "-1";
if (isset($_GET['id'])) {
  $colname_Permisos = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Permisos = sprintf("select link.id,nombre from link, autorizacion where link.id=autorizacion.idlink and autorizacion.nivel=%s", $colname_Permisos);
$Permisos = mysql_query($query_Permisos, $tecnocomm) or die(mysql_error());
$row_Permisos = mysql_fetch_assoc($Permisos);
$totalRows_Permisos = mysql_num_rows($Permisos);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type=text/javascript>
var win= null;
function NewWindow(mypage,myname,w,h,scroll){
  var winl = (screen.width-w)/2;
  var wint = (screen.height-h)/2;
  var settings  ='height='+h+',';
      settings +='width='+w+',';
      settings +='top='+wint+',';
      settings +='left='+winl+',';
      settings +='scrollbars='+scroll+',';
      settings +='resizable=yes';
  win=window.open(mypage,myname,settings);
  if(parseInt(navigator.appVersion) >= 4){win.window.focus();}
}
//-->

function mover(posy,posx)
{
  var winl = (screen.width-posy)/2;
  var wint = (screen.height-posx)/2;
  
if (parseInt(navigator.appVersion)>3)
  top.resizeTo(posy,posx);
  top.moveTo(winl,wint);
}
//mover('1035','400');

</script>
<style type="text/css">
<!--
.Estilo1 {
	color: #455678;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<table width="500" border="0" align="center" class="wrapper">
  <tr>
    <td colspan="4" align="center" class="titulos">Detalle Nivel </td>
  </tr>
  <tr>
    <td colspan="3"><span class="Estilo1">Nombre Nivel: </span><strong><?php echo $row_Nivel['nombre']; ?></strong> </td>
    <td width="101">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><span class="Estilo1">Descripcion: </span><strong><?php echo $row_Nivel['descripsion']; ?></strong></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="115">&nbsp;</td>
    <td width="115">&nbsp;</td>
    <td width="151">&nbsp;</td>
    <td><a href="AgregarPermiso.php?id=<?php echo $row_Nivel['id'];?>" onclick="NewWindow(this.href,'add permiso','400','400','yes');return false"><strong><img src="images/Agregar.png" alt="Agregar" width="24" height="24" border="0" align="middle" />Agregar</strong></a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><strong class="Estilo1">Permisos Concedidos</strong> </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php do { ?>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center"><strong><?php echo $row_Permisos['nombre']; ?></strong> <a href="EliminarPermiso.php?idlink=<?php echo $row_Permisos['id']; ?>&id=<?php echo $row_Nivel['id']; ?>" onclick="NewWindow(this.href,'Eliminar','350','300','yes');return false">
        <?php if ($totalRows_Permisos > 0) { // Show if recordset not empty ?>
          <img src="images/eliminar.gif" alt="eliminar" width="19" height="19" border="0" />
          <?php } // Show if recordset not empty ?></a></td>
      <td>&nbsp;</td>
    </tr>
    <?php } while ($row_Permisos = mysql_fetch_assoc($Permisos)); ?>
    <?php if ($totalRows_Permisos == 0) { // Show if recordset empty ?>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center">No hay permisos concedidos </td>
      <td>&nbsp;</td>
    </tr>
      <?php } // Show if recordset empty ?>
</table>
</body>
</html>
<?php
mysql_free_result($Nivel);

mysql_free_result($Permisos);
?>
