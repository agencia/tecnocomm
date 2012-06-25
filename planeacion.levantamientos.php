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
$query_rsLev = "SELECT * FROM tarea WHERE idlevantamiento is not NULL AND estado = 0";
$rsLev = mysql_query($query_rsLev, $tecnocomm) or die(mysql_error());
$row_rsLev = mysql_fetch_assoc($rsLev);
$totalRows_rsLev = mysql_num_rows($rsLev);

do{
	
	$asignadas[] = $row_rsLev['idlevantamiento'];
	
}while($row_rsLev = mysql_fetch_assoc($rsLev));


$tipe = isset($_GET['type'])?$_GET['type']:0;

if(isset($_GET['q'])){
	
	if(is_numeric($_GET['q'])) $idip = $_GET['q']; else $idip = '-1';
	
	$queryadd = sprintf(' AND (l.descripcion like %s OR l.consecutivo like %s OR c.nombre like %s OR l.idip = %s OR c.abreviacion LIKE %s)',
		GetSQLValueString('%'.$_GET['q'].'%','text'),
		GetSQLValueString('%'.$_GET['q'].'%','text'),								
		GetSQLValueString('%'.$_GET['q'].'%','text'),
		GetSQLValueString($idip,'int'),
		GetSQLValueString('%'.$_GET['q'].'%','text'));
	
}


switch($tipe){

case 0: $query_rsCotizaciones = "SELECT l.*, c.nombre, c.abreviacion FROM levantamientoip l JOIN ip i ON l.idip = i.idip JOIN cliente c ON c.idcliente = i.idcliente JOIN ip ON ip.idip = l.idip WHERE l.estado < 3 AND ip.estado < 2 ".$queryadd . " ORDER BY l.idlevantamientoip DESC";
break;

case 1: $query_rsCotizaciones = sprintf("SELECT *, c.nombre, c.abreviacion, t.estado as estadotarea FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip JOIN ip i ON l.idip = i.idip JOIN cliente c ON c.idcliente = i.idcliente WHERE t.fecharealizar = %s %s",
									   GetSQLValueString($_GET['fecha'],"date"), $queryadd);
break;
case 2: 
if (isset($_GET['q'])) {
	$busqueda = "AND (";
  	$busqueda .= sprintf(" l.consecutivo LIKE %s ", "'%" . $_GET['q'] . "%'");
	if (is_numeric($_GET['q']))
		$busqueda .= sprintf(" OR t.idip = %s ", $_GET['q']);
	$busqueda .= ")";
} else {
	$busqueda = " ";
}
$BUSQUEDA_Lev = "defined";
if (isset($busqueda)) {
  $BUSQUEDA_Lev = $busqueda;
}
$query_rsCotizaciones = sprintf("(SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip, 0 as edo FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip WHERE t.fecharealizo is null AND t.fecharealizar <= DATE(%s) %s) 
UNION
 (SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip, 1 as edo FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip WHERE t.fechaveri <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizo AND t.fechaveri %s) 
UNION
 (SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip, 0 as edo FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip WHERE t.fecharealizo <> DATE(%s) AND t.fecharealizo is not null AND DATE(%s) BETWEEN t.fecharealizar AND t.fecharealizo %s) 
UNION
 (SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip, 1 as edo FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip WHERE t.fecharealizo <= DATE(%s) AND t.fechaveri is null %s)
UNION
 (SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip, 2 as edo FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip WHERE t.fechaveri = %s %s)  ORDER BY idtarea", 
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($BUSQUEDA_Lev, "defined",$BUSQUEDA_Lev), 
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($BUSQUEDA_Lev, "defined",$BUSQUEDA_Lev), 
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($BUSQUEDA_Lev, "defined",$BUSQUEDA_Lev), 
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($BUSQUEDA_Lev, "defined",$BUSQUEDA_Lev), 
 GetSQLValueString($_GET['fecha'], "text"),
 GetSQLValueString($BUSQUEDA_Lev, "defined",$BUSQUEDA_Lev));

break;
}

mysql_select_db($database_tecnocomm, $tecnocomm);
//$query_rsCotizaciones = "SELECT sb.estado, sb.idsubcotizacion, sb.fecha, c.idip, sb.identificador2, cl.nombre, sb.nombre AS descripcioncotizacion FROM subcotizacion sb LEFT JOIN cotizacion c ON c.idcotizacion = sb.idcotizacion LEFT JOIN cliente cl ON cl.idcliente = c.idcliente WHERE sb.estado = 3";
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);


