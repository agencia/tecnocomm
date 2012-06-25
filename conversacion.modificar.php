<?php require_once('Connections/tecnocomm.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}

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

//print_r($_POST['destinatarios']);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmNuevaAlerta")) {
  $insertSQL = sprintf("UPDATE conversacion SET mensaje = %s, fechamodificado = NOW() WHERE idconversacion = %s",
                       GetSQLValueString($_POST['mensaje'], "text"),
                       GetSQLValueString($_POST['idconversacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error() . " SQL: ". $insertSQL);
  $idconversacion = mysql_insert_id();  

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuarios = "SELECT mensaje FROM conversacion WHERE idconversacion = " . $_GET['idconversacion'];
$rsUsuarios = mysql_query($query_rsUsuarios, $tecnocomm) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Modificar Alerta</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Modificar Alerta</h1>
<br />
<form action="<?php echo $editFormAction; ?>" name="frmNuevaAlerta" method="post" id="myform">

<h3>Mensaje</h3>
<label>
<textarea name="mensaje" cols="80" rows="20">
<?php echo $row_rsUsuarios['mensaje'];?>
</textarea>
</label>
<div class="botones">
<button type="submit">Modificar Alerta</button>
</div>
<input type="hidden" name="MM_insert" value="frmNuevaAlerta" />
<input type="hidden" name="idconversacion" value="<?php echo $_GET['idconversacion']?>" />
</form>
</body>
</html>
<?php
mysql_free_result($rsUsuarios);
?>
