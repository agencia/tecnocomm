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
$query_rsIp = "SELECT i.*,sb.idcotizacion,sb.identificador2, sb.moneda, c.nombre,sb.nombre as descoti FROM ip i LEFT JOIN cliente c ON i.idcliente = c.idcliente JOIN cotizacion co ON co.idip = i.idip JOIN subcotizacion sb ON sb.idcotizacion = co.idcotizacion WHERE sb.estado = 3 AND i.estado < 2 AND co.aprobada=0 ORDER BY fecha ASC";
$rsIp = mysql_query($query_rsIp, $tecnocomm) or die(mysql_error());
$row_rsIp = mysql_fetch_assoc($rsIp);
$totalRows_rsIp = mysql_num_rows($rsIp);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoInicial = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.identificador2, c.idip, sb.fecha,sb.idcotizacion, sb.monto  AS montocotizacion, sb.tipo AS tipocotizacion,sb.descuento  FROM subcotizacionarticulo sba,subcotizacion sb, cotizacion c WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 3 AND c.idcotizacion = sb.idcotizacion)";
$rsMontoInicial = mysql_query($query_rsMontoInicial, $tecnocomm) or die(mysql_error());
$row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial);
$totalRows_rsMontoInicial = mysql_num_rows($rsMontoInicial);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoConciliado = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.identificador2, c.idip, sb.fecha,sb.idcotizacion,sb.monto AS montocotizacion, sb.tipo AS tipocotizacion,sb.descuento, sb.montoreal FROM subcotizacionarticulo sba,subcotizacion sb, cotizacion c WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 8 AND c.idcotizacion = sb.idcotizacion)";
$rsMontoConciliado = mysql_query($query_rsMontoConciliado, $tecnocomm) or die(mysql_error());
$row_rsMontoConciliado = mysql_fetch_assoc($rsMontoConciliado);
$totalRows_rsMontoConciliado = mysql_num_rows($rsMontoConciliado);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT f.*, SUM(df.cantidad * df.punitario) as total FROM factura f JOIN detallefactura df ON f.idfactura = df.idfactura WHERE f.estado = 1 GROUP BY f.idfactura";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);

do{
	 $pre =divisa($row_rsMontoInicial['precio_cotizacion'],$row_rsMontoInicial['moneda'],$row_rsMontoInicial['monedaglobal'],$row_rsMontoInicial['tipo_cambio']);
	 $manoobra = divisa($row_rsMontoInicial['mo'],$row_rsMontoInicial['moneda'],$row_rsMontoInicial['monedaglobal'],$row_rsMontoInicial['tipo_cambio']);	
	
	 if($row_rsMontoInicial['tipocotizacion']  == 0)
		$p = round(($pre * $row_rsMontoInicial['utilidad']) + $manoobra,2);
	else{	
		$p = round(($pre * $row_rsMontoInicial['utilidad']),2) ;
		}	
		
	$man[$row_rsMontoInicial['idsubcotizacion']] = $man[$row_rsMontoInicial['idsubcotizacion']] + ($manoobra*$row_rsMontoInicial['cantidad']);
	//$maninst = $maninst + ($manoobra*$row_rsMontoInicial['reall']);
	$sub[$row_rsMontoInicial['idsubcotizacion']] = $sub[$row_rsMontoInicial['idsubcotizacion']] + $row_rsMontoInicial['cantidad'] * $p; 
	//$subinst = $subinst + $row_rsPartidas['reall'] * $p;
	$cotizaciones[$row_rsMontoInicial['idsubcotizacion']] = $row_rsMontoInicial;
	
	//$inicial[$row_rsMontoInicial['idsuboctoizacion']] = $inicial[$row_rsMontoInicial['idsubcotizacion']] + ($row_rsMontoInicial['cantidad'] * $p);
	
}while($row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial));



/*OBTENER TOTAL DE CONCILIACIONES*/
do{
	 $pre =divisa($row_rsMontoConciliado['precio_cotizacion'],$row_rsMontoConciliado['moneda'],$row_rsMontoConciliado['monedaglobal'],$row_rsMontoConciliado['tipo_cambio']);
	 $manoobra = divisa($row_rsMontoConciliado['mo'],$row_rsMontoConciliado['moneda'],$row_rsMontoConciliado['monedaglobal'],$row_rsMontoConciliado['tipo_cambio']);	
	
	 if($row_rsMontoConciliado['tipocotizacion']  == 0)
		$p = round(($pre * $row_rsMontoConciliado['utilidad']) + $manoobra,2);
	else{	
		$p = round(($pre * $row_rsMontoConciliado['utilidad']),2) ;
		}	
		
	$man2[$row_rsMontoConciliado['idsubcotizacion']] = $man2[$row_rsMontoConciliado['idsubcotizacion']] + ($manoobra*$row_rsMontoConciliado['reall']);
	//$maninst = $maninst + ($manoobra*$row_rsMontoInicial['reall']);
	$sub2[$row_rsMontoConciliado['idsubcotizacion']] = $sub2[$row_rsMontoConciliado['idsubcotizacion']] + $row_rsMontoConciliado['reall'] * $p; 
	//$subinst = $subinst + $row_rsPartidas['reall'] * $p;
	$conciliaciones[$row_rsMontoConciliado['idsubcotizacion']] = $row_rsMontoConciliado;
	
	//$inicial[$row_rsMontoInicial['idsuboctoizacion']] = $inicial[$row_rsMontoInicial['idsubcotizacion']] + ($row_rsMontoInicial['cantidad'] * $p);
	
}while($row_rsMontoConciliado = mysql_fetch_assoc($rsMontoConciliado));




