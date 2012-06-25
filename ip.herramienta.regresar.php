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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "regresar")) {
  $updateSQL = sprintf("UPDATE proyecto_herramienta SET fechadevuelto=NOW() WHERE idproyecto_herramienta=%s",
                       GetSQLValueString($_POST['idproyecto_herramienta'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  //quitar herramienta de prestamo
   $updateSQL = sprintf('UPDATE herramienta SET prestada = 0 WHERE id = %s',GetSQLValueString($_POST['idherramienta'],'int'));
  mysql_select_db($database_tecnocomm,$tecnocomm);
  $result = mysql_query($updateSQL,$tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsHerramienta = "-1";
if (isset($_GET['idprestamo'])) {
  $colname_rsHerramienta = $_GET['idprestamo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsHerramienta = sprintf("SELECT ph.*,h.*,u.username FROM proyecto_herramienta ph JOIN herramienta h ON h.id = ph.idherramienta LEFT JOIN usuarios u ON u.id = ph.responsable WHERE ph.idproyecto_herramienta = %s", GetSQLValueString($colname_rsHerramienta, "int"));
$rsHerramienta = mysql_query($query_rsHerramienta, $tecnocomm) or die(mysql_error());
$row_rsHerramienta = mysql_fetch_assoc($rsHerramienta);
$totalRows_rsHerramienta = mysql_num_rows($rsHerramienta);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Regresar Herramienta</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Regresar Herramienta</h1>
<form action="<?php echo $editFormAction; ?>" name="regresar" method="POST" id="myform">
<div>
<h3><?php echo $row_rsHerramienta['descripcion']; ?> </h3>
<h3>Grupo: <?php echo $row_rsHerramienta['grupo']; ?></h3>
<h3>Prestada a: <?php echo $row_rsHerramienta['username']; ?></h3>
<input type="submit" value="Marcar Como Devuelta" />
<input type="hidden" name="idproyecto_herramienta" value="<?php echo $row_rsHerramienta['idproyecto_herramienta']; ?>"/>
<input type="hidden" name="fechadevuelto"  value="<?php echo date('Y-m-d');?>"/>
<input type="hidden" name="idherramienta" value="<?php echo $row_rsHerramienta['idherramienta']; ?>" />
</div>
<input type="hidden" name="MM_update" value="regresar" />
</form>

</body>
</html>
<?php
mysql_free_result($rsHerramienta);
?>
