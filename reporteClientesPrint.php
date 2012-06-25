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

$query = "SELECT cl.idcliente, cl.nombre, cl.abreviacion, f.numfactura, f.tipo, f.moneda, f.fecha, f.fechapago, f.estado,(select (sum(punitario*cantidad)*1.15) from detallefactura de where de.idfactura=f.idfactura) as tot, (SELECT (SELECT identificador2 FROM subcotizacion sb WHERE sb.idsubcotizacion = fc.idcotizacion) FROM facturacotizacion fc WHERE fc.idfactura = f.idfactura)  AS idcotizacionfactura FROM cliente cl,factura f WHERE cl.idcliente = f.idcliente";

if(isset($_GET['buscar'])){
						$query = sprintf("SELECT cl.idcliente, cl.nombre, cl.abreviacion, f.numfactura, f.tipo, f.moneda, f.fecha, f.fechapago, f.estado, (select (sum(punitario*cantidad)*1.15) from detallefactura de where de.idfactura=f.idfactura) as tot,  (SELECT (SELECT identificador2 FROM subcotizacion sb WHERE sb.idsubcotizacion = fc.idcotizacion) FROM facturacotizacion fc WHERE fc.idfactura = f.idfactura)  AS idcotizacionfactura FROM cliente cl,factura f WHERE cl.idcliente = f.idcliente AND cl.nombre like %s",GetSQLValueString('%'.$_GET['buscar'].'%', "text"));
					}

if(isset($_GET['filtrar'])){

				if($_GET['mostrar'] != 0){
						
						switch($_GET['mostrar']){
						
								case 1: $query = $query." AND f.estado = 0"; 
								break;
								case 2:$query = $query." AND f.estado = 1"; 
								break;
								case 3:$query = $query." AND f.estado = 2"; 
								break;
							}
						
						}
				if($_GET['periodo'] != 1){
						
							$datei = $_GET['anoi']."-".$_GET['mesi']."-".$_GET['diai'];
							$datef = $_GET['anof']."-".$_GET['mesf']."-".$_GET['diaf'];
							
						
						switch($_GET['periodo']){
						
								case 2: $query = $query.sprintf(" AND fecha BETWEEN %s AND %s ",GetSQLValueString($datei, "date"),GetSQLValueString($datei, "date")); 
								break;
								
								case 4:$query = $query.sprintf(" AND fechapago BETWEEN %s AND %s ",GetSQLValueString($datei, "date"),GetSQLValueString($datei, "date")); 
								break;
						}	
				}	
	}




mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedores =$query;// "SELECT * FROM proveedor p,cuentasporpagar c WHERE p.idproveedor = c.idproveedor";
$rsProveedores = mysql_query($query_rsProveedores, $tecnocomm) or die(mysql_error());
$row_rsProveedores = mysql_fetch_assoc($rsProveedores);




$concepto = array("Segun orden de Servicio: ","Segun Cotizacion: ");

$estados = array("<img src=\"images/state1.png\" width=\"24\" height=\"24\" title=\"Abierta\" />","<img src=\"images/Cobrar.png\" alt=\"\" width=\"24\" height=\"24\" title=\"Pagada\"/>","<img src=\"images/Terminar.png\" width=\"24\" height=\"24\"  title=\"Cancelada\"/>");

$i=0;
do{

$proveedor[$row_rsProveedores['idcliente']]['nombre'] =  $row_rsProveedores['abreviacion'];
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['estado'] = $estados[$row_rsProveedores['estado']];
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['concepto'] = $row_rsProveedores['numfactura'];
/*
if($row_rsProveedores['tipo']!=1){ 
		$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['concepto'] =$concepto[$row_rsProveedores['tipo']].		
		$row_rsProveedores['referencia'];
} 
if($row_rsProveedores['tipo']==1){
	$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['concepto'] =$concepto[$row_rsProveedores['tipo']].$row_rsProveedores
	['idcotizacionfactura']; 
}
*/
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['moneda'] = $row_rsProveedores['moneda'];
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['monto'] = $row_rsProveedores['tot'];
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['fecha'] = formatDate($row_rsProveedores['fecha']);
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['fechapago'] = formatDate($row_rsProveedores['fechapago']);

$i++;
}while($row_rsProveedores = mysql_fetch_assoc($rsProveedores));

