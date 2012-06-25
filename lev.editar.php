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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "nuevoLevantamiento")) {
  $insertSQL = sprintf("UPDATE levantamientoip SET notas=%s, tipo=%s, descripcion=%s WHERE idlevantamientoip = %s",
                       GetSQLValueString($_POST['notas'], "text"),
                       GetSQLValueString($_POST['tipolevantamiento'], "int"),
					   GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
echo $insertSQL;
  //
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsNumOrden = sprintf("SELECT * FROM levantamientoip WHERE idlevantamientoip = %s", $_GET['idlevantamiento']);
$RsNumOrden = mysql_query($query_RsNumOrden, $tecnocomm) or die(mysql_error());
$row_RsNumOrden = mysql_fetch_assoc($RsNumOrden);
$totalRows_RsNumOrden = mysql_num_rows($RsNumOrden);

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
  <form action="<?php echo $editFormAction; ?>" name="nuevoLevantamiento" method="POST">
  <div>
<h3>Datos Generales</h3>
<label>Folio <b><?php echo $row_RsNumOrden['consecutivo'];?></b></label>
<label>
Tipo De Levantamiento:
<select name="tipolevantamiento">
  <option <?php echo ($row_RsNumOrden['tipo'] == 1) ?'selected="selected"':'';?> value="1">Red Nueva</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 2) ?'selected="selected"':'';?> value="2">Ampliacion</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 3) ?'selected="selected"':'';?> value="3">Voz</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 4) ?'selected="selected"':'';?> value="4">Datos</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 5) ?'selected="selected"':'';?> value="5">Categoria</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 6) ?'selected="selected"':'';?> value="6">Certificable</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 7) ?'selected="selected"':'';?> value="7">Sonido Ambiental</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 8) ?'selected="selected"':'';?> value="8">Telefonia</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 9) ?'selected="selected"':'';?> value="9">Handpunch</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 10) ?'selected="selected"':'';?> value="10">Tuberia</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 11) ?'selected="selected"':'';?> value="11">Obra Civil</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 12) ?'selected="selected"':'';?> value="12">Ducto Aparente</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 13) ?'selected="selected"':'';?> value="13">Charola</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 14) ?'selected="selected"':'';?> value="14">CCTV</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 15) ?'selected="selected"':'';?> value="15">Electrico</option>
  <option <?php echo ($row_RsNumOrden['tipo'] == 16) ?'selected="selected"':'';?> value="16">Fibra Optica</option>
</select>
</label>
<label>
Descripcion:
<textarea name="descripcion">
<?php echo $row_RsNumOrden['descripcion']; ?>
</textarea>
</label>
<label>
Notas:
<textarea name="notas">
<?php echo $row_RsNumOrden['notas']; ?>
</textarea>
</label>

</div>
<div class="botones">
<button type="submit">Aceptar</button>
</div>
<input type="hidden" name="id" value="<?php echo $_GET['idlevantamiento']; ?>"  />
<input type="hidden" name="MM_update" value="nuevoLevantamiento"/>
</form>
</div>
</body>
</html>