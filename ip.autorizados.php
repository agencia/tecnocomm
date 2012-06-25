<?php require_once('Connections/tecnocomm.php'); ?>
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

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsIp = "SELECT i.*, c.nombre FROM ip i LEFT JOIN cliente c ON i.idcliente = c.idcliente WHERE i.estado < 3 ORDER BY fecha ASC";
$rsIp = mysql_query($query_rsIp, $tecnocomm) or die(mysql_error());
$row_rsIp = mysql_fetch_assoc($rsIp);
$totalRows_rsIp = mysql_num_rows($rsIp);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoConciliado = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.identificador2, c.idip, sb.fecha FROM subcotizacionarticulo sba,subcotizacion sb, cotizacion c WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 8) AND c.idcotizacion = sb.idcotizacion";
$rsMontoConciliado = mysql_query($query_rsMontoConciliado, $tecnocomm) or die(mysql_error());
$row_rsMontoConciliado = mysql_fetch_assoc($rsMontoConciliado);
$totalRows_rsMontoConciliado = mysql_num_rows($rsMontoConciliado);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoInicial = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.identificador2, c.idip, sb.fecha FROM subcotizacionarticulo sba,subcotizacion sb, cotizacion c WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 3 AND c.idcotizacion = sb.idcotizacion)";
$rsMontoInicial = mysql_query($query_rsMontoInicial, $tecnocomm) or die(mysql_error());
$row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial);
$totalRows_rsMontoInicial = mysql_num_rows($rsMontoInicial);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT f.*, SUM(df.cantidad * df.punitario) as total FROM factura f JOIN detallefactura df ON f.idfactura = df.idfactura WHERE f.estado = 1 GROUP BY f.idfactura";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);



mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti = sprintf("select sum(importe) as retiros from banco where tipo=1 ORDER BY fecha ASC",GetSQLValueString($fecha1_RsDetalle, "date"));
$RSReti = mysql_query($query_RSReti, $tecnocomm) or die(mysql_error());
$row_RSReti = mysql_fetch_assoc($RSReti);
$totalRows_RSReti = mysql_num_rows($RSReti);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti1 = sprintf("select sum(importe) as depositos from banco where tipo=0 ORDER BY fecha ASC",GetSQLValueString($fecha1_RsDetalle, "date"));
$RSReti1 = mysql_query($query_RSReti1, $tecnocomm) or die(mysql_error());
$row_RSReti1 = mysql_fetch_assoc($RSReti1);
$totalRows_RSReti1 = mysql_num_rows($RSReti1);

$saldo=$row_RSReti1['depositos']-$row_RSReti['retiros'];



do{
		
	if($row_rsMontoConciliado['moneda'] == $row_rsMontoConciliado['monedaglobal']){
			$concepto = (($row_rsMontoConciliado['precio_cotizacion'] + $row_rsMontoConciliado['mo']) * $row_rsMontoConciliado['utilidad'] ) * $row_rsMontoConciliado['cantidad'];
	}elseif($row_rsMontoConciliado['moneda'] == 0 && $row_rsMontoConciliado['monedaglobal'] == 1){
			$concepto = (( ($row_rsMontoConciliado['precio_cotizacion'] / $row_rsMontoConciliado['tipo_cambio']) + $row_rsMontoConciliado['mo']) * $row_rsMontoConciliado['utilidad'] ) * $row_rsMontoConciliado['cantidad'];
		}else{
			$concepto = (( ($row_rsMontoConciliado['precio_cotizacion'] * $row_rsMontoConciliado['tipo_cambio']) + $row_rsMontoConciliado['mo']) * $row_rsMontoConciliado['utilidad'] ) * $row_rsMontoConciliado['cantidad'];
			}
	
	//$identificador = GetIdentificador($row_rsMontoConciliado['identificador2']);
					   
	$montoconciliado[$row_rsMontoConciliado['idip']] = $montoconciliado[$row_rsMontoConciliado['idip']] + $concepto;
	$moneda[$row_rsMontoConciliado['idip']] =$row_rsMontoConciliado['monedaglobal'] ;
	
	//agregar para detalle
	$ip[$row_rsMontoConciliado['idip']]['detalle'][$row_rsMontoConciliado['idsubcotizacion']]['concepto'] = "Conciliacion: ".$row_rsMontoConciliado['identificador2'];
	$ip[$row_rsMontoConciliado['idip']]['detalle'][$row_rsMontoConciliado['idsubcotizacion']]['fecha'] =  $row_rsMontoConciliado['fecha'];
	$ip[$row_rsMontoConciliado['idip']]['detalle'][$row_rsMontoConciliado['idsubcotizacion']]['monto'] =  $montoconciliado[$row_rsMontoConciliado['idip']];
	
}while($row_rsMontoConciliado = mysql_fetch_assoc($rsMontoConciliado));

