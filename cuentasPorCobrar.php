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

$maxRows_rsFactura = 30;
$pageNum_rsFactura = 0;
if (isset($_GET['pageNum_rsFactura'])) {
  $pageNum_rsFactura = $_GET['pageNum_rsFactura'];
}
$startRow_rsFactura = $pageNum_rsFactura * $maxRows_rsFactura;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFactura = "SELECT f.numfactura,f.fecha,f.idfactura,c.idcliente,c.nombre,(SELECT SUM((punitario * cantidad)) FROM detallefactura df WHERE df.idfactura = f.idfactura GROUP BY f.idfactura ) AS monto FROM factura f LEFT JOIN cliente c ON f.idcliente = c.idcliente WHERE f.estado = 0";
$query_limit_rsFactura = sprintf("%s LIMIT %d, %d", $query_rsFactura, $startRow_rsFactura, $maxRows_rsFactura);
$rsFactura = mysql_query($query_limit_rsFactura, $tecnocomm) or die(mysql_error());
$row_rsFactura = mysql_fetch_assoc($rsFactura);

if (isset($_GET['totalRows_rsFactura'])) {
  $totalRows_rsFactura = $_GET['totalRows_rsFactura'];
} else {
  $all_rsFactura = mysql_query($query_rsFactura);
  $totalRows_rsFactura = mysql_num_rows($all_rsFactura);
}
$totalPages_rsFactura = ceil($totalRows_rsFactura/$maxRows_rsFactura)-1;

$maxRows_rsCliente = 30;
$pageNum_rsCliente = 0;
if (isset($_GET['pageNum_rsCliente'])) {
  $pageNum_rsCliente = $_GET['pageNum_rsCliente'];
}
$startRow_rsCliente = $pageNum_rsCliente * $maxRows_rsCliente;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCliente = "SELECT c.nombre,SUM((   SELECT SUM(df.punitario * df.cantidad) FROM detallefactura df WHERE df.idfactura = f.idfactura    )) as monto FROM factura f,cliente c  WHERE f.estado = 0 AND f.idcliente = c.idcliente GROUP BY f.idcliente";
$query_limit_rsCliente = sprintf("%s LIMIT %d, %d", $query_rsCliente, $startRow_rsCliente, $maxRows_rsCliente);
$rsCliente = mysql_query($query_limit_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);

if (isset($_GET['totalRows_rsCliente'])) {
  $totalRows_rsCliente = $_GET['totalRows_rsCliente'];
} else {
  $all_rsCliente = mysql_query($query_rsCliente);
  $totalRows_rsCliente = mysql_num_rows($all_rsCliente);
}
$totalPages_rsCliente = ceil($totalRows_rsCliente/$maxRows_rsCliente)-1;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="940" border="0" cellpadding="0" cellspacing="0" class="wrapper">
  <!--DWLayoutTable-->
  <tr>
    <td height="26" colspan="5" valign="top" class="titulos">Cuentas Por Cobrar:</td>
  </tr>
  <tr>
    <td width="9" height="11"></td>
    <td width="108"></td>
    <td width="232"></td>
    <td width="584"></td>
    <td width="7"></td>
  </tr>
  <tr>
    <td height="24"></td>
    <td align="right" valign="top">Filtrar Por:</td>
    <td valign="top"><form name="filtrar" method="get"><label>
      <select name="filtro" id="filtro">
        <option value="0" <?php if (!(strcmp(0, $_GET['filtro']))) {echo "selected=\"selected\"";} ?>>Factura</option>
        <option value="1" <?php if (!(strcmp(1, $_GET['filtro']))) {echo "selected=\"selected\"";} ?>>Cliente</option>
      </select>
      <input type="hidden" name="mod" value="porcobrar" />
      <input type="submit" name="button" id="button" value="Filtrar" /></label>
    </label></form></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="21"></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <?php if($_GET['filtro'] != 1){?>
  <tr>
    <td height="50"></td>
    <td colspan="3" valign="top">
    
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td height="21" colspan="5" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <tr class="titleTabla">
            <td width="178" height="21" valign="top">Numero Factura</td>
                <td width="303" valign="top">Cliente</td>
                <td width="168" valign="top">Fecha</td>
                <td width="184" valign="top">Cantidad</td>
                <td width="91">&nbsp;</td>
          </tr>
          </table>          </td>
        </tr>
      <?php do { ?>
        <tr>
          <td width="178" height="20" valign="top"><?php echo $row_rsFactura['numfactura']; ?></td>
          <td width="302" valign="top"><?php echo $row_rsFactura['nombre']; ?></td>
          <td width="168" valign="top"><?php echo $row_rsFactura['fecha']; ?></td>
          <td width="184" valign="top"><?php echo $row_rsFactura['monto']; ?></td>
          <td width="92">&nbsp;</td>
        </tr>
        <?php } while ($row_rsFactura = mysql_fetch_assoc($rsFactura)); ?>
      <tr>
        <td height="9"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </table></td>
    <td>&nbsp;</td>
  </tr>
  <?php } ?>
  <tr>
    <td height="29"></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="68"></td>
    <td colspan="3" valign="top">
        <?php if($_GET['filtro'] == 1){?>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="373" height="20" valign="top">Cliente</td>
        <td width="186" valign="top">Cantidad</td>
        <td width="139" valign="top">Opciones</td>
        <td width="226">&nbsp;</td>
      </tr>
      <?php do { ?>
        <tr>
          <td height="18" valign="top"><?php echo $row_rsCliente['nombre']; ?></td>
          <td valign="top"><?php echo $row_rsCliente['monto']; ?></td>
          <td valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
          <td></td>
        </tr>
        <?php } while ($row_rsCliente = mysql_fetch_assoc($rsCliente)); ?>
      <tr>
        <td height="31">&nbsp;</td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      
    </table>
    <?php } ?></td>
    <td></td>
  </tr>
  <tr>
    <td height="124"></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsFactura);

mysql_free_result($rsCliente);
?>
