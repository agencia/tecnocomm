<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

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
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
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

function GetIdentificador($ide){
	
	//obtenemos identificador
	$id = explode('-',$ide);
	if(ctype_alpha($id[2])){
		$identificador = $id[0].$id[1].$id[2];
	}else{
		$identificador = $id[0].$id[1];
	}
	return $identificador;
	

}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProyectos = "SELECT * FROM subcotizacion WHERE estado = 3 ORDER BY identificador ASC";
$rsProyectos = mysql_query($query_rsProyectos, $tecnocomm) or die(mysql_error());
$row_rsProyectos = mysql_fetch_assoc($rsProyectos);
$totalRows_rsProyectos = mysql_num_rows($rsProyectos);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoConciliado = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.identificador2 FROM subcotizacionarticulo sba,subcotizacion sb WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 8)";
$rsMontoConciliado = mysql_query($query_rsMontoConciliado, $tecnocomm) or die(mysql_error());
$row_rsMontoConciliado = mysql_fetch_assoc($rsMontoConciliado);
$totalRows_rsMontoConciliado = mysql_num_rows($rsMontoConciliado);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoInicial = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.identificador2 FROM subcotizacionarticulo sba,subcotizacion sb WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 3)";
$rsMontoInicial = mysql_query($query_rsMontoInicial, $tecnocomm) or die(mysql_error());
$row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial);
$totalRows_rsMontoInicial = mysql_num_rows($rsMontoInicial);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAnticipos = "SELECT ff.numfactura,fc.*,SUM(f.punitario * f.cantidad) AS monto,sb.identificador2 FROM facturacotizacion fc, subcotizacion sb,detallefactura f,factura ff WHERE  ff.idfactura = fc.idfactura AND fc.idcotizacion = sb.idsubcotizacion AND f.idfactura = fc.idfactura GROUP BY fc.idfactura";
$rsAnticipos = mysql_query($query_rsAnticipos, $tecnocomm) or die(mysql_error());
$row_rsAnticipos = mysql_fetch_assoc($rsAnticipos);
$totalRows_rsAnticipos = mysql_num_rows($rsAnticipos);


do{
	$anticipos[GetIdentificador($row_rsAnticipos['identificador2'])][$row_rsAnticipos['numeroanticipo']] = $row_rsAnticipos['monto'];
	$f = "F: ".$row_rsAnticipos['numfactura'];
	$factura[GetIdentificador($row_rsAnticipos['identificador2'])][$row_rsAnticipos['numeroanticipo']] = $f;
}while($row_rsAnticipos = mysql_fetch_assoc($rsAnticipos));


do{
		
	if($row_rsMontoConciliado['moneda'] == $row_rsMontoConciliado['monedaglobal']){
			$concepto = (($row_rsMontoConciliado['precio_cotizacion'] + $row_rsMontoConciliado['mo']) * $row_rsMontoConciliado['utilidad'] ) * $row_rsMontoConciliado['cantidad'];
	}elseif($row_rsMontoConciliado['moneda'] == 0 && $row_rsMontoConciliado['monedaglobal'] == 1){
			$concepto = (( ($row_rsMontoConciliado['precio_cotizacion'] / $row_rsMontoConciliado['tipo_cambio']) + $row_rsMontoConciliado['mo']) * $row_rsMontoConciliado['utilidad'] ) * $row_rsMontoConciliado['cantidad'];
		}else{
			$concepto = (( ($row_rsMontoConciliado['precio_cotizacion'] * $row_rsMontoConciliado['tipo_cambio']) + $row_rsMontoConciliado['mo']) * $row_rsMontoConciliado['utilidad'] ) * $row_rsMontoConciliado['cantidad'];
			}
	
	$identificador = GetIdentificador($row_rsMontoConciliado['identificador2']);
					   
	$montoconciliado[$identificador] = $montoconciliado[$identificador] + $concepto;
}while($row_rsMontoConciliado = mysql_fetch_assoc($rsMontoConciliado));

do{
		
	if($row_rsMontoInicial['moneda'] == $row_rsMontoInicial['monedaglobal']){
			$concepto = (($row_rsMontoInicial['precio_cotizacion'] + $row_rsMontoInicial['mo']) * $row_rsMontoInicial['utilidad'] ) * $row_rsMontoInicial['cantidad'];
	}elseif($row_rsMontoInicial['moneda'] == 0 && $row_rsMontoInicial['monedaglobal'] == 1){
			$concepto = (( ($row_rsMontoInicial['precio_cotizacion'] / $row_rsMontoInicial['tipo_cambio']) + $row_rsMontoInicial['mo']) * $row_rsMontoInicial['utilidad'] ) * $row_rsMontoInicial['cantidad'];
		}else{
			$concepto = (( ($row_rsMontoInicial['precio_cotizacion'] * $row_rsMontoInicial['tipo_cambio']) + $row_rsMontoInicial['mo']) * $row_rsMontoInicial['utilidad'] ) * $row_rsMontoInicial['cantidad'];
			}
	
	$identificador = GetIdentificador($row_rsMontoInicial['identificador2']);
					   
	$monto[$identificador] = $monto[$identificador] + $concepto;
}while($row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial));


