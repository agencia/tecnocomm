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
$query_rsOr = "SELECT * FROM tarea WHERE idordenservicio is not NULL AND estado = 0";
$rsOr = mysql_query($query_rsOr, $tecnocomm) or die(mysql_error());
$row_rsOr = mysql_fetch_assoc($rsOr);
$totalRows_rsOr = mysql_num_rows($rsOr);

do{
	
	$asignadas[] = $row_rsOr['idordenservicio'];
	
}while($row_rsOr = mysql_fetch_assoc($rsOr));

$tipe = isset($_GET['type'])?$_GET['type']:0;


if(isset($_GET['q'])){
	
	if(is_numeric($_GET['q'])) $idip = $_GET['q']; else $idip = '-1';
	
	$queryadd = sprintf(' AND (o.descripcionreporte like %s OR o.identificador like %s OR c.nombre like %s OR o.idip = %s OR c.abreviacion like %s)',
		GetSQLValueString('%'.$_GET['q'].'%','text'),
		GetSQLValueString('%'.$_GET['q'].'%','text'),								
		GetSQLValueString('%'.$_GET['q'].'%','text'),
		GetSQLValueString($idip,'int'),
		GetSQLValueString('%'.$_GET['q'].'%','text'));
	
}

switch($tipe){

case 0: $query_rsCotizaciones = "SELECT o.*, c.nombre, c.abreviacion FROM ordenservicio o JOIN ip i ON i.idip = o.idip JOIN cliente c ON i.idcliente = c.idcliente JOIN ip ON ip.idip = o.idip WHERE o.estado < 3 AND ip.estado < 2".$queryadd;
break;

case 1: $query_rsCotizaciones = sprintf("SELECT *, c.nombre, c.abreviacion FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN ip i ON i.idip = o.idip JOIN cliente c ON i.idcliente = c.idcliente WHERE t.fecharealizar = %s %s",
									   GetSQLValueString($_GET['fecha'],"date"), $queryadd);
break;
case 2: 

if (isset($_GET['q'])) {
	$busqueda = "AND (";
  	$busqueda .= sprintf(" o.descripcionreporte LIKE %s ", "'%" . $_GET['q'] . "%'");
  	$busqueda .= sprintf("OR o.identificador LIKE %s ", "'%" . $_GET['q'] . "%'");
  	$busqueda .= sprintf("OR c.nombre LIKE %s ", "'%" . $_GET['q'] . "%'");
  	$busqueda .= sprintf("OR c.abreviacion LIKE %s ", "'%" . $_GET['q'] . "%'");
	if (is_numeric($_GET['q']))
		$busqueda .= sprintf(" OR t.idip = %s ", $_GET['q']);
	$busqueda .= ")";
} else {
	$busqueda = " ";
}

$BUSQUEDA_Cotizacion = "defined";
if (isset($busqueda)) {
  $BUSQUEDA_Cotizacion = $busqueda;
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = sprintf("
 (SELECT t.*, t.estado as estadotarea, 0 as estadotarea, o.identificador, o.descripcionreporte, c.nombre, c.abreviacion FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN ip i ON i.idip = o.idip JOIN cliente c ON i.idcliente = c.idcliente WHERE t.fecharealizo is null AND t.fecharealizar <= DATE(%s) %s) 
UNION
 (SELECT t.*, t.estado as estadotarea, 1 as estadotarea, o.identificador, o.descripcionreporte, c.nombre, c.abreviacion FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN ip i ON i.idip = o.idip JOIN cliente c ON i.idcliente = c.idcliente WHERE t.fechaveri <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizo AND t.fechaveri %s) 
UNION
 (SELECT t.*, t.estado as estadotarea, 0 as estadotarea, o.identificador, o.descripcionreporte, c.nombre, c.abreviacion FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN ip i ON i.idip = o.idip JOIN cliente c ON i.idcliente = c.idcliente WHERE t.fecharealizo is not null AND t.fecharealizo <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizar AND t.fecharealizo %s) 
UNION
 (SELECT t.*, t.estado as estadotarea, 1 as estadotarea, o.identificador, o.descripcionreporte, c.nombre, c.abreviacion FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN ip i ON i.idip = o.idip JOIN cliente c ON i.idcliente = c.idcliente WHERE t.fecharealizo <= DATE(%s) AND t.fechaveri is null %s)
UNION
 (SELECT t.*, t.estado as estadotarea, 2 as estadotarea, o.identificador, o.descripcionreporte, c.nombre, c.abreviacion FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN ip i ON i.idip = o.idip JOIN cliente c ON i.idcliente = c.idcliente WHERE t.fechaveri = %s %s)  ORDER BY idtarea", 
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

//echo $query_rsCotizaciones;

break;
}

mysql_select_db($database_tecnocomm, $tecnocomm);
//$query_rsCotizaciones = "SELECT sb.estado, sb.idsubcotizacion, sb.fecha, c.idip, sb.identificador2, cl.nombre, sb.nombre AS descripcioncotizacion FROM subcotizacion sb LEFT JOIN cotizacion c ON c.idcotizacion = sb.idcotizacion LEFT JOIN cliente cl ON cl.idcliente = c.idcliente WHERE sb.estado = 3";
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);


if($tipe == 1 || $tipe == 2){
$err_usuarios = false;
$query_usuarios = sprintf("SELECT u.username, t.idtarea FROM tarea t JOIN tarea_usuario tu ON t.idtarea = tu.idtarea LEFT JOIN usuarios u ON u.id = tu.idusuario ",
						  GetSQLValueString($_GET['fecha'],"date"));
mysql_select_db($database_tecnocomm,$tecnocomm);
$rs_usuarios = mysql_query($query_usuarios,$tecnocomm) or die(mysql_error());

while($row_usuarios = mysql_fetch_assoc($rs_usuarios)){
	
	$usuarios[$row_usuarios['idtarea']][] = $row_usuarios['username'];

}

}

?>

<?php if($tipe == 0){ ?>
<table width="100%" cellspacing="0" cellpadding="4">
<tr>
  <td>&nbsp;</td>
<td>Ip</td>
<td>Orden Servicio</td>
<td>Cliente</td>
<td>Descripcion</td>
<td>Opciones</td>
</tr>

<? do{ ?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?>">
  <td valign="top"><?php if(in_array($row_rsCotizaciones['idip'],$asignadas)):?>
<img src="images/bgreen.png" />
<?php endif;?></td>
<td valign="top"><?php echo $row_rsCotizaciones['idip']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['identificador']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['abreviacion']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['descripcionreporte']; ?></td>
<td valign="top"  align="right">

<a href="planeacion.asignar.php?tipoelemento=2&valorreferencia=<?php echo $row_rsCotizaciones['idordenservicio']; ?>&idip=<?php echo $row_rsCotizaciones['idip']; ?>&idjunta=<?php  echo $_GET['idjunta'];?>" tipoelemento="2" valorreferencia="<?php echo $row_rsCotizaciones['idordenservicio']; ?>" idip="<?php echo $row_rsCotizaciones['idip']; ?>" class="popup">
<img src="images/right.png" border="0"/>
</a>

</td>
</tr>
<?php } while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones)); ?>

