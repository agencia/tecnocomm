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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevoBanco")) {
  $insertSQL = sprintf("INSERT INTO ordenservicio (idcliente, descripcionreporte) VALUES (%s, %s)",
                       GetSQLValueString($_POST['idcliente'], "int"),
                       GetSQLValueString($_POST['descripcionreporte'], "text"));

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
$query_rsCliente = "SELECT * FROM cliente ORDER BY nombre ASC";
$rsCliente = mysql_query($query_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);
$totalRows_rsCliente = mysql_num_rows($rsCliente);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Recordset1 = "SELECT (MAX(numeroorden)+1) AS numerodeorden FROM ordenservicio";
$Recordset1 = mysql_query($query_Recordset1, $tecnocomm) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Nueva Orden de Servicio</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Nueva Orden de Servicio</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoBanco" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Cliente:
  <select name="idcliente">
    <?php
do {  
?>
    <option value="<?php echo $row_rsCliente['idcliente']?>"><?php echo $row_rsCliente['nombre']?></option>
    <?php
} while ($row_rsCliente = mysql_fetch_assoc($rsCliente));
  $rows = mysql_num_rows($rsCliente);
  if($rows > 0) {
      mysql_data_seek($rsCliente, 0);
	  $row_rsCliente = mysql_fetch_assoc($rsCliente);
  }
?>
  </select>
</label>

<label>Descripcion Reporte:
  <textarea name="descripcionreporte">
  </textarea>
</label>

<label>Numero Orden:
  <input name="sucursal" type="text" value="<?php echo $row_Recordset1['numerodeorden']; ?>" />
</label>
<label>Tipo Orden Servicio:
  <select name="tipoorden">
  <option value="0">Soporte</option>
  <option value="1">Levantamiento</option>
  <option value="2">Venta</option>
  </select>
</label>

</div>
<div class="botones">
<input type="submit" value="Aceptar" />
</div>
</form>

</div>

</body>
</html>
<?php
mysql_free_result($rsCliente);

mysql_free_result($Recordset1);
?>
