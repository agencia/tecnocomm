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
$query_rsCotizaciones = "SELECT * FROM tarea WHERE idcotizacion is not NULL AND estado = 0";
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);

do{
	
	$asignadas[] = $row_rsCotizaciones['idcotizacion'];
	
}while($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones));

if(isset($_GET['q'])){
	if(is_numeric($_GET['q'])) $idip = $_GET['q']; else $idip = '-1';
	$queryadd = sprintf(' AND (sb.identificador2 like %s OR cl.nombre like %sOR cl.abreviacion like %s OR c.idip = %s OR sb.nombre like %s)',
			GetSQLValueString('%'.$_GET['q'].'%','text'),
			GetSQLValueString('%'.$_GET['q'].'%','text'),								
			GetSQLValueString('%'.$_GET['q'].'%','text'),
			GetSQLValueString($idip,'int'),
			GetSQLValueString('%'.$_GET['q'].'%','text'));
}


$tipe = isset($_GET['type'])?$_GET['type']:0;

switch($tipe){

case 0: $query_rsCotizaciones = "SELECT sb.idcotizacion,sb.estado, sb.idsubcotizacion, sb.fecha, c.idip, sb.identificador2, cl.nombre, sb.nombre AS descripcioncotizacion FROM subcotizacion sb JOIN cotizacion c ON c.idcotizacion = sb.idcotizacion LEFT JOIN cliente cl ON cl.idcliente = c.idcliente JOIN ip ON ip.idip = c.idip WHERE ip.estado < 2 AND (sb.estado = 3 OR sb.estado = 8) ".$queryadd . ' ORDER BY sb.estado ASC';
break;

case 1: $query_rsCotizaciones = sprintf("SELECT t.idtarea, sb.estado, t.estado AS estadotarea, sb.idsubcotizacion, sb.fecha, c.idip, sb.identificador2, cl.nombre, sb.nombre AS descripcioncotizacion FROM subcotizacion sb LEFT JOIN cotizacion c ON c.idcotizacion = sb.idcotizacion JOIN tarea t ON t.idcotizacion = sb.idsubcotizacion  LEFT JOIN cliente cl ON cl.idcliente = c.idcliente WHERE t.fecharealizar = %s %s ",
		GetSQLValueString($_GET['fecha'],"date"), $queryadd);
break;
case 2: 

if (isset($_GET['q'])) {
	$busqueda = "AND (";
  	$busqueda .= sprintf(" sb.identificador2 LIKE %s ", "'%" . $_GET['q'] . "%'");
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
$query_rsCotizaciones = sprintf("(SELECT t.idtarea, t.estado as testado, t.idip, t.fecharealizar, t.fecharealizo, sb.estado, sb.idsubcotizacion, sb.fecha, sb.identificador2, sb.nombre AS descripcioncotizacion,  0 as estadotarea FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion WHERE t.fecharealizo is null AND t.fecharealizar <= DATE(%s) %s) 
UNION
 (SELECT t.idtarea, t.estado as testado, t.idip, t.fecharealizar, t.fecharealizo, sb.estado, sb.idsubcotizacion, sb.fecha, sb.identificador2, sb.nombre AS descripcioncotizacion,  1 as estadotarea FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion WHERE t.fechaveri <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizo AND t.fechaveri %s) 
UNION
 (SELECT t.idtarea, t.estado as testado, t.idip, t.fecharealizar, t.fecharealizo, sb.estado, sb.idsubcotizacion, sb.fecha, sb.identificador2, sb.nombre AS descripcioncotizacion,  0 as estadotarea FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion WHERE t.fecharealizo is not null AND t.fecharealizo <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizar AND t.fecharealizo %s) 
UNION
 (SELECT t.idtarea, t.estado as testado, t.idip, t.fecharealizar, t.fecharealizo, sb.estado, sb.idsubcotizacion, sb.fecha, sb.identificador2, sb.nombre AS descripcioncotizacion,  1 as estadotarea FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion WHERE t.fecharealizo <= DATE(%s) AND t.fechaveri is null %s)
UNION
 (SELECT t.idtarea, t.estado as testado, t.idip, t.fecharealizar, t.fecharealizo, sb.estado, sb.idsubcotizacion, sb.fecha, sb.identificador2, sb.nombre AS descripcioncotizacion,  2 as estadotarea FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion WHERE t.fechaveri = %s %s)  ORDER BY idtarea, estadotarea", 
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

break;
}

mysql_select_db($database_tecnocomm, $tecnocomm);
//$query_rsCotizaciones = "SELECT sb.estado, sb.idsubcotizacion, sb.fecha, c.idip, sb.identificador2, cl.nombre, sb.nombre AS descripcioncotizacion FROM subcotizacion sb LEFT JOIN cotizacion c ON c.idcotizacion = sb.idcotizacion LEFT JOIN cliente cl ON cl.idcliente = c.idcliente WHERE sb.estado = 3";
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);

if($tipe == 1 || $tipe == 2){
$err_usuarios = false;
$query_usuarios = "SELECT u.username, t.idtarea, t.estado FROM tarea t JOIN tarea_usuario tu ON t.idtarea = tu.idtarea LEFT JOIN usuarios u ON u.id = tu.idusuario";
mysql_select_db($database_tecnocomm,$tecnocomm);
$rs_usuarios = mysql_query($query_usuarios,$tecnocomm) or die(mysql_error());

	while($row_usuarios = mysql_fetch_assoc($rs_usuarios)){
		
		$usuarios[$row_usuarios['idtarea']][] = $row_usuarios['username'];
	}
}
?>
<!-- COTIZACIONES SIN TAREA-->
<?php if($tipe == 0){ ?>
<!--agrupar cotizacion -->
<?php 
do{
		$cotizaciones[$row_rsCotizaciones['idcotizacion']] = $row_rsCotizaciones;
}while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones));
?>
<table width="100%" cellspacing="0" cellpadding="4">
<tr>
<td></td>
<td>Ip</td>
<td>Cotizacion</td>
<td>Descripcion</td>
<td>Opciones</td>
</tr>