$meses = array (1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");


$signo = array("$","US$");

?>





<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
</head>

<body onload="print();">
<form name="reporteproveedores" method="get">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="19" colspan="12" valign="top" class="titulos">CUENTAS POR COBRAR</td>
  </tr>
  <tr>
    <td width="81" height="8"></td>
    <td width="215"></td>
    <td width="14"></td>
    <td width="22"></td>
    <td width="47"></td>
    <td width="22"></td>
    <td width="98"></td>
    <td width="31"></td>
    <td width="73"></td>
    <td width="106"></td>
    <td width="31"></td>
    <td width="98"></td>
  </tr>
  <tr>
    <td height="41">&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="4" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="305" height="29" align="right" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
      </tr>
    </table>    </td>
  </tr>
 <?php foreach($proveedor as $pro){?>
  <tr>
    <td height="98" colspan="12" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td height="18" colspan="2" valign="top" class="realte">Cliente:</td>
          <td colspan="10" valign="top" class="realte"><?php echo $pro['nombre'];?></td>
        </tr>
      <tr>
        <td width="74" height="4"></td>
          <td width="50"></td>
          <td width="96"></td>
          <td width="100"></td>
          <td width="75"></td>
          <td width="58"></td>
          <td width="35"></td>
          <td width="102"></td>
          <td width="78"></td>
          <td width="49"></td>
          <td width="91"></td>
          <td width="30"></td>
        </tr>
      <tr>
        <td height="18" colspan="3" valign="top">Detalle De Cuentas:</td>
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
        <td height="18" valign="top">Estado</td>
          <td colspan="3" valign="top">No. Factura</td>
          <td colspan="3" align="center" valign="top">Monto</td>
          <td colspan="2" align="center" valign="top">Fecha</td>
          <td colspan="2" align="center" valign="top">Fecha Pago</td>
          <td>&nbsp;</td>
        </tr>
        <?php $tpesos = $tdolares = 0; foreach($pro['cuentas'] as $cuenta){ ?>
      <tr>
        <td height="20" valign="top"><?php echo $cuenta['estado'];?></td>
          <td colspan="3" valign="top"><?php echo $cuenta['concepto'];?></td>
          <td valign="top"><?php echo $signo[$cuenta['moneda']];?></td>
          <td colspan="2" align="right" valign="top"><?php echo format_money($cuenta['monto']);?></td>
          <td colspan="2" align="center" valign="top"><?php echo $cuenta['fecha'];?></td>
          <td colspan="2" align="center" valign="top"><?php echo $cuenta['fechapago'];?></td>
          <td></td>
        </tr>
        <?php if($cuenta['estado'] != 2)if($cuenta['moneda'] == 0){$tpesos = $tpesos +$cuenta['monto']; } else {$tdolares = $tdolares +$cuenta['monto'];}?>
        <?php  } ?>
      <tr>
        <td height="20"></td>
          <td></td>
          <td></td>
          <td></td>
          <td colspan="2" align="right" valign="top">TOTAL $:</td>
          <td colspan="2" align="right" valign="top"><?php echo format_money($tpesos);?></td>
          <td align="right" valign="top">TOTAL US$:</td>
          <td align="right" valign="top"><?php echo format_money($tdolares);?></td>
          <td>&nbsp;</td>
          <td></td>
        </tr>
      
      
      
      
      
      
      
      
      
    </table></td>
  </tr>
  <?php $totalpesos = $totalpesos + $tpesos; $totaldolars = $totaldolars + $tdolares;?>
  <?php } ?>
  <tr>
    <td height="15" colspan="12" valign="top" class="resaltarTabla">TOTAL</td>
    </tr>
  <tr>
    <td height="21"></td>
    <td></td>
    <td colspan="3" valign="top"><span class="style1">TOTAL $:</span></td>
    <td colspan="2" align="right" valign="top"><span class="style1"><?php echo format_money($totalpesos);?></span></td>
    <td colspan="2" align="right" valign="top"><span class="style1">TOTAL US$:</span></td>
    <td colspan="2" align="right" valign="top"><span class="style1"><?php echo format_money($totaldolares);?></span></td>
    <td>&nbsp;</td>
  </tr>
  
  <tr>
    <td height="10"></td>
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
    <td height="41"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="305" height="33" align="right" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
          </tr>
    </table></td>
  </tr>
</table>
</form>
</body>
</html>
<?php
mysql_free_result($rsProveedores);
?>
