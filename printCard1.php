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

$ide_RsUsr = "-1";
if (isset($_GET['idusuario'])) {
  $ide_RsUsr = $_GET['idusuario'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUsr = sprintf("select * from usuarios where id=%s", GetSQLValueString($ide_RsUsr, "int"));
$RsUsr = mysql_query($query_RsUsr, $tecnocomm) or die(mysql_error());
$row_RsUsr = mysql_fetch_assoc($RsUsr);
$totalRows_RsUsr = mysql_num_rows($RsUsr);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<style type="text/css">
<!--
.Estilo1 {
	font-family: "Times New Roman", Times, serif;
	font-weight: bold;
	font-size: 17px;
	color: #353F96;
}
.Estilo2 {
	font-family: "Times New Roman", Times, serif;
	color: #353F96;
	font-weight: bold;
	font-size: 14px;
}
.Estilo3 {
	color: #353F96;
	font-weight: bold;
	font-size: 14px;
}
-->
</style>
</head>

<body><table width="512" height="345" border="0" background="images/tecnocommcredencia1.gif">
  <tr>
    <td width="110" height="69">&nbsp;</td>
    <td width="14">&nbsp;</td>
    <td width="56">&nbsp;</td>
    <td width="60">&nbsp;</td>
    <td width="52">&nbsp;</td>
    <td width="68">&nbsp;</td>
  </tr>
  <tr>
    <td height="138" align="center" valign="top"><img src="fotos/<?php echo $row_RsUsr['username']?>.jpg" width="94" height="130" /></td>
    <td>&nbsp;</td>
    <td colspan="4" rowspan="2" align="center"><span class="Estilo1"><br /><br /><br /><?php echo $row_RsUsr['nombrereal']; ?></span><br /><br />
      <span class="Estilo2"><?php echo $row_RsUsr['puesto']; ?></span><br /><br />
      <span class="Estilo3"><?php echo $row_RsUsr['email']; ?></span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($RsUsr);
?>

