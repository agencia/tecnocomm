<?php require_once('Connections/tecnocomm.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE cuentasporpagar SET monto=%s, fechavencimiento=%s, nofactura=%s, tipo=%s, moneda=%s, fecha=%s WHERE idcuenta=%s",
                       GetSQLValueString($_POST['monto'], "double"),
                       GetSQLValueString($_POST['fechavencimiento'], "date"),
                       GetSQLValueString($_POST['nofactura'], "text"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['idcuenta'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}




mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedor = "SELECT * FROM proveedor ORDER BY nombrecomercial ASC";
$rsProveedor = mysql_query($query_rsProveedor, $tecnocomm) or die(mysql_error());
$row_rsProveedor = mysql_fetch_assoc($rsProveedor);
$totalRows_rsProveedor = mysql_num_rows($rsProveedor);

$colname_rsCuenta = "-1";
if (isset($_GET['idcuenta'])) {
  $colname_rsCuenta = $_GET['idcuenta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCuenta = sprintf("SELECT * FROM cuentasporpagar WHERE idcuenta = %s", GetSQLValueString($colname_rsCuenta, "int"));
$rsCuenta = mysql_query($query_rsCuenta, $tecnocomm) or die(mysql_error());
$row_rsCuenta = mysql_fetch_assoc($rsCuenta);
$totalRows_rsCuenta = mysql_num_rows($rsCuenta);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css"  rel="stylesheet" type="text/css"/>
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jqueryui.js"></script>
<script language="javascript" src="js/calendario.js"></script>
<title>Editar Cuenta</title>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="form1" id="form1">
  <table align="center" class="wrapper">

    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Tipo:</td>
      <td><select name="tipo"><option value="0" <?php if (!(strcmp(0, $row_rsCuenta['tipo']))) {echo "selected=\"selected\"";} ?>>Factura</option><option value="1" <?php if (!(strcmp(1, $row_rsCuenta['tipo']))) {echo "selected=\"selected\"";} ?>>Nota de Credito</option></select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">No:</td>
      <td><input type="text" name="nofactura" value="<?php echo $row_rsCuenta['nofactura']; ?>" size="10" style="text-align:right;" /></td>
    </tr>
    
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Monto:</td>
      <td><input type="text" name="monto" value="<?php echo $row_rsCuenta['monto']; ?>" size="10" style="text-align:right;" /></td>
    </tr> 
     <tr valign="baseline">
      <td nowrap="nowrap" align="right">Moneda:</td>
      <td><select name="moneda"><option value="0" <?php if (!(strcmp(0, $row_rsCuenta['moneda']))) {echo "selected=\"selected\"";} ?>>PESOS</option><option value="1" <?php if (!(strcmp(1, $row_rsCuenta['moneda']))) {echo "selected=\"selected\"";} ?>>DOLARES</option></select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Fecha:</td>
      <td> <input name="fecha" type="text"   class="fecha" value="<?php echo $row_rsCuenta['fecha']; ?>"/>  </td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Fecha Vencimiento:</td>
      <td><input name="fechavencimiento" type="text"   class="fecha" value="<?php echo $row_rsCuenta['fechapago']; ?>"/>     </td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Guardar" /></td>
    </tr>
  </table>
  
<input type="hidden" name="idcuenta" value="<?php $_GET['idcuenta'];?>"/>
<input type="hidden" name="MM_update" value="form1" />
</form>
</body>
</html>
<?php
mysql_free_result($rsProveedor);

mysql_free_result($rsCuenta);
?>