/*LLENAR TOTALES DE COTIZACIONES*/
foreach($cotizaciones as  $idsubcotizacion => $cotizacion ){
	
	
	
	if($cotizacion['tipocotizacion'] == 1){
			$montoinicial[$cotizacion['idcotizacion']] = ($sub[$idsubcotizacion] + ($man[$idsubcotizacion]  * $cotizacion['montocotizacion'])) ;
	}else{
		$montoinicial[$cotizacion['idcotizacion']] = $sub[$idsubcotizacion];	
	}
		
		$descuento = ($montoinicial[$cotizacion['idcotizacion']] * $cotizacion['descuento'])/100;
		
		$montoinicial[$cotizacion['idcotizacion']] = $montoinicial[$cotizacion['idcotizacion']] - $descuento;
		
		$montoinicial[$cotizacion['idcotizacion']] = $montoinicial[$cotizacion['idcotizacion']] * 1.16;
		
		$idinicial[$cotizacion['idcotizacion']] = $idsubcotizacion;
	
		$mc[$cotizacion['idcotizacion']] = $cotizacion['monedaglobal'];
}


/*LLENAR TOTALES DE CONCILIACIONES*/
foreach($conciliaciones as  $idsubcotizacion => $conciliacion ){
	if($cotizacion['tipocotizacion'] == 1){
			$montofinal[$conciliacion['idcotizacion']] = ($sub2[$idsubcotizacion] + ($man2[$idsubcotizacion]  * $conciliacion['montoreal'])) ;
	}else{
		$montofinal[$conciliacion['idcotizacion']] = $sub2[$idsubcotizacion] ;	
	}
	
		$descuento = ($montofinal[$conciliacion['idcotizacion']] * $conciliacion['descuento'])/100;
		
		$montofinal[$conciliacion['idcotizacion']] = $montofinal[$conciliacion['idcotizacion']] - $descuento;
		$montofinal[$conciliacion['idcotizacion']] = $montofinal[$conciliacion['idcotizacion']] * 1.16;
	
	$idfinal[$conciliacion['idcotizacion']] = $idsubcotizacion;
}

do{
	
	//$facts[$row_rsFacturas['idfactura']] = $row_rsFacturas;
	$row_rsFacturas['total'] = $row_rsFacturas['total'] * (($row_rsFacturas['iva']/100)+1);
	$facturas[$row_rsFacturas['cotizacion']][] = $row_rsFacturas;
	
}while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));


/*OBTENER SALDO EN BANCOS*/
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti = "select sum(importe) as retiros from banco where tipo=1 ORDER BY fecha ASC";
$RSReti = mysql_query($query_RSReti, $tecnocomm) or die(mysql_error());
$row_RSReti = mysql_fetch_assoc($RSReti);
$totalRows_RSReti = mysql_num_rows($RSReti);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti1 = "select sum(importe) as depositos from banco where tipo=0 ORDER BY fecha ASC";
$RSReti1 = mysql_query($query_RSReti1, $tecnocomm) or die(mysql_error());
$row_RSReti1 = mysql_fetch_assoc($RSReti1);
$totalRows_RSReti1 = mysql_num_rows($RSReti1);

$saldoenbancos=$row_RSReti1['depositos']-$row_RSReti['retiros'];


