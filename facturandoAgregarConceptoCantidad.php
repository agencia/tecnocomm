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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "agregarConcepto")) {
  $insertSQL = sprintf("INSERT INTO detallefactura (idfactura, concepto, punitario, cantidad, unidad) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idfactura'], "int"),
                       GetSQLValueString($_POST['concepto'], "text"),
                       GetSQLValueString($_POST['precio'], "double"),
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['umedida'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rsArticulo = "-1";
if (isset($_GET['idarticulo'])) {
  $colname_rsArticulo = $_GET['idarticulo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsArticulo = sprintf("SELECT * FROM articulo WHERE idarticulo = %s", GetSQLValueString($colname_rsArticulo, "int"));
$rsArticulo = mysql_query($query_rsArticulo, $tecnocomm) or die(mysql_error());
$row_rsArticulo = mysql_fetch_assoc($rsArticulo);
$totalRows_rsArticulo = mysql_num_rows($rsArticulo);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Agregar Articulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" name="agregarConcepto" method="POST" >
<table width="490" border="0" cellpadding="0" cellspacing="0" class="wrapper">
  <!--DWLayoutTable-->
  <tr>
    <td height="22" colspan="6" valign="top">Agregar Producto a Factura</td>
  </tr>
  <tr>
    <td width="13" height="6"></td>
    <td width="77"></td>
    <td width="4"></td>
    <td width="289"></td>
    <td width="78"></td>
    <td width="27"></td>
  </tr>
  <tr>
    <td height="21"></td>
    <td align="right" valign="top">Descripcion:</td>
    <td>&nbsp;</td>
    <td colspan="2" valign="top"><textarea name="concepto"   rows="2" cols="35"><?php echo $row_rsArticulo['nombre']; ?></textarea></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="4"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="21"></td>
    <td align="right" valign="top">Cantidad:</td>
    <td></td>
    <td colspan="2" valign="top"><label>
      <input type="text" name="cantidad" id="cantidad" size="10" style="text-align:right" value="1"/>
    </label></td>
    <td></td>
  </tr>
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="22"></td>
    <td align="right" valign="top">U. Medida:</td>
    <td></td>
    <td colspan="2" valign="top"><label>
      <input name="umedida" type="text" id="umedida" value="<?php echo $row_rsArticulo['medida']; ?>"  size="10"/>
    </label></td>
    <td></td>
  </tr>
  <tr>
    <td height="4"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="21"></td>
    <td align="right" valign="top">Precio:</td>
    <td></td>
    <td colspan="2" valign="top"><label>
      <input name="precio" type="text" id="precio" value="<?php echo $row_rsArticulo['precio']; ?>" size="10" style="text-align:right"/>
    </label></td>
    <td></td>
  </tr>
  <tr>
    <td height="10"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td></td>
    <td></td>
    <td></td>
    <td valign="top"><input type="submit" value="Aceptar" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="26"></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
</table>
<input type="hidden"  name="idfactura" value="<?php echo $_GET['idfactura'];?>" />
<input type="hidden" name="MM_insert" value="agregarConcepto" />
</form>
</body>
</html>
<?php
mysql_free_result($rsArticulo);
?>
