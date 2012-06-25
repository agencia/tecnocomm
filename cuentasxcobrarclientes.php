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
$query_rsFacturas = "SELECT c.nombre, SUM(df.punitario * df.cantidad) AS subtotal, COUNT(f.idfactura) AS cuentas, f.iva FROM factura f LEFT JOIN detallefactura df ON f.idfactura = df.idfactura JOIN cliente c ON c.idcliente = f.idcliente WHERE f.estado = 0 GROUP BY c.idcliente ORDER BY numfactura DESC";
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


?>
<h1>Cuentas Por Cobrar</h1>
<div>
<ul>
<li><a href="index.php?mod=porcobrarclientes">Agrupar Por Clientes</a></li>
<li><a href="index.php?mod=porcobrar">Por Factura</a></li>
</ul>
</div>
<div id="distabla">
<table width="100%" cellspacing="0" cellpadding="2">
<thead>
<tr><td colspan="5" align="right"><table border="0">
  <tr>
  <td> Cuentas del <?php echo ($startRow_rsFacturas + 1) ?> al <?php echo min($startRow_rsFacturas + $maxRows_rsFacturas, $totalRows_rsFacturas) ?> de un total de <?php echo $totalRows_rsFacturas ?></td>
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
<tr><td>Cliente:</td><td>Cuentas:</td>
  <td>Monto</td>
  <td>Saldo</td><td>Opciones</td></tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsFacturas['nombre']; ?></td><td><?php echo $row_rsFacturas['cuentas']; ?></td><td><?php echo format_money($row_rsFacturas['subtotal'] * (1 + $row_rsFacturas['iva']/100)); ?></td><td>&nbsp;</td><td>&nbsp;</td>
    </tr>
    <?php } while ($row_rsFacturas = mysql_fetch_assoc($rsFacturas)); ?>
</tbody>
<tfoot>
<tr><td colspan="5" align="right"><table border="0">
  <tr>
  <td> Cuentas del <?php echo ($startRow_rsFacturas + 1) ?> al <?php echo min($startRow_rsFacturas + $maxRows_rsFacturas, $totalRows_rsFacturas) ?> de un total de <?php echo $totalRows_rsFacturas ?></td>
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
