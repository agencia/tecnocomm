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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "nuevoActivo")) {
  $updateSQL = sprintf("UPDATE activos SET descripcion=%s, marca=%s, modelo=%s, numserie=%s, fechacompra=%s, facturacompra=%s, proveedor=%s, polizadiario=%s, valorcontable=%s, ubicacion=%s WHERE id=%s",
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['modelo'], "text"),
                       GetSQLValueString($_POST['numeroserie'], "text"),
                       GetSQLValueString($_POST['fechacompra'], "date"),
                       GetSQLValueString($_POST['fechacompra'], "int"),
                       GetSQLValueString($_POST['proveedor'], "text"),
                       GetSQLValueString($_POST['poliza'], "text"),
                       GetSQLValueString($_POST['valor'], "double"),
                       GetSQLValueString($_POST['ubicacion'], "text"),
                       GetSQLValueString($_POST['idactivo'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_RsActivo = "-1";
if (isset($_GET['id'])) {
  $colname_RsActivo = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsActivo = sprintf("SELECT * FROM activos WHERE id = %s", GetSQLValueString($colname_RsActivo, "int"));
$RsActivo = mysql_query($query_RsActivo, $tecnocomm) or die(mysql_error());
$row_RsActivo = mysql_fetch_assoc($RsActivo);
$totalRows_RsActivo = mysql_num_rows($RsActivo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Modificar Activo</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Modificar Activo</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoActivo" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Descripcion:
  <textarea name="descripcion" id="textarea" cols="45" rows="5"><?php echo $row_RsActivo['descripcion']; ?></textarea>
</label>

<label>Marca:
  <input name="marca" type="text" value="<?php echo $row_RsActivo['marca']; ?>" />
</label>

<label>Modelo:
  <input name="modelo" type="text" value="<?php echo $row_RsActivo['modelo']; ?>" />
</label>

<label>Numero de Serie:
  <input name="numeroserie" type="text" value="<?php echo $row_RsActivo['numserie']; ?>" />
</label>

<label>Factura Compra:
  <select name="factura">
    <option value="1" <?php if (!(strcmp(1, $row_RsActivo['facturacompra']))) {echo "selected=\"selected\"";} ?>>SI</option>
    <option value="0" <?php if (!(strcmp(0, $row_RsActivo['facturacompra']))) {echo "selected=\"selected\"";} ?>>NO</option>
  </select>
</label>

<label>Fecha Compra:
  <input name="fechacompra" type="text" class="fecha" value="<?php echo $row_RsActivo['fechacompra']; ?>" />
</label>

<label>Proveedor:
  <input name="proveedor" type="text" value="<?php echo $row_RsActivo['proveedor']; ?>" />
</label>

<label>Poliza de Diario:
  <input name="poliza" type="text" value="<?php echo $row_RsActivo['polizadiario']; ?>" />
</label>

<label>Valor Contable:
  <input name="valor" type="text" value="<?php echo $row_RsActivo['valorcontable']; ?>" />
</label>


<label>Ubicacion:
  <input name="ubicacion" type="text" value="<?php echo $row_RsActivo['ubicacion']; ?>" />
</label>
</div>

<div>
<input type="submit" value="Aceptar" />
</div>

<input type="hidden" name="idactivo" value="<?php echo $row_RsActivo['id']; ?>" />
<input type="hidden" name="MM_update" value="nuevoActivo" />
</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsActivo);
?>
