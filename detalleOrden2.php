<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php'); ?>
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

$colname_rsOrdenCompra = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsOrdenCompra = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenCompra = sprintf("SELECT *,(SELECT identificador2 FROM subcotizacion s WHERE s.idsubcotizacion = o.idcotizacion ) AS titulocotizacion FROM ordencompra o, proveedor p WHERE o.idordencompra = %s AND p.idproveedor = o.idproveedor", GetSQLValueString($colname_rsOrdenCompra, "int"));
$rsOrdenCompra = mysql_query($query_rsOrdenCompra, $tecnocomm) or die(mysql_error());
$row_rsOrdenCompra = mysql_fetch_assoc($rsOrdenCompra);
$totalRows_rsOrdenCompra = mysql_num_rows($rsOrdenCompra);

$colname_rsDetalle = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsDetalle = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT * FROM articulo a,detalleorden o WHERE a.idarticulo = o.idarticulo AND o.idordencompra = %s ORDER BY o.idpartida ASC", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);


$tip=array(0=>"PL",1=>"C");

$signo = array(0=>"$",1=>"US$");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="js/funciones.js"></script>
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
</head>

<body>
<?php $moneda = array("Pesos","Dolares");?>
<table width="790" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="28" colspan="5" valign="top" class="titulos">DETALLE DE ORDEN DE COMPRA</td>
  </tr>
  <tr>
    <td width="11" height="13"></td>
    <td width="342"></td>
    <td width="205"></td>
    <td width="199"></td>
    <td width="12"></td>
  </tr>
  <tr>
    <td height="81"></td>
    <td colspan="3" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td height="20" colspan="5" valign="top" class="realte">DATOS DE PROVEEDOR</td>
        </tr>
      <tr>
        <td width="157" height="20" valign="top">PROVEEDOR:</td>
          <td colspan="3" valign="top"><?php echo $row_rsOrdenCompra['nombrecomercial']; ?></td>
          <td width="148">&nbsp;</td>
      </tr>
      <tr>
        <td height="20" valign="top">CONTACTO:</td>
          <td colspan="3" valign="top"><?php echo $row_rsOrdenCompra['contacto']; ?></td>
          <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="20" valign="top">TELEFONO:</td>
          <td width="267" valign="top"><?php echo $row_rsOrdenCompra['telefono']; ?></td>
          <td width="61" valign="top">EMAIL:</td>
          <td colspan="2" valign="top"><?php echo $row_rsOrdenCompra['email']; ?></td>
        </tr>
      <tr>
        <td height="1"></td>
        <td></td>
        <td></td>
        <td width="132"></td>
        <td></td>
      </tr>
      
      
      
    </table></td>
  <td></td>
  </tr>
  <tr>
    <td height="10"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="166"></td>
    <td colspan="3" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td height="20" colspan="6" align="left" valign="top" class="realte">DATOS ORDE DE COMPA:</td>
        </tr>
      <tr>
        <td width="158" height="20" valign="top" align="right">CONCEPTO:</td>
          <td colspan="4" valign="top"><span class="style1">
            <?php if($row_rsOrdenCompra['tipoorden'] == 0){ echo "Cotizacion: ".$row_rsOrdenCompra['titulocotizacion'];  } else { echo $row_rsOrdenCompra['tituloconcepto']; } ?>
          </span></td>
          <td width="53">&nbsp;</td>
        </tr>
      <tr>
        <td width="158" height="20" valign="top" align="right">FORMA DE PAGO:</td>
          <td colspan="4" valign="top"><?php echo $row_rsOrdenCompra['formapago']; ?></td>
          <td width="53">&nbsp;</td>
        </tr>
      <tr>
        <td height="20" valign="top" align="right">MONEDA:</td>
          <td colspan="4" valign="top"><?php echo $moneda[$row_rsOrdenCompra['moneda']]; ?></td>
          <td>&nbsp;</td>
        </tr>
      <tr>
        <td height="20" valign="top" align="right">VIGENCIA:</td>
          <td colspan="4" valign="top"><?php echo $row_rsOrdenCompra['vigencia']; ?></td>
          <td></td>
        </tr>
      <tr>
        <td height="20" valign="top" align="right">TIEMPO DE ENTREGA:</td>
          <td colspan="4" valign="top"><?php echo $row_rsOrdenCompra['tiempoentrega']; ?></td>
          <td></td>
        </tr>
      <tr>
        <td height="23" valign="top" align="right">DESCUENTO:</td>
          <td colspan="4" valign="top"><?php echo $row_rsOrdenCompra['descuento']; ?></td>
          <td></td>
        </tr>
      <tr>
        <td height="30" valign="top">    <input type="button" name="Submit" value="cerrar" onclick="window.location='close.php'" /></td>
          <td width="237">&nbsp;</td>
          <td width="132" valign="top"><a href="printOrdenCompraGeneral.php?idordencompra=<?php echo $row_rsOrdenCompra['idordencompra']; ?>" ><img src="images/Imprimir2.png" width="24" height="24" />IMPRIIR</a></td>
          <td width="63">&nbsp;</td>
          <td colspan="2" valign="top"><a href="ordenCompraModificarDatos.php?idordencompra=<?php echo $row_rsOrdenCompra['idordencompra']; ?>" onclick="NewWindow(this.href,'Modificar Datos de Orden de Compra',600,400,'yes');return false;"><img src="images/Edit.png" alt="" width="24" height="24" title="Modifca los Datos de Generales de La orden de compra" />Modificar Datos</a></td>
      </tr>
      <tr>
        <td height="13"></td>
        <td></td>
        <td></td>
        <td></td>
        <td width="99"></td>
        <td></td>
      </tr>
    </table></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="28"></td>
    <td>&nbsp;</td>
    <td valign="top"><a href="compra_nueva_buscar_articulo.php?idordencompra=<?php echo $_GET['idordencompra']; ?>" onclick="NewWindow(this.href,'Agregar de Cotizacion',1150,600,'yes');return false;">AGREGAR DE CATALAGO</a></td>
    <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="27" height="28">&nbsp;</td>
        <td width="172" valign="top"><?php if($row_rsOrdenCompra['tipoorden'] == 0){?><a href="agregarDetalleOrden.php?idordencompra=<?php echo $_GET['idordencompra']; ?>" onclick="NewWindow(this.href,'Agregar de Cotizacion',800,800,'yes');return false;">AGREGAR DE COTIZACION</a><?php } ?></td>
        </tr>
    </table></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="65"></td>
    <td colspan="3" valign="top">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="800" height="21" valign="top" class="realte">PRODUCTOS DE ORDEN DE COMPRA:</td>
        </tr>
      <tr>
        <td height="20" valign="top">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <tr class="titleTabla">
            <td width="50" height="20" valign="top">PART</td>
                <td width="78" valign="top">CODIGO</td>
                <td width="72" valign="top">MARCA</td>
                <td width="240" valign="top">DESCRIPCION</td>
                <td width="42" valign="top">CANT.</td>
                <td width="44" valign="top">U.MED</td>
                <td width="70" valign="top">P. UNIT</td>
                <td width="74" valign="top">P. C/DESC</td>
                <td width="59" valign="top">IMPORTE</td>
                <td width="40" valign="top">OPC</td>
                <td width="40" valign="top">INFO</td>
              </tr>
          </table>        </td>
        </tr>
      <tr>
        <td height="21" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <?php do { ?>
            <tr onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
              <td width="59" height="21" align="center" valign="top"><?php  echo ++$i;?></td>
              <td width="74" align="center" valign="top"><?php echo $row_rsDetalle['codigo']; ?></td>
              <td width="75" align="center" valign="top"><?php echo $row_rsDetalle['marca']; ?></td>
              <td width="280" valign="top"><?php echo $row_rsDetalle['descri']; ?></td>
              <td width="41" align="center" valign="top"><?php echo $row_rsDetalle['cantidad']; ?></td>
              <td width="45" align="center" valign="top"><?php echo $row_rsDetalle['medida']; ?></td>
              <td width="73" align="right" valign="top"><?php echo $row_rsDetalle['costo']; ?></td>
              <td width="73" align="right" valign="top"><?php echo round($row_rsDetalle['costo']-(($row_rsDetalle['descuento']/100)*$row_rsDetalle['costo']),2); ?></td>
              <td width="62" align="right" valign="top"><?php echo format_money($row_rsDetalle['cantidad'] * round(($row_rsDetalle['costo']-(($row_rsDetalle['descuento']/100)*$row_rsDetalle['costo'])),2));?></td>
              <td width="73" align="right" valign="top"><a href="detalleOrdenEliminar.php?iddetalleorden=<?php echo $row_rsDetalle['identificador']; ?>" onclick="NewWindow(this.href,'Quitar Producto de Orden',800,600,'yes'); return false;" ><img src="images/eliminar.gif" width="19" height="19" /></a><a href="detalleOrdenModificarProducto.php?iddetalleorden=<?php echo $row_rsDetalle['identificador']; ?>" onclick="NewWindow(this.href,'Modificar Producto Orden',800,400,'yes'); return false;" ><img src="images/Edit.png" width="24" height="24" /></a></td>
              <td width="40" align="right" valign="top"><?php echo $signo[$row_rsDetalle['moneda']]; ?>&nbsp;&nbsp;<?php echo $tip[$row_rsDetalle['tipo']]; ?></td>
            </tr>
            <?php } while ($row_rsDetalle = mysql_fetch_assoc($rsDetalle)); ?>
          </table>        </td>
        </tr>
      
      
      
    </table></td>
    <td></td>
  </tr>
  <tr>
    <td height="42"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsOrdenCompra);

mysql_free_result($rsDetalle);
?>
