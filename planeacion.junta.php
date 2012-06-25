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
$query_rsUsuarios = "SELECT * FROM usuarios WHERE activar = 1 ORDER BY username ASC";
$rsUsuarios = mysql_query($query_rsUsuarios, $tecnocomm) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = "SELECT sb.estado, sb.idsubcotizacion, sb.fecha, c.idip, sb.identificador2, cl.nombre, sb.nombre AS descripcioncotizacion FROM subcotizacion sb LEFT JOIN cotizacion c ON c.idcotizacion = sb.idcotizacion LEFT JOIN cliente cl ON cl.idcliente = c.idcliente WHERE sb.estado = 3";
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenes = "SELECT o.identificador, o.idordenservicio, o.descripcionreporte, c.nombre, o.estado FROM ordenservicio o LEFT JOIN cliente c ON o.idcliente = c.idcliente WHERE o.estado < 3";
$rsOrdenes = mysql_query($query_rsOrdenes, $tecnocomm) or die(mysql_error());
$row_rsOrdenes = mysql_fetch_assoc($rsOrdenes);
$totalRows_rsOrdenes = mysql_num_rows($rsOrdenes);

$idjunta = $_GET['idjunta'];

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsJuntaAnterior = "SELECT * FROM junta WHERE idjunta < $idjunta ORDER BY idjunta DESC LIMIT 1";
$rsJuntaAnterior = mysql_query($query_rsJuntaAnterior, $tecnocomm) or die(mysql_error());
$row_rsJuntaAnterior = mysql_fetch_assoc($rsJuntaAnterior);
$totalRows_rsJuntaAnterior = mysql_num_rows($rsJuntaAnterior);

$colname_rsJunta = "-1";
if (isset($_GET['idjunta'])) {
  $colname_rsJunta = $_GET['idjunta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsJunta = sprintf("SELECT * FROM junta WHERE idjunta = %s", GetSQLValueString($colname_rsJunta, "int"));
$rsJunta = mysql_query($query_rsJunta, $tecnocomm) or die(mysql_error());
$row_rsJunta = mysql_fetch_assoc($rsJunta);
$totalRows_rsJunta = mysql_num_rows($rsJunta);

//OBTENER JUNTA ANTERIOR


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Tecnocomm Junta De Planeacion</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jqueryui.js"></script>
<script language="javascript" type="text/javascript" src="js/planeacion.junta.js"></script>
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css" />
<link href="css/planeacion.junta.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1> Junta De Planeacion</h1>
<div id="jntEncabezado">
<table>
<tr><td>Fecha:</td><td>16 Julio 2010</td></tr>
<tr><td>Inicio Reunion:</td><td>9:00 am</td></tr>
<tr><td>Finalizo Reunion:</td><td>En Proceso</td></tr>
<tr><td>Asistentes:</td><td></td></tr>
</table>
<div><ul id="asistentes">
  <?php do { ?>
    <li idu="<?php echo $row_rsUsuarios['id']; ?>" ><?php echo $row_rsUsuarios['username']; ?></li>
    <?php } while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios)); ?>
</ul></div>
</div>

<div id="jntArea">
<h3 class="tp">LISTA DE TAREAS POR DIA</h3>
<div id="jntTareas">
<h3>Lista De Tareas</h3>
<ul id="Tcoti">
<h4><a href="#" class="st">Cotizaciones</a></h4>
<div class="st">
<?php do{?>
<li>
<table width="100%" >
<tr><td><?php echo $row_rsCotizaciones['identificador2']; ?></td><td align="right"><?php echo $row_rsCotizaciones['estado']; ?></td></tr>
<tr><td colspan="2"><?php echo $row_rsCotizaciones['nombre']; ?></td></tr>
<tr><td colspan="2"><?php echo $row_rsCotizaciones['descripcioncotizacion']; ?></td></tr>
</table>
</li>
<?php }while($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones));?>
</div>
</ul>

<ul id="Tlev">
<h4>Levantamientos</h4>
<div class="st">
<li></li>
</div>
</ul>
<ul id="Tos">
<h4>Ordenes Servicio</h4>
<div class="st">
<?php do{ ?>
<li>
<table width="100%" >
<tr>
<td><?php echo $row_rsOrdenes['identificador']; ?></td><td align="right"><?php echo $row_rsOrdenes['estado']; ?></td></tr>
<tr>
<td colspan="2"><?php echo $row_rsOrdenes['nombre']; ?></td></tr>
<tr><td colspan="2"><?php echo $row_rsOrdenes['descripcionreporte']; ?></td></tr>
</table>
</li>
<?php }while($row_rsOrdenes =  mysql_fetch_assoc($rsOrdenes)); ?>
</div>
</ul>
</div>