do{
		
	if($row_rsMontoInicial['moneda'] == $row_rsMontoInicial['monedaglobal']){
			$concepto = (($row_rsMontoInicial['precio_cotizacion'] + $row_rsMontoInicial['mo']) * $row_rsMontoInicial['utilidad'] ) * $row_rsMontoInicial['cantidad'];
	}elseif($row_rsMontoInicial['moneda'] == 0 && $row_rsMontoInicial['monedaglobal'] == 1){
			$concepto = (( ($row_rsMontoInicial['precio_cotizacion'] / $row_rsMontoInicial['tipo_cambio']) + $row_rsMontoInicial['mo']) * $row_rsMontoInicial['utilidad'] ) * $row_rsMontoInicial['cantidad'];
		}else{
			$concepto = (( ($row_rsMontoInicial['precio_cotizacion'] * $row_rsMontoInicial['tipo_cambio']) + $row_rsMontoInicial['mo']) * $row_rsMontoInicial['utilidad'] ) * $row_rsMontoInicial['cantidad'];
			}
	
	//$identificador = GetIdentificador($row_rsMontoInicial['identificador2']);
					   
	$monto[$row_rsMontoInicial['idip']] = $monto[$row_rsMontoInicial['idip']] + $concepto;
	$moneda[$row_rsMontoInicial['idip']] = $row_rsMontoInicial['monedaglobal'];
	
	$ip[$row_rsMontoInicial['idip']]['detalle'][$row_rsMontoInicial['idsubcotizacion']]['concepto'] = "Cotizacion: ".$row_rsMontoInicial['identificador2'];
	$ip[$row_rsMontoInicial['idip']]['detalle'][$row_rsMontoInicial['idsubcotizacion']]['fecha'] =  $row_rsMontoInicial['fecha'];
	$ip[$row_rsMontoInicial['idip']]['detalle'][$row_rsMontoInicial['idsubcotizacion']]['monto'] =  $monto[$row_rsMontoInicial['idip']];
	
}while($row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial));

//agrupar por ip
foreach($monto as $km => $m){
		$ip[$km]['coti'] = $ip[$km]['coti'] + $m;
}

foreach($montoconciliado as $km => $m){
		$ip[$km]['conc'] = $ip[$km]['conc'] + $m;
}

do{
	
	$ip[$row_rsFacturas['idip']]['fact'] = $ip[$row_rsFacturas['idip']]['fact'] + $row_rsFacturas['total'];
	
	$ip[$row_rsFacturas['idip']]['detalle']["f".$row_rsFacturas['numfactura']]['concepto'] = "Factura: ".$row_rsFacturas['numfactura'];
	$ip[$row_rsFacturas['idip']]['detalle']["f".$row_rsFacturas['numfactura']]['fecha'] =  $row_rsFacturas['fecha'];
	$ip[$row_rsFacturas['idip']]['detalle']["f".$row_rsFacturas['numfactura']]['monto'] = $row_rsFacturas['total']; 
	
}while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));


//agrupa


$mon = array("$","US$");

?>
<script language="javascript">

$(function(){
		
		$(".ipresumen").click(function(e){
									   
			$(this).next('tr').slideToggle("fast");
		
		});
		   

});

</script>
<h1>Reporte De Proyetos Autorizados</h1>
<p>Muestra Informacion de los proyectos autorizados:</p>

<div id="options">
<form name="opc">
<input type="text" name="tipocambio"   value=""/>
<input type="submit" value="Ver En Moneda Nacional"/>
<input type="hidden" name="mod" value="autorizados" />
</form>
</div>

