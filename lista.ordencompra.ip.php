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

$maxRows_rsOrdenes = 25;
$pageNum_rsOrdenes = 0;
if (isset($_GET['pageNum_rsOrdenes'])) {
  $pageNum_rsOrdenes = $_GET['pageNum_rsOrdenes'];
}
$startRow_rsOrdenes = $pageNum_rsOrdenes * $maxRows_rsOrdenes;

$ide_rsOrdenes = "-1";
if (isset($_GET['idip'])) {
  $ide_rsOrdenes = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenes = sprintf("SELECT *, o.identificador as oidentificador, DATE_FORMAT(o.fecha, '%%d/%%m/%%Y') as ofecha FROM ordencompra o LEFT JOIN subcotizacion s ON s.idsubcotizacion = o.idcotizacion LEFT JOIN cotizacion c ON c.idcotizacion = s.idcotizacion WHERE c.idip = %s", GetSQLValueString($ide_rsOrdenes, "int"));
$query_limit_rsOrdenes = sprintf("%s LIMIT %d, %d", $query_rsOrdenes, $startRow_rsOrdenes, $maxRows_rsOrdenes);
$rsOrdenes = mysql_query($query_limit_rsOrdenes, $tecnocomm) or die(mysql_error());
$row_rsOrdenes = mysql_fetch_assoc($rsOrdenes);

if (isset($_GET['totalRows_rsOrdenes'])) {
  $totalRows_rsOrdenes = $_GET['totalRows_rsOrdenes'];
} else {
  $all_rsOrdenes = mysql_query($query_rsOrdenes);
  $totalRows_rsOrdenes = mysql_num_rows($all_rsOrdenes);
}
$totalPages_rsOrdenes = ceil($totalRows_rsOrdenes/$maxRows_rsOrdenes)-1;
?>
<h1> Orden de Servicio</h1>
<div id="submenu">
<ul>
<li>
    <a href="nuevaOrden2.php" onclick="NewWindow(this.href,'Nueva Orden de Compra',760,450,'yes'); return false;"><img src="images/bullet_16.jpg" width="26" height="22" align="texttop">Nueva Orden De Compra</a>
<!--    <a href="nuevo.orden.servicio.php?ip=<?php echo $_GET['idip'];?>" onclick="NewWindow(this.href,'Modificar Cotizacion','950','980','yes');return false">Nuevo Orden de Servicio</a>-->
</li>

</ul>
<div id="distabla">
  <table width="100%" cellpadding="1" cellspacing="0">
    <thead>
      <tr>
        <td colspan="2">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
        </tr>
      <tr>
        <td>Opciones</td>
        <td>Identidicador</td>
        <td>Descripcion</td><td>Fecha</td>
        <td colspan="2">&nbsp;</td>
      </tr>
    </thead>
    <tbody>
        <?php do { ?>
         <?php if ($totalRows_rsOrdenes == 0) { // Show if recordset empty ?> 
        <tr>
          <td colspan="6" align="center">No hay Ordenes de Servicio para esta IP</td>
        </tr>
        <?php } // Show if recordset empty ?>
        <tr>
              <?php if ($totalRows_rsOrdenes > 0) { // Show if recordset not empty ?>
                <td><a href="detalleOrden2.php?idordencompra=<?php echo $row_rsOrdenes['idordencompra'];?>" onclick="NewWindow(this.href,'Detalle Orden',800,800,'yes'); return false;" ><img src="images/Edit.png" width="24" height="24" /></a><a href="printOrdenCompraGeneral.php?idordencompra=<?php echo $row_rsOrdenes['idordencompra'];?>" onclick="NewWindow(this.href,'Imprimir Orden Compra','900','800','yes'); return false;"><img src="images/Imprimir2.png"  title="Imprimir Orden Compra"/></a></td>
            <td><?php echo $row_rsOrdenes['oidentificador']; ?></td>
            <td>
            <a href="detalleCotizacion.php?idcotizacion=<?php echo $row_rsOrdenes['idencoti']; ?>" 
            onclick="NewWindow(this.href,'Detalle Cotizacion',800,800,'yes');return false;">Cotizacion: <?php echo $row_rsOrdenes['identificador2'];?></a>
            </td>
            <td><?php echo $row_rsOrdenes['ofecha']; ?></td>
            <td colspan="2">&nbsp;</td>
            <?php } // Show if recordset not empty ?>
              </tr>
          <?php } while ($row_rsOrdenes = mysql_fetch_assoc($rsOrdenes)); ?>
    </tbody>
    <tr>
      <td colspan="2">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan="2">&nbsp;</td>
        </tr>
  </table>
</div>
</div>
<?php
mysql_free_result($rsOrdenes);
?>
