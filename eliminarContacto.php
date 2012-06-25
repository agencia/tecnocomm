<?php require_once('Connections/tecnocomm.php'); ?>
<?php
session_start();
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

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
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
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

if ((isset($_POST['idcontacto'])) && ($_POST['idcontacto'] != "")) {
  $deleteSQL = sprintf("DELETE FROM contactoclientes WHERE idcontacto=%s",
                       GetSQLValueString($_POST['idcontacto'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($deleteSQL, $tecnocomm) or die(mysql_error());


   require_once('lib/eventos.php');
	$evt = new evento(32,$_SESSION['MM_Userid'],"Contacto eliminado con el id :".$_POST['idcontacto']);
	$evt->registrar();

  $deleteGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$colname_rsContacto = "-1";
if (isset($_GET['idcontacto'])) {
  $colname_rsContacto = $_GET['idcontacto'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsContacto = sprintf("SELECT * FROM contactoclientes WHERE idcontacto = %s", GetSQLValueString($colname_rsContacto, "int"));
$rsContacto = mysql_query($query_rsContacto, $tecnocomm) or die(mysql_error());
$row_rsContacto = mysql_fetch_assoc($rsContacto);
$totalRows_rsContacto = mysql_num_rows($rsContacto);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
.Estilo2 {color: #FFFFFF}
-->
</style>
</head>

<body>
<form id="eliminarContacto" name="eliminarContacto" method="post" action="">
<table width="350" border="0" align="center" class="wrapper">
  <tr class="titulos">
    <td colspan="3" align="center"><span class="Estilo2">ELIMINAR CLIENTE</span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>ESTAS SEGURO QUE DESEAS ELIMINAR AL CLIENTE:<span class="Estilo1"><?php echo $row_rsContacto['nombre']; ?></span>?</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">
      <label>
        <input type="submit" name="button" id="button" value="Aceptar" />
        </label>    </td>
    <td>&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="idcontacto" value="<?php echo $_GET['idcontacto'];?>"/>
</form>
</body>
</html>
<?php
mysql_free_result($rsContacto);
?>