<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr>
<td>Fecha</td>
<td>IP</td>
<td>Cliente</td>
<td>Descripcion</td>
<td align="right">$ Cotizado</td>
<td align="right">$ Conciliado</td>
<td align="right">Facturado</td>
<td align="right">Saldo</td>
<td align="right">%</td>
</tr>
</thead>
<tbody>
<?php 
//inicializar variables de totales
$totalpesos = $totaldolares = 0;
?>
  <?php do { ?>
  <?php if(isset($ip[$row_rsIp['idip']]['coti'])){?>
    <tr class="ipresumen">
      <td><?php echo formatDate($row_rsIp['fecha']); ?></td>
      <td><?php echo $row_rsIp['idip']; ?></td>
      <td><?php echo $row_rsIp['nombre']; ?></td>
      <td><?php echo $row_rsIp['descripcion']; ?></td>
      <td align="right">
	  <?php 
	  if($ip[$row_rsIp['idip']]['moneda'] == 1 && isset($_GET['tipocambio']) && $_GET['tipocambio'] != ""){
	  echo format_money($ip[$row_rsIp['idip']]['coti'] * $_GET['tipocambio']);
	  $cotizado = $ip[$row_rsIp['idip']]['coti'] * $_GET['tipocambio'];
	  }else{
	 echo $mon[$moneda[$row_rsIp['idip']]].format_money($ip[$row_rsIp['idip']]['coti']);
	 $cotizado = $ip[$row_rsIp['idip']]['coti'];
	}
	  ?>
      
      </td>
      <td align="right">
	  <?php 
	  	  if($ip[$row_rsIp['idip']]['moneda'] == 1 && isset($_GET['tipocambio']) && $_GET['tipocambio'] != "" ){
	  echo format_money($ip[$row_rsIp['idip']]['conc'] * $_GET['tipocambio']);
	  $conciliado = $ip[$row_rsIp['idip']]['conc'] * $_GET['tipocambio'];
	  }else{
		echo $mon[$moneda[$row_rsIp['idip']]].format_money($ip[$row_rsIp['idip']]['conc']);
		 $conciliado = $ip[$row_rsIp['idip']]['conc'];
	}
	 
	  ?></td>
      <td align="right"><?php echo $mon[$moneda[$row_rsIp['idip']]];?> <?php echo format_money($ip[$row_rsIp['idip']]['fact']);?></td>
      <td align="right"><?php echo $mon[$moneda[$row_rsIp['idip']]];?>
	  <?php if((isset($ip[$row_rsIp['idip']]['conc'])  == true)&& $ip[$row_rsIp['idip']]['conc'] > 0){
		  		
			echo format_money($ip[$row_rsIp['idip']]['conc'] - $ip[$row_rsIp['idip']]['fact']);
						
		  }else{
			echo format_money($ip[$row_rsIp['idip']]['coti'] - $ip[$row_rsIp['idip']]['fact']);
		  }?>
      </td>
      <td align="right"><?php if((isset($ip[$row_rsIp['idip']]['conc'])  == true)&& $ip[$row_rsIp['idip']]['conc'] > 0){@$p = ($ip[$row_rsIp['idip']]['fact'] * 100)/$ip[$row_rsIp['idip']]['conc'];}else{@$p = ($ip[$row_rsIp['idip']]['fact'] * 100)/$ip[$row_rsIp['idip']]['coti'];} echo round($p,2);?></td>
      <td></td>
    </tr>
    <?php 
	
	//obtener saldos
	if($moneda[$row_rsIp['idip']] == 0){
		if((isset($ip[$row_rsIp['idip']]['conc'])  == true)&& $ip[$row_rsIp['idip']]['conc'] > 0){			
			$totalpesos += ($ip[$row_rsIp['idip']]['conc']- $ip[$row_rsIp['idip']]['fact']);		
		}else{
			$totalpesos += ($ip[$row_rsIp['idip']]['coti'] - $ip[$row_rsIp['idip']]['fact']);
		}
		
	
	}
	
	if($moneda[$row_rsIp['idip']] == 1){
		if((isset($ip[$row_rsIp['idip']]['conc'])  == true)&& $ip[$row_rsIp['idip']]['conc'] > 0){			
			$totaldolares += ($ip[$row_rsIp['idip']]['conc']- $ip[$row_rsIp['idip']]['fact']);		
		}else{
			$totaldolares += ($ip[$row_rsIp['idip']]['coti'] - $ip[$row_rsIp['idip']]['fact']);
		}
	}
	
	
	?>
    <tr style="display:none"><td></td><td></td><td colspan="7">
    <table cellpadding="4" cellspacing="0">
    <thead>
    <tr><td>Fecha</td><td>Concepto</td><td align="right">Importe</td></tr>
    </thead>
    <tbody>
    <?php 
	$detalle = $ip[$row_rsIp['idip']]['detalle'];
	
	foreach($detalle as $det){
	?>	
		
    <tr class="f<?php echo ++$i; if($i==2){$i=0;}?>">
    <td><?php echo formatDate($det['fecha']);?></td><td><?php echo $det['concepto'];?></td><td align="right"><?php echo format_money($det['monto']);?></td>
    </tr>   
        
	<?php 
	}
    
	?>
    <tr><td colspan="3"><a href="index.php?mod=detalleip&idip=<?php echo $row_rsIp['idip'];?>">Ver Detalle IP</a></td></tr>
	</tbody>
    </table>
    </td></tr>
    
    <?php } ?>
    <?php } while ($row_rsIp = mysql_fetch_assoc($rsIp)); ?>
</tbody>
<tfoot>
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td align="right">Total Pesos:</td>
<td colspan="2" align="right">$<?php echo format_money($totalpesos);?></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td align="right">Total Dolares:</td>
<td colspan="2" align="right">US$<?php echo format_money($totaldolares);?></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td align="right">Saldo En Bancos:</td>
<td colspan="2" align="right">$<?php echo format_money($saldo);?></td>
</tr>
</tfoot>
</table>
</div>
<?php
mysql_free_result($rsIp);

mysql_free_result($rsMontoInicial);

mysql_free_result($rsFacturas);

mysql_free_result($rsMontoConciliado);
?>
