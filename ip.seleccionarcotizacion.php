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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "selCot")) {
  $updateSQL = sprintf("UPDATE ip SET cotizacion=%s WHERE idip=%s",
                       GetSQLValueString($_POST['cotizacion'], "int"),
                       GetSQLValueString($_POST['idip'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsCotizacion = "-1";
if (isset($_GET['idip'])) {
  $colname_rsCotizacion = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT sb.idsubcotizacion, sb.identificador2 FROM cotizacion c, subcotizacion sb WHERE idip = %s AND c.idcotizacion = sb.idcotizacion", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Elija Cotizacion</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" name="selCot" method="POST">
<select name="cotizacion">
  <?php
do {  
?>
  <option value="<?php echo $row_rsCotizacion['idsubcotizacion']?>"><?php echo $row_rsCotizacion['identificador2']?></option>
  <?php
} while ($row_rsCotizacion = mysql_fetch_assoc($rsCotizacion));
  $rows = mysql_num_rows($rsCotizacion);
  if($rows > 0) {
      mysql_data_seek($rsCotizacion, 0);
	  $row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
  }
?>
</select>
<input type="hidden" name="idip" value="<?php echo $_GET['idip'];?>" />
<input type="hidden" name="MM_update" value="selCot" />
<button type="submit">Aceptar</button>
</form>
</body>
</html>
<?php
mysql_free_result($rsCotizacion);
?>
