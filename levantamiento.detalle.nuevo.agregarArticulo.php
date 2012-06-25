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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "agregarConcepto")) {
  $insertSQL = sprintf("INSERT INTO levantamientodetalle (idlevantamiento, idarticulo) VALUES (%s, %s)",
                       GetSQLValueString($_POST['idlevantamiento'], "int"),
                       GetSQLValueString($_POST['idarticulo'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rsConcepto = "-1";
if (isset($_GET['idarticulo'])) {
  $colname_rsConcepto = $_GET['idarticulo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConcepto = sprintf("SELECT * FROM articulo WHERE idarticulo = %s", GetSQLValueString($colname_rsConcepto, "int"));
$rsConcepto = mysql_query($query_rsConcepto, $tecnocomm) or die(mysql_error());
$row_rsConcepto = mysql_fetch_assoc($rsConcepto);
$totalRows_rsConcepto = mysql_num_rows($rsConcepto);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Agregar Concepto</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="myform">
<form method="POST" action="<?php echo $editFormAction; ?>" name="agregarConcepto">
<div>
<h3>Agregar Concepto</h3>
<?php echo $row_rsConcepto['nombre']; ?>
</div>
<div class="botones">
<button type="submit">Aceptar</button>
</div>
<input type="hidden" name="idlevantamiento"  value="<?php echo $_GET['idlevantamiento']?>"/>
<input type="hidden" name="idarticulo"  value="<?php echo $_GET['idarticulo']?>"/>
<input type="hidden" name="MM_insert" value="agregarConcepto" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($rsConcepto);
?>
