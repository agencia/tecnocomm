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
.Estilo3 {
	color: #FF0000;
	font-size: 16px;
}
-->
</style>
</head>

<body><table width="512" height="344" border="0" background="images/tecnocommcredencia2.gif">
  <tr>
    <td width="110" height="69">&nbsp;</td>
    <td width="14">&nbsp;</td>
    <td width="56">&nbsp;</td>
    <td width="60">&nbsp;</td>
    <td width="52">&nbsp;</td>
    <td width="68">&nbsp;</td>
  </tr>
  <tr>
    <td height="157" colspan="6" align="center" valign="top"></td>
  </tr>
  
  <tr>
    <td colspan="6" align="center" valign="top"><span class="Estilo3"><strong>IMSS:<?php echo $row_RsUsr['imss']; ?></strong><br />  
      <strong>TIPO DE SANGRE:<?php echo $row_RsUsr['tipo_sangre']; ?></strong><br />
          <strong>EN CASO DE ACCIDENTE :<?php echo $row_RsUsr['encaso']; ?></strong></span></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($RsUsr);
?>