<? foreach($cotizaciones as $row_rsCotizaciones): ?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?>">
<td>
<?php if(in_array($row_rsCotizaciones['idsubcotizacion'],$asignadas)):?>
<img src="images/bgreen.png" />
<?php endif;?>
</td>
<td valign="top"><?php echo $row_rsCotizaciones['idip']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['identificador2']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['descripcioncotizacion']; ?></td>
<td valign="top"  align="right">
<?php if($tipe == 0){ ?>
<a href="planeacion.asignar.php?tipoelemento=0&valorreferencia=<?php echo $row_rsCotizaciones['idsubcotizacion']; ?>&idip=<?php echo $row_rsCotizaciones['idip']; ?>&idjunta=<?php echo $_GET['idjunta'];?>" tipoelemento="0" valorreferencia="<?php echo $row_rsCotizaciones['idsubcotizacion']; ?>" idip="<?php echo $row_rsCotizaciones['idip']; ?>" class="popup">
<img src="images/right.png" border="0"/>
</a>
<?php } ?>
</td>
</tr>
<?php endforeach; ?>
</table> 
<?php } ?>

<!-- COTIZACIONES PARA HOY-->
<?php if($tipe == 1){ ?>
<table width="100%" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
        	<td>Ip</td>
            <td>Cotizacion</td>
            <td>Descripcion</td>
            <td>Responsable</td>
            <td></td>
        </tr>
    </thead>
<tbody>
<?php do{ ?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> <?php echo $estadotareas[$row_rsCotizaciones['estadotarea']];?>">
<td valign="top"><?php echo $row_rsCotizaciones['idip']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['identificador2']; ?></td>
<td valign="top"><?php echo $row_rsCotizaciones['descripcioncotizacion']; ?></td>
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
</tbody>


</table>

<?php } ?>

<!-- COTIZACIONES DEL DIA ANTERIOR-->
<?php if($tipe == 2){ ?>

<table width="100%" cellspacing="0" cellpadding="4">

<thead>
    <tr>
    	<td>Ip</td><td>Cotizacion</td><td>Descripcion</td><td>Responsable</td><td></td>
    </tr>
</thead>
<tbody>
<?php do{ ?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> <?php echo ($row_rsCotizaciones['testado']==3) ?  $estadotareas[3]: $estadotareas[$row_rsCotizaciones['estadotarea']];?> tarea<?php echo $row_rsCotizaciones['idtarea'];?> too" title="Inicio: <b><?php echo formatDate($row_rsCotizaciones['fecharealizar']); ?></b> 
<?php echo ($row_rsCotizaciones['fecharealizo'] != null) ? "Finalizo: <b>" . formatDate($row_rsCotizaciones['fecharealizo']) . "</b>" : "" ; ?> " >
    <td valign="top"><?php echo $row_rsCotizaciones['idip']; ?></td>
    <td valign="top"><?php echo $row_rsCotizaciones['identificador2']; ?></td>
    <td valign="top"><?php echo $row_rsCotizaciones['descripcioncotizacion']; ?></td>
    <td valign="top"><?php 
    if(is_array($usuarios[$row_rsCotizaciones['idtarea']]))
    echo  implode("<br>",$usuarios[$row_rsCotizaciones['idtarea']]);
    ?></td>
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