$moneda = array("$","US$");
?>
<script language="javascript">
$.extend($.expr[":"], {
    "containsNC": function(elem, i, match, array) {
        return (elem.textContent || elem.innerText || "").toLowerCase
().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
});

$(function(){
			   $(".filtrar").keyup(function(e){					
						$('.bus:not(:containsNC("'+$(this).val()+'"))').each(function(index,e){$(e).css('display','none')});										
						$('.bus:containsNC("'+$(this).val()+'")').each(function(index,e){$(e).css('display','table-row')});
						});

});

</script>
<h1>Reporte De Proyectos Autorizados</h1>
<body>
<div id="submenu">
<ul>
<li><a href="print.ip.autorizados.php?tipocambio=<?php echo isset($_GET['tipocambio'])?$_GET['tipocambio']:""?>"  target="_blank">Imprimir lista de Ip</a></li>
</ul>
</div>
<div id="options">
<form name="opc">
<label>Tipo De Cambio</label><br>
<input type="text" name="tipocambio"   value="<?php echo isset($_GET['tipocambio'])?$_GET['tipocambio']:""?>"/><br>
<button type="submit">Convertir</button>
<input type="hidden" name="mod" value="autorizados" />
<br>
<label>Buscar</label><input type="text" name="filtrar" class="filtrar">
</form>
</div>
<div id="distabla">
<table width="100%" cellpadding="2" cellspacing="0">
<thead>
<tr>
<td>Fecha</td>
<td>Ip</td>
<td>Cliente</td>
<td>Cotizacion</td>
<td>Descripcion</td>
<td></td>
<td align="right"> Inicial</td>
<td align="right"> Conciliado</td>
<td align="right"> Facturado</td>
<td align="right">Saldo</td>
<td align="right">%</td>
<td align="center">Opcion</td>
</tr>
</thead>
<tbody>
  <?php do { ?>
    <tr class="ipresumen bus">
      <td valign="top"><?php echo formatDate($row_rsIp['fecha']); ?></td>
      <td valign="top"><a href="index.php?mod=detalleip&idip=<?php echo $row_rsIp['idip'];?>"><?php echo $row_rsIp['idip']; ?></a></td>
      <td valign="top"><?php echo $row_rsIp['nombre'];?></td>
      <td valign="top">
      <a href="printCotizacion.php?idsubcotizacion=<?php  echo (isset($montofinal[$row_rsIp['idcotizacion']]) && $montofinal[$row_rsIp['idcotizacion']] > 0)?$idfinal[$row_rsIp['idcotizacion']]:$idinicial[$row_rsIp['idcotizacion']];?>" class="popup">
	  
	  <?php echo $row_rsIp['identificador2']; ?></a></td>
      <td valign="top"><?php echo $row_rsIp['descoti']?></td>
      <td valign="top"><?php echo $moneda[$mc[$row_rsIp['idcotizacion']]];?></td>
      <td align="right" valign="top">
      <?php 
	  	
			$mi = $montoinicial[$row_rsIp['idcotizacion']]; 
	  
	  	if(isset($_GET['tipocambio']) && $_GET['tipocambio'] != '' && $mc[$row_rsIp['idcotizacion']] == 1){
				$mi = ($mi * $_GET['tipocambio']);
				$m = $moneda[0];
			}else{
				$m = $moneda[$mc[$row_rsIp['idcotizacion']]];
			}
		 	
			echo format_money($mi);
		  ?>
    
      </td>
      <td align="right" valign="top">
      <?php 
	  	
			$mmf = $montofinal[$row_rsIp['idcotizacion']]; 
	  
	  	if(isset($_GET['tipocambio']) && $_GET['tipocambio'] != '' && $mc[$row_rsIp['idcotizacion']] == 1){
				$mmf = ($mmf * $_GET['tipocambio']);
				$m = $moneda[0];
			}else{
				$m = $moneda[$mc[$row_rsIp['idcotizacion']]];
			}
		 	
			echo format_money($mmf);
		  ?>
       </td>
      <td align="right" valign="top">
            <?php 
			//facturas
			$totfact = 0;
	
			if((isset($_GET['tipocambio']) && $_GET['tipocambio'] != "") || $mc[$row_rsIp['idcotizacion']] == 0){
				if(is_array($facturas[$row_rsIp['idcotizacion']]))
				foreach($facturas[$row_rsIp['idcotizacion']] as $factura){
				$totfact += $factura['total']; 			
				}
			}else{
				if(is_array($facturas[$row_rsIp['idcotizacion']]))
				foreach($facturas[$row_rsIp['idcotizacion']] as $factura){
				
				if($factura['tipocambio'] != 0)
					$totfact += $factura['total'] / $factura['tipocambio'];
				
				}
				
				
			}
		
			echo format_money($totfact);
		
	
			
		  ?>
      </td>
      <td align="right" valign="top">
	  <?php 
	  	
		if($mmf > 0)
			$saldo = $mmf - $totfact;
		else
			$saldo = $mi - $totfact;
			
			
		if($mc[$row_rsIp['idcotizacion']] == 0){
			$totalpesos += $saldo;
		}else{
			$totaldolares += $saldo;
			
		}
		
	  	echo format_money($saldo);
		
	  ?></td>
      <td align="right" valign="top">
      <?php
	  
	  if($saldo > 0){
			$pr = ($mff * 100)/ $saldo;  
  	}else{
		  	$pr = 0;
		}
			
	  echo round($pr,2);
	  
	  ?>%
      </td>
     <td align="center"><a href="ip.cotizacion.finalizar.php?idcotizacion=<?php echo $row_rsIp['idcotizacion']; ?>" class="popup"><img src="images/state3.png" border="0"></a></td>
    </tr>
    <?php } while ($row_rsIp = mysql_fetch_assoc($rsIp)); ?>
</tbody>
<tfoot>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td align="right">&nbsp;</td>
<td colspan="3" align="right">Saldo Total Pesos:</td>
<td colspan="2" align="right"><?php echo format_money($totalpesos);?></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td align="right">&nbsp;</td>
<td colspan="3" align="right">Saldo Total Dolares:</td>
<td colspan="2" align="right"><?php echo format_money($totaldolares);?></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td align="right">&nbsp;</td>
<td colspan="3" align="right">Saldo En Bancos:</td>
<td colspan="2" align="right">$<?php echo format_money($saldoenbancos);?></td>
<td>&nbsp;</td>
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