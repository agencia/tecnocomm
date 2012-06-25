<?php require_once('Connections/tecnocomm.php'); ?>
<?php
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_nombres = "SELECT *,(select count(nivel) from autorizacion where autorizacion.nivel=nombres_accesos.id) as cant FROM nombres_accesos";
$nombres = mysql_query($query_nombres, $tecnocomm) or die(mysql_error());
$row_nombres = mysql_fetch_assoc($nombres);
$totalRows_nombres = mysql_num_rows($nombres);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Niveles</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type=text/javascript>
var win= null;
function NewWindow(mypage,myname,w,h,scroll){
  var winl = (screen.width-w)/2;
  var wint = (screen.height-h)/2;
  var settings  ='height='+screen.height+',';
      settings +='width='+screen.width+',';
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
//mover('1035','800');

</script>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>

<body class="wrapper">
<table width="500" border="0" align="center" class="wrapper">
  <tr>
    <td width="40">&nbsp;</td>
    <td colspan="2" align="center" bgcolor="#F8D3D7" class="titulos">NIVELES DE ACCESO </td>
    <td width="40">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="353">&nbsp;</td>
    <td width="349"><a href="AgregarNivel.php" onclick="NewWindow(this.href,'add level','600','400','yes');return false">Agregar Nivel </a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="left"><strong>Nombre Nivel</strong> </td>
    <td align="center"><strong>Permisos</strong></td>
    <td>&nbsp;</td>
  </tr>
  <?php do { ?>
    <tr>
      <td>&nbsp;</td>
      <td align="left"><?php echo $row_nombres['id'].".-"; ?><?php echo $row_nombres['nombre']; ?></td>
      <td align="center"><a href="nivel.permisos.php?id=<? echo $row_nombres['id']; ?>" onclick="NewWindow(this.href,'Detalle','600','400','yes');return false">Modificar</a></td>
      <td>&nbsp;</td>
    </tr>
    <?php } while ($row_nombres = mysql_fetch_assoc($nombres)); ?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($nombres);
?>
