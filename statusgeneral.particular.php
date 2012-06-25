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
$query_rsCotizaciones = "SELECT s.*, cl.nombre as nombrecliente, c.idip FROM subcotizacion s LEFT JOIN cotizacion c ON c.idcotizacion = s.idcotizacion JOIN cliente cl ON cl.idcliente = c.idcliente WHERE s.estado = 3";
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);



mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTareas = "SELECT t.*, u.username FROM tarea t JOIN tarea_usuario tu ON t.idtarea = tu.idtarea JOIN usuarios u ON u.id = tu.idusuario WHERE t.estado = 0";
$rsTareas = mysql_query($query_rsTareas, $tecnocomm) or die(mysql_error());
$row_rsTareas = mysql_fetch_assoc($rsTareas);
$totalRows_rsTareas = mysql_num_rows($rsTareas);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevantamientos = "SELECT l.*,c.nombre as nombrecliente FROM levantamientoip l JOIN ip ON l.idip = ip.idip JOIN cliente c ON c.idcliente = ip.idcliente WHERE l.estado < 2 ";
$rsLevantamientos = mysql_query($query_rsLevantamientos, $tecnocomm) or die(mysql_error());
$row_rsLevantamientos = mysql_fetch_assoc($rsLevantamientos);
$totalRows_rsLevantamientos = mysql_num_rows($rsLevantamientos);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenes = "SELECT o.*, c.nombre as nombrecliente FROM ordenservicio o JOIN ip ON ip.idip = o.idip JOIN cliente c ON c.idcliente = ip.idcliente WHERE o.estado < 4";
$rsOrdenes = mysql_query($query_rsOrdenes, $tecnocomm) or die(mysql_error());
$row_rsOrdenes = mysql_fetch_assoc($rsOrdenes);
$totalRows_rsOrdenes = mysql_num_rows($rsOrdenes);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT f.*, c.nombre as nombrecliente FROM factura f JOIN ip ON ip.idip = f.idip JOIN cliente c ON c.idcliente = f.idcliente WHERE f.estado < 5";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);




//Cotizaciones
do{
	$cotizaciones[$row_rsTareas['idcotizacion']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));

mysql_data_seek($rsTareas,0);

do{
	$levantamientos[$row_rsTareas['idlevantamiento']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));

mysql_data_seek($rsTareas,0);

do{
	$ordenes[$row_rsTareas['idordenservicio']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));


mysql_data_seek($rsTareas,0);
do{
	$facturas[$row_rsTareas['idfactura']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));

mysql_data_seek($rsTareas,0);
do{
	$cuentasporpagar[$row_rsTareas['idcuentaporpagar']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));

mysql_data_seek($rsTareas,0);
do{
	$administrativo[$row_rsTareas['idcotizacion']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));

?>
<script language="javascript">

$.extend($.expr[":"], {
    "containsNC": function(elem, i, match, array) {
        return (elem.textContent || elem.innerText || "").toLowerCase
().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
});

$(function(){
		   
		   $('.slide').accordion({ collapsible: true,clearStyle: true });
		   
		      $(".filtrar").keyup(function(e){					
						$('.bus:not(:containsNC("'+$(this).val()+'"))').each(function(index,e){$(e).css('display','none')});										
						$('.bus:containsNC("'+$(this).val()+'")').each(function(index,e){$(e).css('display','table-row')});
						});
		
		   
		   });
</script>

<h1>Status General Tecnocomm</h1>

<a href="status.particular.print.php" class="popup"><img src="images/Imprimir2.png" border="none" />IMPRIMIR</a>

<div style="padding:50px">

<label>Buscar Partidas</label>
<input type="text" name="filtrar" class="filtrar" size="45">

<div class="slide">
<h3><a href="#">Cotizaciones</a></a></h3>
<div>
    <div class="distabla">
    <table width="100%" cellpadding="2" cellspacing="0">
    	<thead>
    	<tr>
        	<td>Ip</td>
            <td>Cotizacion</td>
            <td>Cliente</td>
            <td>Descripcion</td>
            <td>Responsable</td>
        </tr>
    	</thead>
        <tbody>
          <?php do { ?>
          <?php if(is_array($cotizaciones[$row_rsCotizaciones['idsubcotizacion']]) ) if(in_array($_SESSION['MM_Username'],$cotizaciones[$row_rsCotizaciones['idsubcotizacion']]) ):?>
  <tr class="bus">
    <td><?php echo $row_rsCotizaciones['idip']; ?></td>
    <td><?php echo $row_rsCotizaciones['identificador2']; ?></td>
    <td><?php echo $row_rsCotizaciones['nombrecliente']; ?></td>
    <td><?php echo $row_rsCotizaciones['nombre']; ?></td>
    <td>
	<?php if(is_array($cotizaciones[$row_rsCotizaciones['idsubcotizacion']]) ):?>
	<?php echo join(', ',$cotizaciones[$row_rsCotizaciones['idsubcotizacion']]);?>
	<?php endif; ?>    
    </td>
  </tr>
  	<?php endif; ?> 
  <?php } while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones)); ?>
        </tbody>
    </table>
    </div>
