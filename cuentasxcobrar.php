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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsFacturas = 30;
$pageNum_rsFacturas = 0;
if (isset($_GET['pageNum_rsFacturas'])) {
  $pageNum_rsFacturas = $_GET['pageNum_rsFacturas'];
}
$startRow_rsFacturas = $pageNum_rsFacturas * $maxRows_rsFacturas;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT f.*,c.nombre, SUM(df.punitario * df.cantidad) AS subtotal, 
    (SELECT SUM(fa.monto) FROM factura_abono fa WHERE fa.idfactura = f.idfactura) as abonado 
    FROM factura f LEFT JOIN detallefactura df ON f.idfactura = df.idfactura JOIN cliente c ON c.idcliente = f.idcliente WHERE (f.estado = 0 OR f.estado = 4) GROUP BY f.idfactura ORDER BY numfactura DESC";
$query_limit_rsFacturas = sprintf("%s LIMIT %d, %d", $query_rsFacturas, $startRow_rsFacturas, $maxRows_rsFacturas);
$rsFacturas = mysql_query($query_limit_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);

if (isset($_GET['totalRows_rsFacturas'])) {
  $totalRows_rsFacturas = $_GET['totalRows_rsFacturas'];
} else {
  $all_rsFacturas = mysql_query($query_rsFacturas);
  $totalRows_rsFacturas = mysql_num_rows($all_rsFacturas);
}
$totalPages_rsFacturas = ceil($totalRows_rsFacturas/$maxRows_rsFacturas)-1;

$queryString_rsFacturas = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsFacturas") == false && 
        stristr($param, "totalRows_rsFacturas") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsFacturas = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsFacturas = sprintf("&totalRows_rsFacturas=%d%s", $totalRows_rsFacturas, $queryString_rsFacturas);

$estadofactura =array ("<img src=\"images/Facturacion.png\"  title=\"Activa\"/>","<img src=\"images/facturapagada.png\" title=\"Pagada\" />","<img src=\"images/facturacancelada.png\" title=\"Pagada\" />","<img src=\"images/Stacked Documents 24 h p.png\" title=\"incobrable\"/>","<img src=\"images/Facturacion.png\"  title=\"Activa\"/>");

?>
<h1>Cuentas Por Cobrar</h1>
<div>
<ul>
<li><a href="index.php?mod=porcobrarclientes">Agrupar Por Clientes</a></li>
<li><a href="index.php?mod=porcobrar">Por Factura</a></li>
<li><a href="cuentasxcobrar.reporte.php" class="popup">Ver Reporte</a></li>
</ul>
</div>
<div id="distabla">
<table cellspacing="0" width="90%" style="margin:25px">
<thead>
<tr><td colspan="11" align="right"><table border="0">
  <tr>
  <td> Facturas del <?php echo ($startRow_rsFacturas + 1) ?> al <?php echo min($startRow_rsFacturas + $maxRows_rsFacturas, $totalRows_rsFacturas) ?> de <?php echo $totalRows_rsFacturas ?></td>
    <td><?php if ($pageNum_rsFacturas > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsFacturas=%d%s", $currentPage, 0, $queryString_rsFacturas); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsFacturas > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsFacturas=%d%s", $currentPage, max(0, $pageNum_rsFacturas - 1), $queryString_rsFacturas); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsFacturas < $totalPages_rsFacturas) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsFacturas=%d%s", $currentPage, min($totalPages_rsFacturas, $pageNum_rsFacturas + 1), $queryString_rsFacturas); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsFacturas < $totalPages_rsFacturas) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsFacturas=%d%s", $currentPage, $totalPages_rsFacturas, $queryString_rsFacturas); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr><td>Estado</td><td>Numero </td><td>Fecha</td><td>Ip</td><td>Cliente</td><td>Concepto:</td><td align="right">Sub Total</td><td align="right">I.V.A</td>
  <td align="right">Total</td>
  <td align="right">Saldo</td>
  <td align="right">Opciones</td>
