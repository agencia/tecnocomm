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

if(isset($_GET['q']) && $_GET['q'] != ''){
	
	if(is_numeric($_GET['q'])) $idip = $_GET['q']; else $idip = '-1';
	
	$queryadd = sprintf(' AND (p.nombrecomercial like %s OR p.nofactura = %s)',
							   	GetSQLValueString('%'.$_GET['q'].'%','text'),
							   	GetSQLValueString($_GET['q'],'int'));
	
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFac = "SELECT * FROM tarea WHERE idcuentaporpagar is not NULL AND estado = 0";
$rsFac = mysql_query($query_rsFac, $tecnocomm) or die(mysql_error());
$row_rsFac = mysql_fetch_assoc($rsFac);
$totalRows_rsFac = mysql_num_rows($rsFac);

do{
	
	$asignadas[] = $row_rsFac['idcuentaporpagar'];
	
}while($row_rsFac = mysql_fetch_assoc($rsFac));

$tipe = isset($_GET['type'])?$_GET['type']:0;
$moneda = array('$','US$');

 if($tipe == 0): 

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT c.*, p.nombrecomercial FROM cuentasporpagar c JOIN proveedor p ON c.idproveedor = p.idproveedor WHERE c.estado = 0 ". $queryadd;
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);


?>
<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr>
  <td>&nbsp;</td>
  <td>Numero</td><td>Cliente</td><td align="right">Monto</td>
<td>Opciones</td>
</tr>
</thead>
<tbody>
<?php if ($totalRows_rsFacturas > 0) { ?>
<?php do{?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?>">
  <td><?php if(in_array($row_rsFacturas['idcuenta'],$asignadas)):?>
<img src="images/bgreen.png" />
<?php endif;?></td>
<td><?php echo $row_rsFacturas['nofactura']; ?></td>
<td><?php echo $row_rsFacturas['nombrecomercial']; ?></td>
<td align="right"><?php echo $moneda[$row_rsFacturas['moneda']];?> <?php echo $row_rsFacturas['monto']; ?></td>
<td align="right"><a href="planeacion.asignar.php?tipoelemento=6&valorreferencia=<?php echo $row_rsFacturas['idcuenta']; ?>&idip=<?php echo $row_rsFacturas['idip']; ?>&idjunta=<?php  echo $_GET['idjunta'];?>" tipoelemento="6" valorreferencia="<?php echo $row_rsFacturas['idcuenta']; ?>" idip="<?php echo $row_rsFacturas['idip']; ?>" class="popup"><img src="images/right.png" border="0"/></a></td>
<?php }while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));?>
<?php } ?>
</tbody>
</table>

<?php endif;?>




<?php if($tipe == 1):?>
<?php 
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = sprintf("SELECT c.*, p.nombrecomercial FROM cuentasporpagar c JOIN proveedor p ON c.idproveedor = p.idproveedor JOIN tarea t ON t.idcuentaporpagar = c.idcuenta WHERE t.fecharealizar = %s %s",
																	  GetSQLValueString($_GET['fecha'],'date'), $queryadd);
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);
?>
<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr class="fdos"><td>Numero</td><td align="center">Ip</td><td>Cliente</td><td align="right">Monto</td>
<td>Opciones</td>
</tr>
</thead>
<tbody>
<?php do{?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?>">
<td><?php echo $row_rsFacturas['idcuenta']; ?></td>
<td><?php echo $row_rsFacturas['nofactura']; ?></td>
<td><?php echo $row_rsFacturas['nombrecomercial']; ?></td>
<td align="right"><?php echo $moneda[$row_rsFacturas['moneda']];?> <?php echo $row_rsFacturas['monto']; ?></td>
<td><a href="#" tipoelemento="5" valorreferencia="<?php echo $row_rsFacturas['idfactura']; ?>" idip="<?php echo $row_rsFacturas['idip']; ?>" class="asignNow"><img src="images/right.png" border="0"/></a></td>
<?php }while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));?>
</tbody>
</table>

<?php endif;?>



<?php if($tipe == 2):?>
<?php
if (isset($_GET['q'])) {
	$busqueda = "AND (";
  	$busqueda .= sprintf(" p.nombrecomercial LIKE %s ", "'%" . $_GET['q'] . "%'");
	if (is_numeric($_GET['q']))
		$busqueda .= sprintf(" OR p.nofactura = %s ", $_GET['q']);
	$busqueda .= ")";
} else {
	$busqueda = " ";
}

