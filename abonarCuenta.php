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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$abono = $_POST['abono'] +  $_POST['saldo'] ;

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "abonar")) {
  $updateSQL = sprintf("UPDATE cuentasporpagar SET saldo=%s WHERE idcuenta=%s",
                       GetSQLValueString($abono, "double"),
                       GetSQLValueString($_POST['idcuenta'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
  
}

$colname_rsAbono = "-1";
if (isset($_GET['idcuenta'])) {
  $colname_rsAbono = $_GET['idcuenta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAbono = sprintf("SELECT * FROM cuentasporpagar c,proveedor p WHERE idcuenta = %s AND c.idproveedor = p.idproveedor", GetSQLValueString($colname_rsAbono, "int"));
$rsAbono = mysql_query($query_rsAbono, $tecnocomm) or die(mysql_error());
$row_rsAbono = mysql_fetch_assoc($rsAbono);
$totalRows_rsAbono = mysql_num_rows($rsAbono);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Abonar Cuenta</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" name="abonar"  method="POST">
<table width="393" border="0" cellpadding="0" cellspacing="0" class="wrapper
">
  <!--DWLayoutTable-->
  <tr>
    <td height="23" colspan="4" valign="top">Abontar Cuentna</td>
    <td width="1"></td>
  </tr>
  <tr>
    <td width="85" height="13"></td>
    <td width="215"></td>
    <td width="90"></td>
    <td width="2"></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="22" align="right" valign="top">Proveedor:</td>
  <td colspan="3" rowspan="2" valign="top"><label></label>
    <?php echo $row_rsAbono['nombrecomercial']; ?></td>
  <td></td>
  </tr>
  <tr>
    <td rowspan="2" align="right" valign="top">Monto:</td>
    <td height="1"></td>
  </tr>
  <tr>
    <td height="18" colspan="3" valign="top"><?php echo $row_rsAbono['monto']; ?></td>
    <td></td>
  </tr>
  <tr>
    <td height="18" align="right" valign="top">Saldo:</td>
    <td colspan="3" valign="top"><?php echo $row_rsAbono['saldo']; ?></td>
    <td></td>
  </tr>
  <tr>
    <td height="21" align="right" valign="top">Abono:</td>
    <td colspan="3" valign="top"><label>
      <input type="text" name="abono" id="abono" />
    </label></td>
    <td></td>
  </tr>
  <tr>
    <td height="6"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="21"></td>
    <td></td>
    <td valign="top"><input type="submit" value="Aceptar"  /></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="10"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="idcuenta" value="<?php echo $_GET['idcuenta'];?>" />
<input type="hidden" name="saldo" value="<?php echo $row_rsAbono['saldo']; ?>" />
<input type="hidden" name="MM_update" value="abonar" />
</form>
</body>
</html>
<?php
mysql_free_result($rsAbono);
?>
