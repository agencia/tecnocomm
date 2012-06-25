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

$currentPage = $_SERVER["PHP_SELF"];

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoConciliado = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.idcotizacion FROM subcotizacionarticulo sba,subcotizacion sb WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 8)";
$rsMontoConciliado = mysql_query($query_rsMontoConciliado, $tecnocomm) or die(mysql_error());
$row_rsMontoConciliado = mysql_fetch_assoc($rsMontoConciliado);
$totalRows_rsMontoConciliado = mysql_num_rows($rsMontoConciliado);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoInicial = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.idcotizacion FROM subcotizacionarticulo sba,subcotizacion sb WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 3)";
$rsMontoInicial = mysql_query($query_rsMontoInicial, $tecnocomm) or die(mysql_error());
$row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial);
$totalRows_rsMontoInicial = mysql_num_rows($rsMontoInicial);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAnticipos = "SELECT ff.numfactura,fc.*,SUM(f.punitario * f.cantidad) AS monto FROM facturacotizacion fc, subcotizacion sb,detallefactura f,factura ff WHERE  ff.idfactura = fc.idfactura AND fc.idcotizacion = sb.idsubcotizacion AND f.idfactura = fc.idfactura GROUP BY fc.idfactura";
$rsAnticipos = mysql_query($query_rsAnticipos, $tecnocomm) or die(mysql_error());
$row_rsAnticipos = mysql_fetch_assoc($rsAnticipos);
$totalRows_rsAnticipos = mysql_num_rows($rsAnticipos);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = "SELECT * FROM subcotizacion WHERE  estado = 3 ";
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);


//cruze de tablas

//monto inicial
do{
	
	if($row_rsMontoInicial['moneda'] == $row_rsMontoInicial['monedaglobal']){
			$concepto = (($row_rsMontoInicial['precio_cotizacion'] + $row_rsMontoInicial['mo']) * $row_rsMontoInicial['utilidad'] ) * $row_rsMontoInicial['cantidad'];
	}elseif($row_rsMontoInicial['moneda'] == 0 && $row_rsMontoInicial['monedaglobal'] == 1){
			$concepto = (( ($row_rsMontoInicial['precio_cotizacion'] / $row_rsMontoInicial['tipo_cambio']) + $row_rsMontoInicial['mo']) * $row_rsMontoInicial['utilidad'] ) * $row_rsMontoInicial['cantidad'];
		}else{
			$concepto = (( ($row_rsMontoInicial['precio_cotizacion'] * $row_rsMontoInicial['tipo_cambio']) + $row_rsMontoInicial['mo']) * $row_rsMontoInicial['utilidad'] ) * $row_rsMontoInicial['cantidad'];
			}
	
	$montoinicial[$row_rsMontoInicial['idcotizacion']] = $montoinicial[$row_rsMontoInicial['idcotizacion']] + $concepto;
}while($row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial));

//monto Conciliado

if(isset($_GET['tipocambio']) && $_GET['tipocambio']!=1){
	$tipocambio = $_GET['tipocambio'];
}else{
	$tipocambio= 13.5;
}

do{
	
	if($row_rsMontoConciliado['moneda'] == $row_rsMontoConciliado['monedaglobal']){
			$concepto = (($row_rsMontoConciliado['precio_cotizacion'] + $row_rsMontoConciliado['mo']) * $row_rsMontoConciliado['utilidad'] ) * $row_rsMontoConciliado['cantidad'];
	}elseif($row_rsMontoInicial['moneda'] == 0 && $row_rsMontoInicial['monedaglobal'] == 1){
			$concepto = (( ($row_rsMontoConciliado['precio_cotizacion'] / $tipocambio) + $row_rsMontoConciliado['mo']) * $row_rsMontoConciliado['utilidad'] ) * $row_rsMontoConciliado['cantidad'];
		}else{
			$concepto = (( ($row_rsMontoConciliado['precio_cotizacion'] * $tipocambio) + $row_rsMontoConciliado['mo']) * $row_rsMontoConciliado['utilidad'] ) * $row_rsMontoConciliado['cantidad'];
			}
	
	$montoconciliado[$row_rsMontoConciliado['idcotizacion']] = $montoconciliado[$row_rsMontoConciliado['idcotizacion']] + $concepto;
}while($row_rsMontoConciliado = mysql_fetch_assoc($rsMontoConciliado));

//anticipos

do{
	$anticipos[$row_rsAnticipos['idcotizacion']][$row_rsAnticipos['numeroanticipo']] = $row_rsAnticipos['monto'];
	$f = "F: ".$row_rsAnticipos['numfactura'];
	$factura[$row_rsAnticipos['idcotizacion']][$row_rsAnticipos['numeroanticipo']] = $f;
}while($row_rsAnticipos = mysql_fetch_assoc($rsAnticipos));


