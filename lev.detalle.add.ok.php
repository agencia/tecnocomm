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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addLevant")) {
  $insertSQL = sprintf("INSERT INTO levantamientoipdetalle (idlevantamientoip, idarticulo, cantidad) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['idlevantamiento'], "int"),
                       GetSQLValueString($_POST['idarticulo'], "int"),
                       GetSQLValueString($_POST['cantidad'], "double"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_Recordset1 = "-1";
if (isset($_GET['idarticulo'])) {
  $colname_Recordset1 = $_GET['idarticulo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Recordset1 = sprintf("SELECT * FROM articulo WHERE idarticulo = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $tecnocomm) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Agregar A Levantamiento</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Especificar Cantidad</h1>
<div id="myform">
<form action="<?php echo $editFormAction; ?>" name="addLevant" method="POST">
<h3><?php echo $row_Recordset1['nombre']; ?></h3>
<div>
<label>
Cantidad:
<input type="text" name="cantidad" value="" />
</label>
<input type="hidden" name="idlevantamiento" value="<?php echo $_GET['idlevantamiento'];?>" />
<input type="hidden" name="idarticulo" value="<?php echo $row_Recordset1['idarticulo']; ?>" />
</div>
<div id="botones">
<button type="submit">Aceptar</button>
</div>
<input type="hidden" name="MM_insert" value="addLevant" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($Recordset1);
?>
