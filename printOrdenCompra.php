<?php require_once('Connections/tecnocomm.php'); ?>
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

$colname_rsDetalle = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsDetalle = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT p.idproveedor, p.nombrecomercial, dpo.cantidadsolicitada, sb.descri, a.codigo, a.marca,dpo.unidad, p.domicilio, p.contacto, p.email,p.telefono FROM detalleorden don, detalleproductoorden dpo, proveedor p, subcotizacionarticulo sb,articulo a WHERE don.identificador = dpo.iddetalleorden AND dpo.idproveedor = p.idproveedor AND don.idpartida = sb.idsubcotizacionarticulo  AND a.idarticulo = sb.idarticulo AND don.idordencompra = %s", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);

$colname_rsCotizacion = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsCotizacion = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT * FROM ordencompra oc,subcotizacion sb WHERE  oc.idcotizacion = sb.idsubcotizacion AND oc.idordencompra = %s", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);
?>
<?php 

$i=0;
do{

	$proveedores[$row_rsDetalle['idproveedor']]['nombre']=$row_rsDetalle['nombrecomercial'];
	$proveedores[$row_rsDetalle['idproveedor']]['contacto']=$row_rsDetalle['contacto'];
	$proveedores[$row_rsDetalle['idproveedor']]['direccion']=$row_rsDetalle['domicilio'];
	$proveedores[$row_rsDetalle['idproveedor']]['email']=$row_rsDetalle['email'];
	$proveedores[$row_rsDetalle['idproveedor']]['telefono']=$row_rsDetalle['telefono'];
	$proveedores[$row_rsDetalle['idproveedor']]['idproveedor']=$row_rsDetalle['idproveedor'];
	
	
	$proveedores[$row_rsDetalle['idproveedor']]['articulos'][$i]['cantidad']=$row_rsDetalle['cantidadsolicitada'];
	$proveedores[$row_rsDetalle['idproveedor']]['articulos'][$i]['descripcion']=$row_rsDetalle['descri'];
	$proveedores[$row_rsDetalle['idproveedor']]['articulos'][$i]['codigo']=$row_rsDetalle['codigo'];
	$proveedores[$row_rsDetalle['idproveedor']]['articulos'][$i]['marca']=$row_rsDetalle['marca'];
	$proveedores[$row_rsDetalle['idproveedor']]['articulos'][$i]['unidad']=$row_rsDetalle['unidad']; 

$i++;
}while($row_rsDetalle = mysql_fetch_assoc($rsDetalle));

//print_r($proveedores);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Imprimir Orden de Compra</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="840" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="28" colspan="4" valign="top" class="titulos">Orden De Compra</td>
  </tr>
  <tr>
    <td width="50" height="15"></td>
    <td width="134"></td>
    <td width="335"></td>
    <td width="319"></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td valign="top">ORDEN DE COMPRA:</td>
    <td valign="top"><?php echo $row_rsCotizacion['identificador2']; ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="29"></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="235"></td>
    <td colspan="3" valign="top">
    <?php foreach($proveedores as $proveedor) { ?><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      
      <tr>
        <td width="88" height="20" valign="top">PROVEEDOR:</td>
          <td colspan="3" valign="top"><?php echo $proveedor['nombre']; ?></td>
          <td width="124">&nbsp;</td>
          <td width="74" valign="top"><a href="PrintOrdenCompraPDF.php?idordencompra=<?php echo $_GET['idordencompra'];?>&idproveedor=<?php echo $proveedor['idproveedor']; ?>" ><img src="images/Imprimir2.png" width="24" height="24" /></a></td>
        </tr>
      <tr>
        <td height="1"></td>
          <td width="328"></td>
          <td width="88"></td>
          <td width="78"></td>
          <td></td>
          <td></td>
        </tr>
      <tr>
        <td height="2"></td>
          <td></td>
          <td rowspan="2" valign="top">TELEFONO:</td>
          <td colspan="2" rowspan="2" valign="top"><?php echo $proveedor['telefono']; ?></td>
          <td></td>
        </tr>
      
      <tr>
        <td height="20" valign="top">CONTACTO:</td>
          <td valign="top"><?php echo $proveedor['contacto']; ?></td>
          <td></td>
        </tr>
      
      <tr>
        <td height="1"></td>
          <td rowspan="3" valign="top"><?php echo $proveedor['direccion']; ?></td>
          <td></td>
          <td colspan="2" rowspan="3" valign="top"><?php echo $proveedor['correo']; ?></td>
          <td></td>
        </tr>
      <tr>
        <td height="1"></td>
          <td rowspan="2" valign="top">CORREO:</td>
          <td></td>
        </tr>
      <tr>
        <td height="20" valign="top">DIRECCION:</td>
          <td></td>
        </tr>
      <tr>
        <td height="51" colspan="6" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
       <tr class="titleTabla">
        <td height="20" valign="top">CODIGO</td>
          <td valign="top">DESCRIPCION</td>
          <td valign="top">MARCA</td>
          <td colspan="2" valign="top">CANTIDAD</td>
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
      <?php foreach($proveedor['articulos'] as $articulo){?>
      <tr>
        <td height="18" valign="top"><? echo $articulo['codigo'] ?></td>
          <td valign="top"><? echo $articulo['descripcion'] ?></td>
          <td valign="top"><? echo $articulo['marca'] ?></td>
          <td valign="top"><? echo $articulo['cantidad'] ?></td>
          <td valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
          <td></td>
        </tr>
      <?php } ?>

        </table>        </td>
        </tr>
      <tr>
        <td height="20">&nbsp;</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
     </table>
    <?php } ?></td>
  </tr>
  <tr>
    <td height="96"></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsDetalle);

mysql_free_result($rsCotizacion);
?>