</tr>
</thead>
<tbody>
<?php 
$total = 0;
$saldo = 0;

do { ?>
  <tr>
    <td valign="top"><a href="verPago.php?idfactura=<?php echo $row_rsFacturas['idfactura']; ?>" class="popup"><?php echo $estadofactura[$row_rsFacturas['estado']];?></a></td>
    <td align="center" valign="top"><?php echo $row_rsFacturas['numfactura']; ?></td>
    <td valign="top"><?php echo formatDate($row_rsFacturas['fecha']); ?></td>
    <td align="center" valign="top"><a href="index.php?mod=detalleip&idip=<?php echo $row_rsFacturas['idip']; ?>"><?php echo $row_rsFacturas['idip']; ?></a></td>
    <td valign="top"><?php echo $row_rsFacturas['nombre']; ?></td><td valign="top">:</td><td align="right" valign="top"><?php echo format_money($row_rsFacturas['subtotal']); ?></td>
    <td align="right" valign="top"><?php echo format_money($row_rsFacturas['subtotal'] * $row_rsFacturas['iva']/100); ?></td>
    <td align="right" valign="top"><?php echo format_money($row_rsFacturas['subtotal'] * (1 + $row_rsFacturas['iva']/100));
    $total += $row_rsFacturas['subtotal'] * (1 + $row_rsFacturas['iva']/100);
    ?></td>
    <td align="right" valign="top"><?php echo format_money(($row_rsFacturas['subtotal'] * (1 + $row_rsFacturas['iva']/100)) - $row_rsFacturas['abonado']);
    $saldo += ($row_rsFacturas['subtotal'] * (1 + $row_rsFacturas['iva']/100)) - $row_rsFacturas['abonado'];
    ?></td>
    <td align="right" valign="top">
    <a href="printFacturaPDF.php?idfactura=<?php echo $row_rsFacturas['idfactura']; ?>" class="popup"><img src="images/Imprimir2.png" width="24" height="24" border="0"  title="Imprimir Factura"/></a>
            <a href="eliminarFactura.php?idfactura=<?php echo $row_rsFacturas['idfactura']; ?>" class="popup"><img src="images/eliminar.gif" alt="eliminar" width="19" height="19" border="0" title="ELIMINAR FACTURA" /></a>
            <?php if(in_array($row_rsFacturas['estado'],array(0,4))){?><a href="factura.pagar.php?idfactura=<?php echo $row_rsFacturas['idfactura']; ?>" class="popup"><img src="images/porpagar1.png" width="24" height="24" /></a><?php } ?>
            </td>
  </tr>
  <?php } while ($row_rsFacturas = mysql_fetch_assoc($rsFacturas)); ?>
  <tr><td colspan="8">&nbsp; </td>
  <td align="right">Total: <?php echo format_money($total); ?></td>
  <td align="right">Saldo: <?php echo format_money($saldo); ?></td>
  <td align="right">&nbsp;</td>
</tr>
  </tbody>
  <tfoot>
<tr><td colspan="11" align="right"><table border="0">
  <tr>
  <td> Facturas del <?php echo ($startRow_rsFacturas + 1) ?> al <?php echo min($startRow_rsFacturas + $maxRows_rsFacturas, $totalRows_rsFacturas) ?> de <?php echo $totalRows_rsFacturas ?></td>
    <td><?php if ($pageNum_rsFacturas > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsFacturas=%d%s", $currentPage, 0, $queryString_rsFacturas); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsFacturas > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsFacturas=%d%s", $currentPage, max(0, $pageNum_rsFacturas - 1), $queryString_rsFacturas); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsFacturas < $totalPages_rsFacturas) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsFacturas=%d%s", $currentPage, min($totalPages_rsFacturas, $pageNum_rsFacturas + 1), $queryString_rsFacturas); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsFacturas < $totalPages_rsFacturas) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsFacturas=%d%s", $currentPage, $totalPages_rsFacturas, $queryString_rsFacturas); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
  </tfoot>
</table>
</div>
<?php
mysql_free_result($rsFacturas);
?>