</div>
	<h3><a href="#">Levantamientos</a></h3>
<div>

    <div class="distabla">
    <table width="100%" cellpadding="2" cellspacing="0">
    	<thead>
    	<tr>
        	<td>Ip</td>
            <td>Levantamiento</td>
            <td>Cliente</td>
            <td>Descripcion</td>
            <td>Responsable</td>
        </tr>
    	</thead>
        <tbody>
          <?php do { ?>
          <?php if(is_array($levantamientos[$row_rsLevantamientos['idlevantamientoip']]) ) if(in_array($_SESSION['MM_Username'],$levantamientos[$row_rsLevantamientos['idlevantamientoip']]) ):?>
  <tr class="bus">
    <td><?php echo $row_rsLevantamientos['idip']; ?></td>
    <td><?php echo $row_rsLevantamientos['consecutivo']; ?></td>
    <td><?php echo $row_rsLevantamientos['nombrecliente']; ?></td>
    <td><?php echo $row_rsLevantamientos['descripcion']; ?></td>
    <td><?php if(is_array($levantamientos[$row_rsLevantamientos['idlevantamientoip']]) ):?>
      <?php echo join(', ',$levantamientos[$row_rsLevantamientos['idlevantamientoip']]);?>
      <?php endif; ?> </td>
  </tr>
    <?php endif; ?>
  <?php } while ($row_rsLevantamientos = mysql_fetch_assoc($rsLevantamientos)); ?>
  
        </tbody>
     </table>
      </div>
</div>
<h3><a href="#">Ordenes De Servicio</a></h3>
<div>
	
    <div class="distabla">
    <table width="100%" cellpadding="2" cellspacing="0">
    	<thead>
    	<tr class="bus">
        	<td>Ip</td>
            <td>Orden Servicio</td>
            <td>Cliente</td>
            <td>Descripcion</td>
            <td>Responsable</td>
        </tr>
    	</thead>
        <tbody>
          <?php do { ?>
          <?php if(is_array($ordenes[$row_rsOrdenes['idordenservicio']]) ) if(in_array($_SESSION['MM_Username'],$ordenes[$row_rsOrdenes['idordenservicio']]) ):?>
  <tr class="bus">
    <td><?php echo $row_rsOrdenes['idip']; ?></td>
    <td><?php echo $row_rsOrdenes['identificador']; ?></td>
    <td><?php echo $row_rsOrdenes['nombrecliente']; ?></td>
    <td><?php echo $row_rsOrdenes['descripcionreporte']; ?></td>
    <td><?php if(is_array($ordenes[$row_rsOrdenes['idordenservicio']]) ):?>
      <?php echo join(', ',$ordenes[$row_rsOrdenes['idordenservicio']]);?>
      <?php endif; ?> </td>
  </tr>
  <?php endif; ?> 
  <?php } while ($row_rsOrdenes = mysql_fetch_assoc($rsOrdenes)); ?>
      </tbody>
  </table>
   </div>
</div>
<h3><a href="#">Administrativo U Operativo</a></h3>
<div>
	
</div>
	<h3><a href="#">Facturas</a></h3>
<div>

    <div class="distabla">
      <table width="100%" cellpadding="2" cellspacing="0">
    	<thead>
    	<tr class="bus">
        	<td>Ip</td>
            <td>Factura</td>
            <td>Cliente</td>
            <td>Concepto</td>
            <td>Responsable</td>
        </tr>
    	</thead>
        <tbody>
          <?php do { ?>
          <?php  if(is_array($facturas[$row_rsFacturas['idfactura']]) ) if(in_array($_SESSION['MM_Username'],$facturas[$row_rsFacturas['idfactura']]) ):?>
  <tr>
    <td><?php echo $row_rsFacturas['idip']; ?></td>
    <td><?php echo $row_rsFacturas['numfactura']; ?></td>
    <td><?php echo $row_rsFacturas['nombrecliente']; ?></td>
    <td><?php echo $row_rsFacturas['referencia1']; ?><?php echo $row_rsFacturas['referencia2']; ?><?php echo $row_rsFacturas['referencia3']; ?></td>
    <td><?php if(is_array($facturas[$row_rsFacturas['idfactura']]) ):?>
      <?php echo join(', ',$facturas[$row_rsFacturas['idfactura']]);?>
      <?php endif; ?> </td>
  </tr>
    <?php endif; ?>
  <?php } while ($row_rsFacturas = mysql_fetch_assoc($rsFacturas)); ?>
      </tbody>
  </table>
  </div>
</div>

<h3><a href="#">Cuentas Por Pagar</a></h3>
<div>
	
</div>
</div>
</div>
<?php
mysql_free_result($rsCotizaciones);

mysql_free_result($rsTareas);

mysql_free_result($rsLevantamientos);

mysql_free_result($rsOrdenes);

mysql_free_result($rsFacturas);
?>
