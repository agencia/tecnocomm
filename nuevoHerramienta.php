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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevoHerramienta")) {
  $insertSQL = sprintf("INSERT INTO herramienta (descripcion, marca, modelo, numserie, fechacompra, facturacompra, proveedor, polizadiario, valorcontable, ubicacion, tipo, clave, grupo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
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
					   GetSQLValueString($_POST['clave'], "text"),
					   GetSQLValueString($_POST['grupo'], "text");

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSig = "SELECT max(id) as ultimo FROM herramienta";
$RsSig = mysql_query($query_RsSig, $tecnocomm) or die(mysql_error());
$row_RsSig = mysql_fetch_assoc($RsSig);
$totalRows_RsSig = mysql_num_rows($RsSig);
$num=$row_RsSig['ultimo']+1;

$lon=strlen($num);

if($lon==1){$cad="000".$num;}
if($lon==2){$cad="00".$num;}
if($lon==3){$cad="0".$num;}
if($lon==4){$cad=$num;}
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
<label>Clave:
<input type="text" name="clave" value="HE<?php echo $cad;?>"  readonly="true" />
</label>
<label>Descripcion:
<input type="text" name="descripcion" class="requerido" />
</label>

<label>Marca:
<input type="text" name="marca" class="requerido" />
</label>

<label>Modelo:
<input type="text" name="modelo" />
</label>

<label>Numero de Serie	:
<input type="text" name="numserie" />
</label>

<label>Fecha de Compra:
<input type="text" name="fechacompra" class="fecha" />
</label>

<label>Factura de Compra:
<select name="facturacompra">
  <option value="1">Si</option>
  <option value="0">No</option>
</select>
</label>

<label>Proveedor:
<input type="text" name="proveedor" />
</label>

<label>Poliza de Diario:
<input type="text" name="poliza" />
</label>

<label>Valor Contable:
<input type="text" name="valor" />
</label>

<label>Grupo De Herramientas:
<input type="text" name="grupo" />
</label>

<label>Responsable:
<input type="text" name="ubicacion" />
</label>
<label>Tipo:<select name="tipo">
  <option value="0">Uso Comun</option>
  <option value="1">Uso Personal</option>
</select>

</label>
<input type="submit" value="Aceptar" />
</div>



<input type="hidden" name="MM_insert" value="nuevoHerramienta">
</form>

</div>

</body>
</html>