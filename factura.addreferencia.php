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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "addRef")) {
  $updateSQL = sprintf("UPDATE factura SET referencia1=%s, referencia2=%s, referencia3=%s WHERE idfactura=%s",
                       GetSQLValueString($_POST['referencia1'], "text"),
                       GetSQLValueString($_POST['referencia2'], "text"),
                       GetSQLValueString($_POST['referencia3'], "text"),
                       GetSQLValueString($_POST['idfactura'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsCotizacion = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsCotizacion = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT * FROM factura WHERE idfactura = %s", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Agregar Referencia</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Agregar Referencia a Factura</h1>

<form action="<?php echo $editFormAction; ?>" name="addRef" method="POST" id="myform">
<div>
<h3>Seleccione</h3>
<label>
Segun Cotizacion: <input type="text" value="<?php echo $row_rsCotizacion['referencia1'];?>" name="referencia1" />
</label>
<label>
Segun Orden De Servicio: 
<input type="text" value="<?php echo $row_rsCotizacion['referencia3'];?>" name="referencia2" />
</label>
<label>
Segun Orden De Compra: 
  <input type="text" value="<?php echo $row_rsCotizacion['referencia3'];?>" name="referencia3" />
</label>
</div>
<div>
<button name="aceptar" type="submit">Aceptar</button>
<input type="hidden" name="idfactura" value="<?php echo $_GET['idfactura']?>" />
</div>
<input type="hidden" name="MM_update" value="addRef" />
</form>
</body>
</html>