<?php require_once('Connections/tecnocomm.php'); ?>
<?php
session_start();
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


  $updateSQL = sprintf("UPDATE articulo SET nombre=%s, codigo=%s, marca=%s,  medida=%s, moneda=%s, precio=%s, instalacion=%s, empaque=%s, tipo=%s WHERE idarticulo=%s",
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['codigo'], "text"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['medida'], "text"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['precio'], "double"),
					   GetSQLValueString($_POST['instalacion'], "double"),
                       GetSQLValueString($_POST['empaque'], "text"),
					   GetSQLValueString($_POST['preco'], "int"),
                       GetSQLValueString($_POST['idarticulo'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error() . " " . $updateSQL);

   require_once('lib/eventos.php');
	$evt = new evento(40,$_SESSION['MM_Userid'],"Articulo modificado con la descripcion  :".htmlentities($_POST['nombre'],ENT_QUOTES | ENT_IGNORE, "UTF-8"));
	$evt->registrar();

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsProducto = "-1";
if (isset($_GET['idarticulo'])) {
  $colname_rsProducto = $_GET['idarticulo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProducto = sprintf("SELECT * FROM articulo WHERE idarticulo = %s", GetSQLValueString($colname_rsProducto, "int"));
$rsProducto = mysql_query($query_rsProducto, $tecnocomm) or die(mysql_error());
$row_rsProducto = mysql_fetch_assoc($rsProducto);
$totalRows_rsProducto = mysql_num_rows($rsProducto);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Modificar Articulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
#form1{
top: 0px;
}
-->
</style>
</head>

<body onload="document.forms[0].precio.focus();">
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center" class="wrapper">
    <tr  class="titulos">
      <td colspan="2" align="center" nowrap="nowrap"><span class="Estilo1">MODIFICAR ARTICULO</span></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
     <tr valign="baseline">
      <td align="right" valign="top" nowrap="nowrap">Descripcion:</td>
      <td><textarea name="nombre"  cols="80" rows="12"><?php echo $row_rsProducto['nombre']; ?></textarea></td>
    </tr>

    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Codigo:</td>
      <td><input type="text" name="codigo" value="<?php echo htmlentities($row_rsProducto['codigo'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Marca:</td>
      <td><input type="text" name="marca" value="<?php echo htmlentities($row_rsProducto['marca'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Medida:</td>
      <td><input type="text" name="medida" value="<?php echo htmlentities($row_rsProducto['medida'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>nnua    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Moneda:</td>
      <td><select name="moneda">
        <option value="0" <?php if (!(strcmp(0, htmlentities($row_rsProducto['moneda'], ENT_COMPAT, 'UTF-8')))) {echo "SELECTED";} ?>>Pesos</option>
        <option value="1" <?php if (!(strcmp(1, htmlentities($row_rsProducto['moneda'], ENT_COMPAT, 'UTF-8')))) {echo "SELECTED";} ?>>Dolares</option>
      </select>      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right"><label>
        <select name="preco" id="preco">
          <option value="0" <?php if (!(strcmp(0, $row_rsProducto['tipo']))) {echo "selected=\"selected\"";} ?>>Precio</option>
          <option value="1" <?php if (!(strcmp(1, $row_rsProducto['tipo']))) {echo "selected=\"selected\"";} ?>>Costo</option>
        </select>
      </label>:</td>
      <td><input type="text" name="precio" value="<?php echo htmlentities($row_rsProducto['precio'], ENT_COMPAT, 'UTF-8'); ?>" size="10" style="text-align:right" /></td>
    </tr>
    
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Costo Instalacion:</td>
      <td><input type="text" name="instalacion" value="<?php echo htmlentities($row_rsProducto['instalacion'], ENT_COMPAT, 'UTF-8'); ?>" size="10" style="text-align:right"/></td>
    </tr>
      <tr valign="baseline">
      <td nowrap="nowrap" align="right">Empaque:</td>
      <td><input type="text" name="empaque" value="<?php echo htmlentities($row_rsProducto['empaque'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td align="right"><input type="submit" value="Aceptar" /></td>
    </tr>
  </table>
  <input type="hidden" name="idarticulo" value="<?php echo $row_rsProducto['idarticulo']; ?>" />
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="idarticulo" value="<?php echo $row_rsProducto['idarticulo']; ?>" />
</form>
</body>
</html>
<?php
mysql_free_result($rsProducto);
?>
