<?php require_once('Connections/tecnocomm.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

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
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Modsueldo")) {

$porce=($_POST['newsueldo']*100)/$_POST['oldsueldo'];
$porce=round($porce-100,2);
  $insertSQL = sprintf("INSERT INTO empleadosueldo (idempleado, monto, comentario, porcentaje, fecha) VALUES (%s, %s, %s, %s, now())",
                       GetSQLValueString($_POST['idempleado'], "int"),
                       GetSQLValueString($_POST['newsueldo'], "double"),
                       GetSQLValueString($_POST['comentario'], "text"),
					   GetSQLValueString($porce, "double"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  
//  actualizamos el sueldo en el empleado

  $updateSQL = sprintf("UPDATE empleado SET suledo=%s WHERE idempleado=%s",
                       GetSQLValueString($_POST['newsueldo'], "double"),
                       GetSQLValueString($_POST['idempleado'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}





$colname_RsEmpleado = "-1";
if (isset($_GET['idempleado'])) {
  $colname_RsEmpleado = $_GET['idempleado'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsEmpleado = sprintf("SELECT * FROM empleado WHERE idempleado = %s", GetSQLValueString($colname_RsEmpleado, "int"));
$RsEmpleado = mysql_query($query_RsEmpleado, $tecnocomm) or die(mysql_error());
$row_RsEmpleado = mysql_fetch_assoc($RsEmpleado);
$totalRows_RsEmpleado = mysql_num_rows($RsEmpleado);

$colname_RsUltimo = "-1";
if (isset($_GET['idempleado'])) {
  $colname_RsUltimo = $_GET['idempleado'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUltimo = sprintf("SELECT * FROM empleadosueldo WHERE idempleado = %s ORDER BY fecha DESC", GetSQLValueString($colname_RsUltimo, "int"));
$RsUltimo = mysql_query($query_RsUltimo, $tecnocomm) or die(mysql_error());
$row_RsUltimo = mysql_fetch_assoc($RsUltimo);
$totalRows_RsUltimo = mysql_num_rows($RsUltimo);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Modificar sueldo</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Modificar Sueldo de  Empleado</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="Modsueldo" method="POST">
<div>
<h3>Datos Actuales</h3>
<label>Nombre de Empleado:

<?php echo $row_RsEmpleado['nombre']; ?></label>
<label>Sueldo Actual:

<?php echo $row_RsEmpleado['suledo']; ?></label>
<input type="hidden" name="oldsueldo" value="<?php echo $row_RsEmpleado['suledo'];?>"/>
<label>Porcentaje de Aumento actual:

<?php echo $row_RsUltimo['porcentaje']; ?></label>

<label>Fecha Ultima Modificacion

<?php echo $row_RsUltimo['fecha']; ?></label>
<label>Comentario Anterior:

<?php echo $row_RsUltimo['comentario']; ?></label>
</div>


<div>
<h3>Modificar Sueldo</h3>
<label>Nuevo Sueldo:
<input type="text" name="newsueldo"  />
</label>
<label>Comentario:
<textarea name="comentario" cols="" rows=""></textarea>
</label>

</div>


<div class="botones">
<button type="submit" class="button"><span>Aceptar</span></button>
  </div>  
<input type="hidden" name="idempleado" value="<?php echo $_GET['idempleado'];?>"/>
<input type="hidden" name="MM_insert" value="Modsueldo" />

</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsEmpleado);

mysql_free_result($RsUltimo);
?>
