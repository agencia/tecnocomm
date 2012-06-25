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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevoLevantamiento")) {
  $insertSQL = sprintf("INSERT INTO levantamientoip (idip, consecutivo, notas, tipo, fecha, descripcion, orden) VALUES (%s, %s, %s, %s, NOW(), %s, %s)",
                       GetSQLValueString($_POST['idip'], "int"),
                       GetSQLValueString($_POST['consecutivo'], "text"),
                       GetSQLValueString($_POST['notas'], "text"),
                       GetSQLValueString($_POST['tipolevantamiento'], "int"),
					   GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['orden'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "lev.detalle.php?idlevantamiento=".$idlev."&idip=".$_POST['idip'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
echo $insertSQL;
  //
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsNumOrden = "SELECT MAX(orden) as orden FROM levantamientoip WHERE EXTRACT(YEAR from fecha) = YEAR(NOW())";
$RsNumOrden = mysql_query($query_RsNumOrden, $tecnocomm) or die(mysql_error());
$row_RsNumOrden = mysql_fetch_assoc($RsNumOrden);
$totalRows_RsNumOrden = mysql_num_rows($RsNumOrden);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsAbre = "SELECT abreviacion, idcliente FROM cliente WHERE idcliente = (SELECT idcliente FROM ip WHERE idip = " . $_GET['idip'] . ")";
$RsAbre = mysql_query($query_RsAbre, $tecnocomm) or die(mysql_error() . " " .$query_RsAbre);
$row_RsAbre = mysql_fetch_assoc($RsAbre);
$totalRows_RsAbre = mysql_num_rows($RsAbre);

$consecutivo = sprintf("LEV-%03d-%s%s",($row_RsNumOrden['orden']+1),date("y"),$row_RsAbre['abreviacion']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Levantamiento</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div id="myform">
<?php include("ip.encabezado.php");?>
<form action="<?php echo $editFormAction; ?>" name="nuevoLevantamiento" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Folio <input type="text" name="consecutivo" value="<?php echo $consecutivo;?>" /></label>
 <input type="hidden" name="orden" value="<?php echo $row_RsNumOrden['orden'] +1; ?>" />
<label>
Tipo De Levantamiento:
<select name="tipolevantamiento">
  <option value="1">Red Nueva</option>
  <option value="2">Ampliacion</option>
  <option value="3">Voz</option>
  <option value="4">Datos</option>
  <option value="5">Categoria</option>
  <option value="6">Certificable</option>
  <option value="7">Sonido Ambiental</option>
  <option value="8">Telefonia</option>
  <option value="9">Handpunch</option>
  <option value="10">Tuberia</option>
  <option value="11">Obra Civil</option>
  <option value="12">Ducto Aparente</option>
  <option value="13">Charola</option>
  <option value="14">CCTV</option>
  <option value="15">Electrico</option>
  <option value="16">Fibra Optica</option>
</select>
</label>
<label>
Descripcion:
<textarea name="descripcion">
<?php echo $row_rsEncabezado['descripcion']; ?>
</textarea>
</label>
<label>
Notas:
<textarea name="notas">

</textarea>
</label>

</div>
<div class="botones">
<button type="submit">Aceptar</button>
</div>
<input type="hidden" name="abreviacion" value="<?php echo $row_rsEncabezado['abreviacion'];?>"/>
<input type="hidden" name="idip" value="<?php echo $_GET['idip'];?>" />
<input type="hidden" name="MM_insert" value="nuevoLevantamiento"/>
</form>
</div>
</body>
</html>