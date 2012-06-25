 <?php
require_once('Connections/tecnocomm.php'); ?>
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

$colname_rsCotdef = "-1";
if (isset($_GET['idip'])) {
  $colname_rsCotdef = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotdef = sprintf("SELECT s.* FROM subcotizacion s, ip i WHERE s.idsubcotizacion = i.cotizacion AND i.idip = %s", GetSQLValueString($colname_rsCotdef, "int"));
$rsCotdef = mysql_query($query_rsCotdef, $tecnocomm) or die(mysql_error());
$row_rsCotdef = mysql_fetch_assoc($rsCotdef);
$totalRows_rsCotdef = mysql_num_rows($rsCotdef);

$ide_rsCotiIp2 = "-1";
if (isset($_GET['idip'])) {
  $ide_rsCotiIp2 = (get_magic_quotes_gpc()) ? $_GET['idip'] : addslashes($_GET['idip']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotiIp2 = sprintf("SELECT * from ip,cotizacion co, subcotizacion sub where ip.idip=co.idip and co.idcotizacion=sub.idcotizacion and ip.idip=%s", $ide_rsCotiIp2);
$rsCotiIp2 = mysql_query($query_rsCotiIp2, $tecnocomm) or die(mysql_error());
$totalRows_rsCotiIp2 = mysql_num_rows($rsCotiIp2);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotiIp3 = sprintf("SELECT * from ordenservicio WHERE idip=%s", $ide_rsCotiIp2);
$rsCotiIp3 = mysql_query($query_rsCotiIp3, $tecnocomm) or die(mysql_error());
$totalRows_rsCotiIp3 = mysql_num_rows($rsCotiIp3);


?>
<script type="text/javascript">
$(function(){
	
	$("#tabs").tabs({ cookie: { expires: 30 }});
	
	});

</script>
<h1>Detalle De Proyecto (IP)</h1>
<?php include("ip.encabezado.php");?>
<br />
<br />
<div>Cotizacion Aurizada:
  <?php if ($totalRows_rsCotdef > 0) { // Show if recordset not empty ?>
    <?php echo $row_rsCotdef['identificador2']; ?>
    <?php } // Show if recordset not empty ?>
    <?php if ($totalRows_rsCotdef == 0) { // Show if recordset empty ?>
      <a href="ip.seleccionarcotizacion.php?idip=<?php echo $_GET['idip'];?>" onclick="NewWindow(this.href,'Seleccionar Ip',400,400,'yes');return false;">Seleccionar Cotizacion</a>
  <?php } // Show if recordset empty ?>
</div>
<div>
<h2>Movimientos de Ip</h2>
<div id="tabs">
	<ul>
    	<li><a href="#caler">Alertas</a></li>
		<li><a href="#tabs-1">Ord. Servicio (<?php echo $totalRows_rsCotiIp3; ?>)</a></li>
		<li><a href="#tabs-2">Levantamiento</a></li>
		<li><a href="#tabs-3">Cotizacion (<?php echo $totalRows_rsCotiIp2 ?>)</a></li>
         <li><a href="#tabs-12">Ord. Compra</a></li>
         <li><a href="#tabs-4">Materiales</a></li>
         <li><a href="#tabs-11">Herramienta</a></li>
         <li><a href="#tabs-7">Avance</a></li>
         <li><a href="#tabs-8">Supervicion</a></li>
         <li><a href="#tabs-9">Conciliacion</a></li>
         <li><a href="#tabs-10">Facturas (<?php echo $totalRows_rsFacturas;?>) </a></li>
         <li><a href="#tabs-b">Bitacora</a></li>
         <li><a href="#historialasignaciones">Historial Asignaciones</a></li>
	</ul>
    <div id="caler">
    <?php include("ip.conversacion.php");?>
    </div>
    
	<div id="tabs-1">
	<?php include("lista.ordenservicio.ip.php");?>	
	</div>
    
	<div id="tabs-12">
	<?php include("lista.ordencompra.ip.php");?>	
	</div>
    
    <div id="tabs-2">
		<?php include("levantamientos.php");?>	
	</div>
    <div id="tabs-3">
		<?php include("ip.cotizaciones.php");?>
	</div>
    <div id="tabs-4">
		  <?php include("ip.material.php"); ?>
	</div>


    <div id="tabs-7">
		  <?php if($row_rsCotdef['idsubcotizacion'] != ""){  include("avance.reporte.php");}else{?> 
	No se ha definido la cotizacion del proyecto, defina una para continuar...
	<?php }?>
	</div>
    <div id="tabs-8">
		  <?php if($row_rsCotdef['idsubcotizacion'] != ""){ include("ip.supervicion.php");}else{?> 
	No se ha definido la cotizacion del proyecto, defina una para continuar...
	<?php }?>
	</div>
	
    <div id="tabs-9">
        <?php if($row_rsCotdef['idsubcotizacion'] > 0){
	include("ip.consiliacion.php");
	}else{?> 
	No se ha definido la cotizacion del proyecto, defina una para continuar...
	<?php }?>
    </div>
    
    <div id="tabs-10">
   	<?php include("ip.facturas.php");?>
	</div>
    
    <div id="tabs-11">
   <?php include("ip.herramienta.php"); ?>
	</div>

	<div id="tabs-b">
    <?php include('ip.bitacora2.php');?>
    </div>    
    
    <div id="historialasignaciones">
    	<?php echo include('ip.historialasignaciones.php');?>
    </div>
    
</div>
<?php
mysql_free_result($rsCotiIp2);
?>