</table> 
<?php } ?>

<?php if($tipe == 1){ ?>

<table width="100%" cellspacing="0" cellpadding="4">

<thead>
<tr>
<td>Ip</td><td>Orden Servicio</td><td>Cliente</td><td>Descripcion</td><td>Responsable</td><td></td>
</tr>
</thead>
<tbody>
<?php do{ ?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> <?php echo $estadotareas[$row_rsCotizaciones['estadotarea']];?>">
<td valign="top"><?php echo $row_rsCotizaciones['idip']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['identificador']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['abreviacion']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['descripcionreporte']; ?></td>
<td valign="top">
<?php 

if(is_array($usuarios[$row_rsCotizaciones['idtarea']]))
echo  implode("<br>",$usuarios[$row_rsCotizaciones['idtarea']]);

?>
</td>
<td valign="top">
<a href="planeacion.delEvent.php?idtarea=<?php echo $row_rsCotizaciones['idtarea']; ?>" onclick="delTarea(this); return false;">
<img src="images/close.png" border="0" />
</a>
</td>
</tr>
<?php }while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones)); ?>
</tbody>
</table>

<?php } ?>


<?php if($tipe == 2){ ?>

<table width="100%" cellspacing="0" cellpadding="4">

<thead>
<tr>
<td>Ip</td><td>Orden Servicio</td><td>Cliente</td><td>Descripcion</td><td>Responsable</td><td></td>
</tr>
</thead>
<tbody>
<?php do{ ?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> <?php echo ($row_rsCotizaciones['estado']==3) ?  $estadotareas[3]:$estadotareas[$row_rsCotizaciones['estadotarea']];?> tarea<?php echo $row_rsCotizaciones['idtarea'];?> too" title="Inicio: <b><?php echo formatDate($row_rsCotizaciones['fecharealizar']); ?></b> 
<?php echo ($row_rsCotizaciones['fecharealizo'] != null) ? "Finalizo: <b>" . formatDate($row_rsCotizaciones['fecharealizo']) . "</b>" : "" ; ?> ">
<td valign="top"><?php echo $row_rsCotizaciones['idip']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['identificador']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['abreviacion']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['descripcionreporte']; ?></td>
<td valign="top">
<?php 

if(is_array($usuarios[$row_rsCotizaciones['idtarea']]))
echo  implode("<br>",$usuarios[$row_rsCotizaciones['idtarea']]);

?>
</td>
<td valign="top" width="60px">
<a href="planeacion.event.historial.php?idtarea=<?php echo $row_rsCotizaciones['idtarea'];?>" class="verHistorial">
<img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/></a>
<a href="planeacion.comentario.php?idtarea=<?php echo $row_rsCotizaciones['idtarea']; ?>" class="popup" idtarea="<?php echo $row_rsCotizaciones['idtarea']; ?>"><img src="images/right.png" border="0" style="display:inline"/></a>
</td>
</tr>
<?php }while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones)); ?>
</tbody>
</table>
<?php } ?>

<?php
mysql_free_result($rsCotizaciones);
?>

