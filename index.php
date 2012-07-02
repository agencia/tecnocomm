<?php 
 require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
<?php
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
  $_SESSION['mnuevos'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
  unset($_SESSION['mnuevos']);
	
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
 

$niv_Niveles = "-1";
if (isset($_SESSION['MM_UserGroup'])) {
  $niv_Niveles = $_SESSION['MM_UserGroup'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Niveles = sprintf("select * from autorizacion where nivel=%s", GetSQLValueString($niv_Niveles, "int"));
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

//conversaciones sin respuestas


do{
	$array_niveles[]=$row_Niveles['idlink'];
} while ($row_Niveles = mysql_fetch_assoc($Niveles)); 
?>
<html>
<head>
<title>Sistema De Gestion Tecnocomm</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css">
<link href="menu.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" href="dhtmlgoodies_calendar.css" media="screen" />
<script src="js/jquery.min.js" language="javascript"></script>
<script src="js/jqueryui.js" language="javascript"></script>
<script src="js/jquery.cookie.js" language="javascript"></script>
<script src="js/jfastmenu.js" language="javascript"></script>
<script language="javascript"  src="js/funciones.js"></script>
<script>
function alertas(){
	$("#alertas").load("modulos/alertas/index.php");
}
			$(document).ready(function(){
				$.jFastMenu("#menu");
				checkAlerts();
                                
                            $("#iradtalle").submit(function(e){
                                if(!$.isNumeric($("#iradtalle_ip").val()) && $("#iradtalle_ip").attr("name") != "bus"){
                                    e.preventDefault();
                                    $("#iradtalle_mod").val("ip");
                                    $("#iradtalle_ip").attr("name", "bus");
                                    $("#iradtalle").append('<input type="hidden" name="estado" value="-1" />');
                                    $("#iradtalle").append('<input type="hidden" name="usuario" value="-1" />');
                                    $("#iradtalle").append('<input type="hidden" name="filtro" value="1" />');
                                    $("#iradtalle").submit();
                                }
                            });
			});

			function checkAlerts() {
				alertas();
				setTimeout(checkAlerts, 10000);
			};
                        
			
</script>
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" <?php if ($_GET['mod']=='ip'){echo "onload='document.forms[0].bus.focus();'";}?>>
<!-- Save for Web Slices (tecnocomm.psd) -->
<table id="Tabla_01" width="1024" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="4">
			<img src="images/btnsalir.png" width="1024" height="74" alt=""></td>
	</tr>
	<tr>
		<td rowspan="2">
			<img src="images/btnsalir-02.png" width="673" height="33" alt=""></td>
		<td colspan="2" rowspan="2" background="images/session.png" width="272" height="33">
			<div id="session"><?php echo substr($row_RsUsr['nombrereal'],0,20); ?><?php  echo (strlen($row_RsUsr['nombrereal']) > 20 ) ? "..." : " ";  ?></div></td>
		<td>
			<img src="images/index_04.png" width="79" height="1" alt=""></td>
	</tr>
	<tr>
		<td>
			<a href="<?php echo $logoutAction ?>" ><img src="images/btnsalir-04.png" width="79" height="32" alt=""></a></td>
	</tr>
	<tr>
		<td colspan="2" background="images/menu.png" width="674" height="42" valign="middle">
		<div id="menu">
        <ul>
        <li><a href="index.php?mod=portada"><img src="images/Portada.png" alt="portada"  border="0" align="absmiddle"/>Portada</a></li>
        
        <li> <a href="#"> <img src="images/Administracion.png" alt="Administracion"  border="0" align="absmiddle"/> Admin</a> 
        
       <ul>
       <li><a href="index.php?mod=planeacion">Planeacion</a></li>
       <li> <a href="#">Proyectos</a> 
       <ul> 
        <li><?php if(in_array(6,$array_niveles)){?>
    <a href="index.php?mod=cotizacion"><img src="images/Cotizaciones.png" alt="cotizaciones"  border="0" align="middle"/>Cotizaciones</a>
	<? }?>
    </li>
    <li>
    <a href="index.php?mod=levantamientos">
    	Levantamientos
    </a>
    </li>
    </ul>
       </li>
       <li><a href="#">Director</a>
       <ul>
       <li><a href="index.php?mod=autorizados">IP Autorizadas</a></li>
       <li><a href="index.php?mod=ralertas">Rep. Alertas</a></li>
       </ul>
       </li>
       
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
  
    <li><?php if(in_array(7,$array_niveles)){?>
    <a href="index.php?mod=bancos" ><img src="images/Bancos.png" alt="bancos" border="0" align="middle" />Bancos</a>
	<? }?>
    </li>
	 <li><?php if(in_array(11,$array_niveles)){?>
    <a href="index.php?mod=reportes" ><img src="images/Reportes.png" alt="reportes" border="0" align="middle" />Reportes</a>
	<? }?>
    </li>
    <?php if(in_array(6,$array_niveles)){?>
	 <li>
    <a href="index.php?mod=reportescot" ><img src="images/Reportes.png" alt="reportes" border="0" align="middle" />Reportes Cot</a>
    </li>
    	<? }?>
         <li>
    <a href="index.php?mod=os" >OS</a>
    </li>
	</ul>
        
     </li>
        
        
        
        <li>
     
        <a href="#"> <img src="images/Productosyservicios.png" alt="Productos y servicios" border="0" align="absmiddle"/> Catalagos</a>
       <ul>
       <li><a href="#"> Modificar </a>
       		<ul> 
            
            <?php if(in_array(17,$array_niveles)){?><li><a href="index.php?mod=catactivos&option=edit">Activos</a></li><? }?>
            <?php if(in_array(7,$array_niveles)){?><li><a href="index.php?mod=catbancos&option=edit">Bancos</a></li><? }?>
            <?php if(in_array(8,$array_niveles)){?><li><a href="index.php?mod=prodyserv" ><img src="images/Catologo.png" alt="catalogo" border="0" align="middle"/>Conceptos</a></li><? }?>
              <?php if(in_array(3,$array_niveles)){?> <li><a href="index.php?mod=clientes"><img src="images/Clientes.png" alt="clientes" border="0" align="middle" />Clientes</a></li><? }?>
              <?php if(in_array(16,$array_niveles)){?><li><a href="index.php?mod=catempleados&option=edit">Empleados</a></li><? }?>
              <?php if(in_array(19,$array_niveles)){?><li><a href="index.php?mod=catherramientas&option=edit">Herramientas</a></li><? }?>
              <?php if(in_array(10,$array_niveles)){?><li><a href="index.php?mod=catempleados&option=edit">Proveedores</a></li><? }?>
			   <?php if(in_array(18,$array_niveles)){?><li><a href="index.php?mod=catsubcontratistas&option=edit">SubContratistas</a></li><? }?>
               <li><a href="index.php?mod=listapuestos&option=edit">Puestos</a></li>
  				
               
                
 
    
     
            </ul>
       </li>
       <li><a href="#"> Consulta </a> </li>
       <li> <a href="#"> Imprimir </a> 
       		<ul> 
            <li><a href="#"> Clientes </a></li>
            <li><a href="#"> Proveedores</a></li>
            <li><a href="#"> Conceptos</a></li>
            
            </ul>
       
       </li>
       
	</ul>
       
   
        </li>
        <li> <a href="#"><img src="images/Configuracion.png" alt="configuracion"  border="0" align="absmiddle"/> Configuracion</a> 
        
        <ul>
	<li><?php if(in_array(9,$array_niveles)){?>
    <a href="index.php?mod=usuarios" ><img src="images/Usuarios.png" alt="catalogo" border="0" align="middle"/>Usuarios</a>
	<? }?>
    </li>
    <li><a href="mysqldump/execute_db_backup.php">Respaldar</a></li>
	</ul>
        </li>
    <li>
    <a href="#"><img src="images/light_bulb.png" width="16" height="16">Ip</a>
    <ul>
    <li><a href="nuevoIP.php" onClick="NewWindow(this.href,'Nuevo IP','800','600','yes'); return false;"> <img src="images/light_bulb__plus.png" width="16" height="16">Nuevo </a></li>
    <li><a href="index.php?mod=ip"><img src="images/light_bulb_off.png" width="16" height="16">Consultar</a></li>
    <li><form name="iradtalle" id="iradtalle" method="get" style="display:inline"> Ir 
            <input type="text" name="idip" id="iradtalle_ip" size="8" style="display:inline">
            <input type="hidden" name="mod" id="iradtalle_mod" value="detalleip">
        </form></li>
    </ul>
    
    </li>
    
    <li>
    <?php if(in_array(28,$array_niveles)){?>
    	<a href="index.php?mod=sgeneral"><img src="images/jinfo.gif" width="16" height="16" />Status General</a>
    <?php } else { ?>
    	<a href="index.php?mod=sgeneralparticular"><img src="images/jinfo.gif" width="16" height="16" />Status Particular</a>
    <?php } ?>
    </li>
        </ul>
        </div>
        </td>
		<td colspan="2" background="images/alertas.png" width="350" height="42" >
        <div id="alertas">
        </div><small style="margin-left: 60px;"><a href="conversacionenlista.php" class="popup">abrir en ventana</a></small>
		</td>
	</tr>
	<tr>
		<td colspan="4"><img src="images/tecnocomm2_08.png" width="1024" height="37" alt=""></td>
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
<table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
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
			case "facturacion":if(in_array(2,$array_niveles)){include('facturas.list.php');}
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
			case "porcobrar":if(in_array(5,$array_niveles)){include('cuentasxcobrar.php');}
			break;
			case "porcobrarclientes":include('cuentasxcobrarclientes.php');
			break;
			case "porcobrarold":if(in_array(5,$array_niveles)){include('porCobrar.php');}
			break;
			case "ordenCompra":if(in_array(12,$array_niveles)){include('ordenCompra.php');}
			break;
			case "detalleOrden":if(in_array(12,$array_niveles)){include('detalleOrden.php');}
			break;
			case "facturando":if(in_array(2,$array_niveles)){include('facturando.php');}
			break;
			case "impresioncatalagos":if(in_array(14,$array_niveles)){include('ImpresionCatalagos.php');}
			break;
			case "catbancos":if(in_array(15,$array_niveles)){include('catBancos.php');}
			break;
			case "catactivos":if(in_array(17,$array_niveles)){include('catActivos.php');}
			break;
			case "catsubcontratistas":if(in_array(18,$array_niveles)){include('catSubcontratistas.php');}
			break;
			case "catherramientas":if(in_array(19,$array_niveles)){include('catHerramientas.php');}
			break;
			case "autorizados":if(in_array(24,$array_niveles)){include('ip.autorizados2.php');}
			break;
			case "reportescot":include('catCotReporte.php');
			break;
			case "catAutorizados":if(in_array(22,$array_niveles)){include('catCotiAuto.php');}
			break;
			case "catReporte":if(in_array(21,$array_niveles)){include('catProyectosParticipa.php');}
			break; 
			case "ordenservicio":if(in_array(23,$array_niveles)){include('lista.ordenservicio.php');}
			break; 
			case "catavisos":include('catAvisos2.php');
			break; 
			case "catavisosadmin":if(in_array(20,$array_niveles)){include('catAvisosAdmin.php');}
			break; 
			case "catempleados":if(in_array(16,$array_niveles)){include('catEmpleados.php');}
			break; 
			case "ip":include('ip.php');
			break; 
			case "detalleip":include('detalleIp.php');
			break; 
			case "levantipos":include('levantamiento.tipos.php');
			break; 
			case "levantamientos":include('levantamientos.lista.php');
			break; 
			case "sal":include("salida.php");
			break;
			case "confsalida": include("salida.confirmar.php");
			break;
			case "confavance": include("avance.confirmar.php");
			break;
			case "misalertas": include("conversacionenlista.php");
			break;
			case "ralertas": include("rconversacion.php");
			break;
			case "planeacion": include("planeacion.php");
			break;
			case "os": include("lista.ordenservicio.php");
			break;
			
			case "sgeneral" : include('statusgeneral2.php');
			break;
			
			case "listapuestos" : include('lista.puestos.php');
			break;
			
			case "sgeneralparticular" : include('statusgeneral.particular.php');
			break;
			
			default:
				$_GET['mod'] = "mi"; 
				include('portada3.php');//include('portada.php');
			break;
			
		}
		 ?>
    </div>
</td>
</tr>
</table>
<!-- End Save for Web Slices -->
</body>
</html>
<?php
mysql_free_result($Niveles);

?>
