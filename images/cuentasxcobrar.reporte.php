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

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT f.numfactura , f.fecha, f.idip, f.idcliente, f.cotizacion, f.oservicio, c.nombre, c.diasdecredito, f.moneda, f.estado, SUM(df.cantidad * df.punitario) AS montofactura, f.iva FROM factura f LEFT JOIN detallefactura df ON f.idfactura = df.idfactura  LEFT JOIN cliente c ON c.idcliente = f.idcliente GROUP BY f.idfactura ORDER BY f.numfactura DESC";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);

do{
	
	$clientes[$row_rsFacturas['idcliente']]['nombre'] = $row_rsFacturas['nombre'];
	$clientes[$row_rsFacturas['idcliente']][$row_rsFacturas['moneda']] = $clientes[$row_rsFacturas['idcliente']][$row_rsFacturas['moneda']] + $row_rsFacturas['montofactura']; 
	$clientes[$row_rsFacturas['idcliente']]['data'][] = $row_rsFacturas;
	
	$totales[$row_rsFacturas['moneda']] += $row_rsFacturas['montofactura'];
}while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Reporte Cuentas Por Cobrar</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jqueryui.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<script language="javascript">
$(function(){
	$(".fecha").datepicker();	
	
	$(".detallecuenta").css('display','none');
	
	$(".cuenta").click(function(){
				$(this).children(".distabla").children(".detallecuenta").slideToggle();					
								
				});
	
});
</script>
</head>
<body>
<h1>Reporte Cuentas Por Cobrar</h1>
<div id="opciones">
<a href="cuentasxcobrar.reporte.pdf.php" class="popup"><img src="­images/Imprimir2.png" title="Imprimir"/>Imprimir</a>
</div>
<form name="cuentasxcobrar" method="get">
<div id="opciones">
<label>Buscar: <input type="text" name="buscar" /></label>
<label>Estado: 
<select name="estado">
<option value="0">Abiertas</option>
<option value="1">Pagadas</option>
<option value="2">Canceladas</option>
<option value="-1">Todas</option>
</select>
</label>
</div>

<div id="opciones">
<label><input type="checkbox" name="periodo" /> Filtrar Por Periodo</label>
&nbsp;&nbsp;
<label>
<input type="text" name="fechai"  class="fecha"/>
<input type="text" name="fechaf"  class="fecha" />
</label>
</div>
</form>
<div id="cuentas">
<?php foreach($clientes as $cliente){?>
<div class="cuenta <?php if($i%2 == 0)echo "funo";else echo "fdos";$i++;?>">
<h3>
<table width="100%">
<tr>
<td><?php echo $cliente['nombre']; ?></td><td align="right" width="15%">US$ <?php echo format_money($cliente[1]);?></td><td align="right" width="15%">$ <?php echo format_money($cliente[0]);?></td>
</tr>
</table>
</h3>
<div class="distabla">
<table width="100%" class="detallecuenta border">
<thead>
<tr><td width="20%">Estado</td><td width="20%">No Factura</td><td width="20%">Fecha</td><td width="20%">Monto</td><td width="20%">Vence En</td></tr>
</thead>
<tbody>
<?php foreach($cliente['data'] as $factura){?>
<tr>
<td><?php echo $factura['estado'];?></td>
<td><?php echo $factura['numfactura'];?></td>
<td><?php echo formatDate($factura['fecha']);?></td>
<td align="right"><?php echo format_money($factura['montofactura']);?></td>
<td align="right"><?php echo $factura['diasdecredito'];?></td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</div>
<?php } ?>
<div class="cuenta f1">
<h3>
<table width="100%">
<tr>
<td></td><td align="right" width="15%">US$ <?php echo format_money($totales[1]);?></td><td align="right" width="15%">$ <?php echo format_money($totales[0]);?></td>
</tr>
</table>
</h3>
</div>

</body>
</html>
<?php
mysql_free_result($rsFacturas);
?>