if(isset($_GET['tipocambio']) && $_GET['tipocambio']!=1){
	$tipocambio = $_GET['tipocambio'];
}else{
	$tipocambio= 13.5;
}
?>

<h1> Reporte de Proyectos Autorizados </h1>
<div id="submenu"></div>
   <form name="tpoCambio" method="get"> 
<div id="opciones">

    </label>
    <label><span>Tipo de Cambio</span>
      <input type="text" name="tipocambio" value="<?php echo $tipocambio;?>"  class="moneda" />
    </label>
    <input type="submit" value="Actualizar" />
    <input type="hidden" name="mod" value="autorizados">
    </div>
        </form>
<div id="distabla">
<table width="100%" cellpadding="2" cellspacing="0">
<thead>
<tr><td colspan="10" align="right" valign="baseline">Hay <?php echo $totalRows_rsCotizacion; ?> Proyectos Autorizados</td></tr>
<tr>
<td>Fecha</td>
<td>Cotizacion</td><td>Descripcion de Proyecto</td><td colspan="2">Monto Inicial</td><td>Monto Conciliado</td><td>Anticipo1</td><td>Anticipo2</td><td>Anticipo3</td><td>Saldo</td>
</tr></thead>
<tbody>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsProyectos['fecha']; ?></td>
      <td><?php echo $row_rsProyectos['identificador2']; ?></td><td><?php echo $row_rsProyectos['nombre']; ?></td><td colspan="2" align="right">
	  <?php 
	  echo format_Money(divisa($monto[GetIdentificador($row_rsProyectos['identificador2'])],$row_rsProyectos['moneda'],0,$tipocambio));
	  ?>
      </td>
      <td align="right" class="f">
        <?php 
	  echo format_Money(divisa($montoconciliado[GetIdentificador($row_rsProyectos['identificador2'])],$row_rsProyectos['moneda'],0,$tipocambio));
	  ?>
      </td>
      <td align="right" ><?php echo format_money($anticipos[GetIdentificador($row_rsProyectos['identificador2'])][1]);?> <br /> &nbsp;<?php echo $factura[GetIdentificador($row_rsProyectos['identificador2'])][1];?> </td>
      <td align="right" class="f"> <?php echo format_money($anticipos[GetIdentificador($row_rsProyectos['identificador2'])][2]);?><br />&nbsp;<?php echo $factura[GetIdentificador($row_rsCotizacion['identificador2'])][2];?></td>
      <td align="right"><?php echo format_money($anticipos[GetIdentificador($row_rsProyectos['identificador2'])][3]);?><br />&nbsp;<?php echo $factura[$row_rsProyectos['identificador2']][3];?></td><td class="f" align="right">
      
      <?php 
	  if($montoconciliado[GetIdentificador($row_rsProyectos['identificador2'])] > 0){
	  $saldo = divisa($montoconciliado[GetIdentificador($row_rsProyectos['identificador2'])],$row_rsProyectos['moneda'],0,$tipocambio) - $anticipos[GetIdentificador($row_rsProyectos['identificador2'])][1] - $anticipos[GetIdentificador($row_rsProyectos['identificador2'])][2] -$anticipos[GetIdentificador($row_rsProyectos['identificador2'])][3] ;
	  }else{
		  $saldo = divisa($monto[GetIdentificador($row_rsProyectos['identificador2'])],$row_rsProyectos['moneda'],0,$tipocambio) - $anticipos[GetIdentificador($row_rsProyectos['identificador2'])][1] - $anticipos[GetIdentificador($row_rsProyectos['identificador2'])][2] -$anticipos[GetIdentificador($row_rsProyectos['identificador2'])][3] ;
		 }
	  echo format_Money($saldo);
	  ?>
      
      </td>
    </tr>
    <?php } while ($row_rsProyectos = mysql_fetch_assoc($rsProyectos)); ?>
</tbody>
<tfoot>
<tr><td colspan="10" align="right">
    </td></tr>
</tfoot>
</table>

</div>
<?php
mysql_free_result($rsProyectos);
?>