<div id="jntAsignadoAnteriro">
<h3> <?php echo formatDate($row_rsJuntaAnterior['fecha']); ?>  </h3>

<ul id="Acoti">
<h4>Cotizaciones</h4>
<li >
<table width="100%" >
<tr><td>C-035-MAHLE</td><td align="right">Proceso</td></tr>
<tr><td colspan="2">UNIVERSIDAD AUTONOMA DE AGUASCALIENTES</td></tr>
<tr><td colspan="2">INSTALACION DE NODO DE RED</td></tr>
</table>
</li>
</ul>
<ul id="Alev">
<h4>Levantamientos</h4>
<li></li>
</ul>
<ul id="Aos">
<h4>Ordenes Servicio</h4>
<li></li>
</ul>
</div>

<div id="jntAsignadoAhora">
<h3><?php echo formatDate($_GET['fecha']); ?></h3>
<ul id="Ncoti">
<h4>Cotizaciones</h4>
<li>
<table width="100%" >
<tr><td>C-035-MAHLE</td><td align="right">Proceso</td></tr>
<tr><td colspan="2">UNIVERSIDAD AUTONOMA DE AGUASCALIENTES</td></tr>
<tr><td colspan="2">INSTALACION DE NODO DE RED</td></tr>
</table>
</li>
</ul>
<ul id="Nlev">
<h4>Levantamientos</h4>
<li></li>
</ul>
<ul id="Nos">
<h4>Ordenes Servicio</h4>
<li></li>
</ul>
</div>
</div>

<?php 
mysql_data_seek($rsUsuarios,0);
mysql_data_seek($rsCotizaciones,0);
mysql_data_seek($rsOrdenes,0);

?>
<div id="jntArea2">
<h3 class="tp">Tareas Por Personal</h3>
<table width="100%">
<tr>
<td width="250px">Tareas</td>
<td>&nbsp;</td>
<?php do{ ?>
<td width="150px" align="center"><?php echo $row_rsUsuarios['username']; ?></td>
<?php } while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios)); ?>
</tr>
<tr>
<?php mysql_data_seek($rsUsuarios,0);?>
<td>
<div>
<h3>Lista De Tareas</h3>
<ul id="Tcoti">
<h4><a href="#" class="st">Cotizaciones</a></h4>
<div class="st">
<?php do{?>
<li>
<table width="100%" >
<tr><td><?php echo $row_rsCotizaciones['identificador2']; ?></td><td align="right"><?php echo $row_rsCotizaciones['estado']; ?></td></tr>
<tr><td colspan="2"><?php echo $row_rsCotizaciones['nombre']; ?></td></tr>
<tr><td colspan="2"><?php echo $row_rsCotizaciones['descripcioncotizacion']; ?></td></tr>
</table>
</li>
<?php }while($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones));?>
</div>
</ul>

<ul id="Tlev">
<h4>Levantamientos</h4>
<div class="st">
<li></li>
</div>
</ul>
<ul id="Tos">
<h4>Ordenes Servicio</h4>
<div class="st">
<?php do{ ?>
<li>
<table width="100%" >
<tr>
<td><?php echo $row_rsOrdenes['identificador']; ?></td><td align="right"><?php echo $row_rsOrdenes['estado']; ?></td></tr>
<tr>
<td colspan="2"><?php echo $row_rsOrdenes['nombre']; ?></td></tr>
<tr><td colspan="2"><?php echo $row_rsOrdenes['descripcionreporte']; ?></td></tr>
</table>
</li>
<?php }while($row_rsOrdenes =  mysql_fetch_assoc($rsOrdenes)); ?>
</div>
</ul>
</div>
</td>
<td>&nbsp;</td>
<?php do{ ?>
<td valign="top" class="usTas <?php if($i%2 == 0)echo "funo";else echo "fdos";$i++;?>">
<ul>
</ul>
</td>
<?php } while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios)); ?>
</tr>
</table>
</div>
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsCotizaciones);

mysql_free_result($rsOrdenes);

mysql_free_result($rsJuntaAnterior);

mysql_free_result($rsJunta);
?>
