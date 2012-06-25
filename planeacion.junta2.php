<?php
session_start(); ?>
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

$colname_rs_Junta = "-1";
if (isset($_GET['idjunta'])) {
  $colname_rs_Junta = $_GET['idjunta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rs_Junta = sprintf("SELECT * FROM junta WHERE idjunta = %s", GetSQLValueString($colname_rs_Junta, "int"));
$rs_Junta = mysql_query($query_rs_Junta, $tecnocomm) or die(mysql_error());
$row_rs_Junta = mysql_fetch_assoc($rs_Junta);
$totalRows_rs_Junta = mysql_num_rows($rs_Junta);

$colname_rsAsistentes = "-1";
if (isset($_GET['idjunta'])) {
  $colname_rsAsistentes = $_GET['idjunta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAsistentes = sprintf("SELECT * FROM junta_asistente ju LEFT JOIN usuarios u ON u.id = ju.idusuario WHERE idjunta = %s", GetSQLValueString($colname_rsAsistentes, "int"));
$rsAsistentes = mysql_query($query_rsAsistentes, $tecnocomm) or die(mysql_error());
$row_rsAsistentes = mysql_fetch_assoc($rsAsistentes);
$totalRows_rsAsistentes = mysql_num_rows($rsAsistentes);

$colname_rsAnterior = "-1";
if (isset($row_rs_Junta['fecha'])) {
  $colname_rsAnterior = $row_rs_Junta['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAnterior = sprintf("SELECT * FROM junta WHERE fecha < %s ORDER BY fecha DESC", GetSQLValueString($colname_rsAnterior, "date"));
$rsAnterior = mysql_query($query_rsAnterior, $tecnocomm) or die(mysql_error());
$row_rsAnterior = mysql_fetch_assoc($rsAnterior);
$totalRows_rsAnterior = mysql_num_rows($rsAnterior);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsSubcontratistas = "SELECT * FROM subcontratistas ORDER BY abreviacion ASC";
$rsSubcontratistas = mysql_query($query_rsSubcontratistas, $tecnocomm) or die(mysql_error());
$row_rsSubcontratistas = mysql_fetch_assoc($rsSubcontratistas);
$totalRows_rsSubcontratistas = mysql_num_rows($rsSubcontratistas);
;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title>Junta De Planeacion</title>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jqueryui.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery.qtip-1.0.0-rc3.min.js"></script>
<script language="javascript" type="text/javascript" src="js/planeacion.junta2.js"></script>
<script language="javascript" type="text/ecmascript" src="js/funciones.js"></script>
<?php if ($row_rs_Junta['horafin'] == null) { ?>
<script type="text/javascript">
			$(document).ready(function(){
				checkAlerts();
			});

			function checkAlerts() {
				alert("La junta no ha sido cerrada aun");
				setTimeout(checkAlerts, 900000);
			};
</script><?php } ?>
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css" />
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/planeacion.junta.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Reunion De Planeacion</h1>
<table cellspacing="0" class="titjuntac">
<tr><td class="f1">Fecha:</td>
<td><?php echo formatDate($row_rs_Junta['fecha']); ?></td>
<td class="f1">Inicio Reunion:</td>
<td><?php echo $row_rs_Junta['horainicio']; ?></td>
<td class="f1">Finalizo Reunion:</td>
<td><?php echo ($row_rs_Junta['horafin'] != null) ? $row_rs_Junta['horafin'] : '<a href="planeacion.junta.terminar.php" class="popup">Terminar Reunion</a>'; ?></td></tr>
<tr>
<td class="f1">Asistentes:</td><td colspan="5">
<?php do{
	echo $row_rsAsistentes['username']."&nbsp;|&nbsp;";
}while($row_rsAsistentes = mysql_fetch_assoc($rsAsistentes));?>
<a href="planeacion.usuarios.php?idjunta=<?php echo $row_rs_Junta['idjunta']; ?>" class="popup">Añadir</a></td></tr>
<tr><td colspan="7">
<a href="planeacion.gral.print.php?idjunta=<?php echo $row_rs_Junta['idjunta']; ?>" class="popup"><img src="images/Imprimir2.png" border="none" />IMPRIMIR</a>
</tr>
</table>
<div>
<table>
    <tr class="realizado"><td>Realizado</td></tr>
    <tr class="finalizado"><td>Verificado</td></tr>
    <tr class="reasignada"><td>Reasignado</td></tr>
</table>
</div>

<div id="tabsTareas">
	<ul>
		<li><a href="#tabst-1">Pendientes</a></li>
		<li><a href="#tabst-2">Reunion Anterior</a></li>
		<li><a href="#tabst-3">Reunion Actual</a></li>
    </ul>
    <div id="tabst-1">
        <div id="tareas">
            <h3>Pendientes</h3>
            <div>
                <h3><a href="#d1">Cotizaciones</a></h3>
                    <div>
                    <label>Buscar:
                    <br /><input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.cotizaciones.php?type=0',this,'#ncotizaciones')" id="buscoti"/>
                    </label><hr />
                        <div id="ncotizaciones">
                        <?php include("planeacion.cotizaciones.php");?>
                        </div>
                    </div>
        
                <h3><a href="#d1">Levantamientos</a></h3>
                    <div>
                    <label>Buscar:
                    <br /><input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.levantamientos.php?type=0',this,'#nlevantamientos')" id="busleva"/>
                    </label><hr />
                        <div id='nlevantamientos'>
                        <?php include("planeacion.levantamientos.php");?>
                        </div>
                    </div>
                    
                <h3><a href="#d1">Ordenes Servicio</a></h3>
                    <div>
                    <label>Buscar:
                    <br /><input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.ordenservicio.php?type=0',this,'#nordenservicio')" id="busorde"/></label><hr />
                        <div id="nordenservicio">
                        <?php include("planeacion.ordenservicio.php");?>
                        </div>
                    </div>
        
                <h3><a href="#d1">Administrativo u Operativo</a></h3>
                    <div>
                    <a href="planeacion.asignar.php?tipoelemento=4&valorreferencia=0&idjunta=<?php  echo $_GET['idjunta'];?>" tipoelemento="4" valorreferencia="0" idip="" class="popup">Nueva Tarea</a>
                    </div>
        
                <h3><a href="#">Facturas</a></h3>
                    <div>
                    <label>Buscar:<br /><input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.admin.facturas.php?type=0',this,'#nfacturas')"id="busfact"/></label><hr />
                        <div id="nfacturas"><?php include('planeacion.admin.facturas.php');?></div>
                    </div>
        
                <h3><a href="#">Cuentas Por Pagar</a></h3>
                    <div><label>Buscar:<br /><input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.admin.cxp.php?type=0',this,'#ncxp')" id="buscuen"/></label><hr />
                        <div id="ncxp">
                        <?php include('planeacion.admin.cxp.php');?>
                        </div>
                    </div>
        
                </div>
        </div>
    </div>
    <div id="tabst-2">
	    <div id="diaanterior">
<h3>Reunion Anterior</h3>
<div>
<h3><a href="#d1">Cotizaciones</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.cotizaciones.php?type=2&fecha=' + $('#fechaplaneacion').val(),this,'.ayercotizaciones')" id="buscoti"/>
</label>
<hr />
<div class="ayercotizaciones">
</div>
</div>

<h3><a href="#d1">Levantamientos</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="q" size="35" onkeyup="buscar('planeacion.levantamientos.php?type=2&fecha=' + $('#fechaplaneacion').val(),this,'.ayerlevantamientos')" id="buscoti"/>
</label>
<hr />
<div class="ayerlevantamientos">
</div>
</div>

<h3><a href="#d1">Ordenes Servicio</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="q" size="35" onkeyup="buscar('planeacion.ordenservicio.php?type=2&fecha=' + $('#fechaplaneacion').val(),this,'.ayerordenservicio')" id="buscoti"/>
</label>
<hr />
<div class="ayerordenservicio">
</div>
</div>

<h3><a href="#d1">Administrativo u Operativo</a></h3>
<div>
    <label>Buscar:<br />
    	<input type="text" name="q" size="35" onkeyup="buscar('planeacion.admin.php?type=2&fecha=' + $('#fechaplaneacion').val(),this, '.ayeradmin')" id="buscoti"/>
    </label>
    <hr />
    <div class='ayeradmin'></div>
</div>

<h3><a href="#">Facturas</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.admin.facturas.php?type=2&fecha=' + $('#fechaplaneacion').val(),this,'.ayerfacturas')" id="buscoti"/>
</label>
<hr />
<div class="ayerfacturas"></div></div>

<h3><a href="#">Cuentas Por Pagar</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.admin.cxp.php?type=2&fecha=' + $('#fechaplaneacion').val(),this,'.ayercxo')" id="buscoti"/>
</label>
<hr />
<div class="ayercxo"></div>
</div>
</div>
</div>
    </div>
    <div id="tabst-3">
    	<div id="diahoy">
<h3>Reunion Actual</h3>
<div>
<h3><a href="#d1">Cotizaciones</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.cotizaciones.php?type=1&fecha=' + $('#fechaplaneacion').val(),this,'.cotizaciones')" id="buscoti"/>
</label>
<hr />
<div class="cotizaciones">
</div></div>

<h3><a href="#d1">Levantamientos</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.levantamientos.php?type=1&fecha=' + $('#fechaplaneacion').val(),this,'.levantamientos')" id="buscoti"/>
</label>
<hr />
<div class="levantamientos"></div></div>

<h3><a href="#d1">Ordenes Servicio</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.ordenservicio.php?type=1&fecha=' + $('#fechaplaneacion').val(),this,'.ordenservicio')" id="buscoti"/>
</label>
<hr />
<div class="ordenservicio">
</div></div>

<h3><a href="#d1">Administrativo u Operativo</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.admin.php?type=1&fecha=' + $('#fechaplaneacion').val(),this,'.admin')" id="buscoti"/>
</label>
<hr />
<div class="admin"></div>
</div>

<h3><a href="#">Facturas</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.admin.facturas.php?type=1&fecha=' + $('#fechaplaneacion').val(),this,'.facturas')" id="buscoti"/>
</label>
<hr />
<div class="facturas"></div></div>

<h3><a href="#">Cuentas Por Pagar</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.admin.cxp.php?type=1&fecha=' + $('#fechaplaneacion').val(),this,'.cxp')" id="buscoti"/>
</label>
<hr />
<div class="cxp"></div></div>

</div>
</div>
    </div>
</div>

<div id="historial" title="Comentarios">
<div id="contHistorial">
</div>
</div>
<input type="hidden" name="fechaplaneacion" id="fechaplaneacion" value="<?php echo $row_rs_Junta['fecha']; ?>" />

<input type="hidden" name="idjunta" id="idjunta" value="<?php echo $_GET['idjunta']; ?>" />

<div id="concentrado"></div>

<div id="addBitacora" title="Escribir Bitacora">
<div id="contentBitacora"></div>
</div>

</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rs_Junta);

mysql_free_result($rsAsistentes);

mysql_free_result($rsAnterior);

mysql_free_result($rsSubcontratistas);
?>