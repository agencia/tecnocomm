<?php require_once('Connections/tecnocomm.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

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
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
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

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenes = "select * from ordenservicio a";
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
<li><a href="nuevo.orden.servicio.php" onclick="NewWindow(this.href,'Modificar Cotizacion','950','980','yes');return false">Nuevo Orden de Servicio</a></li>

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
        <td>Descripcion</td><td width="10%">Fecha</td>
        
  <td>Observaciones</td>
  <td>IP</td></tr>
    </thead>
    <tbody>
        <?php do { ?>
         <?php if ($totalRows_rsOrdenes == 0) { // Show if recordset empty ?> 
        <tr>
          <td colspan="6" align="center">No hay datos</td>
        </tr>
        <?php } // Show if recordset empty ?>
        <tr>
              <?php if ($totalRows_rsOrdenes > 0) { // Show if recordset not empty ?>
                <td><a href="editar.orden.detalle.php?idordenservicio=<?php echo $row_rsOrdenes['idordenservicio'];?>&idip=<?php echo $row_rsOrdenes['idip'];?>" onclick="if(confirm('Estas seguro que deseas modificar esta orden de servicio?')){NewWindow(this.href,'Modificar Orden','950','980','yes');return false}else{return false;}"><img src="images/Edit.png" border="0" title="Editar Orden de Servicio"></a></td>
            <td><?php echo $row_rsOrdenes['identificador']; ?></td>
            <td><?php echo $row_rsOrdenes['descripcionreporte']; ?></td>
            <td><?php echo $row_rsOrdenes['fecha']; ?></td>
            <td><?php echo $row_rsOrdenes['observaciones']; ?></td>
            <td><a href="index.php?idip=<?php echo $row_rsOrdenes['idip']; ?>&mod=detalleip"><?php echo $row_rsOrdenes['idip']; ?></a></td>
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
