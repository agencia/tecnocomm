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
  $insertSQL = sprintf("INSERT INTO levantamientoip (idip, descripcion, notas) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['ip'], "int"),
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['notas'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $idlevantamientoip = mysql_insert_id();
  
  $insertSQL = sprintf("INSERT INTO levantamientoipdetalle(idlevantamientoip, idarticulo) SELECT %s,ld.idarticulo FROM levantamientodetalle ld WHERE ld.idlevantamiento = %s",$idlevantamientoip,$_POST['tipoLevantamiento']);

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "levantamientoip.nuevo.detalle.php?idlevantamientoip=".$idlevantamientoip;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevatamientosTipo = "SELECT * FROM levantamiento ORDER BY titulo ASC";
$rsLevatamientosTipo = mysql_query($query_rsLevatamientosTipo, $tecnocomm) or die(mysql_error());
$row_rsLevatamientosTipo = mysql_fetch_assoc($rsLevatamientosTipo);
$totalRows_rsLevatamientosTipo = mysql_num_rows($rsLevatamientosTipo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Levantamiento Nuevo</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Nuevo Levantamiento</h1>
<div id="myform">
<form action="<?php echo $editFormAction; ?>" name="nuevoLevantamiento" method="POST">
<div>
<h3>Ip</h3>
<label>
<input type="text" name="ip" />
</label>
</div>
<div>
<h3>Tipo de Levantamiento</h3>
<label>
<select name="tipoLevantamiento">
  <?php
do {  
?>
  <option value="<?php echo $row_rsLevatamientosTipo['idlevantamiento']?>"><?php echo $row_rsLevatamientosTipo['titulo']?></option>
  <?php
} while ($row_rsLevatamientosTipo = mysql_fetch_assoc($rsLevatamientosTipo));
  $rows = mysql_num_rows($rsLevatamientosTipo);
  if($rows > 0) {
      mysql_data_seek($rsLevatamientosTipo, 0);
	  $row_rsLevatamientosTipo = mysql_fetch_assoc($rsLevatamientosTipo);
  }
?>
</select>
</label>
<label>
Descripcion
<textarea name="descripcion">

</textarea>
</label>
<label>
Notas
<textarea name="notas">

</textarea>
</label>
</div>
<div class="botones">
<button type="submit">Aceptar</button>
</div>
<input type="hidden" name="MM_insert" value="nuevoLevantamiento" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($rsLevatamientosTipo);
?>
