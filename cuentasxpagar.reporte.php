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

if(isset($_GET['buscar']) and $_GET['buscar'] != '')
{
	$buscar = "AND p.nombrecomercial like '%%".$_GET['buscar']."%%'";
	$filtro = $_GET['buscar'];
}
if(isset($_GET['estado']) and $_GET['estado'] != -1)
{
	$estado = " AND c.estado = ".$_GET['estado'];
	$filtro1 = $_GET['estado'];
}else 
{
	$filtro1 = -1;
	$estado = " AND c.estado = 0";
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT p.idproveedor, p.nombrecomercial, c.monto, c.moneda, c.fecha, c.fechavencimiento, c.nofactura, c.estado, DATEDIFF(c.fechavencimiento,NOW()) as diasdecredito FROM cuentasporpagar c LEFT JOIN proveedor p ON c.idproveedor = p.idproveedor WHERE 1 $buscar $estado ORDER BY diasdecredito";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);

do{
	
	$clientes[$row_rsFacturas['idproveedor']]['nombrecomercial'] = $row_rsFacturas['nombrecomercial'];
	$clientes[$row_rsFacturas['idproveedor']][$row_rsFacturas['moneda']] = $clientes[$row_rsFacturas['idproveedor']][$row_rsFacturas['moneda']] + $row_rsFacturas['monto']; 
	$clientes[$row_rsFacturas['idproveedor']]['data'][] = $row_rsFacturas;
	
	$totales[$row_rsFacturas['moneda']] += $row_rsFacturas['monto'];
}while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Reporte Cuentas Por Pagar</title>
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
<h1>Reporte Cuentas Por Pagar</h1>
<div id="opciones">
<a href="cuentasxpagar.reporte.pdf.php?filtro=<?php echo $filtro; ?>&filtro1=<?php echo $filtro1; ?>" class="popup"><img src="images/Imprimir2.png" border="0" title="Imprimir" width="24" height="24"/>Imprimir</a>
</div>
<form name="cuentasxcobrar" method="get">
<div id="opciones">
<label>Buscar: <input type="text" name="buscar" value="<?php echo $_GET['buscar']; ?>" /></label>
<label>Estado: 
<select name="estado">
<option value="-1" <?php if (!(strcmp(-1, $_GET['estado']))) {echo "selected=\"selected\"";} ?>>Todas</option>
<option value="0" <?php if (!(strcmp(0, $_GET['estado']))) {echo "selected=\"selected\"";} ?>>Abiertas</option>
<option value="1" <?php if (!(strcmp(1, $_GET['estado']))) {echo "selected=\"selected\"";} ?>>Pagadas</option>
<option value="2" <?php if (!(strcmp(2, $_GET['estado']))) {echo "selected=\"selected\"";} ?>>Canceladas</option>
</select>
<input type="submit" name="busca" value="Buscar" />
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
<td><?php echo $cliente['nombrecomercial']; ?></td><td align="right" width="15%">US$ <?php echo format_money($cliente[1]);?></td><td align="right" width="15%">$ <?php echo format_money($cliente[0]);?></td>
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
<td><?php echo $factura['nofactura'];?></td>
<td><?php echo formatDate($factura['fecha']);?></td>
<td align="right"><?php echo format_money($factura['monto']);?></td>
<td align="right"><?php echo $factura['diasdecredito']." Dias";?></td>
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
