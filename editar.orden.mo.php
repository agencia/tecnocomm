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
  $updateSQL = sprintf("UPDATE ordenservicio SET  manoobra=%s WHERE idordenservicio=%s",
                       GetSQLValueString($_POST['descripcion'], "double"),
                       GetSQLValueString($_POST['idordenservicio'], "int"));

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
if (isset($_GET['idordenservicio'])) {
  $colname_rsArticulo = $_GET['idordenservicio'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsArticulo = sprintf("SELECT * FROM ordenservicio WHERE idordenservicio = %s", GetSQLValueString($colname_rsArticulo, "int"));
$rsArticulo = mysql_query($query_rsArticulo, $tecnocomm) or die(mysql_error());
$row_rsArticulo = mysql_fetch_assoc($rsArticulo);
$totalRows_rsArticulo = mysql_num_rows($rsArticulo);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Banco</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Editar Mano de Obra</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoProd" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Mano de obra:
  <input name="descripcion" type="text" class="requerido" value="<?php echo $row_rsArticulo['manoobra']; ?>"  />
</label>


</div>


<div class="botones">
<input type="submit" value="Aceptar" />
</div>

<input type="hidden" name="idordenservicio" value="<?php echo $row_rsArticulo['idordenservicio']; ?>"/>
<input type="hidden" name="MM_update" value="nuevoProd" />


</form>

</div>

</body>
</html>
<?php
@mysql_free_result($RsOrden);

@mysql_free_result($rsArticulo);
?>