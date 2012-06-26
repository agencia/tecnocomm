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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "asignar")) {
  $insertSQL = sprintf("INSERT INTO detalleorden (cantidad, costo, idordencompra,idarticulo, descri, descuento) VALUES (%s, %s, %s,%s,%s,%s)",
                       GetSQLValueString($_POST['cantidad'], "double"),
					   GetSQLValueString($_POST['precio'], "double"),
                       GetSQLValueString($_POST['idordencompra'], "int"),
					   GetSQLValueString($_POST['idarticulo'], "int"),
					   GetSQLValueString($_POST['descri'], "text"),
					   GetSQLValueString($_POST['descuento'], "double")
					   );

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rsPartidas = "-1";
if (isset($_GET['idarticulo'])) {
  $colname_rsPartidas = $_GET['idarticulo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT * FROM articulo WHERE idarticulo = %s", GetSQLValueString($colname_rsPartidas, "int"));
$rsPartidas = mysql_query($query_rsPartidas, $tecnocomm) or die(mysql_error());
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);
$totalRows_rsPartidas = mysql_num_rows($rsPartidas);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" name="asignar" method="POST">
<table width="491" border="0" cellpadding="0" cellspacing="0" class="wrapper">
  <!--DWLayoutTable-->
  <tr>
    <td height="21" colspan="6" valign="top" class="realte">ASIGNAR DETALLE</td>
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
    <td colspan="2" valign="top"><textarea name="descri"><?php echo $row_rsPartidas['nombre']; ?></textarea></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="22"></td>
    <td valign="top">CANTIDAD:</td>
    <td colspan="2" valign="top"><input name="cantidad" type="text" id="cantidad" value="<?php echo $row_rsPartidas['cantidad']; ?>" /></td>
    <td></td>
    <td></td>
  </tr>
  
  
  <tr>
    <td height="25"></td>
    <td valign="top">PRECIO:</td>
    <td colspan="2" valign="top"><input name="precio" type="text" id="precio" value="<?php echo $row_rsPartidas['precio']; ?>" /></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="25"></td>
    <td valign="top">DESCUENTO:</td>
    <td colspan="2" valign="top"><input name="descuento" type="text" id="descuento" value="0" /></td>
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
<input type="hidden" name="idordencompra" value="<?php echo $_GET['idordencompra']; ?>" />
<input type="hidden" name="idarticulo" value="<?php echo $_GET['idarticulo']; ?>"/>
<input type="hidden" name="MM_insert" value="asignar" />
</form>
</body>
</html>
<?php
mysql_free_result($rsPartidas);
?>
