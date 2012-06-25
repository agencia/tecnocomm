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
$query_rsFac = "SELECT * FROM tarea WHERE idfactura is not NULL AND estado = 0";
$rsFac = mysql_query($query_rsFac, $tecnocomm) or die(mysql_error());
$row_rsFac = mysql_fetch_assoc($rsFac);
$totalRows_rsFac = mysql_num_rows($rsFac);

do{
	
	$asignadas[] = $row_rsFac['idfactura'];
	
}while($row_rsFac = mysql_fetch_assoc($rsFac));



$tipe = isset($_GET['type'])?$_GET['type']:0;

$moneda = array('$','US$');

if(isset($_GET['q'])){
	
	if(is_numeric($_GET['q'])) $idip = $_GET['q']; else $idip = '-1';
	
	$queryadd = sprintf(' AND (c.abreviacion like %s OR c.nombre like %s OR numfactura = %s OR f.idip = %s)',
							   	GetSQLValueString('%'.$_GET['q'].'%','text'),
								GetSQLValueString('%'.$_GET['q'].'%','text'),	
								GetSQLValueString($idip,'int'),
								GetSQLValueString($idip,'int'));
	
}
?>
<?php if($tipe == 0):?>
<?php 

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT f.*, c.nombre, c.abreviacion, SUM(df.cantidad * df.punitario) AS montofactura FROM factura f LEFT JOIN cliente c ON f.idcliente = c.idcliente LEFT JOIN detallefactura df ON df.idfactura = f.idfactura WHERE f.estado = 0 ".$queryadd."GROUP BY f.idfactura";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);
?>
<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr>
  <td></td>
  <td>Numero</td><td align="center">Ip</td><td>Cliente</td><td align="right">Monto</td>
<td>Opciones</td>
</tr>
</thead>
<tbody>
<?php do{?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?>">
  <td><?php if(in_array($row_rsFacturas['idfactura'],$asignadas)):?>
<img src="images/bgreen.png" />
<?php endif;?></td>
<td><?php echo $row_rsFacturas['numfactura']; ?></td>
<td align="center"><?php echo $row_rsFacturas['idip']; ?></td>
<td><?php echo $row_rsFacturas['nombre']; ?></td>
<td align="right"><?php echo $moneda[$row_rsFacturas['moneda']];?><?php echo $row_rsFacturas['montofactura']; ?></td>
<td><a href="planeacion.asignar.php?tipoelemento=5&valorreferencia=<?php echo $row_rsFacturas['idfactura']; ?>&idip=<?php echo $row_rsFacturas['idip']; ?>&idjunta=<?php  echo $_GET['idjunta'];?>" tipoelemento="5" valorreferencia="<?php echo $row_rsFacturas['idfactura']; ?>" idip="<?php echo $row_rsFacturas['idip']; ?>" class="popup"><img src="images/right.png" border="0"/></a></td>
<?php }while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));?>
</tbody>
</table>

<?php endif;?>

<?php if($tipe == 1):?>
<?php 
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = sprintf("SELECT f.*, c.nombre, c.abreviacion, SUM(df.cantidad * df.punitario) AS montofactura FROM factura f LEFT JOIN cliente c ON f.idcliente = c.idcliente LEFT JOIN detallefactura df ON df.idfactura = f.idfactura  JOIN tarea t ON t.idfactura = f.idfactura WHERE f.estado = 0  AND t.fecharealizar = %s GROUP BY f.idfactura",
																	  GetSQLValueString($_GET['fecha'],'date'));
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
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
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?>">
<td><?php echo $row_rsFacturas['numfactura']; ?></td>
<td align="center"><?php echo $row_rsFacturas['idip']; ?></td>
<td><?php echo $row_rsFacturas['nombre']; ?></td>
<td align="right"><?php echo $moneda[$row_rsFacturas['moneda']];?><?php echo $row_rsFacturas['montofactura']; ?></td>
<td><a href="#" tipoelemento="5" valorreferencia="<?php echo $row_rsFacturas['idfactura']; ?>" idip="<?php echo $row_rsFacturas['idip']; ?>" class="asignNow"><img src="images/right.png" border="0"/></a></td>
<?php }while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));?>
</tbody>
</table>

<?php endif;?>



<?php if($tipe == 2):?>
<?php 

$query_fecha = sprintf("SELECT fecharealizar FROM tarea WHERE fecharealizar < %s GROUP BY fecharealizar ORDER BY fecharealizar DESC LIMIT 1",
					   	GetSQLValueString($_GET['fecha'],"date"));

mysql_select_db($database_tecnocomm,$tecnocomm);
$rs_fecha = mysql_query($query_fecha,$tecnocomm);
$row_fecha = mysql_fetch_assoc($rs_fecha);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = sprintf("SELECT f.*, c.nombre, c.abreviacion, SUM(df.cantidad * df.punitario) AS montofactura FROM factura f LEFT JOIN cliente c ON f.idcliente = c.idcliente LEFT JOIN detallefactura df ON df.idfactura = f.idfactura  JOIN tarea t ON t.idfactura = f.idfactura WHERE f.estado = 0  AND t.fecharealizar = %s GROUP BY f.idfactura",
																	  GetSQLValueString($row_fecha['fecharealizar'],'date'));
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
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
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> tarea<?php echo $row_rsCotizaciones['idtarea']; ?>">
<td><?php echo $row_rsFacturas['numfactura']; ?></td>
<td align="center"><?php echo $row_rsFacturas['idip']; ?></td>
<td><?php echo $row_rsFacturas['nombre']; ?></td>
<td align="right"><?php echo $moneda[$row_rsFacturas['moneda']];?><?php echo $row_rsFacturas['montofactura']; ?></td>
<td>
<a href="planeacion.event.historial.php?idtarea=<?php echo $row_rsCotizaciones['idtarea'];?>" class="verHistorial">
<img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/>
</a>
<a href="planeacion.updateEvent.php?idtarea=<?php echo $row_rsCotizaciones['idtarea']; ?>" class="guardarComentario" idtarea="<?php echo $row_rsCotizaciones['idtarea']; ?>">
<img src="images/right.png" border="0" style="display:inline"/>
</a></td>
<?php }while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));?>
</tbody>
</table>

<?php endif;?>




<?
//mysql_free_result($rsFacturas);
?>
