<?php
require_once('Connections/tecnocomm.php');
//initialize the session
if (!isset($_SESSION)) {
  session_start();
  // echo ini_get("session.gc_maxlifetime"); 
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "index1.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
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
?><?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

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
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
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
 

$niv_Niveles = "-1";
if (isset($_SESSION['MM_UserGroup'])) {
  $niv_Niveles = (get_magic_quotes_gpc()) ? $_SESSION['MM_UserGroup'] : addslashes($_SESSION['MM_UserGroup']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Niveles = sprintf("select * from autorizacion where nivel=%s", $niv_Niveles);
$Niveles = mysql_query($query_Niveles, $tecnocomm) or die(mysql_error());
$row_Niveles = mysql_fetch_assoc($Niveles);
$totalRows_Niveles = mysql_num_rows($Niveles);

$ide_RsUsr = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $ide_RsUsr = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUsr = sprintf("select * from usuarios where id=%s", GetSQLValueString($ide_RsUsr, "int"));
$RsUsr = mysql_query($query_RsUsr, $tecnocomm) or die(mysql_error());
$row_RsUsr = mysql_fetch_assoc($RsUsr);
$totalRows_RsUsr = mysql_num_rows($RsUsr);
do{
	$array_niveles[]=$row_Niveles['idlink'];
} while ($row_Niveles = mysql_fetch_assoc($Niveles)); 
?>
<html>
<head>
<title>tecnocomm</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
<link href="menu.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar.css" media="screen" />
<script language="javascript"  src="js/funciones.js"></script>
<script language="javascript" src="js/calendar.js"></script>
<script src="js/jquery.js" language="javascript"> </script>
<script src="js/jfastmenu.js"></script>
<script>

			$(document).ready(function(){
			
				$.jFastMenu("#menu");
			
			});

</script>

</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<!-- Save for Web Slices (tecnocomm.psd) -->
<table id="Tabla_01" width="1024" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="4">
			<img src="images/btnsalir.png" width="1024" height="74" alt=""></td>
	</tr>
	<tr>
		<td rowspan="2">
			<img src="images/btnsalir-03.png" width="673" height="33" alt=""></td>
		<td colspan="2" rowspan="2" background="images/session.png" width="272" height="33">
			<div id="login">Juan Antonio Saldivar Aramburu</div></td>
		<td>
			<img src="images/index_04.png" width="79" height="1" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/btnsalir-06.png" width="79" height="32" alt=""></td>
	</tr>
	<tr>
		<td colspan="2" background="images/menu.png" width="674" height="42" valign="middle">
		<div id="menu">
        <ul>
        <li><a href="index.php?mod=portada"><img src="images/Portada.png" alt="portada"  border="0" align="absmiddle"/>Portada</a></li>
        
        <li> <a href="#"> <img src="images/Administracion.png" alt="Administracion"  border="0" align="absmiddle"/> Admin</a> 
        
       <ul>
    <li><?php if(in_array(2,$array_niveles)){?>
    <a href="index.php?mod=facturacion"><img src="images/Facturacion.png" alt="facturacion" border="0" align="middle"/>Facturacion</a>	
	<? }?>
    </li>
      <li><?php if(in_array(12,$array_niveles)){?>
    <a href="index.php?mod=ordenCompra"><img src="images/orden.gif" alt="ORDEN" border="0" align="middle"/>Ordenes de Compra</a>	
	<? }?>
    </li>
    <li><?php if(in_array(4,$array_niveles)){?>
    <a href="index.php?mod=porpagar"><img src="images/Pagar.png" alt="por pagar"  border="0" align="middle"/>Cuentas por Pagar</a>
	<? }?>
    </li>
    <li><?php if(in_array(5,$array_niveles)){?>
    <a href="index.php?mod=porcobrar"><img src="images/Cobrar.png" alt="por cobrar" border="0" align="middle" />Cuentas por Cobrar</a>
	<? }?>
    </li>
    <li><?php if(in_array(6,$array_niveles)){?>
    <a href="index.php?mod=cotizacion"><img src="images/Cotizaciones.png" alt="cotizaciones"  border="0" align="middle"/>Cotizaciones</a>
	<? }?>
    </li>
    <li><?php if(in_array(7,$array_niveles)){?>
    <a href="index.php?mod=bancos" ><img src="images/Bancos.png" alt="bancos" border="0" align="middle" />Bancos</a>
	<? }?>
    </li>
	 <li><?php if(in_array(11,$array_niveles)){?>
    <a href="index.php?mod=reportes" ><img src="images/Reportes.png" alt="reportes" border="0" align="middle" />Reportes</a>
	<? }?>
    </li>
	</ul>
        
     </li>
        
        <li>
        <a href="#">
        <img src="images/Productosyservicios.png" alt="Productos y servicios" border="0" align="absmiddle"/> Operativo 
        </a>
        </li>
        
        <li>
     
        <a href="#"> <img src="images/Productosyservicios.png" alt="Productos y servicios" border="0" align="absmiddle"/> Catalagos</a>
       <ul>
       <li><a href="#"> Modificar </a></li>
       <li><a href="#"> Consulta </a> </li>
       <li> <a href="#"> Imprimir </a> </li>
       
    <li>
    <?php if(in_array(14,$array_niveles)){?>
    <a href="index.php?mod=impresioncatalagos"><img src="images/Imprimir1.png" />Impresion</a>	
	<? }?>
    </li>
    <li><?php if(in_array(8,$array_niveles)){?>
            <a href="index.php?mod=prodyserv" ><img src="images/Catologo.png" alt="catalogo" border="0" align="middle"/>Catalago</a>
			<? }?>
            </li>
  
    <li><?php if(in_array(3,$array_niveles)){?>
    <a href="index.php?mod=clientes"><img src="images/Clientes.png" alt="clientes" border="0" align="middle" />Clientes</a>
	<? }?>
    </li>
     
    
   
	</ul>
       
   
        </li>
        <li> <a href="#"><img src="images/Configuracion.png" alt="configuracion"  border="0" align="absmiddle"/> Configuracion</a> 
        
        <ul>
	<li><?php if(in_array(9,$array_niveles)){?>
    <a href="index.php?mod=usuarios" ><img src="images/Usuarios.png" alt="catalogo" border="0" align="middle"/>Usuarios</a>
	<? }?>
    </li>
	</ul>
        
        </li>
        </ul>
        </div>
        </td>
		<td colspan="2" background="images/alertas.png" width="350" height="42" >
			<div id="alertas"> Alertas </div> </td>
	</tr>
	<tr>
		<td colspan="4">
			<img src="images/index_08.png" width="1024" height="23" alt=""></td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td>
			<img src="images/espacio.gif" width="673" height="1" alt=""></td>
		<td>
			<img src="images/espacio.gif" width="1" height="1" alt=""></td>
		<td>
			<img src="images/espacio.gif" width="271" height="1" alt=""></td>
		<td>
			<img src="images/espacio.gif" width="79" height="1" alt=""></td>
	</tr>
</table>
<table width="1024" border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
<td width="100%">

<div class="wrapper">
    <?php 
		
		if(isset($_GET['mod'])){
			$mod = $_GET['mod'];
		}	else{
			$mod = "";
		}
		
		switch($mod){
			case "prodyserv":  if(in_array(8,$array_niveles)){include("productosyservicios.php");}
			break;
			case "facturacion":if(in_array(2,$array_niveles)){include('facturas.php');}
			break;
			case "cotizacion":if(in_array(6,$array_niveles)){include('cotizaciones.php');}
			break;
			case "clientes":if(in_array(3,$array_niveles)){include('cliente.php');}
			break;
			case "portada":if(in_array(1,$array_niveles)){include('portada.php');}
			break;
			case "usuarios":if(in_array(9,$array_niveles)){include('AdmonUsers.php');}
			break;
			case "bancos":if(in_array(7,$array_niveles)){include('bancos.php');}
			break;
			case "proveedores":if(in_array(10,$array_niveles)){include('proveedor.php');}
			break;
			case "reportes":if(in_array(11,$array_niveles)){include('topten.php');}
			break;
			case "porpagar":if(in_array(4,$array_niveles)){include('cuentasPorPagar.php');}
			break;
			case "porcobrar":if(in_array(5,$array_niveles)){include('porCobrar.php');}
			break;
			case "ordenCompra":if(in_array(12,$array_niveles)){include('ordenCompra.php');}
			break;
			case "detalleOrden":if(in_array(12,$array_niveles)){include('detalleOrden.php');}
			break;
			case "facturando":if(in_array(2,$array_niveles)){include('facturando.php');}
			break;
			case "impresioncatalagos":if(in_array(14,$array_niveles)){include('ImpresionCatalagos.php');}
			break;
			default: echo "portada";//include('portada.php');
			break;
		}
		 ?></div>
</td>
</tr>
</table>
<!-- End Save for Web Slices -->
</body>
</html>