$BUSQUEDA_Cotizacion = "defined";
if (isset($busqueda)) {
  $BUSQUEDA_Cotizacion = $busqueda;
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = sprintf("(SELECT t.idtarea, t.idip, t.fecharealizar, t.fecharealizo, c.*, p.nombrecomercial, 0 as estadotarea FROM tarea t JOIN cuentasporpagar c ON t.idcuentaporpagar = c.idcuenta JOIN proveedor p ON c.idproveedor = p.idproveedor WHERE t.fecharealizo is null AND t.fecharealizar <= DATE(%s) %s) 
UNION
 (SELECT t.idtarea, t.idip, t.fecharealizar, t.fecharealizo, c.*, p.nombrecomercial, 1 as estadotarea FROM tarea t JOIN cuentasporpagar c ON t.idcuentaporpagar = c.idcuenta JOIN proveedor p ON c.idproveedor = p.idproveedor WHERE t.fechaveri <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizo AND t.fechaveri %s) 
UNION
 (SELECT t.idtarea, t.idip, t.fecharealizar, t.fecharealizo, c.*, p.nombrecomercial, 0 as estadotarea FROM tarea t JOIN cuentasporpagar c ON t.idcuentaporpagar = c.idcuenta JOIN proveedor p ON c.idproveedor = p.idproveedor WHERE t.fecharealizo is not null AND t.fecharealizo <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizar AND t.fecharealizo %s) 
UNION
 (SELECT t.idtarea, t.idip, t.fecharealizar, t.fecharealizo, c.*, p.nombrecomercial, 1 as estadotarea FROM tarea t JOIN cuentasporpagar c ON t.idcuentaporpagar = c.idcuenta JOIN proveedor p ON c.idproveedor = p.idproveedor WHERE t.fecharealizo <= DATE(%s) AND t.fechaveri is null %s)
UNION
 (SELECT t.idtarea, t.idip, t.fecharealizar, t.fecharealizo, c.*, p.nombrecomercial, 2 as estadotarea FROM tarea t JOIN cuentasporpagar c ON t.idcuentaporpagar = c.idcuenta JOIN proveedor p ON c.idproveedor = p.idproveedor WHERE t.fechaveri = %s %s)  ORDER BY idtarea, estadotarea", 
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($BUSQUEDA_Cotizacion, "defined",$BUSQUEDA_Cotizacion), 
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($BUSQUEDA_Cotizacion, "defined",$BUSQUEDA_Cotizacion), 
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($BUSQUEDA_Cotizacion, "defined",$BUSQUEDA_Cotizacion), 
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($BUSQUEDA_Cotizacion, "defined",$BUSQUEDA_Cotizacion), 
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($BUSQUEDA_Cotizacion, "defined",$BUSQUEDA_Cotizacion));
$rsFacturas = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error() . "<br /> SQL: " . $query_rsFacturas);
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);
?>
<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr><td>Numero</td><td align="center">Ip</td><td>Cliente</td><td align="right">Monto</td>
<td>Opciones</td>
</tr>
</thead>
<tbody>
<?php do{?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> <?php echo $estadotareas[$row_rsCotizaciones['t.estado']];?> tarea<?php echo $row_rsCotizaciones['idtarea']; ?>">
<td><?php echo $row_rsFacturas['nofactura']; ?></td>
<td><?php echo $row_rsFacturas['nombrecomercial']; ?></td>
<td align="right"><?php echo $moneda[$row_rsFacturas['moneda']];?> <?php echo $row_rsFacturas['monto']; ?></td>
<td>
<a href="planeacion.event.historial.php?idtarea=<?php echo $row_rsCotizaciones['idtarea'];?>" class="verHistorial">
<img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/>
</a>
<a href="planeacion.comentario.php?idtarea=<?php echo $row_rsCotizaciones['idtarea']; ?>" class="popup" idtarea="<?php echo $row_rsCotizaciones['idtarea']; ?>">
<img src="images/right.png" border="0" style="display:inline"/>
</a></td>
<?php }while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));?>
</tbody>
</table>

<?php endif;?>




<?
//mysql_free_result($rsFacturas);
?>
