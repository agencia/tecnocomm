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

$colname_rsFactIp = "-1";
if (isset($_GET['idip'])) {
  $colname_rsFactIp = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFactIp = sprintf("SELECT f.*,SUM(df.cantidad * df.punitario) AS total FROM factura f LEFT JOIN detallefactura df ON f.idfactura = df.idfactura WHERE idip = %s GROUP BY df.idfactura", GetSQLValueString($colname_rsFactIp, "int"));
$rsFactIp = mysql_query($query_rsFactIp, $tecnocomm) or die(mysql_error());
$row_rsFactIp = mysql_fetch_assoc($rsFactIp);
$totalRows_rsFactIp = mysql_num_rows($rsFactIp);


$estado =array ("<img src=\"images/Facturacion.png\"  title=\"Activa\"/>","<img src=\"images/Cobrar.png\" title=\"Pagada\" />","cancelada","<img src=\"images/Stacked Documents 24 h p.png\" title=\"incobrable\"/>");
?>
<h1>Facturas</h1>
<div id="opciones">
<ul><li><a href="ip.factura.nueva.php?idip=<?php echo $_GET['idip']; ?>" class="popup">Nueva Factura</a></li></ul>
</div>
<?php if ($totalRows_rsFactIp > 0) { // Show if recordset not empty ?>
  <div id="distabla">
    <table width="80%" cellspacing="0">
      <thead>
        <tr>
          <td>Estado</td>
          <td>Opciones</td>
          
          <td>Numero</td>
          <td>Fecha</td>
          <td>Concepto</td>
          <td>Monto</td>
        </tr>
      </thead>
      <tbody>
        <?php do { ?>
          <tr>
            <td><?php echo $estado[$row_rsFactIp['estado']];?></td>
            <td><a href="printFacturaPDF.php?idfactura=<?php echo $row_rsFactIp['idfactura']; ?>" target="_blank"><img src="images/Imprimir2.png" width="24" height="24"  title="Imprimir Factura"/>
              </a>
            <a href="facturando.php?idfactura=<?php echo $row_rsFactIp['idfactura']; ?>" class="popup"><img src="images/Edit.png" width="24" height="24" /></a><a href="eliminarFactura.php?idfactura=<?php echo $row_rsFactIp['idfactura']; ?>" onclick="NewWindow(this.href,'eliminar factura','850','600','YES');return false"><img src="images/eliminar.gif" alt="eliminar" width="19" height="19" border="0" title="ELIMINAR FACTURA" /></a></td>
            
            <td><?php echo $row_rsFactIp['numfactura']; ?></td>
            <td align="left"><?php echo formatDate($row_rsFactIp['fecha']); ?></td>
            <td><?php  if($row_rsFactIp['anticipo'] < 10){ echo "Anticipo ".$row_rsFactIp['anticipo'];}?><?php  if($row_rsFactIp['anticipo'] == 10){ echo "Finiquito ";}?></td>
            <td align="right"><?php $monto = $row_rsFactIp['total'] + ($row_rsFactIp['total']*$row_rsFactIp['iva']/100);  echo format_money($monto); $total += ($row_rsFactIp['estado']==2) ? 0: $monto;?></td>
          </tr>
          <?php } while ($row_rsFactIp = mysql_fetch_assoc($rsFactIp)); ?>
      </tbody>
      <tfoot>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td>/
          <td align="right">Total:</td>
          <td align="right"><?php  echo format_money($total);?></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_rsFactIp == 0) { // Show if recordset empty ?>
  <p>No hay facturas creadas para esta IP</p>
  <?php } // Show if recordset empty ?>
<?php
mysql_free_result($rsFactIp);
?>
