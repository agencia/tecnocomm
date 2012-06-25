<?php require_once('Connections/tecnocomm.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "nuevoHerramienta")) {
  $updateSQL = sprintf("UPDATE herramienta SET descripcion=%s, marca=%s, modelo=%s, numserie=%s, fechacompra=%s, facturacompra=%s, proveedor=%s, polizadiario=%s, valorcontable=%s, ubicacion=%s, tipo=%s, grupo=%s WHERE id=%s",
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['modelo'], "text"),
                       GetSQLValueString($_POST['numserie'], "text"),
                       GetSQLValueString($_POST['fechacompra'], "date"),
                       GetSQLValueString($_POST['facturacompra'], "int"),
                       GetSQLValueString($_POST['proveedor'], "text"),
                       GetSQLValueString($_POST['poliza'], "text"),
                       GetSQLValueString($_POST['valor'], "double"),
                       GetSQLValueString($_POST['ubicacion'], "text"),
                       GetSQLValueString($_POST['tipo'], "int"),
					   GetSQLValueString($_POST['grupo'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_RsHerramienta = "-1";
if (isset($_GET['id'])) {
  $colname_RsHerramienta = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsHerramienta = sprintf("SELECT * FROM herramienta WHERE id = %s", $colname_RsHerramienta);
$RsHerramienta = mysql_query($query_RsHerramienta, $tecnocomm) or die(mysql_error());
$row_RsHerramienta = mysql_fetch_assoc($RsHerramienta);
$totalRows_RsHerramienta = mysql_num_rows($RsHerramienta);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Banco</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Nueva Herramienta</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoHerramienta" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Descripcion:
<input name="descripcion" type="text" class="requerido" value="<?php echo $row_RsHerramienta['descripcion']; ?>" />
</label>

<label>Marca:
<input name="marca" type="text" class="requerido" value="<?php echo $row_RsHerramienta['marca']; ?>" />
</label>

<label>Modelo:
<input name="modelo" type="text" value="<?php echo $row_RsHerramienta['modelo']; ?>" />
</label>

<label>Numero de Serie	:
<input name="numserie" type="text" value="<?php echo $row_RsHerramienta['numserie']; ?>" />
</label>

<label>Fecha de Compra:
<input name="fechacompra" type="text" class="fecha" value="<?php echo $row_RsHerramienta['fechacompra']; ?>" />
</label>

<label>Factura de Compra:
<select name="facturacompra">
  <option value="1" <?php if (!(strcmp(1, $row_RsHerramienta['facturacompra']))) {echo "selected=\"selected\"";} ?>>Si</option>
  <option value="0" <?php if (!(strcmp(0, $row_RsHerramienta['facturacompra']))) {echo "selected=\"selected\"";} ?>>No</option>
</select>
</label>

<label>Proveedor:
<input name="proveedor" type="text" value="<?php echo $row_RsHerramienta['proveedor']; ?>" />
</label>

<label>Poliza de Diario:
<input name="poliza" type="text" value="<?php echo $row_RsHerramienta['polizadiario']; ?>" />
</label>

<label>Valor Contable:
<input name="valor" type="text" value="<?php echo $row_RsHerramienta['valorcontable']; ?>" />
</label>


<label>Grupo De Herramientas:
<input type="text" name="grupo" value="<?php echo $row_RsHerramienta['grupo'];?>" />
</label>

<label>Responsable:
<input name="ubicacion" type="text" value="<?php echo $row_RsHerramienta['ubicacion']; ?>" />
</label>

<label>Tipo:<select name="tipo">
  <option value="0" <?php if (!(strcmp(0, $row_RsHerramienta['tipo']))) {echo "selected=\"selected\"";} ?>>Uso Comun</option>
  <option value="1" <?php if (!(strcmp(1, $row_RsHerramienta['tipo']))) {echo "selected=\"selected\"";} ?>>Uso Personal</option>
</select>

</label>
<input type="submit" value="Aceptar" />
</div>


<input type="hidden" name="id" value="<?php echo $_GET['id'];?>" />
<input type="hidden" name="MM_update" value="nuevoHerramienta">

</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsHerramienta);
?>