$signo = array("$","US$");
?>
<link href="style2.css" rel="stylesheet" type="text/css" />


<h1> Reporte de Proyectos Autorizados </h1>
<div id="submenu"></div>
<div id="buscar">Buscar: <input type="text" name="buscar"/></div>
<div id="opciones">
   <form name="tpoCambio" method="get"> 
    </label>
    <label><span>Tipo de Cambio</span>
      <input type="text" name="tipocambio" value="<?php echo $tipocambio;?>"  class="moneda" />
    </label>
    <input type="submit" value="Actualizar" />
    </form>
    </div>
<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="10" align="right" valign="baseline">Hay <?php echo $totalRows_rsCotizacion; ?> Proyectos Autorizados</td></tr>
<tr>
<td>Inicio</td>
<td>Cotizacion</td><td>Descripcion de Proyecto</td><td colspan="2">Monto Inicial</td><td>Monto Conciliado</td><td>Anticipo1</td><td>Anticipo2</td><td>Anticipo3</td><td>Saldo</td>
</tr></thead>
<tbody>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
      <td><?php echo formatDate($row_rsCotizacion['fecha']); ?></td>
      <td><?php echo $row_rsCotizacion['identificador2']; ?></td><td ><?php echo $row_rsCotizacion['nombre']; ?></td><td align="right"><?php echo $signo[$moneda];?></td>
      <td align="right"><?php echo format_money(divisa($montoinicial[$row_rsCotizacion['idsubcotizacion']],$row_rsCotizacion['moneda'],$moneda,$tipocambio));?></td>
      <td align="right" <?php echo "class=\"f".$i,"\""; ?>><?php echo format_money(divisa($montoconciliado[$row_rsCotizacion['idsubcotizacion']],$row_rsCotizacion['moneda'],$moneda,$tipocambio));?></td><td align="right" ><?php echo format_money($anticipos[$row_rsCotizacion['idsubcotizacion']][1]);?> <br /> &nbsp;<?php echo $factura[$row_rsCotizacion['idsubcotizacion']][1];?> </td><td align="right" <?php  echo "class=\"f".$i,"\""; ?>> <?php echo format_money($anticipos[$row_rsCotizacion['idsubcotizacion']][2]);?><br />&nbsp;<?php echo $factura[$row_rsCotizacion['idsubcotizacion']][2];?></td><td align="right"><?php echo format_money($anticipos[$row_rsCotizacion['idsubcotizacion']][3]);?><br />&nbsp;<?php echo $factura[$row_rsCotizacion['idsubcotizacion']][3];?></td>
      <td align="right" <?php echo "class=\"f".$i,"\""; ?> > <?php  $saldo = divisa($montoinicial[$row_rsCotizacion['idsubcotizacion']],$row_rsCotizacion['moneda'],$moneda,$tipocambio) - ($anticipos[$row_rsCotizacion['idsubcotizacion']][1] + $anticipos[$row_rsCotizacion['idsubcotizacion']][2] + $anticipos[$row_rsCotizacion['idsubcotizacion']][3]); echo format_money($saldo); ?></td>
    </tr>
    <?php } while ($row_rsCotizacion = mysql_fetch_assoc($rsCotizacion)); ?>
</tbody>
<tfoot>
<tr><td colspan="10" align="right"><table border="0">
      <tr>
        <td> Registros <?php echo ($startRow_rsCotizacion + 1) ?> a <?php echo min($startRow_rsCotizacion + $maxRows_rsCotizacion, $totalRows_rsCotizacion) ?> de <?php echo $totalRows_rsCotizacion ?>
<?php if ($pageNum_rsCotizacion > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsCotizacion=%d%s", $currentPage, 0, $queryString_rsCotizacion); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsCotizacion > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsCotizacion=%d%s", $currentPage, max(0, $pageNum_rsCotizacion - 1), $queryString_rsCotizacion); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsCotizacion < $totalPages_rsCotizacion) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsCotizacion=%d%s", $currentPage, min($totalPages_rsCotizacion, $pageNum_rsCotizacion + 1), $queryString_rsCotizacion); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsCotizacion < $totalPages_rsCotizacion) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsCotizacion=%d%s", $currentPage, $totalPages_rsCotizacion, $queryString_rsCotizacion); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table>
    
    </td></tr>
</tfoot>
</table>

</div>
<?php

mysql_free_result($rsMontoConciliado);

mysql_free_result($rsMontoInicial);

mysql_free_result($rsAnticipos);

mysql_free_result($rsCotizacion);
?>
