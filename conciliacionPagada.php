<?php require_once('Connections/tecnocomm.php'); ?>
<?php
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE subcotizacion SET estado=%s WHERE idsubcotizacion=%s",
                       GetSQLValueString($_POST['estado'], "int"),
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$ide_RsSub = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $ide_RsSub = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSub = sprintf("select * from subcotizacion where idsubcotizacion=%s", GetSQLValueString($ide_RsSub, "int"));
$RsSub = mysql_query($query_RsSub, $tecnocomm) or die(mysql_error());
$row_RsSub = mysql_fetch_assoc($RsSub);
$totalRows_RsSub = mysql_num_rows($RsSub);
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

<body><form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
<table width="400" border="0" align="center" class="wrapper">
  <tr class="titulos">
    <td colspan="4" align="center"><span class="Estilo2">PAGAR CONCILIACION</span></td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" align="center">DESEAS CAMBIAR DE ESTADO LA CONCILIACION <span class="Estilo1"><?php echo $row_RsSub['identificador2']; ?></span>(<span class="Estilo1"><?php echo $row_RsSub['nombre']; ?></span>)A PAGADA?</td>
    </tr>
  
  <tr>
    <td colspan="4" align="center"><input type="submit" value="ACEPTAR" /></td>
    </tr>
</table>



  <input type="hidden" name="idsubcotizacion" value="<?php echo $row_RsSub['idsubcotizacion']; ?>" />
  <input type="hidden" name="estado" value="9" />
  <input type="hidden" name="MM_update" value="form1" />
 
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($RsSub);
?>
