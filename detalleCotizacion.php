<?php require_once('Connections/tecnocomm.php'); ?>
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

$colname_rsDetalleCotizacion = "-1";
if (isset($_GET['idcotizacion'])) {
  $colname_rsDetalleCotizacion = $_GET['idcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalleCotizacion = sprintf("SELECT *,(SELECT SUM(dor.cantidad) FROM detalleorden dor WHERE idpartida = sb.idsubcotizacionarticulo) AS pedido FROM subcotizacionarticulo sb,articulo a WHERE  sb.idarticulo = a.idarticulo AND sb.idsubcotizacion = %s ORDER BY idsubcotizacionarticulo ASC ", GetSQLValueString($colname_rsDetalleCotizacion, "int"));
$rsDetalleCotizacion = mysql_query($query_rsDetalleCotizacion, $tecnocomm) or die(mysql_error());
$row_rsDetalleCotizacion = mysql_fetch_assoc($rsDetalleCotizacion);
$totalRows_rsDetalleCotizacion = mysql_num_rows($rsDetalleCotizacion);



$colname_rsDetalle = "-1";
if (isset($_GET['idcotizacion'])) {
  $colname_rsDetalle = $_GET['idcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT * FROM ordencompra o, articulo a, detalleorden don WHERE o.idcotizacion = %s AND don.idordencompra = o.idordencompra AND a.idarticulo = don.idarticulo AND don.idpartida = 0", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="js/funciones.js"></script>
</head>

<body>
<table width="740" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="20" colspan="5" valign="top" class="realte">ORDEN DE COMPRA</td>
  </tr>

  
  <tr>
    <td height="13"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="88" colspan="5" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="765" height="21" valign="top" class="realte">DENTRO DE LA COTIZACION:</td>
        </tr>
      <tr>
        <td height="20" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <tr>
            <td width="58" height="20" valign="top">PARTIDA</td>
                <td width="78" valign="top">CODIGO</td>
                <td width="72" valign="top">MARCA</td>
                <td width="264" valign="top">DESCRIPCION</td>
                <td width="42" valign="top">CANT.</td>
                <td width="44" valign="top">U.MED</td>
                <td width="74" valign="top">P. UNITARIO</td>
                <td width="59" valign="top">IMPORTE</td>
                <td width="74" valign="top">OPCIONES</td>
              </tr>
          </table>        </td>
        </tr>
      <tr>
        <td height="21" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <?php do { ?>
            <tr>
                <td width="59" height="24" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
              <td width="74" valign="top"><div align="center"><?php echo $row_rsDetalleCotizacion['codigo']; ?></div></td>
              <td width="75" valign="top"><div align="center"><?php echo $row_rsDetalleCotizacion['marca']; ?></div></td>
              <td width="240" valign="top"><?php echo $row_rsDetalleCotizacion['nombre']; ?></td>
              <td width="12">&nbsp;</td>
              <td width="41" valign="top"><div align="center"><?php $pedido = ($row_rsDetalleCotizacion['pedido'])?$row_rsDetalleCotizacion['pedido']:0; echo$pedido; ?>/<?php echo $row_rsDetalleCotizacion['cantidad']; ?></div></td>
              <td width="45" valign="top"><div align="center"><?php echo $row_rsDetalleCotizacion['medida']; ?></div></td>
              <td width="73" valign="top"><div align="right"><?php echo $row_rsDetalleCotizacion['precio']; ?> </div></td>
              <td width="62" valign="top"><div align="right"><?php echo format_money($row_rsDetalleCotizacion['precio'] *$row_rsDetalleCotizacion['cantidad'] ); ?></div></td>
              <td width="73" valign="top"><div align="center"><?php if( ($row_rsDetalleCotizacion['cantidad'] -  $row_rsDetalleCotizacion['pedido']) <= 0){?><img src="images/verde.gif" width="10" height="10" /><?php } else {?><img src="images/rojo.gif" width="10" height="10" /><?php } ?></div></td>
            </tr>
            <?php } while ($row_rsDetalleCotizacion = mysql_fetch_assoc($rsDetalleCotizacion)); ?>
</table>        </td>
        </tr>
      
      
      
    </table></td>
  </tr>
  <tr>
    <td height="22">&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
<table width="740" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td width="740" height="20" valign="top" class="realte">FUERA DE LA COTIZACION:</td>
  </tr>
  <tr>
    <td height="94"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <?php do { ?>
            <tr>
                <td width="59" height="24" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
              <td width="74" valign="top"><div align="center"><?php echo $row_rsDetalle['codigo']; ?></div></td>
              <td width="75" valign="top"><div align="center"><?php echo $row_rsDetalle['marca']; ?></div></td>
              <td width="245" valign="top"><?php echo $row_rsDetalle['nombre']; ?></td>
              <td width="41" valign="top"><div align="center"><?php echo $row_rsDetalle['cantidad']; ?></div></td>
              <td width="45" valign="top"><div align="center"><?php echo $row_rsDetalle['medida']; ?></div></td>
              <td width="73" valign="top"><div align="right"><?php echo $row_rsDetalle['costo']; ?></div></td>
              <td width="62" valign="top"><div align="right"><?php echo format_money($row_rsDetalle['costo']*$row_rsDetalle['cantidad']); ?></div></td>
              <td width="73" valign="top"><div align="right"></div></td>
            </tr>
            <?php } while ($row_rsDetalle = mysql_fetch_assoc($rsDetalle)); ?>
</table>   </td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsDetalleCotizacion);

mysql_free_result($rsDetalle);
?>

