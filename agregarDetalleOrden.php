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
if (isset($_GET['idordencompra'])) {
  $colname_rsDetalleCotizacion = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalleCotizacion = sprintf("SELECT *,
    (SELECT SUM(dor.cantidad) FROM detalleorden dor WHERE idpartida = sb.idsubcotizacionarticulo) AS pedido,
    a.idarticulo
    FROM subcotizacionarticulo sb,articulo a,ordencompra o WHERE o.idordencompra = %s AND sb.idarticulo = a.idarticulo AND o.idcotizacion = sb.idsubcotizacion ORDER BY idsubcotizacionarticulo ASC ", GetSQLValueString($colname_rsDetalleCotizacion, "int"));
$rsDetalleCotizacion = mysql_query($query_rsDetalleCotizacion, $tecnocomm) or die(mysql_error());
$row_rsDetalleCotizacion = mysql_fetch_assoc($rsDetalleCotizacion);
$totalRows_rsDetalleCotizacion = mysql_num_rows($rsDetalleCotizacion);

$colname_rsOrdenCompra = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsOrdenCompra = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenCompra = sprintf("SELECT *  FROM ordencompra o,subcotizacion sb,proveedor p WHERE o.idcotizacion = sb.idsubcotizacion AND o.idproveedor = p.idproveedor AND o.idordencompra = %s", GetSQLValueString($colname_rsOrdenCompra, "int"));
$rsOrdenCompra = mysql_query($query_rsOrdenCompra, $tecnocomm) or die(mysql_error());
$row_rsOrdenCompra = mysql_fetch_assoc($rsOrdenCompra);
$totalRows_rsOrdenCompra = mysql_num_rows($rsOrdenCompra);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/funciones.js"></script>

<style type="text/css">
<!--
.Estilo1 {
	color: #FFFFFF;
	font-size: 17px;
}
-->
</style>
</head>

<body >
    <table width="1036" border="0" align="center" class="wrapper">
  <tr class="titulos">
    <td colspan="7" align="center" class="Estilo1" >AGREGAR ARTICULOS A ORDEN DE COMPRA</td>
    </tr>
  <tr>
    <td width="100">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td colspan="4">&nbsp;</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="6" align="center"><label></label></td>
    </tr>
        <tr class="titleTabla">
            <td width="58" height="20" valign="top">&nbsp;</td>
                <td width="78" valign="top">CODIGO</td>
                <td width="72" valign="top">MARCA</td>
                <td width="264" valign="top">DESCRIPCION</td>
                <td width="42" valign="top">CANT.</td>
                <td width="74" valign="top">P. UNITARIO</td>
                <td width="74" valign="top">OPCIONES</td>
              </tr>
  <tr>
    <td colspan="7">&nbsp;</td>
    </tr>
          <?php do { ?>
            <tr onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
                <td width="59" height="24" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
              <td width="74" valign="top"><div align="center"><?php echo $row_rsDetalleCotizacion['codigo']; ?></div></td>
              <td width="75" valign="top"><div align="center"><?php echo $row_rsDetalleCotizacion['marca']; ?></div></td>
              <td width="240" valign="top"><?php echo $row_rsDetalleCotizacion['nombre']; ?></td>
              <td width="41" valign="top"><div align="center"><?php $pedido = ($row_rsDetalleCotizacion['pedido'])?$row_rsDetalleCotizacion['pedido']:0; echo$pedido; ?>/<?php echo $row_rsDetalleCotizacion['cantidad']; ?></div></td>
              <td width="73" valign="top"><div align="right"><?php echo $row_rsDetalleCotizacion['precio']; ?> </div></td>
              <td width="73" valign="top"><div align="right"><a href="asignarDetalleOrdenCatalago.php?idordencompra=<?php echo $_GET['idordencompra']?>&idpartida=<?php echo $row_rsDetalleCotizacion['idsubcotizacionarticulo']; ?>" onclick="NewWindow(this.href,'Asignar',800,800,'yes');return false;"><img src="images/Checkmark.png" width="24" height="24" /></a></div></td>
            </tr>
            <?php } while ($row_rsDetalleCotizacion = mysql_fetch_assoc($rsDetalleCotizacion)); ?>

</table>
    
<!--    
<table width="740" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  DWLayoutTable
  <tr>
    <td height="20" colspan="5" valign="top">AGREGAR ARTICULO A ORDEN DE COMPRA</td>
  </tr>
  <tr>
    <td width="108" height="13"></td>
    <td width="352"></td>
    <td width="181"></td>
    <td width="86"></td>
    <td width="11"></td>
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
      DWLayoutTable
      <tr>
        <td width="765" height="21" valign="top">PRODUCTOS DE ORDEN DE COMPRA:</td>
        </tr>
      <tr>
        <td height="20" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          DWLayoutTable
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
          DWLayoutTable
          <?php do { ?>
            <tr>
                <td width="59" height="24" valign="top">DWLayoutEmptyCell&nbsp;</td>
              <td width="74" valign="top"><div align="center"><?php echo $row_rsDetalleCotizacion['codigo']; ?></div></td>
              <td width="75" valign="top"><div align="center"><?php echo $row_rsDetalleCotizacion['marca']; ?></div></td>
              <td width="240" valign="top"><?php echo $row_rsDetalleCotizacion['nombre']; ?></td>
              <td width="12">&nbsp;</td>
              <td width="41" valign="top"><div align="center"><?php $pedido = ($row_rsDetalleCotizacion['pedido'])?$row_rsDetalleCotizacion['pedido']:0; echo$pedido; ?>/<?php echo $row_rsDetalleCotizacion['cantidad']; ?></div></td>
              <td width="45" valign="top"><div align="center"><?php echo $row_rsDetalleCotizacion['medida']; ?></div></td>
              <td width="73" valign="top"><div align="right"><?php echo $row_rsDetalleCotizacion['precio']; ?> </div></td>
              <td width="62" valign="top"><div align="right"><?php echo format_money($row_rsDetalleCotizacion['precio'] *$row_rsDetalleCotizacion['cantidad'] ); ?></div></td>
              <td width="73" valign="top"><div align="right"><a href="asignarDetalleOrden.php?idordencompra=<?php echo $_GET['idordencompra']?>&idpartida=<?php echo $row_rsDetalleCotizacion['idsubcotizacionarticulo']; ?>" onclick="NewWindow(this.href,'Asignar',800,800,'yes');return false;"><img src="images/Checkmark.png" width="24" height="24" /></a></div></td>
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
</table>-->
</body>
</html>
<?php
mysql_free_result($rsDetalleCotizacion);

mysql_free_result($rsOrdenCompra);
?>
