<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('numtoletras.php');?>
<?php require_once('utils.php');?>
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

$maxRows_rsFactura = 10;
$pageNum_rsFactura = 0;
if (isset($_GET['pageNum_rsFactura'])) {
  $pageNum_rsFactura = $_GET['pageNum_rsFactura'];
}
$startRow_rsFactura = $pageNum_rsFactura * $maxRows_rsFactura;

$colname_rsFactura = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsFactura = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFactura = sprintf("SELECT * FROM factura f LEFT JOIN cliente c ON f.idcliente = c.idcliente WHERE idfactura = %s", GetSQLValueString($colname_rsFactura, "int"));
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

$colname_rsDetalle = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsDetalle = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT * FROM detallefactura WHERE idfactura = %s", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Imprimir Factura</title>
</head>

<body>
<table width="800" border="0" cellpadding="0" cellspacing="0" background="images/factura.jpg" >
  <!--DWLayoutTable-->
  <tr>
    <td width="38" height="219">&nbsp;</td>
    <td width="23">&nbsp;</td>
    <td width="56">&nbsp;</td>
    <td width="172">&nbsp;</td>
    <td width="18">&nbsp;</td>
    <td width="18">&nbsp;</td>
    <td width="188">&nbsp;</td>
    <td width="15">&nbsp;</td>
    <td width="45">&nbsp;</td>
    <td width="37">&nbsp;</td>
    <td width="14">&nbsp;</td>
    <td width="40">&nbsp;</td>
    <td width="41">&nbsp;</td>
    <td width="17">&nbsp;</td>
    <td width="31">&nbsp;</td>
    <td width="9">&nbsp;</td>
    <td width="38">&nbsp;</td>
  </tr>
  <tr>
    <td height="20">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="5" valign="top"><?php echo $row_rsFactura['razonsocial']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="18">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="20"></td>
    <td></td>
    <td></td>
    <td colspan="5" valign="top"><?php echo $row_rsFactura['direccionfacturacion']; ?>  </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="13"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
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
    <td valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="2"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="2" rowspan="2" valign="top"><?php echo $row_rsFactura['rfc']; ?></td>
    <td></td>
    <td rowspan="2" align="center" valign="top"><?php $fecha=split("-",$row_rsFactura['fecha']); echo $fecha[2]; ?></td>
    <td></td>
    <td colspan="2" rowspan="2" align="center" valign="top"><?php echo $fecha[1]; ?></td>
    <td></td>
    <td rowspan="2" align="center" valign="top"><?php echo $fecha[0];?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td></td>
    <td></td>
    <td colspan="2" valign="top"><?php echo $row_rsFactura['ciudadfacturacion']; ?></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="49">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>

  <tr>
    <td height="400" valign="top">&nbsp;</td>
    <td colspan="15" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
    <?php $subtotal=$i=0;?>
  <?php do { ?>
  <?php $i++; ?>
    <tr>
      <td width="42" height="25" align="center" valign="top"><?php echo $i; ?></td>
      <td width="67" align="center" valign="top"><?php echo $row_rsDetalle['cantidad']; ?></td>
      <td width="55" align="center" valign="top"><?php echo $row_rsDetalle['unidad']; ?></td>
      <td width="359" align="center" valign="top"><?php echo $row_rsDetalle['concepto']; ?></td>
      <td width="103" align="center" valign="top"><?php echo format_money($row_rsDetalle['punitario']); ?></td>
      <td width="98" align="right" valign="top"><?php $importe=$row_rsDetalle['cantidad']*$row_rsDetalle['punitario']; echo format_money($importe);?> </td>
      </tr>
    <?php $subtotal = $subtotal + $importe; ?>
    <?php } while ($row_rsDetalle = mysql_fetch_assoc($rsDetalle)); ?>
    </table>    </td>
    <td></td>
  </tr>
  <tr>
    <td height="32"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
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
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" rowspan="2" align="right" valign="middle"><?php echo format_money($importe); ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="13"></td>
    <td></td>
    <td colspan="5" rowspan="4" valign="top"><?php $t = $subtotal*1.15;  echo num2letras(money_format('%!n',$t),false,true,$row_rsFactura['moneda']) ; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="18"></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="26"></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" align="right" valign="middle"><?php echo format_money($subtotal *.15); ?></td>
    <td></td>
    <td></td>
  </tr>
  
  
  <tr>
    <td height="39"></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" align="right" valign="middle"><?php  echo format_money($subtotal*1.15); ?></td>
    <td></td>
    <td></td>
  </tr>
  
  
  <tr>
    <td height="105"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsFactura);

mysql_free_result($rsDetalle);
?>