if($tipe == 1 || $tipe == 2){
$err_usuarios = false;
$query_usuarios = "SELECT u.username, t.idtarea FROM tarea t JOIN tarea_usuario tu ON t.idtarea = tu.idtarea LEFT JOIN usuarios u ON u.id = tu.idusuario";
mysql_select_db($database_tecnocomm,$tecnocomm);
$rs_usuarios = mysql_query($query_usuarios,$tecnocomm) or die(mysql_error());

while($row_usuarios = mysql_fetch_assoc($rs_usuarios)){
	
	$usuarios[$row_usuarios['idtarea']][] = $row_usuarios['username'];
}
}
?><?php if($tipe == 0){ ?>
<table width="100%" cellspacing="0" cellpadding="4">
<tr>
  <td>&nbsp;</td>
<td>Ip</td>
<td>Levantamiento</td>
<td>Descripcion</td>
<td>Opciones</td>
</tr>
<? do{ ?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?>">
  <td valign="top"><?php if(in_array($row_rsCotizaciones['idlevantamientoip'],$asignadas)):?>
<img src="images/bgreen.png" />
<?php endif;?></td>
<td valign="top"><?php echo $row_rsCotizaciones['idip']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['consecutivo']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['descripcion']; ?></td>
<td valign="top"  align="right">

<a href="planeacion.asignar.php?tipoelemento=1&valorreferencia=<?php echo $row_rsCotizaciones['idlevantamientoip']; ?>&idip=<?php echo $row_rsCotizaciones['idip']; ?>&idjunta=<?php  echo $_GET['idjunta'];?>" tipoelemento="1" valorreferencia="<?php echo $row_rsCotizaciones['idlevantamientoip']; ?>" idip="<?php echo $row_rsCotizaciones['idip']; ?>" class="popup">
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
<td>Ip</td>
<td>Levantamiento</td><td>Descripcion</td><td>Responsable</td><td></td>
</tr>
</thead>
<tbody>
<?php if ($totalRows_rsCotizaciones > 0) { ?>
<?php do{ ?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?>  <?php echo $estadotareas[$row_rsCotizaciones['estadotarea']];?>">
<td valign="top"><?php echo $row_rsCotizaciones['idip']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['consecutivo']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['descripcion']; ?></td>
<td valign="top">
<?php 

if(is_array($usuarios[$row_rsCotizaciones['idtarea']]))
echo  implode("<br>",$usuarios[$row_rsCotizaciones['idtarea']]);

?>
</td>
<td valign="top">
<a href="planeacion.delEvent.php?idtarea=<?php echo $row_rsCotizaciones['idtarea']; ?>" onclick="delTarea(this); return false;">
<img src="images/close.png" border="0" />
</a></td>
</tr>
<?php }while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones)); ?>
<?php } ?>
</tbody>


</table>

<?php } ?>


<?php if($tipe == 2){ ?>

<table width="100%" cellspacing="0" cellpadding="4">

<thead>
<tr>
<td>Ip</td>
<td width="120px">Lev.</td><td>Descripcion</td><td>Responsable</td><td></td>
</tr>
</thead>
<tbody>
<?php if ($totalRows_rsCotizaciones) { ?>
<?php do{ ?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?>  <?php echo ($row_rsCotizaciones['estado']==3) ?  $estadotareas[3]:$estadotareas[$row_rsCotizaciones['edo']];?> tarea<?php echo $row_rsCotizaciones['idtarea']; ?> too" title="Inicio: <b><?php echo formatDate($row_rsCotizaciones['fecharealizar']); ?></b> 
<?php echo ($row_rsCotizaciones['fecharealizo'] != null) ? "Finalizo: <b>" . formatDate($row_rsCotizaciones['fecharealizo']) . "</b>" : "" ; ?> ">
<td valign="top" width="30px"><?php echo $row_rsCotizaciones['idip']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['consecutivo']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['descripcion']; ?></td>
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
<?php } ?>
</tbody>
</table>
<?php } ?><?php
mysql_free_result($rsCotizaciones);
?>

