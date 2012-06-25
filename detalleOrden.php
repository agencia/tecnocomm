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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "almacen")) {
  $updateSQL = sprintf("UPDATE detalleorden SET  almacen=%s WHERE identificador=%s",
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['identificador'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
}

$maxRows_detalleOrden = 30;
$pageNum_detalleOrden = 0;
if (isset($_GET['pageNum_detalleOrden'])) {
  $pageNum_detalleOrden = $_GET['pageNum_detalleOrden'];
}
$startRow_detalleOrden = $pageNum_detalleOrden * $maxRows_detalleOrden;

$colname_detalleOrden = "-1";
if (isset($_GET['idorden'])) {
  $colname_detalleOrden = $_GET['idorden'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_detalleOrden = sprintf("SELECT d.almacen,d.idpartida,d.identificador,(SELECT descri FROM subcotizacionarticulo sb WHERE sb.idsubcotizacionarticulo = d.idpartida) AS descproducto,d.cantidad,SUM(dpo.cantidadsurtida) AS suma FROM detalleorden d LEFT JOIN detalleproductoorden dpo ON d.identificador = dpo.iddetalleorden WHERE d.idordencompra = %s GROUP BY d.identificador ORDER BY idpartida ASC", GetSQLValueString($colname_detalleOrden, "int"));
$query_limit_detalleOrden = sprintf("%s LIMIT %d, %d", $query_detalleOrden, $startRow_detalleOrden, $maxRows_detalleOrden);
$detalleOrden = mysql_query($query_limit_detalleOrden, $tecnocomm) or die(mysql_error());
$row_detalleOrden = mysql_fetch_assoc($detalleOrden);

if (isset($_GET['totalRows_detalleOrden'])) {
  $totalRows_detalleOrden = $_GET['totalRows_detalleOrden'];
} else {
  $all_detalleOrden = mysql_query($query_detalleOrden);
  $totalRows_detalleOrden = mysql_num_rows($all_detalleOrden);
}
$totalPages_detalleOrden = ceil($totalRows_detalleOrden/$maxRows_detalleOrden)-1;

$colname_rsOrden = "-1";
if (isset($_GET['idorden'])) {
  $colname_rsOrden = $_GET['idorden'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrden = sprintf("SELECT oc.idordencompra,sb.idsubcotizacion,sb.identificador2,clc.nombre AS nombrecontacto,cl.nombre AS nombrecliente FROM ordencompra oc, subcotizacion sb,cotizacion c,cliente cl,contactoclientes clc WHERE oc.idcotizacion = sb.idsubcotizacion AND sb.idcotizacion = c.idcotizacion AND c.idcliente = cl.idcliente AND clc.idcontacto = sb.contacto AND oc.idordencompra = %s", GetSQLValueString($colname_rsOrden, "int"));
$rsOrden = mysql_query($query_rsOrden, $tecnocomm) or die(mysql_error());
$row_rsOrden = mysql_fetch_assoc($rsOrden);
$totalRows_rsOrden = mysql_num_rows($rsOrden);
?>

<link href="multibox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/mootools2.js"></script>
<script type="text/javascript" src="js/overlay.js"></script>
<script type="text/javascript" src="js/multibox.js"></script>

<table width="900" border="0" cellpadding="0" cellspacing="0" class="wrapper">
  <!--DWLayoutTable-->
  <tr>
    <td height="28" colspan="3" valign="top" class="titulos">Detalle Para La orden de Compra</td>
  </tr>
  <tr>
    <td width="62" height="24">&nbsp;</td>
    <td width="764">&nbsp;</td>
    <td width="72">&nbsp;</td>
  </tr>
  <tr>
    <td height="64" colspan="3" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="53" height="20"></td>
          <td width="76" align="right" valign="top">Cotizacion:</td>
          <td width="8">&nbsp;</td>
          <td width="314" valign="top"><?php echo $row_rsOrden['identificador2']; ?></td>
          <td width="119">&nbsp;</td>
          <td width="135" rowspan="2" valign="top"><a href="printOrdenCompra.php?idordencompra=<?php echo $_GET['idorden'];?>" onclick="NewWindow(this.href,'Imprimir Orden Compra','900','800','yes'); return false;"><img src="images/bullet_16.jpg" width="26" height="22" align="middle" />Ver Reporte</a></td>
          <td width="18">&nbsp;</td>
          <td width="110" rowspan="2" valign="top"><a href="index.php?mod=ordenCompra"><img src="images/bullet_16.jpg" width="26" height="22" align="middle" />Regresar</a></td>
          <td width="75">&nbsp;</td>
      </tr>
      <tr>
        <td height="22"></td>
          <td align="right" valign="top">Cliente:</td>
          <td>&nbsp;</td>
          <td valign="top"><?php echo $row_rsOrden['nombrecliente']; ?></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="22"></td>
          <td align="right" valign="top">Contacto:</td>
          <td>&nbsp;</td>
          <td valign="top"><?php echo $row_rsOrden['nombrecontacto']; ?></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
      </tr>
      
      
    </table></td>
  </tr>
  <tr>
    <td height="21">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="56">&nbsp;</td>
    <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="126" height="27" valign="middle">Opciones</td>
          <td width="60" valign="middle">Partida</td>
          <td width="349" valign="middle">Descripcion</td>
          <td width="118" valign="middle">Estado</td>
          <td width="180" valign="middle">Almacen</td>
        </tr>
      <tr>
        <td colspan="5" valign="top">
          <table width="100%" border="0" cellpadding="0" cellspacing="0" class="resaltarTabla">
            <!--DWLayoutTable-->
            <?php do { ?>
              <tr>
                <td width="126" height="24" align="right" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
                  <td width="59" valign="top"><?php echo ++$i;?></td>
                  <td width="350" valign="top"><?php echo $row_detalleOrden['descproducto'];?></td>
                  <td width="118" valign="top"><?php $val = ($row_detalleOrden['almacen'] > 0)?$row_detalleOrden['almacen']:0; $sur = ($row_detalleOrden['suma'])?$row_detalleOrden['suma']:0; echo 
		$sur."/".($row_detalleOrden['cantidad']-$val);?>
                  <a href="detalleModificar.php?idpartida=<?php echo $row_detalleOrden['identificador'];?>" onClick="NewWindow(this.href,'Modificar Detalle',825,500,'yes'); return false;"><?php if(($sur - ($row_detalleOrden['cantidad']-$val))!=0){ ?><img src="images/Checkmark.png" alt="Surtido" width="24" height="24" /><?php } else {?><img src="images/CheckmarkGreen.png" alt="Surtido" width="24" height="24" /><?php } ?></a></td>
                  <td width="180" valign="top"><a href="ordenAlmacen.php?identificador=<?php echo $row_detalleOrden['identificador'];?>" onClick="NewWindow(this.href,'Modificar Detalle',300,150,'yes'); return false;"><?php echo $val;?></a></td>
                </tr>
              <?php } while ($row_detalleOrden = mysql_fetch_assoc($detalleOrden)); ?>
            </table>           </td>
        </tr>
      
    </table></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="236"></td>
    <td></td>
    <td></td>
  </tr>
</table>
 <div class="multiBoxDesc mb10"></div>
<script type="text/javascript">
			var box = {};
			window.addEvent('domready', function(){
				box = new MultiBox('mb', {descClassName: 'multiBoxDesc', useOverlay: true});
			});
		</script>
<?php
mysql_free_result($detalleOrden);

mysql_free_result($rsOrden);
?>
