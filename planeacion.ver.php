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

$colname_rsTareas = "-1";
if (isset($_GET['fecha'])) {
  $colname_rsTareas = $_GET['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTareas = sprintf("SELECT t.*, tu.idusuario as user FROM tarea t LEFT JOIN tarea_usuario tu ON t.idtarea = tu.idtarea WHERE fecharealizar = date(%s) ", GetSQLValueString($colname_rsTareas, "date"));
$rsTareas = mysql_query($query_rsTareas, $tecnocomm) or die(mysql_error());
$row_rsTareas = mysql_fetch_assoc($rsTareas);
$totalRows_rsTareas = mysql_num_rows($rsTareas);

//echo $query_rsTareas;
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUusarios = "SELECT * FROM usuarios ORDER BY username ASC";
$rsUusarios = mysql_query($query_rsUusarios, $tecnocomm) or die(mysql_error());
$row_rsUusarios = mysql_fetch_assoc($rsUusarios);
$totalRows_rsUusarios = mysql_num_rows($rsUusarios);



$colname_rsFacturas = "-1";
if (isset($_GET['fecha'])) {
  $colname_rsFacturas = $_GET['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = sprintf("SELECT t.*, f.numfactura, c.abreviacion FROM tarea t JOIN factura f ON t.idfactura = f.idfactura  JOIN cliente c ON f.idcliente = c.idcliente WHERE fecharealizar = date(%s) ", GetSQLValueString($colname_rsFacturas, "date"));
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());

$totalRows_rsFacturas = mysql_num_rows($rsFacturas);

$colname_rsLevantamientos = "-1";
if (isset($_GET['fecha'])) {
  $colname_rsLevantamientos = $_GET['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevantamientos = sprintf("SELECT t.*, l.consecutivo, l.descripcion FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip WHERE fecharealizar = date(%s) ", GetSQLValueString($colname_rsLevantamientos, "date"));
$rsLevantamientos = mysql_query($query_rsLevantamientos, $tecnocomm) or die(mysql_error());
$totalRows_rsLevantamientos = mysql_num_rows($rsLevantamientos);

$colname_rsOrdenes = "-1";
if (isset($_GET['fecha'])) {
  $colname_rsOrdenes = $_GET['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenes = sprintf("SELECT t.*, o.descripcion, o.identificador FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio WHERE fecharealizar = date(%s) ", GetSQLValueString($colname_rsOrdenes, "date"));
$rsOrdenes = mysql_query($query_rsOrdenes, $tecnocomm) or die(mysql_error());
$totalRows_rsOrdenes = mysql_num_rows($rsOrdenes);


$colname_rsCotizaciones = "-1";
if (isset($_GET['fecha'])) {
  $colname_rsCotizaciones = $_GET['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = sprintf("SELECT t.*, sb.identificador2, sb.nombre FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion WHERE fecharealizar = date(%s) ", GetSQLValueString($colname_rsCotizaciones, "date"));
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);


$colname_rsCuentas = "-1";
if (isset($_GET['fecha'])) {
  $colname_rsCuentas = $_GET['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCuentas = sprintf("SELECT t.*, ct.nofactura, ct.idcuenta, ct.monto, ct.nameprov  FROM tarea t JOIN (select *,(select nombrecomercial from proveedor where idproveedor= cuentasporpagar.idproveedor) as nameprov from cuentasporpagar  ) ct ON t.idcuentaporpagar	 = ct.idcuenta WHERE fecharealizar = date(%s) ", GetSQLValueString($colname_rsCuentas, "date"));
$rsCuentas = mysql_query($query_rsCuentas, $tecnocomm) or die(mysql_error());
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);
$coti = toArray($rsCotizaciones,'idcotizacion');
$lev = toArray($rsLevantamientos,'idlevantamientoip');
$ord = toArray($rsOrdenes,'idordenservicio');
$facs = toArray($rsFacturas,'idfactura');
$cxp = toArray($rsCuentas,'idcuenta');

do{
	
	$usuarios[$row_rsUusarios['id']] = $row_rsUusarios;
	
}while($row_rsUusarios = mysql_fetch_assoc($rsUusarios));



do{
	
	if($row_rsTareas['idcotizacion'] != ''){
		$cotizaciones[$row_rsTareas['user']][$row_rsTareas['idtarea']] = $row_rsTareas;
		
	}
	
	if($row_rsTareas['idlevantamiento'] != ''){
		$levantamientos[$row_rsTareas['user']][$row_rsTareas['idtarea']] = $row_rsTareas;
	}
	
	if($row_rsTareas['idordenservicio'] != ''){
		$ordenservicio[$row_rsTareas['user']][$row_rsTareas['idtarea']] = $row_rsTareas;
	}
		
	if($row_rsTareas['idfactura'] != ''){
		$facturas[$row_rsTareas['user']][$row_rsTareas['idtarea']] = $row_rsTareas;
	}
	
	if($row_rsTareas['administrativo'] != ''){
		$administrativos[$row_rsTareas['user']][$row_rsTareas['idtarea']] = $row_rsTareas;
	}
	
	if($row_rsTareas['idcuentaporpagar'] != ''){
		$cuentas[$row_rsTareas['user']][$row_rsTareas['idtarea']] = $row_rsTareas;
	}
	
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));


function toArray($rs,$campo)
{
	$array = array();
	
	while($row = mysql_fetch_assoc($rs)){		
		$array[$row[$campo]] = $row;
		
	}
	
	return $array;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Junta de Planacion <?php echo formatDate($_GET['fecha']); ?></title>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jqueryui.js"></script>
<script language="javascript" type="text/javascript" src="js/planeacion.junta2.js"></script>
<script language="javascript" type="text/ecmascript" src="js/funciones.js"></script>
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css" />
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/planeacion.junta.css" rel="stylesheet" type="text/css" />
</head>

<body>


<div id="selUsuario" title="Seleccione Usuarios">
<form name="addEvento" id="addEvento" method="post">
<table width="100%">
<tr>
<td valign="top" style="font-size:14px;"><ul>
  <?php do { ?>
    <li><label><input type="checkbox"  name="usuarios[]" value="<?php echo $row_rsUsuarios['id']; ?>"/><?php echo $row_rsUsuarios['username']; ?></label></li>
    <?php } while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios)); ?>
</ul></td>
<td valign="top"  style="font-size:14px;"><textarea style="width:600px;height:400px" name="comentario" id="comentarioadd"></textarea></td>
<td valign="top"  style="font-size:14px;"><div id="calendario">
</div></td>
</tr>
</table>

<input type="hidden" name="fecha" value="<?php echo date("Y/m/d");?>" id="fechadest"/>
<input type="hidden" name="idjunta" value="<?php echo $_GET['idjunta'];?>" >
<input type="hidden" name="valorreferencia" id="valorreferencia" value="" />
<input type="hidden" name="idip" id="idip" value="" />
<input type="hidden" name="tipoelemento" id="tipoelemento" value="" />

</form>

</div>


<div id="comentario" title="Marcar Como Realizada">
<div style="float:left">
<form name="realizado" id="realizado">
<label>
Estado:
</label>
<select name="estado">
<option value="0"> Pendiente </option>
<option value="1"> Realizado </option>
<option value="2"> Verificado </option>
<option value="3"> Reasignado </option>
</select>
<br />
<br />
<label>Comentario:</label>
<br />
<textarea style="width:600px;height:400px" name="comentario" class="comment">
</textarea>
<input type="hidden" name="idtarea" value="" class='idtarea'/>
<input type="hidden" name="fecha" value="<?php echo date('Y/m/d');?>" id="fechadest"/>
</form>
</div>
<div style="float:right">
<div id="calendario2">
</div>
</div>
</div>

<div id="comentar" style="display:none" title="Actualizar Tarea">

</div>

<div id="historial" title="Comentarios">
<div id="contHistorial">
</div>
</div>
<input type="hidden" name="fechaplaneacion" id="fechaplaneacion" value="<?php echo $_GET['fecha']; ?>" />

<div id="addBitacora" title="Escribir Bitacora">
<div id="contentBitacora"></div>
</div>

<div id="concentrado2">

<h3>Asignaciones Por Personal</h3>
<table cellpadding="0" cellspacing="0" width="100%" style="border:1px solid #39F;">
<thead>
<tr>
<?php $j = 0;?>
<?php foreach($usuarios as $kusuario => $usuario): ?>
<td class="<?php echo ($j%2)?"fdos":" ";$j++;?>" align="center" style="font-weight:bold;font-size:12px;">
<?php echo $usuario['username']?>
</td>
<?php endforeach; ?>
</tr>
</thead>
<tbody>
<?php $j=0;?>
<?php foreach($usuarios as $kusuario => $usuario): ?>
<td class="<?php echo ($j%2)?"fdos":" ";$j++;?>" valign="top">
<img src="images/espacio.gif" width="100px" height="1px"/>
<?php 
if(is_array($cotizaciones[$kusuario])): ?>
<h3>Cotizaciones</h3>
<table width="100%" cellspacing="0" cellpadding="4">
<tr>
<td>Ip</td>
<td>Cotizacion</td>
<td></td>
</tr>
<?php
foreach($cotizaciones[$kusuario] as $idtarea => $cotizacion): 
?>
<tr class="<?php echo($cotizacion['estado'] == 1)?'realizado':'';?> <?php echo($cotizacion['estado'] == 2)?'finalizado':'';?>">
<td valign="top"><?php echo $coti[$cotizacion['idcotizacion']]['idip']; ?></td>
<td valign="top"><?php echo $coti[$cotizacion['idcotizacion']]['identificador2']; ?></td>
<td align="right" >
<a href="#" onclick="addBitacora(<?php echo $idtarea ?>);return false;">
<img src="images/addbitacora.gif" border="0" /></a>
<a href="planeacion.event.historial.php?idtarea=<?php echo $idtarea ?>" class="verHistorial">
<img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/></a>
<a href="#" class="guardarComentario" idtarea="<?php echo $idtarea ?>" ><img src="images/right.png" border="0" style="display:inline"/>
</a></td>
</tr>
<?php endforeach;?>
</table>
<?php endif; //fin de cotizaciones?>

<?php if(is_array($levantamientos[$kusuario])):?>
<h3>Levantamientos</h3>
<table width="100%">
<thead>
<tr><td>Ip</td><td>Levantamiento</td><td></td></tr>
<tr><td colspan="3">Descripcion</td></tr>
</thead>
<tbody>
<?php foreach($levantamientos[$kusuario] as $idtarea =>  $levantamiento): ?>
<tr class="<?php echo($levantamiento['estado'] == 1)?'realizado':'';?> <?php echo($levantamiento['estado'] == 2)?'finalizado':'';?>">
<td><?php echo $lev[$levantamiento['idlevantamiento']]['idip'];?></td>
<td><?php echo $lev[$levantamiento['idlevantamiento']]['consecutivo'];?></td>
<td align="right">
<a href="#" onclick="addBitacora(<?php echo $idtarea ?>);return false;">
<img src="images/addbitacora.gif" border="0" /></a>
<a href="planeacion.event.historial.php?idtarea=<?php echo $idtarea ?>" class="verHistorial">
<img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/></a>
<a href="#" class="guardarComentario" idtarea="<?php echo $idtarea ?>" ><img src="images/right.png" border="0" style="display:inline"/>
</a></td>
</tr>
<tr><td colspan="3"><?php echo $lev[$levantamiento['idlevantamientoip']]['descripcion'];?>
</td></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

<!-- ORDENES DE SERVICIO -->
<?php if(is_array($ordenservicio[$kusuario])):?>
<h3>Ordenes Servicio</h3>
<table width="100%">
<thead>
<tr><td>Ip</td><td>Orden Servicio</td><td></td></tr>
<tr><td colspan="3">Descripcion</td></tr>
</thead>
<tbody>
<?php foreach($ordenservicio[$kusuario] as$idtarea =>  $orden): ?>
<tr class="<?php echo($orden['estado'] == 1)?'realizado':'';?> <?php echo($orden['estado'] == 2)?'finalizado':'';?>">
<td><?php echo $orden['idip'];?></td>
<td><?php echo $ord[$orden['idordenservicio']]['identificador'];?></td>
<td align="right"><a href="#" onclick="addBitacora(<?php echo $idtarea ?>);return false;">
<img src="images/addbitacora.gif" border="0" /></a>
<a href="planeacion.event.historial.php?idtarea=<?php echo $idtarea ?>" class="verHistorial">
<img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/></a>
<a href="#" class="guardarComentario" idtarea="<?php echo $idtarea ?>" ><img src="images/right.png" border="0" style="display:inline"/></a>
</td>
</tr>
<tr></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>



<!-- ADMINISTRATIVO -->
<?php if(is_array($administrativos[$kusuario])):?>
<h3>Administrativo U Operativo</h3>
<table width="100%">
<thead>
<tr>
<td>Descripcion</td>
<td>Opciones</td>
</tr>
</thead>
<tbody>
<?php foreach($administrativos[$kusuario] as $idtarea => $administrativo): ?>
<tr class="<?php echo($administrativo['estado'] == 1)?'realizado':'';?> <?php echo($administrativo['estado'] == 2)?'finalizado':'';?>">
<td><?php echo $administrativo['administrativo'];?></td>
<td align="right"><a href="#" onclick="addBitacora(<?php echo $idtarea ?>);return false;">
<img src="images/addbitacora.gif" border="0" /></a>
<a href="planeacion.event.historial.php?idtarea=<?php echo $idtarea; ?>" class="verHistorial">
<img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/></a>
<a href="#" class="guardarComentario" idtarea="<?php echo $idtarea ?>" ><img src="images/right.png" border="0" style="display:inline"/></a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

<!-- FACTURAS -->
<?php if(is_array($facturas[$kusuario])):?>
<h3>Facturas</h3>
<table width="100%">
<thead>
<tr><td>Ip</td><td>Numero</td><td>Cliente</td></tr>
</thead>
<tbody>
<?php foreach($facturas[$kusuario] as $idtarea => $factura): ?>
<tr class="<?php echo($factura['estado'] == 1)?'realizado':'';?> <?php echo($factura['estado'] == 2)?'finalizado':'';?>" >
<td><?php echo $factura['idip'];?></td>
<td><?php echo $facs[$factura['idfactura']]['numfactura'];?></td>
<td><?php echo $facs[$factura['idfactura']]['abreviacion'];?></td>


<td align="right"><a href="#" onclick="addBitacora(<?php echo $idtarea ?>);return false;">
<img src="images/addbitacora.gif" border="0" /></a>
<a href="planeacion.event.historial.php?idtarea=<?php echo $idtarea ?>" class="verHistorial">
<img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/></a>
<a href="#" class="guardarComentario" idtarea="<?php echo $idtarea ?>" ><img src="images/right.png" border="0" style="display:inline"/></a>
</td>
</tr>
<tr></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>



<!-- Cuentas -->
<?php if(is_array($cuentas[$kusuario])):?>
<h3>Cuentas por Pagar</h3>
<table width="100%">
<thead>
<tr><td>Numero</td><td>Cliente</td></tr>
</thead>
<tbody>
<?php foreach($cuentas[$kusuario] as $idtarea => $cuenta): ?>
<tr class="<?php echo $estadotareas[$cuenta['estado']];?>" >
<td><?php echo $cxp[$cuenta['idcuentaporpagar']]['nofactura'];?></td>
<td><?php echo $cxp[$cuenta['idcuentaporpagar']]['nameprov'];?></td>


<td align="right"><a href="#" onclick="addBitacora(<?php echo $idtarea ?>,'<?php echo $kusuario; ?>');return false;">
<img src="images/addbitacora.gif" border="0" /></a>
<a href="planeacion.event.historial.php?idtarea=<?php echo $idtarea ?>" class="verHistorial">
<img src="images/Stacked Documents 24 h p.png" border="0" style="display:inline"/></a>
<a href="#" onclick="addComent(<?php echo $idtarea ?>); return false;" ><img src="images/right.png" border="0" style="display:inline"/></a>
</td>
</tr>
<tr></tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>


</td>
<?php endforeach; ?>
</tbody>
</table>
</div>
</body>

</html>
<?php
@mysql_free_result($rsTareas);

@mysql_free_result($rsUusarios);

@mysql_free_result($rsAdministrativos);

@mysql_free_result($rsFacturas);

@mysql_free_result($rsLevantamientos);

@mysql_free_result($rsOrdenes);

@mysql_free_result($rsCotizaciones);
?>