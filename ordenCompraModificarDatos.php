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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE ordencompra SET vigencia=%s, formapago=%s, tiempoentrega=%s, notas=%s, descuento=%s, moneda=%s, tituloconcepto=%s WHERE idordencompra=%s",
                       GetSQLValueString($_POST['vigencia'], "text"),
                       GetSQLValueString($_POST['formapago'], "text"),
                        GetSQLValueString($_POST['tiempoentrega'], "text"),
                       GetSQLValueString($_POST['notas'], "text"),
                       GetSQLValueString($_POST['descuento'], "text"),
					   GetSQLValueString($_POST['moneda'], "int"),
					   GetSQLValueString($_POST['tituloconcepto'], "text"),
                       GetSQLValueString($_POST['idordencompra'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsOrdenCompra = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsOrdenCompra = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenCompra = sprintf("SELECT * FROM ordencompra WHERE idordencompra = %s", GetSQLValueString($colname_rsOrdenCompra, "int"));
$rsOrdenCompra = mysql_query($query_rsOrdenCompra, $tecnocomm) or die(mysql_error());
$row_rsOrdenCompra = mysql_fetch_assoc($rsOrdenCompra);
$totalRows_rsOrdenCompra = mysql_num_rows($rsOrdenCompra);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="form1" id="form1">
  <table align="center" class="wrapper">
    <!--DWLayoutTable-->
 <?php if($row_rsOrdenCompra['tipoorden'] == 1){ ?>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">CONCEPTO:</td>
      <td><label id="fo">
        <input type="text" name="tituloconcepto" value="<?php echo $row_rsOrdenCompra['tituloconcepto']; ?>" size="32" />
      </label></td>
      </tr>
    <?php } ?>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">FORMA DE PAGO:</td>
      <td><label id="fo">
        <input type="text" name="formapago" value="<?php echo $row_rsOrdenCompra['formapago']; ?>" size="32" />
      </label></td>
      </tr>
      
       <tr valign="baseline">
      <td nowrap="nowrap" align="right">MONEDA:</td>
      <td><select name="moneda">
        <option value="0" <?php if (!(strcmp(0, $row_rsOrdenCompra['moneda']))) {echo "selected=\"selected\"";} ?>>PESOS</option>
        <option value="1" <?php if (!(strcmp(1, $row_rsOrdenCompra['moneda']))) {echo "selected=\"selected\"";} ?>>DOLARES</option>
      </select>
      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">VIGENCIA:</td>
      <td><label id="vig"></label>      <input type="text" name="vigencia" value="<?php echo $row_rsOrdenCompra['vigencia']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">TIEMPO DE ENTREGA:</td>
      <td><input type="text" name="tiempoentrega" value="<?php echo $row_rsOrdenCompra['tiempoentrega']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">DESCUENTO:</td>
      <td><input type="text" name="descuento" value="<?php echo $row_rsOrdenCompra['descuento']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">NOTAS:</td>
      <td><textarea name="notas" rows="3" cols="35"><?php echo $row_rsOrdenCompra['notas']; ?></textarea></td>
    </tr>
    <tr valign="baseline">
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td align="right"><input type="submit" value="ACEPTAR DATOS" /></td>
    </tr>
  </table>
  
  <input type="hidden" name="idordencompra" value="<?php echo $row_rsOrdenCompra['idordencompra']; ?>" />
  <input type="hidden" name="MM_update" value="form1" />
</form>
<p>&nbsp;</p>
</body>

</html>
<?php
mysql_free_result($rsOrdenCompra);
?>
