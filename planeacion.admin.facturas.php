<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
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
$query_rsFacturas = "SELECT f.*, c.nombre, c.abreviacion, SUM(df.cantidad * df.punitario) AS montofactura FROM factura f LEFT JOIN cliente c ON f.idcliente = c.idcliente LEFT JOIN detallefactura df ON df.idfactura = f.idfactura JOIN ip ON ip.idip = f.idip WHERE f.estado < 5 AND ip.estado < 2 ".$queryadd." GROUP BY f.idfactura";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);
?>
<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr>
  <td>&nbsp;</td>
  <td>Numero</td><td align="center">Ip</td><td>Cliente</td><td align="right">Monto</td>
<td>Opciones</td>
</tr>
</thead>
<tbody>
<?php do{?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> <?php echo $estadotareas[$row_rsFacturas['estadotarea']];?>">
  <td><?php if(in_array($row_rsFacturas['idfactura'],$asignadas)):?>
<img src="images/bgreen.png" />
<?php endif;?></td>
<td><?php echo $row_rsFacturas['numfactura']; ?></td>
<td align="center"><?php echo $row_rsFacturas['idip']; ?></td>
<td align="right"><?php echo $row_rsFacturas['montofactura']; ?></td>
<td><?php echo $row_rsFacturas['nombre']; ?></td>
<td><a href="planeacion.asignar.php?tipoelemento=5&valorreferencia=<?php echo $row_rsFacturas['idfactura']; ?>&idip=<?php echo $row_rsFacturas['idip']; ?>&idjunta=<?php  echo $_GET['idjunta'];?>" tipoelemento="5" valorreferencia="<?php echo $row_rsFacturas['idfactura']; ?>" idip="<?php echo $row_rsFacturas['idip']; ?>" class="popup">
<img src="images/right.png" border="0"/></a></td>
</tr>
<?php }while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));?>
</tbody>
</table>

<?php endif;?>




<?php if($tipe == 1):?>
<?php 
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = sprintf("SELECT t.*,f.*, c.nombre, c.abreviacion, SUM(df.cantidad * df.punitario) AS montofactura FROM factura f LEFT JOIN cliente c ON f.idcliente = c.idcliente LEFT JOIN detallefactura df ON df.idfactura = f.idfactura  JOIN tarea t ON t.idfactura = f.idfactura WHERE f.estado < 5  AND t.fecharealizar = %s %s GROUP BY f.idfactura",
																	  GetSQLValueString($_GET['fecha'],'date'), $queryadd);
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
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> <?php echo $estadotareas[$row_rsFacturas['estadotarea']];?>">
<td><?php echo $row_rsFacturas['numfactura']; ?></td>
<td align="center"><?php echo $row_rsFacturas['idip']; ?></td>
<td align="right"><?php echo $row_rsFacturas['montofactura']; ?></td>
<td><?php echo $row_rsFacturas['nombre']; ?></td>
<td>
<a href="planeacion.delEvent.php?idtarea=<?php echo $row_rsFacturas['idtarea']; ?>" onclick="delTarea(this); return false;">
<img src="images/close.png" border="0" />
</a>
</td>
</tr>
<?php }while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));?>
</tbody>
</table>

<?php endif;?>



<?php if($tipe == 2):?>
<?php 

if (isset($_GET['q'])) {
	$busqueda = "AND (";
  	$busqueda .= sprintf(" c.abreviacion LIKE %s ", "'%" . $_GET['q'] . "%'");
	if (is_numeric($_GET['q']))
		$busqueda .= sprintf(" OR f.numfactura = %s ", $_GET['q']);
	$busqueda .= ")";
} else {
	$busqueda = " ";
}

