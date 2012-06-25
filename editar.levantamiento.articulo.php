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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "nuevoProd")) {
  $updateSQL = sprintf("UPDATE levantamientoipdetalle SET cantidad=%s WHERE iddetallelevantamientoip=%s",
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['iddetallelevantamientoip'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


$colname_rsArticulo = "-1";
if (isset($_GET['iddetallelevantamientoip'])) {
  $colname_rsArticulo = $_GET['iddetallelevantamientoip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsArticulo = sprintf("SELECT *,articulo.nombre as name FROM levantamientoipdetalle,articulo WHERE levantamientoipdetalle.idarticulo=articulo.idarticulo and iddetallelevantamientoip = %s", GetSQLValueString($colname_rsArticulo, "int"));
$rsArticulo = mysql_query($query_rsArticulo, $tecnocomm) or die(mysql_error());
$row_rsArticulo = mysql_fetch_assoc($rsArticulo);
$totalRows_rsArticulo = mysql_num_rows($rsArticulo);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Editar Articulo</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Editar Porducto o Servicio </h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoProd" method="POST">

<div>
<h3>Datos Generales</h3>

Articulo:<?php echo $row_rsArticulo['name'];?>

<label>Cantidad:
<input name="cantidad" type="text" value="<?php echo $row_rsArticulo['cantidad']; ?>" />
</label>


</div>

<div class="botones">
<input type="submit" value="Aceptar" />
</div>

<input type="hidden" name="iddetallelevantamientoip" value="<?php echo $_GET['iddetallelevantamientoip']; ?>"/>
<input type="hidden" name="MM_update" value="nuevoProd" />
</form>

</div>

</body>
</html>
<?php
@mysql_free_result($RsOrden);

@mysql_free_result($rsArticulo);
?>