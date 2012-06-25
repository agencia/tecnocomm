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
$query_rsIp = "SELECT i.*,sb.idcotizacion,sb.identificador2, sb.moneda, c.nombre FROM ip i LEFT JOIN cliente c ON i.idcliente = c.idcliente JOIN cotizacion co ON co.idip = i.idip JOIN subcotizacion sb ON sb.idcotizacion = co.idcotizacion WHERE sb.estado = 3 OR estado = 8 ORDER BY fecha ASC";
$rsIp = mysql_query($query_rsIp, $tecnocomm) or die(mysql_error());
$row_rsIp = mysql_fetch_assoc($rsIp);
$totalRows_rsIp = mysql_num_rows($rsIp);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoInicial = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.identificador2, c.idip, sb.fecha FROM subcotizacionarticulo sba,subcotizacion sb, cotizacion c WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 3 AND c.idcotizacion = sb.idcotizacion)";
$rsMontoInicial = mysql_query($query_rsMontoInicial, $tecnocomm) or die(mysql_error());
$row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial);
$totalRows_rsMontoInicial = mysql_num_rows($rsMontoInicial);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoConciliado = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.identificador2, c.idip, sb.fecha FROM subcotizacionarticulo sba,subcotizacion sb, cotizacion c WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 8) AND c.idcotizacion = sb.idcotizacion";
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
	
	 if($row_rsMontoInicial['tipo']  == 0)
		$p = round(($pre * $row_rsMontoInicial['utilidad']) + $manoobra,2);
	else{	
		$p = round(($pre * $row_rsMontoInicial['utilidad']),2) ;
		}	
		
	$man[$row_rsMontoInicial['idsuboctoizacion']] = $man[$row_rsMontoInicial['idsuboctoizacion']] + ($manoobra*$row_rsMontoInicial['cantidad']);
	//$maninst = $maninst + ($manoobra*$row_rsMontoInicial['reall']);
	$sub[$row_rsMontoInicial['idsuboctoizacion']] = $sub[$row_rsMontoInicial['idsuboctoizacion']] + $row_rsPartidas['cantidad'] * $p; 
	//$subinst = $subinst + $row_rsPartidas['reall'] * $p;
	$cotizaciones[$row_rsMontoInicial['idsubcotizacion']] = $row_rsMontoInicial;
	
	//$inicial[$row_rsMontoInicial['idsuboctoizacion']] = $inicial[$row_rsMontoInicial['idsubcotizacion']] + ($row_rsMontoInicial['cantidad'] * $p);
	
}while($row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial));


foreach($cotizaciones as $cotizacion => $idsubctoizacion){
	if($cotizacion['tipo'] == 1){
	$montoinicial[$cotizacion['idcotizacion']] = $sub[$cotizacion['idsubcotizacion']] + ($man[$cotizacion['idsubcotizacion']]  * $cotizacion['monto']);
	}else{
	$montoinicial[$cotizacion['idcotizacion']] = $sub[$cotizacion['idsubcotizacion']];	
	}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Reporte De Proyectos Autorizados</title>
</head>
<body>
<div id="distabla">
<table width="100%" cellpadding="2" cellspacing="0">
<thead>
<tr>
<td>Fecha</td>
<td>Ip</td>
<td>Cotizacion</td>
<td>Monto Inicial</td>
<td>Monto Conciliado</td>
<td>Monto Facturado</td>
<td>Saldo</td>
<td>%</td>
</tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsIp['fecha']; ?></td>
      <td><?php echo $row_rsIp['idip']; ?></td>
      <td><?php echo $row_rsIp['identificador2']; ?></td>
      <td><?php echo $montoinicial[$row_rsIp['idcotizacion']];?></td>
      <td>Monto Conciliado</td>
      <td>Monto Facturado</td>
      <td>Saldo</td>
      <td>%</td>
    </tr>
    <?php } while ($row_rsIp = mysql_fetch_assoc($rsIp)); ?>
</tbody>
</table>
</div>
</body>
</html>
<?php
mysql_free_result($rsIp);

mysql_free_result($rsMontoInicial);

mysql_free_result($rsFacturas);

mysql_free_result($rsMontoConciliado);
?>