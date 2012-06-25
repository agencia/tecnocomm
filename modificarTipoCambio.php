<?php require_once('Connections/tecnocomm.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE subcotizacion SET tipo_cambio=%s WHERE idsubcotizacion=%s",
                       GetSQLValueString($_POST['textfield'], "double"),
                       GetSQLValueString($_POST['idsub'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
  
  
$updateSQL2 = sprintf("UPDATE subcotizacionarticulo SET tipo_cambio=%s WHERE idsubcotizacion=%s",
                       GetSQLValueString($_POST['textfield'], "double"),
                       GetSQLValueString($_POST['idsub'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result12 = mysql_query($updateSQL2, $tecnocomm) or die(mysql_error());


  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


 


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

$ide_rsSub = "-1";
if (isset($_GET['idsub'])) {
  $ide_rsSub = $_GET['idsub'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsSub = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion=%s", GetSQLValueString($ide_rsSub, "int"));
$rsSub = mysql_query($query_rsSub, $tecnocomm) or die(mysql_error());
$row_rsSub = mysql_fetch_assoc($rsSub);
$totalRows_rsSub = mysql_num_rows($rsSub);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
<table width="400" border="0" align="center" class="wrapper">
  <tr>
    <td colspan="3" align="center" class="titulos">CAMBIAR TIPO DE CAMBIO</td>
  </tr>
  <tr>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="center">TIPO DE CAMBIO:
    
      <label>
          <input type="text" name="textfield" id="textfield" value="<?php echo $row_rsSub['tipo_cambio']?>" />
        </label>    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center"><label>
      <input type="submit" name="button" id="button" value="ACEPTAR" />
    </label></td>
    <td>&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="idsub" value="<?php echo $_GET['idsub'];?>"/>
<input type="hidden" name="MM_update" value="form1">
</form>
 
</body>
</html>
<?php
mysql_free_result($rsSub);
?>