$BUSQUEDA_Cotizacion = "defined";
if (isset($busqueda)) {
  $BUSQUEDA_Cotizacion = $busqueda;
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = sprintf("(SELECT t.*,f.*, c.nombre, c.abreviacion, SUM(df.cantidad * df.punitario) AS montofactura, 0 as estadotarea FROM tarea t JOIN factura f ON t.idfactura = f.idfactura LEFT JOIN cliente c ON f.idcliente = c.idcliente LEFT JOIN detallefactura df ON df.idfactura = f.idfactura WHERE t.fecharealizo is null AND t.fecharealizar <= DATE(%s) %s GROUP BY f.idfactura) 
UNION
 (SELECT t.*,f.*, c.nombre, c.abreviacion, SUM(df.cantidad * df.punitario) AS montofactura, 1 as estadotarea FROM tarea t JOIN factura f ON t.idfactura = f.idfactura LEFT JOIN cliente c ON f.idcliente = c.idcliente LEFT JOIN detallefactura df ON df.idfactura = f.idfactura WHERE t.fechaveri <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizo AND t.fechaveri %s GROUP BY f.idfactura) 
UNION
 (SELECT t.*,f.*, c.nombre, c.abreviacion, SUM(df.cantidad * df.punitario) AS montofactura, 0 as estadotarea FROM tarea t JOIN factura f ON t.idfactura = f.idfactura LEFT JOIN cliente c ON f.idcliente = c.idcliente LEFT JOIN detallefactura df ON df.idfactura = f.idfactura WHERE t.fecharealizo is not null AND t.fecharealizo <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizar AND t.fecharealizo %s GROUP BY f.idfactura) 
UNION
 (SELECT t.*,f.*, c.nombre, c.abreviacion, SUM(df.cantidad * df.punitario) AS montofactura, 1 as estadotarea FROM tarea t JOIN factura f ON t.idfactura = f.idfactura LEFT JOIN cliente c ON f.idcliente = c.idcliente LEFT JOIN detallefactura df ON df.idfactura = f.idfactura WHERE t.fecharealizo <= DATE(%s) AND t.fechaveri is null %s GROUP BY f.idfactura)
UNION
 (SELECT t.*,f.*, c.nombre, c.abreviacion, SUM(df.cantidad * df.punitario) AS montofactura, 2 as estadotarea FROM tarea t JOIN factura f ON t.idfactura = f.idfactura LEFT JOIN cliente c ON f.idcliente = c.idcliente LEFT JOIN detallefactura df ON df.idfactura = f.idfactura WHERE t.fechaveri = %s %s GROUP BY f.idfactura)  ORDER BY idtarea, estadotarea", 
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

$rsFacturas = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas)
?>
<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr><td>Numero</td><td align="center">Ip</td><td>Cliente</td><td align="right">Monto</td>
<td>Opciones</td>
</tr>
</thead>
<tbody>
<?php if ($totalRows_rsFacturas>0) { ?>
<?php do{?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> <?php echo ($row_rsFacturas['estado']==3) ? $estadotareas[3]:$estadotareas[$row_rsFacturas['estadotarea']];?> tarea<?php echo $row_rsFacturas['idtarea']; ?>">
    <td><?php echo $row_rsFacturas['numfactura']; ?></td>
    <td align="center"><?php echo $row_rsFacturas['idip']; ?></td>
    <td align="right"><?php echo $row_rsFacturas['montofactura']; ?></td>
    <td><?php echo $row_rsFacturas['nombre']; ?></td>
    <td>
    <a href="planeacion.event.historial.php?idtarea=<?php echo $row_rsFacturas['idtarea'];?>" class="popup verHistorial">
    <img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/>
    </a>
    <a href="planeacion.comentario.php?idtarea=<?php echo $row_rsFacturas['idtarea']; ?>"  class="popup guardarComentario" idtarea="<?php echo $row_rsFacturas['idtarea']; ?>">
    <img src="images/right.png" border="0" style="display:inline"/>
    </a></td>
</tr>
<?php }while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));?>
<?php } ?>
</tbody>
</table>
<?php endif;?>