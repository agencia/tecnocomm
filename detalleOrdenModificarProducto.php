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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "asignar")) {
  $updateSQL = sprintf("UPDATE detalleorden SET cantidad=%s,costo=%s, descuento=%s, descri=%s WHERE identificador=%s",
                       GetSQLValueString($_POST['cantidad'], "double"),
					   GetSQLValueString($_POST['precio'], "double"),
					   GetSQLValueString($_POST['descuento'], "double"),
					   GetSQLValueString($_POST['descri'], "text"),
                       GetSQLValueString($_POST['iddetalleorden'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsDetalle = "-1";
if (isset($_GET['iddetalleorden'])) {
  $colname_rsDetalle = $_GET['iddetalleorden'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT don.descuento, don.descri, a.nombre, don.cantidad, don.costo FROM detalleorden don,articulo a WHERE don.identificador = %s AND  don.idarticulo = a.idarticulo", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="asignar" id="asignar">
  <table width="491" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
    <!--DWLayoutTable-->
    <tr>
      <td height="21" colspan="6" valign="top" class="realte">MODIFICAR </td>
    </tr>
    <tr>
      <td width="14" height="16"></td>
      <td width="118"></td>
      <td width="221"></td>
      <td width="40"></td>
      <td width="59"></td>
      <td width="39"></td>
    </tr>
    <tr>
      <td height="22"></td>
      <td valign="top">DESCRIPCION:</td>
      <td colspan="2" valign="top"><textarea name="descri"><?php echo $row_rsDetalle['descri']; ?></textarea></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="22"></td>
      <td valign="top">CANTIDAD:</td>
      <td colspan="2" valign="top"><input name="cantidad" type="text" id="cantidad" value="<?php echo $row_rsDetalle['cantidad']; ?>" /></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="25"></td>
      <td valign="top">PRECIO:</td>
      <td colspan="2" valign="top"><input name="precio" type="text" id="precio" value="<?php echo $row_rsDetalle['costo']; ?>" /></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="25"></td>
      <td valign="top">DESCUENTO:</td>
      <td colspan="2" valign="top"><input name="descuento" type="text" id="precio" value="<?php echo $row_rsDetalle['descuento']; ?>" />%</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="15"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="21"></td>
      <td></td>
      <td></td>
      <td colspan="2" valign="top"><input type="submit" value="Aceptar" /></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="20"></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
    </tr>
  </table>
  <input type="hidden" name="iddetalleorden" value="<?php echo $_GET['iddetalleorden'];?>"/>
  <input type="hidden" name="MM_insert" value="asignar" />
  <input type="hidden" name="MM_update" value="asignar" />
</form>
</body>
</html>
<?php
mysql_free_result($rsDetalle);
?>
