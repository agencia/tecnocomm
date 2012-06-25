<?php require_once('Connections/tecnocomm.php');
  require_once('utils.php'); 
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if(isset($_POST["idcuenta"])) {
	

$colname_rsCuenta = "-1";
if (isset($_POST['idcuenta'])) {
  $colname_rsCuenta = $_POST['idcuenta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCuenta = sprintf("DELETE FROM cuentasporpagar WHERE idcuenta = %s" , GetSQLValueString($colname_rsCuenta, "int"));
$rsCuenta = mysql_query($query_rsCuenta, $tecnocomm) or die(mysql_error());
header("Location: close.php");
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ELIMINAR CUENTA POR PAGAR</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" name="pagarCuenta" method="POST">
<table width="250" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="22" colspan="3" valign="top" class="titulos">Eliminar Cuenta</td>
  </tr>
  <tr>
    <td width="25"></td>
    <td></td>
    <td width="25"></td>
    </tr>
  
  <tr>
    <td height="22">&nbsp;</td>
    <td colspan="5" valign="top">¿Desea eliminar la cuenta?</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    </tr>
  <tr>
    <td height="22"></td>
    <td valign="top"><input type="submit" value="Aceptar" /></td>
    <td></td>
    </tr>
  <tr>
    <td height="26"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="idcuenta" value="<?php echo $_GET['idcuenta'];?>" />
</form>
</body>
</html>
<?php
mysql_free_result($rsCuenta);

mysql_free_result($rsProveedor);
?>
