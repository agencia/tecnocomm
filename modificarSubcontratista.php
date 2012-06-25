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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "modActivo")) {
  $updateSQL = sprintf("UPDATE subcontratistas SET nombre=%s, abreviacion=%s, calle=%s, colonia=%s, ciudad=%s, estado=%s, tel1=%s, tel2=%s, cel1=%s, cel2=%s, correo1=%s, correo2=%s, fecha_inicio=%s WHERE id=%s",
                       GetSQLValueString($_POST['Nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['calle'], "text"),
                       GetSQLValueString($_POST['colonia'], "text"),
                       GetSQLValueString($_POST['ciudad'], "text"),
                       GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['tel1'], "text"),
                       GetSQLValueString($_POST['tel2'], "text"),
                       GetSQLValueString($_POST['cel1'], "text"),
                       GetSQLValueString($_POST['cel2'], "text"),
                       GetSQLValueString($_POST['mail1'], "text"),
                       GetSQLValueString($_POST['mail2'], "text"),
                       GetSQLValueString($_POST['fechainicio'], "date"),
                       GetSQLValueString($_POST['idsub'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_RsSub = "-1";
if (isset($_GET['id'])) {
  $colname_RsSub = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSub = sprintf("SELECT * FROM subcontratistas WHERE id = %s", GetSQLValueString($colname_RsSub, "int"));
$RsSub = mysql_query($query_RsSub, $tecnocomm) or die(mysql_error());
$row_RsSub = mysql_fetch_assoc($RsSub);
$totalRows_RsSub = mysql_num_rows($RsSub);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Modificar Subcontratista</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>


<body>
<h1>Modificar SubContratista</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="modActivo" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Nombre:
  <input name="Nombre" type="text" value="<?php echo $row_RsSub['nombre']; ?>" class="requerido" />
</label>

<label>Abreviacion:
  <input name="abreviacion" type="text" value="<?php echo $row_RsSub['abreviacion']; ?>" class="requerido" />
</label>

<label>Calle y Numero:
  <input name="calle" type="text" value="<?php echo $row_RsSub['calle']; ?>" />
</label>

<label>Colonia:
  <input name="colonia" type="text" value="<?php echo $row_RsSub['colonia']; ?>" />
</label>

<label>Ciudad:
  <input name="ciudad" type="text" value="<?php echo $row_RsSub['ciudad']; ?>" />
</label>

<label>Estado:
  <input name="estado" type="text" value="<?php echo $row_RsSub['estado']; ?>" />
</label>

<label>Telefono 1:
  <input name="tel1" type="text" value="<?php echo $row_RsSub['tel1']; ?>" />
</label>

<label>Telefono 2:
  <input name="tel2" type="text" value="<?php echo $row_RsSub['tel2']; ?>" />
</label>

<label>Celular 1:
  <input name="cel1" type="text" value="<?php echo $row_RsSub['cel1']; ?>" />
</label>


<label>Celular 2:
  <input name="cel2" type="text" value="<?php echo $row_RsSub['cel2']; ?>" />
</label>

<label>Correo 1:
  <input name="mail1" type="text" value="<?php echo $row_RsSub['correo1']; ?>" />
</label>

<label>Correo 2:
  <input name="mail2" type="text" value="<?php echo $row_RsSub['correo2']; ?>" />
</label>

<label>Fecha Inicio de Operaciones:
  <input name="fechainicio" type="text" class="fecha" value="<?php echo $row_RsSub['fecha_inicio']; ?>" />
</label>
<input type="submit" value="Aceptar" />
</div>




<input type="hidden" name="idsub" value="<?php echo $row_RsSub['id']; ?>" />
<input type="hidden" name="MM_update" value="modActivo" />
</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsSub);
?>
