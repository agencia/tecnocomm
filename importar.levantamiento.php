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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "lev")) {
	
	$ide_rsArticulos = "-1";
if (isset($_POST['levantamiento'])) {
  $ide_rsArticulos = $_POST['levantamiento'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsArticulos = sprintf("SELECT * FROM articulo a,levantamientoipdetalle l where l.idarticulo=a.idarticulo and l.idlevantamientoip=%s", GetSQLValueString($ide_rsArticulos, "int"));
$rsArticulos = mysql_query($query_rsArticulos, $tecnocomm) or die(mysql_error());
$row_rsArticulos = mysql_fetch_assoc($rsArticulos);
$totalRows_rsArticulos = mysql_num_rows($rsArticulos);

$colname_rsSubcotizacion = "-1";
if (isset($_POST['idsubcotizacion'])) {
  $colname_rsSubcotizacion = $_POST['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsSubcotizacion = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_rsSubcotizacion, "int"));
$rsSubcotizacion = mysql_query($query_rsSubcotizacion, $tecnocomm) or die(mysql_error());
$row_rsSubcotizacion = mysql_fetch_assoc($rsSubcotizacion);
$totalRows_rsSubcotizacion = mysql_num_rows($rsSubcotizacion);

do{
	
  $insertSQL = sprintf("INSERT INTO subcotizacionarticulo (idsubcotizacion, idarticulo, descri, precio_cotizacion, cantidad, utilidad, mo, moneda, tipo_cambio, marca1) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",																																												
                       GetSQLValueString($_POST['idsubcotizacion'], "int"),
					   GetSQLValueString($row_rsArticulos['idarticulo'], "int"),
					   GetSQLValueString($row_rsArticulos['nombre'], "text"),
					   GetSQLValueString($row_rsArticulos['precio'], "double"),
					   GetSQLValueString($row_rsArticulos['cantidad'], "double"),
					   GetSQLValueString($row_rsSubcotizacion['utilidad_global'], "double"),
					   GetSQLValueString($row_rsArticulos['instalacion'], "double"),
					   GetSQLValueString($row_rsArticulos['moneda'], "int"),
					   GetSQLValueString($row_rsSubcotizacion['tipo_cambio'], "double"),
					   GetSQLValueString($row_rsArticulos['marca'], "text"));
//echo $insertSQL;
  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

}while($row_rsArticulos = mysql_fetch_assoc($rsArticulos));

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_Recordset1 = "-1";
if (isset($_GET['idip'])) {
  $colname_Recordset1 = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Recordset1 = sprintf("SELECT * FROM levantamientoip WHERE idip = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $tecnocomm) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Importar Levantamiento</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js" language="javascript"> </script>
<script language="javascript"  src="js/funciones.js"></script>
</head>

<body>
<h1>Importar Datos de Levantamiento</h1>
<div id="opciones">
<form action="<?php echo $editFormAction; ?>" name="lev" method="POST">
<label>Selecciona el Levantamiento:<select name="levantamiento">
  <?php
do {  
?>
  <option value="<?php echo $row_Recordset1['idlevantamientoip']?>"><?php echo $row_Recordset1['consecutivo']?>(<?php echo $row_Recordset1['idlevantamientoip']?>)</option>
  <?php
} while ($row_Recordset1 = mysql_fetch_assoc($Recordset1));
  $rows = mysql_num_rows($Recordset1);
  if($rows > 0) {
      mysql_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysql_fetch_assoc($Recordset1);
  }
?>
</select></label>
<input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion']; ?>" />

<button type="button" onclick="javascript:window.location = 'close.php'">Cancelar</button>
<button type="submit" >Aceptar</button>
<input type="hidden" name="MM_insert" value="lev" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($Recordset1);

@mysql_free_result($rsSubcotizacion);

@mysql_free_result($rsArticulos);
?>
