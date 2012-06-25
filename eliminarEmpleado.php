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

if ((isset($_POST['idempleado'])) && ($_POST['idempleado'] != "")) {
  $deleteSQL = sprintf("DELETE FROM empleado WHERE idempleado=%s",
                       GetSQLValueString($_POST['idempleado'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($deleteSQL, $tecnocomm) or die(mysql_error());
  ///////////////////
  $deleteSQL2 = sprintf("DELETE FROM empleadosueldo WHERE idempleado=%s",
                       GetSQLValueString($_POST['idempleado'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result12 = mysql_query($deleteSQL2, $tecnocomm) or die(mysql_error());
  /////////////////////////////
  $deleteSQL3 = sprintf("DELETE FROM empleadopuesto WHERE idempleado=%s",
                       GetSQLValueString($_POST['idempleado'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result13 = mysql_query($deleteSQL3, $tecnocomm) or die(mysql_error());

  $deleteGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}


$colname_RseMPLEADO = "-1";
if (isset($_GET['idempleado'])) {
  $colname_RseMPLEADO = $_GET['idempleado'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RseMPLEADO = sprintf("SELECT * FROM empleado WHERE idempleado = %s", GetSQLValueString($colname_RseMPLEADO, "int"));
$RseMPLEADO = mysql_query($query_RseMPLEADO, $tecnocomm) or die(mysql_error());
$row_RseMPLEADO = mysql_fetch_assoc($RseMPLEADO);
$totalRows_RseMPLEADO = mysql_num_rows($RseMPLEADO);
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
<form id="form1" name="form1" method="post" action="">
<table width="350" border="0" align="center" class="wrapper">
  <tr class="titulos">
    <td colspan="3" align="center"><span class="Estilo2">ELIMINAR EMPLEADO</span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>ESTAS SEGURO QUE DESEAS ELIMINAR AL EMPLEADO:<span class="Estilo1"><?php echo $row_RseMPLEADO['nombre']; ?></span>?</td>
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
<input type="hidden" name="idempleado" value="<?php echo $_GET['idempleado'];?>"/>
 </form>
</body>
</html>
<?php
mysql_free_result($RseMPLEADO);
?>
