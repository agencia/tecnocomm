<?php require_once('Connections/tecnocomm.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index1.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
include("utils.php");

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

$currentPage = $_SERVER["PHP_SELF"];

$colname_rsIp = "-1";
if (isset($_GET['idip'])) {
  $colname_rsIp = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsIp = sprintf("SELECT * FROM ip WHERE idip = %s", GetSQLValueString($colname_rsIp, "int"));
$rsIp = mysql_query($query_rsIp, $tecnocomm) or die(mysql_error());
$row_rsIp = mysql_fetch_assoc($rsIp);
$totalRows_rsIp = mysql_num_rows($rsIp);

$colname_rsCliente = "-1";
if (isset($_GET['idip'])) {
  $colname_rsCliente = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCliente = sprintf("SELECT c.* FROM ip i, cliente c WHERE idip = %s AND i.idcliente = c.idcliente", GetSQLValueString($colname_rsCliente, "int"));
$rsCliente = mysql_query($query_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);
$totalRows_rsCliente = mysql_num_rows($rsCliente);

$colname_rsContacto = "-1";
if (isset($_GET['idip'])) {
  $colname_rsContacto = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsContacto = sprintf("SELECT c.* FROM ip i, contactoclientes c WHERE idip = %s AND i.idcontacto = c.idcontacto", GetSQLValueString($colname_rsContacto, "int"));
$rsContacto = mysql_query($query_rsContacto, $tecnocomm) or die(mysql_error());
$row_rsContacto = mysql_fetch_assoc($rsContacto);
$totalRows_rsContacto = mysql_num_rows($rsContacto);

$maxRows_RsArticulos = 30;
$pageNum_RsArticulos = 0;
if (isset($_GET['pageNum_RsArticulos'])) {
  $pageNum_RsArticulos = $_GET['pageNum_RsArticulos'];
}
$startRow_RsArticulos = $pageNum_RsArticulos * $maxRows_RsArticulos;

$colname_RsArticulos = "-1";
if (isset($_GET['clave'])) {
  $colname_RsArticulos = $_GET['clave'];
}

$colname_rsOrdenDetalle = "-1";
if (isset($_GET['idordenservicio'])) {
  $colname_rsOrdenDetalle = $_GET['idordenservicio'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenDetalle = sprintf("SELECT * FROM ordenservicio_detalle WHERE idordenservicio = %s ORDER BY idordenservicio_detalle ASC", GetSQLValueString($colname_rsOrdenDetalle, "int"));
$rsOrdenDetalle = mysql_query($query_rsOrdenDetalle, $tecnocomm) or die(mysql_error());
$row_rsOrdenDetalle = mysql_fetch_assoc($rsOrdenDetalle);
$totalRows_rsOrdenDetalle = mysql_num_rows($rsOrdenDetalle);

$colname_rsOrden = "-1";
if (isset($_GET['idordenservicio'])) {
  $colname_rsOrden = $_GET['idordenservicio'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrden = sprintf("SELECT * FROM ordenservicio WHERE idordenservicio = %s", GetSQLValueString($colname_rsOrden, "int"));
$rsOrden = mysql_query($query_rsOrden, $tecnocomm) or die(mysql_error());
$row_rsOrden = mysql_fetch_assoc($rsOrden);
$totalRows_rsOrden = mysql_num_rows($rsOrden);

$queryString_RsArticulos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_RsArticulos") == false && 
        stristr($param, "totalRows_RsArticulos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_RsArticulos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_RsArticulos = sprintf("&totalRows_RsArticulos=%d%s", $totalRows_RsArticulos, $queryString_RsArticulos);

$k=1;
do{
	$partidas[$row_rsOrdenDetalle['idordenservicio_detalle']]=$k;
	$k++;
}while($row_rsOrdenDetalle = mysql_fetch_assoc($rsOrdenDetalle));

@mysql_data_seek($rsOrdenDetalle, 0);
$row_rsOrdenDetalle = mysql_fetch_assoc($rsOrdenDetalle);
$money = array(0=>"Pesos",1=>"Dolares");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Editar Orden de Servicio</title>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="style2.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar.css" media="screen" />
<link type="text/css" rel="stylesheet" href="css/redmond/jquery.css" media="screen" />
<script language="javascript"  src="js/funciones.js"></script>
<script src="js/jquery.js" language="javascript"> </script>
<script src="js/jqueryui.js" language="javascript"></script>
<script type="text/javascript">
	$(function() {
		$(".tabs").tabs();
	});
	$(function() {
        $("#busqueda").keyup( function (event) {
          lk = "busqueda.articulos.orden.php?busqueda="+$("#busqueda").attr("value")+"&idordenservicio="+$("#busid").attr("value");
          $("#resulbus").load(lk);
        })
});
	</script>

</head>

<body class="wrapper">
<h1>Orden de Servicio: <span class="sip"><?php echo $row_rsOrden['identificador']; ?></span></h1>
<div class="tabs">
	<ul>
		<li><a href="#tabs-1">Datos Cotizacion</a></li>
		<li><a href="#tabs-2">Datos IP</a></li>
		
	</ul>
	<div id="tabs-1">
		<table width="80%" id="encip" cellpadding="3" cellspacing="0"  class="encabezado">
  <tr>
    <td align="right" bgcolor="#CCCCCC">Identificador:</td>
    <td><span class="sip"><?php echo $row_rsOrden['identificador']; ?></span></td>
    <td align="right" bgcolor="#CCCCCC">IP:</td>
    <td><span class="sip"><?php echo $_GET['idip'];?></span></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#CCCCCC">Orden no.:</td>
    <td><?php echo $row_rsOrden['numeroorden']; ?></td>
    <td align="right" bgcolor="#CCCCCC">Contacto:</td>
    <td><?php echo $row_rsContacto['nombre']; ?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#CCCCCC">Utilidad Global:</td>
    <td><span class="sip"><?php echo $row_rsOrden['utilidad']?></span></td>
    <td align="right" bgcolor="#CCCCCC">Email:</td>
    <td><?php echo $row_rsContacto['correo']; ?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#CCCCCC">Fecha:</td>
    <td><?php echo formatDate($row_rsOrden['fecha']); ?></td>
    <td align="right" bgcolor="#CCCCCC">Tel:</td>
    <td><?php echo $row_rsContacto['telefono']; ?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#CCCCCC">Tipo de cambio:</td>
    <td><span class="sip"><?php echo $row_rsOrden['tipo_cambio']; ?></span></td>
    <td align="right" bgcolor="#CCCCCC">Opciones:</td>
    <td><a href="editar.orden.servicio.php?idordenservicio=<?php echo $_GET['idordenservicio'];?>" onclick="NewWindow(this.href,'Buscar articulo','500','500','yes');return false"><img src="images/Edit.png" width="24" height="24" border="0" align="middle" title="Editar Datos de Orden de Servicio" /></a><a href="printOrdenServicio.php?idordenservicio=<?php echo $_GET['idordenservicio'];?>" onclick="NewWindow(this.href,'IMPRIMIR Orden','1100','980','yes');return false;"><img src="images/Imprimir2.png" alt="Imprimir" width="24" height="24" border="0" align="middle"  title="Imprimir Orden de servicio"/></a>
      <?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?>
      <a href="orden.cargar.precios.php?idordenservicio=<?php echo $_GET['idordenservicio'];?>" onclick="NewWindow(this.href,'Buscar articulo','500','500','yes');return false"><img src="images/Agregar.png" width="24" height="24" border="0" align="absmiddle" title="Cargar Precios" /></a><?php } ?></td>
  </tr>
  <tr>
      <td align="right" bgcolor="#CCCCCC">Moneda:</td>
    <td colspan="3"><?php echo $money[$row_rsOrden['moneda']]; ?></td>
  </tr>
  </tr>
    <td  align="right" bgcolor="#CCCCCC">Descripcion:</td>
    <td colspan="3" valign="top" ><?php echo $row_rsOrden['descripcionreporte']; ?></td>
    </tr>
  <tr>
    <td  align="right" bgcolor="#CCCCCC">Observaciones:</td>
    <td colspan="3" valign="top" ><?php echo $row_rsOrden['observaciones']; ?></td>
  </tr>
  </table>
</div>
    
    <div id="tabs-2">
    <?php include("ip.encabezado.php");?>
	</div>
</div>




<div id="submenu">
    <a href="orden_nueva_buscar_articulo.php?idordenservicio=<?php echo $_GET['idordenservicio'];?>" onclick="NewWindow(this.href,'Buscar articulo','1150','800','yes');return false">
        <button type="button">
            <img src="images/Agregar.png" width="24" height="24" border="0" align="absmiddle" />
            Agregar Partida
        </button>
    </a>
</div>
    <div id="distabla">
  <table width="100%" cellpadding="1" cellspacing="0">
    <thead>
      <tr>
        <td>Partida</td>
        <td>Descripcion</td>
        <td>Marca</td>
        <td>Codigo</td>
        <?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?><td>Precio</td><?php } ?>
        <td>Cantidad</td>
        <?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?>
        <td>Importe</td>
        <td>Opciones</td>
        <?php } ?>
        </tr>
    </thead>
    <tbody>
            <?php if ($totalRows_rsOrdenDetalle > 0) { // Show if recordset not empty ?>

      <?php 
			   
			   $subtotal = 0;
			   
			   do { 
               
               $pre =divisa($row_rsOrdenDetalle['precio'],$row_rsOrdenDetalle['moneda'],$row_rsOrden['moneda'],$row_rsOrden['tipo_cambio']);
				 $manoobra = divisa($row_rsOrdenDetalle['mano_obra'],$row_rsOrdenDetalle['moneda'],$row_rsOrden['moneda'],$row_rsOrden['tipo_cambio']);

				  $p = round((($pre * $row_rsOrdenDetalle['utilidad'])),2);
               ?>
      <tr>
        <td><?php echo $partidas[$row_rsOrdenDetalle['idordenservicio_detalle']];?></td>
        <td><?php echo $row_rsOrdenDetalle['descripcion']; ?></td>
        <td><?php echo $row_rsOrdenDetalle['marca']; ?></td>
        <td><?php echo $row_rsOrdenDetalle['codigo']; ?></td>
       <?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?> <td align="right"><?php echo format_money($p); ?></td><? }?>
        <td align="right"><?php echo $row_rsOrdenDetalle['cantidad']; ?></td>
        <?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?><td align="right"><?php $importe=$p*$row_rsOrdenDetalle['cantidad'];
				  echo format_money($importe);
				  $subtotal += $importe;
?></td>
        <td><a href="editar.orden.articulo.php?idordenservicio_detalle=<?php echo $row_rsOrdenDetalle['idordenservicio_detalle'];?>"  onclick="NewWindow(this.href,'Modificar articulo','550','300','yes');return false" ><img src="images/Edit.png" alt="cambiar" width="24" height="24" border="0" title="MODIFICAR ARTICULO" /></a><a href="eliminar.orden.producto.php?idordenservicio_detalle=<?php echo $row_rsOrdenDetalle['idordenservicio_detalle'];?>" onclick="NewWindow(this.href,'Eliminar articulo','550','300','yes');return false"><img src="images/eliminar.gif" alt="eliminar" width="19" height="19" border="0" title="ELIMINAR ARTICULO" /></a></td>
        <? }?>
      </tr>
      <?php } while ($row_rsOrdenDetalle = mysql_fetch_assoc($rsOrdenDetalle)); ?>
      <tr>
        <td></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td></td>
        <td></td>
      </tr>
      <?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?>
      <tr>
        <td></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">Subtotal:</td>
        <td align="right">$<?php echo  format_money($subtotal)?></td>
        <td></td>
      </tr>
      <tr>
        <td></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="2" align="right">&nbsp;</td>
        <td align="right">Total MO:</td>
        <td align="right">$<?php echo format_money($row_rsOrden['manoobra']);$subtotal+=$row_rsOrden['manoobra']; ?></td>
        <td><a href="editar.orden.mo.php?idordenservicio=<?php echo $_GET['idordenservicio'];?>"  onclick="NewWindow(this.href,'Modificar articulo','550','300','yes');return false" ><img src="images/Edit.png" alt="cambiar" width="24" height="24" border="0" align="absmiddle" title="MODIFICAR ARTICULO" /></a></td>
      </tr>
      <tr>
        <td></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">IVA:</td>
        <td align="right">$          <?php 
						  $iva=($subtotal)*($row_rsOrden['iva']/100);
						  echo  format_money($iva)?></td>
        <td></td>
      </tr>
      <tr>
        <td></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">Total:</td>
        <td align="right">$<?php echo  format_money($subtotal+$iva)?></td>
        <td></td>
      </tr>
      <? }?>
        <?php } // Show if recordset not empty ?>

      <?php if ($totalRows_rsOrdenDetalle == 0) { // Show if recordset empty ?>
        
        <tr>
          <td colspan="8" align="center">No Hay Productos o Servicios agregados</td>
        </tr><?php } // Show if recordset empty ?>
    </tbody>
  </table>
    </div>
</body>
</html>
<?php
@mysql_free_result($rsIp);

@mysql_free_result($rsCliente);

@mysql_free_result($rsContacto);

@mysql_free_result($rsOrdenDetalle);

@mysql_free_result($rsOrden);
?>
