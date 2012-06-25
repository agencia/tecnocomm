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
$query_usuarios = "SELECT * FROM tarea t JOIN tarea_usuario tu ON t.idtarea = tu.idtarea JOIN usuarios u ON u.id = tu.idusuario WHERE administrativo <> 'null'";
$rs_usuarios = mysql_query($query_usuarios, $tecnocomm) or die(mysql_error());
$totalRows_usuarios = mysql_num_rows($rs_usuarios);

$tipe = isset($_GET['type'])?$_GET['type']:0;

while($row_usuarios = mysql_fetch_assoc($rs_usuarios)){
	
	$usuarios[$row_usuarios['idtarea']][] = $row_usuarios['username'];

}

?><?php if($tipe == 1):?>
<?php 
if (isset($_GET['q'])) {
	$queryadd = sprintf(' AND administrativo LIKE %s', GetSQLValueString('%'.$_GET['q'].'%','text'));
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = sprintf("SELECT * FROM tarea WHERE fecharealizar = %s AND administrativo is not NULL %s",GetSQLValueString($_GET['fecha'],'date'), $queryadd);
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);
?>
<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr>
    <td>Tarea</td>
    <td>Responsable</td>
    <td>Opciones</td>
</tr>
</thead>
<tbody>
<?php do{?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> <?php echo $estadotareas[$row_rsFacturas['estado']];?>">
<td><?php echo $row_rsFacturas['administrativo'];?></td>
<td><?php 

if(is_array($usuarios[$row_rsFacturas['idtarea']]))
echo  implode("<br>",$usuarios[$row_rsFacturas['idtarea']]);

?></td>
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

	$busqueda = "AND (";
  	$busqueda .= sprintf(" t.administrativo LIKE %s ", "'%" . $_GET['q'] . "%'");
	if (is_numeric($_GET['q']))
		$busqueda .= sprintf(" OR t.idip = %s ", $_GET['q']);
	$busqueda .= ")";

$BUSQUEDA_Cotizacion = "defined";
if (isset($busqueda)) {
  $BUSQUEDA_Cotizacion = $busqueda;
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = sprintf("
 (SELECT t.idtarea, t.estado, t.idip, t.fecharealizar, t.fecharealizo, t.administrativo,  0 as estadotarea FROM tarea t WHERE t.fecharealizo is null AND t.fecharealizar <= DATE(%s) %s) 
UNION
 (SELECT t.idtarea, t.estado, t.idip, t.fecharealizar, t.fecharealizo, t.administrativo, 1 as estadotarea FROM tarea t WHERE t.fechaveri <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizo AND t.fechaveri %s) 
UNION
 (SELECT t.idtarea, t.estado, t.idip, t.fecharealizar, t.fecharealizo, t.administrativo, 0 as estadotarea FROM tarea t WHERE t.fecharealizo is not null AND t.fecharealizo <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizar AND t.fecharealizo %s) 
UNION
 (SELECT t.idtarea, t.estado, t.idip, t.fecharealizar, t.fecharealizo, t.administrativo, 1 as estadotarea FROM tarea t WHERE t.fecharealizo <= DATE(%s) AND t.fechaveri is null %s)
UNION
 (SELECT t.idtarea, t.estado, t.idip, t.fecharealizar, t.fecharealizo, t.administrativo, 2 as estadotarea FROM tarea t WHERE t.fechaveri = %s %s)  ORDER BY idtarea, estadotarea", 
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
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);
//echo $query_rsFacturas;
?>
<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr>
    <td width="60">IP</td>
    <td>Tarea</td>
    <td width="100">Responsable</td>
    <td width="100">Opciones</td>
</tr>
</thead>
<tbody>
<?php do{?>
<tr class="<?php echo ($i%2)?"fdos":" ";$i++;?> <?php echo ($row_rsFacturas['estado']==3) ? $estadotareas[3]:$estadotareas[$row_rsFacturas['estadotarea']];?> tarea<?php echo $row_rsFacturas['idtarea']; ?>">
    <td width="30"><?php echo $row_rsFacturas['idip'];?></td>
    <td><?php echo $row_rsFacturas['administrativo'];?></td>
    <td><?php 
    if(is_array($usuarios[$row_rsFacturas['idtarea']]))
    echo  implode("<br>",$usuarios[$row_rsFacturas['idtarea']]);
    ?></td>
    <td><a href="planeacion.event.historial.php?idtarea=<?php echo $row_rsFacturas['idtarea'];?>" class="verHistorial">
    <img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/>
    </a>
    <a href="planeacion.comentario.php?idtarea=<?php echo $row_rsFacturas['idtarea']; ?>" class="popup" idtarea="<?php echo $row_rsFacturas['idtarea']; ?>">
    <img src="images/right.png" border="0" style="display:inline"/>
    </a></td>
</tr>
<?php }while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));?>
</tbody>
</table>
<?php endif;?>
<?
//mysql_free_result($rsFacturas);
?>
<?php
mysql_free_result($rs_usuarios);
?>
