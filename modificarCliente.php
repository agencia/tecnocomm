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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE cliente SET nombre=%s, abreviacion=%s, direccion=%s, ciudad=%s, fraccionamiento=%s, estado=%s, cp=%s, razonsocial=%s, direccionfacturacion=%s, ciudadfacturacion=%s, rfc=%s, telefono=%s, diasdecredito=%s WHERE idcliente=%s",
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['direccion'], "text"),
                       GetSQLValueString($_POST['ciudad'], "text"),
                       GetSQLValueString($_POST['fraccionamiento'], "text"),
                       GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['cp'], "text"),
                       GetSQLValueString($_POST['razonsocial'], "text"),
                       GetSQLValueString($_POST['direccionfacturacion'], "text"),
                       GetSQLValueString($_POST['ciudadfacturacion'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
					   GetSQLValueString($_POST['telefono'], "text"),
					   GetSQLValueString($_POST['diasdecredito'], "int"),
                       GetSQLValueString($_POST['idcliente'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());


   require_once('lib/eventos.php');
	$evt = new evento(28,$_SESSION['MM_Userid'],"Cliente modificado con el nombre de  :".$_POST['nombre']);
	$evt->registrar();

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsCliente = "-1";
if (isset($_GET['idcliente'])) {
  $colname_rsCliente = $_GET['idcliente'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCliente = sprintf("SELECT * FROM cliente WHERE idcliente = %s", GetSQLValueString($colname_rsCliente, "int"));
$rsCliente = mysql_query($query_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);
$totalRows_rsCliente = mysql_num_rows($rsCliente);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center" class="wrapper">
    <tr valign="baseline">
      <td nowrap="nowrap" align="center" colspan="2" class="titulos">MODIFICAR DATOS DE CLIENTE</td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">NOMBRE:</td>
      <td><input type="text" name="nombre" value="<?php echo htmlentities($row_rsCliente['nombre'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">ABREVIACION:</td>
      <td><input type="text" name="abreviacion" value="<?php echo htmlentities($row_rsCliente['abreviacion'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">DIRECCION:</td>
      <td><input type="text" name="direccion" value="<?php echo htmlentities($row_rsCliente['direccion'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">CIUDAD:</td>
      <td><input type="text" name="ciudad" value="<?php echo htmlentities($row_rsCliente['ciudad'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">FRACCIONAMIENTO:</td>
      <td><input type="text" name="fraccionamiento" value="<?php echo htmlentities($row_rsCliente['fraccionamiento'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">ESTADO:</td>
      <td><input type="text" name="estado" value="<?php echo htmlentities($row_rsCliente['estado'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">TELEFONO:</td>
      <td><input type="text" name="telefono" value="<?php echo htmlentities($row_rsCliente['telefono'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">CP:</td>
      <td><input type="text" name="cp" value="<?php echo htmlentities($row_rsCliente['cp'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
      <tr valign="baseline" class="titulos">
      <td nowrap="nowrap" align="center" colspan="2"><span class="Estilo1">DATOS DE FACTURACION</span></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">RAZON SOCIAL:</td>
      <td><input type="text" name="razonsocial" value="<?php echo $row_rsCliente['razonsocial']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">DIRECCION:</td>
      <td><input type="text" name="direccionfacturacion" value="<?php echo $row_rsCliente['direccionfacturacion']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">CIUDAD:</td>
      <td><input type="text" name="ciudadfacturacion" value="<?php echo $row_rsCliente['ciudadfacturacion']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">RFC:</td>
      <td><input type="text" name="rfc" value="<?php echo $row_rsCliente['rfc']; ?>" size="32" /></td>
    </tr>
    </tr>
      <tr valign="baseline" class="titulos">
      <td nowrap="nowrap" align="center" colspan="2"><span class="Estilo1">DATOS CREDITO</span></td>
    </tr>
    <tr>
    <td align="right">Dias Credito</td><td><input type="text" name="diasdecredito" value="<?php echo $row_rsCliente['diasdecredito'];?>"/></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td align="right"><input type="submit" value="ACEPTAR" /></td>
    </tr>
  </table>
  <input type="hidden" name="idcliente" value="<?php echo $row_rsCliente['idcliente']; ?>" />
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="idcliente" value="<?php echo $row_rsCliente['idcliente']; ?>" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rsCliente);
?>
