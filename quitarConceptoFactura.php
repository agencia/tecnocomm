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

if ((isset($_POST['idconcepto'])) && ($_POST['idconcepto'] != "") && (isset($_POST['borrar']))) {
  $deleteSQL = sprintf("DELETE FROM detallefactura WHERE iddetalle=%s",
                       GetSQLValueString($_POST['idconcepto'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($deleteSQL, $tecnocomm) or die(mysql_error());

  $deleteGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$colname_rsConcepto = "-1";
if (isset($_GET['idconcepto'])) {
  $colname_rsConcepto = $_GET['idconcepto'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConcepto = sprintf("SELECT * FROM detallefactura WHERE iddetalle = %s", GetSQLValueString($colname_rsConcepto, "int"));
$rsConcepto = mysql_query($query_rsConcepto, $tecnocomm) or die(mysql_error());
$row_rsConcepto = mysql_fetch_assoc($rsConcepto);
$totalRows_rsConcepto = mysql_num_rows($rsConcepto);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form name="elimiarConcepto" method="post">
<table width="340" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="24" colspan="4" valign="top">Borrar Concepto Factura</td>
  </tr>
  <tr>
    <td width="11" height="13"></td>
    <td width="229"></td>
    <td width="86"></td>
    <td width="14"></td>
  </tr>
  <tr>
    <td height="46"></td>
    <td colspan="2" valign="top"><?php echo $row_rsConcepto['concepto']; ?></td>
    <td></td>
  </tr>
  <tr>
    <td height="26"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td height="14"></td>
    <td></td>
    <td valign="top"><input type="submit" value="Aceptar" /></td>
    <td></td>
  </tr>
  <tr>
    <td height="17"></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="borrar" value="ok" />
<input type="hidden" name="idconcepto" value="<?php echo $_GET['idconcepto'];?>" /> 
</form>
</body>
</html>
<?php
mysql_free_result($rsConcepto);
?>
