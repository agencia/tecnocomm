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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevoActivo")) {
  $insertSQL = sprintf("INSERT INTO activos (descripcion, marca, modelo, numserie, fechacompra, facturacompra, proveedor, polizadiario, valorcontable, ubicacion, clave) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['modelo'], "text"),
                       GetSQLValueString($_POST['numeroserie'], "text"),
                       GetSQLValueString($_POST['fechacompra'], "date"),
                       GetSQLValueString($_POST['factura'], "int"),
                       GetSQLValueString($_POST['proveedor'], "text"),
                       GetSQLValueString($_POST['poliza'], "text"),
                       GetSQLValueString($_POST['valor'], "double"),
                       GetSQLValueString($_POST['ubicacion'], "text"),
					   GetSQLValueString($_POST['clave'], "text"));

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
$query_RsSig = "SELECT max(id) as ultimo FROM activos";
$RsSig = mysql_query($query_RsSig, $tecnocomm) or die(mysql_error());
$row_RsSig = mysql_fetch_assoc($RsSig);
$totalRows_RsSig = mysql_num_rows($RsSig);
$num=$row_RsSig['ultimo']+1;

$lon=strlen($num);

if($lon==1){$cad="000".$num;}
if($lon==2){$cad="00".$num;}
if($lon==3){$cad="0".$num;}
if($lon==4){$cad=$num;}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Activo</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Nuevo Activo</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoActivo" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Clave:
<input type="text" name="clave" value="AC<?php echo $cad;?>"  readonly="true" />
</label>
<label>Descripcion:
  <textarea name="descripcion" id="textarea" cols="45" rows="5" class="requerido"></textarea>
</label>

<label>Marca:
  <input type="text" name="marca" />
</label>

<label>Modelo:
  <input type="text" name="modelo" />
</label>

<label>Numero de Serie:
  <input type="text" name="numeroserie" />
</label>

<label>Factura Compra:
  <select name="factura">
    <option value="1">SI</option>
    <option value="0">NO</option>
  </select>
</label>

<label>Fecha Compra:
  <input type="text" name="fechacompra" class="fecha" />
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


<label>Ubicacion:
  <input type="text" name="ubicacion" />
</label>
<input type="submit" value="Aceptar" />
</div>


<input type="hidden" name="MM_insert" value="nuevoActivo" />
</form>

</div>

</body>
</html>