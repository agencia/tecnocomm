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

if (isset($_GET['b'])) {
	$busqueda = "AND (";
  	$busqueda .= sprintf(" sb.identificador2 LIKE %s ", "'%" . $_GET['b'] . "%'");
	if (is_numeric($_GET['b']))
		$busqueda .= sprintf(" OR t.idip = %s ", $_GET['b']);
	$busqueda .= ")";
} else {
	$busqueda = " ";
}

$USUARIO_Cotizacion = "-1";
if (isset($_GET['iduser'])) {
  $USUARIO_Cotizacion = $_GET['iduser'];
}
$BUSQUEDA_Cotizacion = "defined";
if (isset($busqueda)) {
  $BUSQUEDA_Cotizacion = $busqueda;
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Cotizacion = sprintf("(SELECT t.*, sb.identificador2, sb.nombre, 0 as edo FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo is null AND t.fecharealizar <= DATE(%s) %s) 
UNION
 (SELECT t.*, sb.identificador2, sb.nombre, 1 as edo FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fechaveri <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizo AND t.fechaveri %s) 
UNION
 (SELECT t.*, sb.identificador2, sb.nombre, 0 as edo FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo is not null AND t.fecharealizo <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizar AND t.fecharealizo %s) 
UNION
 (SELECT t.*, sb.identificador2, sb.nombre, 1 as edo FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo <= DATE(%s) AND t.fechaveri is null %s)
UNION
 (SELECT t.*, sb.identificador2, sb.nombre, 2 as edo FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fechaveri = %s %s)  ORDER BY idtarea, edo", 
 GetSQLValueString($USUARIO_Cotizacion, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Cotizacion, "defined",$BUSQUEDA_Cotizacion), 
 GetSQLValueString($USUARIO_Cotizacion, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Cotizacion, "defined",$BUSQUEDA_Cotizacion), 
 GetSQLValueString($USUARIO_Cotizacion, "int"),
  GetSQLValueString($_GET['fecharealizo'], "text"),
  GetSQLValueString($_GET['fecharealizo'], "text"),
  GetSQLValueString($BUSQUEDA_Cotizacion, "defined",$BUSQUEDA_Cotizacion), 
  GetSQLValueString($USUARIO_Cotizacion, "int"), 
  GetSQLValueString($_GET['fecharealizo'], "text"),
  GetSQLValueString($BUSQUEDA_Cotizacion, "defined",$BUSQUEDA_Cotizacion), 
  GetSQLValueString($USUARIO_Cotizacion, "int"), 
  GetSQLValueString($_GET['fecharealizo'], "text"),
  GetSQLValueString($BUSQUEDA_Cotizacion, "defined",$BUSQUEDA_Cotizacion));
$Cotizacion = mysql_query($query_Cotizacion, $tecnocomm) or die(mysql_error() . " Cot");
$row_Cotizacion = mysql_fetch_assoc($Cotizacion);
$totalRows_Cotizacion = mysql_num_rows($Cotizacion);


// LEVANTAMIENTOS
if (isset($_GET['b'])) {
	$busqueda = "AND (";
  	$busqueda .= sprintf(" l.consecutivo LIKE %s ", "'%" . $_GET['b'] . "%'");
	if (is_numeric($_GET['b']))
		$busqueda .= sprintf(" OR t.idip = %s ", $_GET['b']);
	$busqueda .= ")";
} else {
	$busqueda = " ";
}
$USUARIO_Lev = "-1";
if (isset($_GET['iduser'])) {
  $USUARIO_Lev = $_GET['iduser'];
}
$BUSQUEDA_Lev = "defined";
if (isset($busqueda)) {
  $BUSQUEDA_Lev = $busqueda;
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Lev = sprintf("(SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip, 0 as edo FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo is null AND t.fecharealizar <= DATE(%s) %s) 
UNION
 (SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip, 1 as edo FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fechaveri <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizo AND t.fechaveri %s) 
UNION
 (SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip, 0 as edo FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo <> DATE(%s) AND t.fecharealizo is not null AND DATE(%s) BETWEEN t.fecharealizar AND t.fecharealizo %s) 
UNION
 (SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip, 1 as edo FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo <= DATE(%s) AND t.fechaveri is null %s)
UNION
 (SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip, 2 as edo FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fechaveri = %s %s)  ORDER BY idtarea", 
 GetSQLValueString($USUARIO_Lev, "int"),
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Lev, "defined",$BUSQUEDA_Lev), 
 GetSQLValueString($USUARIO_Lev, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Lev, "defined",$BUSQUEDA_Lev), 
 GetSQLValueString($USUARIO_Lev, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Lev, "defined",$BUSQUEDA_Lev), 
 GetSQLValueString($USUARIO_Lev, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Lev, "defined",$BUSQUEDA_Lev), 
 GetSQLValueString($USUARIO_Lev, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Lev, "defined",$BUSQUEDA_Lev));
$Lev = mysql_query($query_Lev, $tecnocomm) or die(mysql_error() . " Lev");
$row_Lev = mysql_fetch_assoc($Lev);
$totalRows_Lev = mysql_num_rows($Lev);

// ORDENES DE SERVICIO
if (isset($_GET['b'])) {
	$busqueda = "AND (";
  	$busqueda .= sprintf(" o.identificador LIKE %s ", "'%" . $_GET['b'] . "%'");
	if (is_numeric($_GET['b']))
		$busqueda .= sprintf(" OR t.idip = %s ", $_GET['b']);
	$busqueda .= ")";
} else {
	$busqueda = " ";
}
$USUARIO_OS = "-1";
if (isset($_GET['iduser'])) {
  $USUARIO_OS = $_GET['iduser'];
}
$BUSQUEDA_OS = "defined";
if (isset($busqueda)) {
  $BUSQUEDA_OS = $busqueda;
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_OS = sprintf("(SELECT t.*, o.descripcion, o.identificador, 0 as edo FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo is null AND t.fecharealizar <= DATE(%s) %s) 
UNION
 (SELECT t.*, o.descripcion, o.identificador, 1 as edo FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fechaveri <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizo AND t.fechaveri %s) 
UNION
 (SELECT t.*, o.descripcion, o.identificador, 0 as edo FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo is not null AND t.fecharealizo <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizar AND t.fecharealizo %s) 
UNION
 (SELECT t.*, o.descripcion, o.identificador, 1 as edo FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo <= DATE(%s) AND t.fechaveri is null %s)
UNION
 (SELECT t.*, o.descripcion, o.identificador, 2 as edo FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fechaveri = %s %s)  ORDER BY idtarea",
 GetSQLValueString($USUARIO_OS, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_OS, "defined",$BUSQUEDA_OS), 
 GetSQLValueString($USUARIO_OS, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_OS, "defined",$BUSQUEDA_OS), 
 GetSQLValueString($USUARIO_OS, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_OS, "defined",$BUSQUEDA_OS), 
 GetSQLValueString($USUARIO_OS, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_OS, "defined",$BUSQUEDA_OS), 
 GetSQLValueString($USUARIO_OS, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_OS, "defined",$BUSQUEDA_OS));
$OS = mysql_query($query_OS, $tecnocomm) or die(mysql_error() . "OS");
$row_OS = mysql_fetch_assoc($OS);
$totalRows_OS = mysql_num_rows($OS);

// ORDENES DE SERVICIO
if (isset($_GET['b'])) {
	$busqueda = "AND (";
  	$busqueda .= sprintf(" tarea.administrativo LIKE %s ", "'%" . $_GET['b'] . "%'");
	if (is_numeric($_GET['b']))
		$busqueda .= sprintf(" OR tarea.idip = %s ", $_GET['b']);
	$busqueda .= ")";
} else {
	$busqueda = " ";
}
$USUARIO_Admin = "-1";
if (isset($_GET['iduser'])) {
  $USUARIO_Admin = $_GET['iduser'];
}
$BUSQUEDA_Admin = "defined";
if (isset($busqueda)) {
  $BUSQUEDA_Admin = $busqueda;
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Admin = sprintf("(SELECT tarea.*, 0 as edo  FROM `tarea` JOIN tarea_usuario tu on tarea.idtarea = tu.idtarea WHERE administrativo is not null AND tu.idusuario = %s AND fecharealizo is null AND fecharealizar <= DATE(%s) %s) 
UNION
 (SELECT tarea.*, 1 as edo FROM tarea JOIN tarea_usuario tu on tarea.idtarea = tu.idtarea WHERE administrativo is not null AND tu.idusuario = %s AND fechaveri <> DATE(%s) AND DATE(%s) BETWEEN fecharealizo AND fechaveri %s) 
UNION
 (SELECT tarea.*, 0 as edo FROM tarea JOIN tarea_usuario tu on tarea.idtarea = tu.idtarea WHERE administrativo is not null AND tu.idusuario = %s AND fecharealizo is not null AND fecharealizo <> DATE(%s) AND DATE(%s) BETWEEN fecharealizar AND fecharealizo %s) 
UNION
 (SELECT tarea.*, 1 as edo FROM tarea JOIN tarea_usuario tu on tarea.idtarea = tu.idtarea WHERE administrativo is not null AND tu.idusuario = %s AND fecharealizo <= DATE(%s) AND fechaveri is null %s)
UNION
 (SELECT tarea.*, 2 as edo FROM tarea JOIN tarea_usuario tu on tarea.idtarea = tu.idtarea WHERE administrativo is not null AND tu.idusuario = %s AND tarea.fechaveri = %s %s)  ORDER BY idtarea", 
 GetSQLValueString($USUARIO_Admin, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Admin, "defined",$BUSQUEDA_Admin), 
 GetSQLValueString($USUARIO_Admin, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Admin, "defined",$BUSQUEDA_Admin), 
 GetSQLValueString($USUARIO_Admin, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Admin, "defined",$BUSQUEDA_Admin), 
 GetSQLValueString($USUARIO_Admin, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Admin, "defined",$BUSQUEDA_Admin), 
 GetSQLValueString($USUARIO_Admin, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Admin, "defined",$BUSQUEDA_Admin));
$Admin = mysql_query($query_Admin, $tecnocomm) or die(mysql_error() . "<br />Admin: " . $query_Admin);
$row_Admin = mysql_fetch_assoc($Admin);
$totalRows_Admin = mysql_num_rows($Admin);
//echo $query_OS ;

// FACTURAS
if (isset($_GET['b'])) {
	$busqueda = "AND (";
  	$busqueda .= sprintf(" c.abreviacion LIKE %s ", "'%" . $_GET['b'] . "%'");
	if (is_numeric($_GET['b'])) {
		$busqueda .= sprintf(" OR t.idip = %s ", $_GET['b']);
		$busqueda .= sprintf(" OR f.numfactura = %s ", $_GET['b']);
	}
	$busqueda .= ")";
} else {
	$busqueda = " ";
}
$USUARIO_Fac = "-1";
if (isset($_GET['iduser'])) {
  $USUARIO_Fac = $_GET['iduser'];
}
$BUSQUEDA_Fac = "defined";
if (isset($busqueda)) {
  $BUSQUEDA_Fac = $busqueda;
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Fac = sprintf("(SELECT t.*, f.numfactura, c.abreviacion, 0 as edo FROM tarea t JOIN factura f ON t.idfactura = f.idfactura  JOIN cliente c ON f.idcliente = c.idcliente JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo is null AND t.fecharealizar <= DATE(%s) %s) 
UNION
 (SELECT t.*, f.numfactura, c.abreviacion, 1 as edo FROM tarea t JOIN factura f ON t.idfactura = f.idfactura JOIN cliente c ON f.idcliente = c.idcliente JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fechaveri <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizo AND t.fechaveri %s) 
UNION
 (SELECT t.*, f.numfactura, c.abreviacion, 0 as edo FROM tarea t JOIN factura f ON t.idfactura = f.idfactura  JOIN cliente c ON f.idcliente = c.idcliente JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo is not null AND t.fecharealizo <> DATE(%s) AND DATE(%s) BETWEEN t.fecharealizar AND t.fecharealizo %s) 
UNION
 (SELECT t.*, f.numfactura, c.abreviacion, 1 as edo FROM tarea t JOIN factura f ON t.idfactura = f.idfactura  JOIN cliente c ON f.idcliente = c.idcliente JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fecharealizo <= DATE(%s) AND t.fechaveri is null %s)
UNION
 (SELECT t.*, f.numfactura, c.abreviacion, 2 as edo FROM tarea t JOIN factura f ON t.idfactura = f.idfactura  JOIN cliente c ON f.idcliente = c.idcliente JOIN tarea_usuario tu on t.idtarea = tu.idtarea WHERE tu.idusuario = %s AND t.fechaveri = %s %s)  ORDER BY idtarea", 
 GetSQLValueString($USUARIO_Fac, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Fac, "defined",$BUSQUEDA_Fac), 
 GetSQLValueString($USUARIO_Fac, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Fac, "defined",$BUSQUEDA_Fac), 
 GetSQLValueString($USUARIO_Fac, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Fac, "defined",$BUSQUEDA_Fac), 
 GetSQLValueString($USUARIO_Fac, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Fac, "defined",$BUSQUEDA_Fac), 
 GetSQLValueString($USUARIO_Fac, "int"), 
 GetSQLValueString($_GET['fecharealizo'], "text"),
 GetSQLValueString($BUSQUEDA_Fac, "defined",$BUSQUEDA_Fac));
$Fac = mysql_query($query_Fac, $tecnocomm) or die(mysql_error() . "<br />" .$query_Fac);
$row_Fac = mysql_fetch_assoc($Fac);
$totalRows_Fac = mysql_num_rows($Fac);
//echo $query_OS ;

$estadotareas = array(0=>"",1=>"realizado",2=>"finalizado", 3=>'reasignada');
?>
<script type="text/javascript" language="javascript">
function refrescaTarea(idtarea, edo)
{
	$("tr.tarea" + idtarea).addClass(edo);
}
</script>
<?php if ($totalRows_Cotizacion > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td colspan="3" align="center"><h3>Cotizaciones</h3></td>
    </tr>
    <tr>
      <td>Cotizaci&oacute;n</td>
    </tr>
    <?php do { ?>
      <tr class="<?php echo ($row_Cotizacion['estado']==3) ? $estadotareas[3]:$estadotareas[$row_Cotizacion['edo']];?> tarea<?php echo $row_Cotizacion['idtarea']; ?>">
        <td valign="top"><?php echo $row_Cotizacion['identificador2']; ?></td>
      </tr>
      <tr>
      <td align="right">IP: <a href="index.php?idip=<?php echo $row_Cotizacion['idip']; ?>&mod=detalleip" class="iraip"><?php echo $row_Cotizacion['idip']; ?></a> <a href="planeacion.addBitacora.php?idtarea=<?php echo $row_Cotizacion['idtarea']; ?>&idusuario=<?php echo $_GET['iduser']; ?>"  class="popup" title="Bitacora">
        <img src="images/addbitacora.gif" width="18" border="0" /></a> <a href="planeacion.event.historial.php?idtarea=<?php echo $row_Cotizacion['idtarea']; ?>" class="verHistorial" title="Comentarios"><img src="images/Stacked Documents 24 h p.png" width="20" border="0" /></a> <a href="planeacion.comentario.php?idtarea=<?php echo $row_Cotizacion['idtarea']; ?>" class="popup" title="Actualizar tarea" ><img src="images/right.png" width="22" border="0" /></a><hr /></td>
        </tr>
      <?php } while ($row_Cotizacion = mysql_fetch_assoc($Cotizacion)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
  
  <?php //LEVANTAMIENTOS ?>
<?php if ($totalRows_Lev > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td colspan="3" align="center"><h3>Levantamientos</h3></td>
    </tr>
    <tr>
      <td>Levantamiento</td>
    </tr>
    <?php do { ?>
      <tr class="<?php echo ($row_Lev['estado']==3) ? $estadotareas[3]:$estadotareas[$row_Lev['edo']];?> tarea<?php echo $row_Lev['idtarea']; ?>">
        <td valign="top"><?php echo $row_Lev['consecutivo']; ?></td>
      </tr>
      <tr>
      	<td align="right">IP: <a href="index.php?idip=<?php echo $row_Lev['idip']; ?>&mod=detalleip" class="iraip"><?php echo $row_Lev['idip']; ?></a><a href="planeacion.addBitacora.php?idtarea=<?php echo $row_Lev['idtarea']; ?>&idusuario=<?php echo $_GET['iduser']; ?>"  class="popup" title="Bitacora">
            <img src="images/addbitacora.gif" border="0" /></a>
            <a href="planeacion.event.historial.php?idtarea=<?php echo $row_Lev['idtarea']; ?>" class="verHistorial" title="Comentarios">
            <img src="images/Stacked Documents 24 h p.png" width="20" border="0" style="display:inline"/></a>
            <a href="planeacion.comentario.php?idtarea=<?php echo $row_Lev['idtarea'];?>" class="popup" title="Actualizar tarea" ><img src="images/right.png" width="22" border="0" style="display:inline"/>
            </a><hr /></td>
        </tr>
      <?php } while ($row_Lev = mysql_fetch_assoc($Lev)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
  
  
  <?php //ORDENES DE SERVICIO ?>
<?php if ($totalRows_OS > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td colspan="3" align="center"><h3>Ordenes de Servicio</h3></td>
    </tr>
    <tr>
      <td>Descripcion</td>
    </tr>
    <?php do { ?>
      <tr class="<?php echo ($row_OS['estado']==3) ? $estadotareas[3]:$estadotareas[$row_OS['edo']];?>">
        <td valign="top"><?php echo $row_OS['identificador']; ?></td>
      </tr>
      <tr>
        <td align="right">IP: <a href="index.php?idip=<?php echo $row_OS['idip']; ?>&mod=detalleip" class="iraip"><?php echo $row_OS['idip']; ?></a> <a href="planeacion.addBitacora.php?idtarea=<?php echo $row_OS['idtarea']; ?>&idusuario=<?php echo $_GET['iduser']; ?>"  class="popup" title="Bitacora"><img src="images/addbitacora.gif" width="18" border="0" /></a>
            <a href="planeacion.event.historial.php?idtarea=<?php echo $row_OS['idtarea']; ?>" class="verHistorial" title="Comentarios">
            <img src="images/Stacked Documents 24 h p.png" width="20" border="0" style="display:inline"/></a>
            <a href="planeacion.comentario.php?idtarea=<?php echo $row_OS['idtarea']; ?>" class="popup" title="Actualizar tarea"><img src="images/right.png" width="22" border="0" style="display:inline"/></a><hr /></td>
      </tr>
      <?php } while ($row_OS = mysql_fetch_assoc($OS)); ?>
  </table>
  <?php } // Show if recordset not empty ?>

  <?php //ADMINISTRATIVO ?>
<?php if ($totalRows_Admin > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td colspan="3" align="center"><h3>Administrativo</h3></td>
    </tr>
    <tr>
      <td>Descripcion</td>
    </tr>
    <?php do { ?>
      <tr class="<?php echo ($row_Admin['estado']==3) ? $estadotareas[3]:$estadotareas[$row_Admin['edo']];?>">
        <td valign="top"><?php echo $row_Admin['administrativo']; ?></td>
      </tr>
      <tr>
        <td align="right"><?php if($row_Admin['idip'] != NULL) { ?>IP: <a href="index.php?idip=<?php echo $row_Admin['idip']; ?>&mod=detalleip" class="iraip"><?php echo $row_Admin['idip']; ?></a> <?php } ?><a href="planeacion.addBitacora.php?idtarea=<?php echo $row_Admin['idtarea'];  ?>&idusuario=<?php echo $_GET['iduser']; ?>"  class="popup" title="Bitacora">
        <img src="images/addbitacora.gif" width="18" border="0" /></a>
        <a href="planeacion.event.historial.php?idtarea=<?php echo $row_Admin['idtarea']; ?>" class="verHistorial" title="Comentarios">
        <img src="images/Stacked Documents 24 h p.png" width="20" border="0" style="display:inline"/></a>
        <a href="planeacion.comentario.php?idtarea=<?php echo $row_Admin['idtarea'];  ?>" class="popup" title="Actualizar tareas" ><img src="images/right.png" width="22" border="0" style="display:inline"/></a><hr /></td>
      </tr>
      <?php } while ($row_Admin = mysql_fetch_assoc($Admin)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
  

  
  <?php //FACTURAS ?>
<?php if ($totalRows_Fac > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td colspan="3" align="center"><h3>Facturas</h3></td>
    </tr>
    <tr>
      <td>Número</td>
      <td>Cliente</td>
    </tr>
    <?php do { ?>
      <tr class="<?php echo ($row_Fac['estado']==3) ? $estadotareas[3] : $estadotareas[$row_Fac['edo']];?>">
        <td valign="top" align="right"><?php echo $row_Fac['numfactura']; ?></td>
        <td><?php echo $row_Fac['abreviacion']; ?></td>
      </tr>
      <tr>
      		<td align="right" colspan="3">IP: <a href="index.php?idip=<?php echo $row_Fac['idip'];?>&mod=detalleip" class="iraip"><?php echo $row_Fac['idip'];?></a> <a href="planeacion.addBitacora.php?idtarea=<?php echo $row_Fac['idtarea']; ?>&idusuario=<?php echo $_GET['iduser'];; ?>"  class="popup" title="Bitacora">
            <img src="images/addbitacora.gif" border="0" /></a>
            <a href="planeacion.event.historial.php?idtarea=<?php echo $row_Fac['idtarea']; ?>" class="verHistorial" title="Comentarios">
            <img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/></a>
            <a href="planeacion.comentario.php?idtarea=<?php echo $row_Fac['idtarea']; ?>" class="popup" title="Actualizar tarea" ><img src="images/right.png" border="0" style="display:inline"/></a><hr /></td>
            </tr>
      <?php } while ($row_Fac = mysql_fetch_assoc($Fac)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
  
  
<?php
mysql_free_result($Cotizacion);
?>
