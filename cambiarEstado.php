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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "cambiarEstado")) {
  $updateSQL = sprintf("UPDATE factura SET estado=%s WHERE idfactura=%s",
                       GetSQLValueString($_POST['estadoFactura'], "int"),
                       GetSQLValueString($_POST['idfactura'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" name="cambiarEstado" method="POST">
<table width="217" border="0" cellpadding="0" cellspacing="0" class="wrapper">
  <!--DWLayoutTable-->
  <tr class="titleTabla">
    <td height="21" colspan="4" valign="top" >ESTADO DE LA FACTURA</td>
  <td width="9"></td>
  </tr>
  <tr>
    <td width="1" height="11"></td>
    <td width="75"></td>
    <td width="20"></td>
    <td width="110"></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="20"></td>
    <td valign="top">ESTADO:</td>
    <td colspan="2" valign="top"><label>
      <select name="estadoFactura" id="estadoFactura">
          <option value="0"  selected="selected">ACTIVA</option>
          <option value="1">PAGADA</option>
          <option value="2">CANCELADA</option>
          <option value="3">INCOBRABLE</option>         
      </select>
    </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="8"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td></td>
    <td></td>
    <td align="right" valign="top"><input type="submit" value="ACEPTAR" /></td>
    <td></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="idfactura" value="<?php echo $_GET['idfactura'];?>" />
<input type="hidden" name="MM_update" value="cambiarEstado" />
</form>
</body>
</html>
