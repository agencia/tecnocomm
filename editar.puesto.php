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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "nuevopuesto")) {
  $updateSQL = sprintf("UPDATE puesto SET  nombre=%s, descripcion=%s, funciones=%s, sueldo=%s WHERE idpuesto=%s",
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['funciones'], "text"),
                       GetSQLValueString($_POST['sueldo'], "double"),
                       GetSQLValueString($_POST['idpuesto'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsPuesto = "-1";
if (isset($_GET['idpuesto'])) {
  $colname_rsPuesto = $_GET['idpuesto'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPuesto = sprintf("SELECT * FROM puesto WHERE idpuesto = %s", GetSQLValueString($colname_rsPuesto, "int"));
$rsPuesto = mysql_query($query_rsPuesto, $tecnocomm) or die(mysql_error());
$row_rsPuesto = mysql_fetch_assoc($rsPuesto);
$totalRows_rsPuesto = mysql_num_rows($rsPuesto);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Editar Puesto</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Editar Puesto</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevopuesto" method="POST">
<div>
<h3>Datos del Puesto</h3>
<label>Clave:
<input type="text" name="clave" value="<?php echo $row_rsPuesto['clave']; ?>"  readonly="true" />
</label>
<label>Nombre:
<input name="nombre" type="text" class="requerido" value="<?php echo $row_rsPuesto['nombre']; ?>" />
</label>

<label>Descripcion
<textarea name="descripcion" cols="" rows=""><?php echo $row_rsPuesto['descripcion']; ?></textarea>
</label>

<label>Funciones:
<textarea name="funciones" cols="" rows=""><?php echo $row_rsPuesto['funciones']; ?></textarea>
</label>

<label>Sueldo:
<input name="sueldo" type="text" value="<?php echo $row_rsPuesto['sueldo']; ?>" />
</label>

</div>


<div>
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="idpuesto" value="<?php echo $_GET['idpuesto'];?>" />
<input type="hidden" name="MM_update" value="nuevopuesto" />
</form>

</div>
</body>
</html>
<?php
mysql_free_result($rsPuesto);
?>
