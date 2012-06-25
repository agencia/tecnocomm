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

$MM_restrictGoTo = "systemFail.php";
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
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

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







require_once('numtoletras.php');
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSub1 = sprintf("select *,(select nombre from contactoclientes where idcontacto=subcotizacion.contacto) as conta,contacto as con,(select telefono from contactoclientes where idcontacto=subcotizacion.contacto) as tele, (select correo from contactoclientes where idcontacto=subcotizacion.contacto) as mail from subcotizacion where idsubcotizacion=%s", $_GET['idsubcotizacion']);

$RsSub1 = mysql_query($query_RsSub1, $tecnocomm) or die(mysql_error(). "error en consult1");
$row_RsSub1 = mysql_fetch_assoc($RsSub1);
$totalRows_RsSub1 = mysql_num_rows($RsSub1);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSub12 = sprintf("select * from subcotizacion where identificador=%s", $row_RsSub1['identificador']);

$RsSub12 = mysql_query($query_RsSub12, $tecnocomm) or die(mysql_error(). "error en consult2");
$row_RsSub12 = mysql_fetch_assoc($RsSub12);
$totalRows_RsSub12 = mysql_num_rows($RsSub12);


if ($_SESSION['bandera']==1){

$identi=$row_RsSub1['identificador2']."-Conc";

$insertSQL = sprintf("INSERT INTO subcotizacion (idcotizacion, identificador,identificador2, fecha, formapago, moneda, vigencia, tipoentrega, garantia, estado,nombre,contacto,tipo_cambio,notas,usercreo,utilidad_global,tipo,descrimano,monto,descuento, cantidad, unidad, codigo, marca, montoreal ) VALUES (%s, %s, %s, now(), %s, %s, %s, %s, %s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",$_GET['idcotizacion'],
                       GetSQLValueString($row_RsSub1['identificador'],"int"),
					   GetSQLValueString($identi,"text"),
                       GetSQLValueString($row_RsSub1['formapago'],"text"),
                       GetSQLValueString($row_RsSub1['moneda'],"text"),
                       GetSQLValueString($row_RsSub1['vigencia'],"text"),
                       GetSQLValueString($row_RsSub1['tipoentrega'],"text"),
                       GetSQLValueString($row_RsSub1['garantia'],"text"),
					   GetSQLValueString(6,"int"),
					   GetSQLValueString($row_RsSub1['nombre'],"text"),
					   GetSQLValueString($row_RsSub1['contacto'],"int"),
					   GetSQLValueString($row_RsSub1['tipo_cambio'],"double"),
					   GetSQLValueString($row_RsSub1['notas'],"text"),
					   GetSQLValueString($_SESSION['MM_Userid'],"int"),
					   GetSQLValueString($row_RsSub1['utilidad_global'],"double"),
					   GetSQLValueString($row_RsSub1['tipo'],"int"),
					   GetSQLValueString($row_RsSub1['descrimano'],"text"),
					   GetSQLValueString($row_RsSub1['monto'],"double"),
					   GetSQLValueString($row_RsSub1['descuento'],"double"),
					   GetSQLValueString($row_RsSub1['cantidad'],"int"),
					   GetSQLValueString($row_RsSub1['unidad'],"text"),
					   GetSQLValueString($row_RsSub1['codigo'],"text"),
					   GetSQLValueString($row_RsSub1['marca'],"text"),
					   GetSQLValueString($row_RsSub1['monto'],"double"));
			//echo 	$insertSQL;	   
					   

mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error(). " error en insert");
  
  }
  
  mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos1 = sprintf("SELECT *,suba.moneda AS monedacotizacion,articulo.idarticulo as ida, precio_cotizacion as pc FROM subcotizacion sub,subcotizacionarticulo suba, articulo WHERE sub.idsubcotizacion=suba.idsubcotizacion and suba.idarticulo=articulo.idarticulo and suba.idsubcotizacion=%s ORDER BY  suba.idsubcotizacionarticulo ASC", $_GET['idsubcotizacion']);

$RsArticulos1 = mysql_query($query_RsArticulos1, $tecnocomm) or die(mysql_error(). "error en consult3");
$row_RsArticulos1 = mysql_fetch_assoc($RsArticulos1);
$totalRows_RsArticulos1 = mysql_num_rows($RsArticulos1);

   mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSubCoti = "select idsubcotizacion from subcotizacion order by idsubcotizacion desc";

$RsSubCoti = mysql_query($query_RsSubCoti, $tecnocomm) or die(mysql_error(). "error en consult4");
$row_RsSubCoti = mysql_fetch_assoc($RsSubCoti);
$_SESSION['subcoti']=$row_RsSubCoti['idsubcotizacion'];
$totalRows_RsSubCoti = mysql_num_rows($RsSubCoti);
if ($_SESSION['bandera']==1){
do{
 $insertSQL = sprintf("INSERT INTO subcotizacionarticulo (idsubcotizacion, idarticulo, precio_cotizacion, cantidad, utilidad,descri,mo, moneda,tipo_cambio,reall,modd,marca1) VALUES (%s, %s, %s, %s, %s, %s,%s,%s,%s,%s,%s,%s)",
                       GetSQLValueString($row_RsSubCoti['idsubcotizacion'],"int"),
                       GetSQLValueString($row_RsArticulos1['idarticulo'],"int"),
                       GetSQLValueString($row_RsArticulos1['pc'],"double"),
                       GetSQLValueString($row_RsArticulos1['cantidad'],"double"),
                       GetSQLValueString($row_RsArticulos1['utilidad'],"double"),
					   GetSQLValueString($row_RsArticulos1['descri'],"text"),
					   GetSQLValueString($row_RsArticulos1['mo'],"double"),
					   GetSQLValueString($row_RsArticulos1['monedacotizacion'],"int"),
					   GetSQLValueString($row_RsArticulos1['tipo_cambio'],"double"),
					   GetSQLValueString($row_RsArticulos1['reall'],"double"),
					   GetSQLValueString(0,"int"),
					   GetSQLValueString($row_RsArticulos1['marca1'],"text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error(). "error en insert2");

}while($row_RsArticulos1 = mysql_fetch_assoc($RsArticulos1));

  

require_once('lib/eventos.php');
	$evt = new evento(13,$_SESSION['MM_Userid'],"conciliacion creada para la cotizacion:".$row_RsSub1['identificador2']);
	$evt->registrar();
	$_SESSION['bandera']=0;
}

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$bus='';
if((isset($_GET['buscar']))and($_GET['buscar']!='')){

	$bus=" and (descri like '%".$_GET['buscar']."%' or marca1 like '%".$_GET['buscar']."%' or articulo.codigo like '%".$_GET['buscar']."%') ";

}

$idesuba_RsArticulos = "-1";
if (isset($row_RsSubCoti['idsubcotizacion'])) {
  $idesuba_RsArticulos = $row_RsSubCoti['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos = sprintf("SELECT *,suba.moneda AS monedacotizacion,articulo.idarticulo as ida, precio_cotizacion as pc, articulo.moneda as monart, articulo.tipo as tip FROM subcotizacion sub,subcotizacionarticulo suba, articulo WHERE sub.idsubcotizacion=suba.idsubcotizacion and suba.idarticulo=articulo.idarticulo and suba.idsubcotizacion=%s %s order by suba.idsubcotizacionarticulo ASC", GetSQLValueString($idesuba_RsArticulos, "int"),$bus);

$RsArticulos = mysql_query($query_RsArticulos, $tecnocomm) or die(mysql_error(). "error en consult5");
$row_RsArticulos = mysql_fetch_assoc($RsArticulos);
$totalRows_RsArticulos = mysql_num_rows($RsArticulos);

$ide_RsSub = "-1";
if (isset($row_RsSubCoti['idsubcotizacion'])) {
  $ide_RsSub = $row_RsSubCoti['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSub = sprintf("select *,(select nombre from contactoclientes where idcontacto=subcotizacion.contacto) as conta,contacto as con,(select telefono from contactoclientes where idcontacto=subcotizacion.contacto) as tele, (select correo from contactoclientes where idcontacto=subcotizacion.contacto) as mail from subcotizacion where idsubcotizacion=%s", GetSQLValueString($ide_RsSub, "int"));
$RsSub = mysql_query($query_RsSub, $tecnocomm) or die(mysql_error(). "error en consult6");
$row_RsSub = mysql_fetch_assoc($RsSub);
$totalRows_RsSub = mysql_num_rows($RsSub);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCliente = "select * from cliente where idcliente=".$_GET['idcliente'];
$RsCliente = mysql_query($query_RsCliente, $tecnocomm) or die(mysql_error(). "error en consult7");
$row_RsCliente = mysql_fetch_assoc($RsCliente);
$totalRows_RsCliente = mysql_num_rows($RsCliente);

$ide_RsMO = "-1";
if (isset($row_RsSubCoti['idsubcotizacion'])) {
  $ide_RsMO = $row_RsSubCoti['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsMO = sprintf("select * from subcotizacion where idsubcotizacion=%s", GetSQLValueString($ide_RsMO, "int"));
$RsMO = mysql_query($query_RsMO, $tecnocomm) or die(mysql_error()." error en MO");
$row_RsMO = mysql_fetch_assoc($RsMO);
$totalRows_RsMO = mysql_num_rows($RsMO);

$forma=array(0=>"CONTADO",1=>"50 % ANTICIPO y 50% CONTRAENTREGA",2=>"50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE");



mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsPar = sprintf("SELECT * FROM subcotizacionarticulo suba WHERE suba.idsubcotizacion=%s order by suba.idsubcotizacionarticulo ASC", GetSQLValueString($idesuba_RsArticulos, "int"));
$RsPar = mysql_query($query_RsPar, $tecnocomm) or die(mysql_error(). "error en consult5");
$row_RsPar = mysql_fetch_assoc($RsPar);
$k=1;
do{
	$partidas[$row_RsPar['idsubcotizacionarticulo']]=$k;
	$k++;
}while($row_RsPar = mysql_fetch_assoc($RsPar));


$tip=array(0=>"PL",1=>"C");

$signo = array(0=>"$",1=>"US$");


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $identi;?></title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/funciones.js"></script>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>

<body class="wrapper">
<table width="1275" border="0" align="center" >
  <tr>
    <td colspan="11" align="center" class="titulos">CONCILIAR</td>
  </tr>
  <tr>
    <td colspan="11"><fieldset>
      <legend>DATOS COTIZACION </legend>
	    <table width="908" border="0">
          <tr>
            <td width="224">IDENTIFICADOR:<span class="Estilo1"><?php echo $row_RsSub['nombre'];?></span></td>
            <td width="257"><p>COTIZACION No.:<span class="Estilo1"><?php echo $row_RsSub['identificador2'];?></span></p></td>
            <td width="229">TIPO DE CAMBIO:<span class="Estilo1"><?php echo $row_RsSub['tipo_cambio'];?></span></td>
            <td width="72">&nbsp;</td>
          </tr>
          <tr>
            <td>
FORMA DE PAGO:<?php echo $row_RsSub['formapago'];?></td>
            <td>VIGENCIA:<?php echo $row_RsSub['vigencia'];?></td>
            <td>TIEMPO ENTREGA:<?php echo $row_RsSub['tipoentrega'];?></td>
            <td><a href="cotizaciones_nueva_modDatos.php?idsubcotizacion=<?php echo $_SESSION['subcoti'];?>" onclick="NewWindow(this.href,'Buscar articulo','500','500','yes');return false"><img src="images/Edit.png" width="24" height="24" border="0" align="middle" title="EDITAR DATOS DE LA COTIZACION" />MODIFICAR</a></td>
          </tr>
          <tr>
            <td>GARANTIA:<?php echo $row_RsSub['garantia'];?></td>
            <td colspan="3">UTILIDAD GLOBAL:<span class="Estilo1"><?php echo $row_RsSub['utilidad_global'];?>(Este factor puede cambiar dependiendo del articulo)</span></td>
          </tr>
        </table>
	    
        </fieldset>
       
<fieldset><legend>DATOS CLIENTE</legend>
        <table width="800" border="0">
          <tr>
            <td width="441">NOMBRE CLIENTE: <?php echo $row_RsCliente['nombre']; ?></td>
            <td width="315">RFC:<?php echo $row_RsCliente['rfc']; ?></td>
            <td width="30">&nbsp;</td>
          </tr>
          <tr>
            <td>DIRECCION:<?php echo $row_RsCliente['direccion']; ?></td>
            <td>TELEFONO:<?php echo $row_RsSub['tele']; ?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>CONTACTO: <a href="SelectContacto.php?idsubcotizacion=<?php echo $_SESSION['subcoti'];?>&idcliente=<?php echo $row_RsCliente['idcliente'];?>" onclick="NewWindow(this.href,'Buscar articulo','400','400','yes');return false"><span class="Estilo1"><?php if($row_RsSub['con']!=0){echo $row_RsSub['conta'];}else{echo "CONTACTO NO ASIGNADO";}?></span> <img src="images/Edit.png" width="24" height="24" border="0" align="middle" title="CAMBIAR CONTACTO" /></a><a href="contactos.php?idcliente=<?php echo $row_RsCliente['idcliente']; ?>" onclick="NewWindow(this.href,'Contactos','850','500','no');return false;"><img src="images/Clientes.png" alt="" width="24" height="24" align="middle" title="Administrar Contactos" /></a></td>
            <td>EMAIL:<?php echo $row_RsSub['mail']; ?></td>
            <td>&nbsp;</td>
          </tr>
        </table>
        </fieldset>  </td>
  </tr>
  
  <tr>
    <td width="30">&nbsp;</td>
    <td width="335">&nbsp;</td>
    <td colspan="3" align="center">DESCUENTO:<span class="Estilo1"><?php echo $row_RsSub['descuento'];  ?>%</span></td>
    <td colspan="6">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="11" align="center" class="titulos">MATERIALES Y ARTICULOS</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><a href="printCotizacion.php?idsubcotizacion=<?php echo $_SESSION['subcoti'];?>" onclick="NewWindow(this.href,'IMPRIMIR COTIZACION','1100','980','yes');return false;"><img src="images/Imprimir2.png" alt="SAD" width="24" height="24" border="0"  title="IMPRIMIR COTIZACION"/> </a><a href="conciliacionEnviada.php?idsubcotizacion=<?php echo $_SESSION['subcoti'];?>" onclick="NewWindow(this.href,'ENVIAR Cotizacion','450','200','yes');return false"><img src="images/state2.png" alt="SAD" width="24" height="24" border="0" title="ENVIAR COTIZACION" /></a></td>
    <td colspan="3"><a href="cargarDatosAvance.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion']; ?>&idsub=<?php echo $_SESSION['subcoti'];?>" onclick="NewWindow(this.href,'Nueva Partida Extra',600,800,'yes'); return false;"><img src="images/Agregar.png" alt="" width="24" height="24" border="0" align="middle" title="CARGAR DATOS DE AVANCE" />CARGAR DATOS DE AVANCE</a><br />
      <a href="catPartidasExtra.php?idsub=<?php echo $_GET['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Ver Partida Extra',600,800,'yes'); return false;"><img src="images/Agregar.png" alt="" width="24" height="24" border="0" align="middle" title="CARGAR DATOS DE AVANCE" />VER PARTIDAS EXTRAS</a><br />
       <a href="exportarExcel.php?query=<?php echo $query_RsArticulos; ?>" onclick="NewWindow(this.href,'Ver Partida Extra',600,800,'yes'); return false;"><img src="images/Agregar.png" alt="" width="24" height="24" border="0" align="middle" title="CARGAR DATOS DE AVANCE" />EXPORTAR A EXCEL</a></td>
    <td colspan="6" align="right"><a href="cotizaciones_nueva_buscar_articulo.php?idsubcotizacion=<?php echo $row_RsSubCoti['idsubcotizacion'];?>&tipo=<?php echo $row_RsSub['tipo'];?>&cambio=<?php echo $row_RsSub['tipo_cambio'];?>" onclick="NewWindow(this.href,'Buscar articulo','950','800','yes');return false"><img src="images/AddTask.gif" alt="Agregar" width="24" height="24" border="0" align="middle" />AGREGAR </a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="7"><form id="form1" name="form1" method="get" action="<?php echo $editFormAction; ?>">
      <label>Buscar
        <input type="text" name="buscar" id="textfield" value="<?php echo $_GET['buscar'];?>" />
        </label>
      <label>
      <input type="submit" name="button" id="button" value="Buscar" />
      </label>
      <input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion'];?>"/>
      <input type="hidden" name="idcliente" value="<?php echo $_GET['idcliente'];?>"/>
      <input type="hidden" name="idcotizacion" value="<?php echo $_GET['idcotizacion'];?>"/>
    </form>    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td width="119">&nbsp;</td>
    <td width="120">&nbsp;</td>
    <td width="69">&nbsp;</td>
    <td width="133">&nbsp;</td>
    <td width="95">&nbsp;</td>
    <td width="115">&nbsp;</td>
    <td width="75">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
  </tr>
  <?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
    <tr class="titleTabla">
      <td align="center">NO.</td>
      
      <td align="center">DESCRIPCION</td>
      <td align="center">MARCA</td>
      <td align="center">CODIGO</td>
      <td align="center">PRECIO</td>
      <td align="center">CANTIDAD  COT.</td>
      <td align="center">CANTIDAD INST.</td>
      <td align="center">SUBTOTAL</td>
      <td align="center">SUBTOTAL INST</td>
      <td width="82" align="center">OPCIONES</td>
      <td width="56" align="center">INFO</td>
    </tr>
   <?php } // Show if recordset not empty ?> 

  <?php $subtotal=0;$mano=0; $subtotalreal=0; $cont=0; do { 
  $cont++;
  $preca=0;
  //////////////////////////////////////////////////////////////////////////////cambio de moneda  
if(($row_RsSub['moneda']==0) and ($row_RsArticulos['monedacotizacion']==0)){

	

	$precio=$row_RsArticulos['pc'];

	$manoobra = $row_RsArticulos['mo'];

}



if(($row_RsSub['moneda']==0) and ($row_RsArticulos['monedacotizacion']==1)){

	

	$precio=$row_RsArticulos['pc']*$row_RsArticulos['tipo_cambio'];

	$manoobra = $row_RsArticulos['mo'] * $row_RsArticulos['tipo_cambio'];

	

}



if(($row_RsSub['moneda']==1) and ($row_RsArticulos['monedacotizacion']==0)){

	

	@$precio=$row_RsArticulos['pc']/$row_RsArticulos['tipo_cambio'];

	@$manoobra = $row_RsArticulos['mo'] / $row_RsArticulos['tipo_cambio'];

	

}



if(($row_RsSub['moneda']==1) and ($row_RsArticulos['monedacotizacion']==1)){

	

	$precio=$row_RsArticulos['pc'];

	$manoobra = $row_RsArticulos['mo'];

}
  /////////////////////////////////////////////////////////////////////////////////
  
  
  if($row_RsSub['tipo']==0){$mo=$manoobra*$row_RsArticulos['cantidad'];}else{$mo=0.0;}
  if($row_RsSub['tipo']==0){$m=$manoobra;}else{$m=0.0;}
  
   if($row_RsSub['tipo']==0){$moreal=$manoobra*$row_RsArticulos['reall'];}else{$moreal=0.0;}

   if($row_RsSub['tipo']==0){$mreal=$manoobra;}else{$mreal=0.0;}
  
  ?>
  <?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
    <tr onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
      <td align="center"><?php echo $partidas[$row_RsArticulos['idsubcotizacionarticulo']];?></td>
      
      <td align="center"><?php if($row_RsArticulos['modd']==0){ ?><img src="images/rojo.gif" width="10" height="10" /><?php } ?><?php if($row_RsArticulos['modd']==1){ ?><img src="images/verde.gif" width="10" height="10" /><?php } ?><?php echo $row_RsArticulos['descri']; ?></td>
      <td align="center"><?php echo $row_RsArticulos['marca1']; ?></td>
      <td align="center"><?php echo $row_RsArticulos['codigo']; ?></td>
      <td align="center"><?php echo  $preca=round($precio*$row_RsArticulos['utilidad']+$m,2); ?></td>
      <td align="center"><?php echo $row_RsArticulos['cantidad']; ?></td>
      <td align="center"><?php echo $row_RsArticulos['reall']; ?></td>
      <td align="center"><?php echo  round(($preca)*$row_RsArticulos['cantidad'],2); ?></td>
      <td align="center"><?php echo  round(($preca)*$row_RsArticulos['reall'],2); ?></td>
      <td align="center"><?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
            <a href="#" name="<?php echo $row_RsArticulos['idsubcotizacionarticulo'];?>"><a href="cotizaciones_nueva_modificar_articulo.php?idsubcotizacion=<?php echo $row_RsArticulos['idsubcotizacionarticulo'];?>&tipo=<?php echo $row_RsSub['tipo'];?>&conc=1" onclick="NewWindow(this.href,'Modificar articulo','550','300','yes');return false"><img src="images/Edit.png" alt="cambiar" width="24" height="24" border="0" title="MODIFICAR ARTICULO EN ESTA COTIZACION" /></a>
            <?php } // Show if recordset not empty ?>
        <?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
         
          &nbsp;&nbsp;&nbsp;<a href="cotizaciones_nueva_eliminar_articulo.php?idsubcotizacion=<?php echo $row_RsArticulos['idsubcotizacionarticulo'];?>&tipo=<?php echo $row_RsSub['tipo'];?>" onclick="NewWindow(this.href,'Eliminar articulo','550','300','yes');return false"><img src="images/eliminar.gif" alt="eliminar" width="19" height="19" border="0" title="ELIMINAR ARTICULO DE ESTA COTIZACION" /></a>
          <?php } // Show if recordset not empty ?></td>
      <td align="center"><?php echo $signo[$row_RsArticulos['monart']]; ?>&nbsp;&nbsp;<?php echo $tip[$row_RsArticulos['tip']]; ?></td>
    </tr>
    <?php } // Show if recordset not empty ?>
    <?php
	if($row_RsSub['tipo']==1){$mano+=$manoobra*$row_RsArticulos['cantidad'];}
	$subtotal+=round(($preca)*$row_RsArticulos['cantidad'],2);
	
	if($row_RsSub['tipo']==1){$manoreal+=$manoobra*$row_RsArticulos['reall'];}
	$subtotalreal+=round(($preca)*$row_RsArticulos['reall'],2);
	 } while ($row_RsArticulos = mysql_fetch_assoc($RsArticulos)); ?>
    <?php if ($totalRows_RsArticulos == 0) { // Show if recordset empty ?>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="9" align="center">NO HAY MATERIALES O ARTICULOS AGREGADOS</td>
      </tr>
      <?php } // Show if recordset empty ?>
               
           <?php if($row_RsSub['tipo']==1 and $totalRows_RsArticulos > 0){?>
           <tr>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td colspan="6" align="right"><?php echo $row_RsMO['descrimano']; ?>:</td>
             <td>&nbsp;</td>
             <td colspan="2">+$<?php echo money_format('%i',$mano*$row_RsMO['monto']*$row_RsMO['cantidad']); ?></td>
           </tr><?php 
		   
		   
		   
		   $subtotal+=round($mano*$row_RsMO['monto']*$row_RsMO['cantidad'],2);
		   
		   } // Show if recordset not empty ?>
           
           
           
            <?php if($row_RsSub['tipo']==1 and $totalRows_RsArticulos > 0){?>
           <tr>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td colspan="6" align="right"><a href="modificarMO.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>&cant= <?php echo  money_format('%i',$manoreal*$row_RsMO['montoreal']); ?>&real=1" onclick="NewWindow(this.href,'Buscar articulo','400','250','yes');return false"><?php echo $row_RsMO['descrimano']; ?> REAL:</a></td>
             <td>&nbsp;</td>
             <td colspan="2">+$<?php echo money_format('%i',$manoreal*$row_RsMO['montoreal']*$row_RsMO['cantidadreal']); ?></td>
           </tr><?php 
		   
		   
		   
		   $subtotalreal+=round($manoreal*$row_RsMO['montoreal']*$row_RsMO['cantidadreal'],2);
		   
		   } // Show if recordset not empty ?>
           
           
           <?php if ($totalRows_RsArticulos > 0) {   
		   $descu=$row_RsSub['descuento']/100*$subtotal; 
		   $descu2=$row_RsSub['descuento']/100*$subtotalreal; 
		   
		   $subtotal=round($subtotal-$descu,2);  
		   $subtotalreal=round($subtotalreal-$descu2,2); 
		   // Show if recordset not empty ?>
           <tr>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td align="right">&nbsp;</td>
             <td align="right">&nbsp;</td>
             <td align="right">&nbsp;</td>
             <td align="right">DESCUENTO:</td>
             <td align="left">-$<?php echo money_format('%i', $descu)?></td>
             <td align="right">&nbsp;</td>
             <td>DESCUENTO REAL:</td>
             <td colspan="2">-$<?php echo money_format('%i', $descu)?></td>
           </tr>
           <?php  } // Show if recordset not empty ?>
           <?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td>SUBTOTAL:</td>
            <td align="left">$ <?php echo money_format('%i', $subtotal);   ?></td>
            <td align="right">SUBTOTAL REAL:</td>
            <td>&nbsp;</td>
            <td colspan="2">$            <?php echo money_format('%i', $subtotalreal);   ?></td>
          </tr>
          <?php } // Show if recordset not empty ?>
           <?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td>IVA:</td>
            <td align="left">$            
            <?php $iva=$subtotal*$row_RsSub['iva']/100; echo  money_format('%i', $iva)?></td>
            <td align="right">IVA:</td>
            <td>&nbsp;</td>
            <td colspan="2">$
            
            <?php $ivareal=$subtotalreal*$row_RsSub['iva']/100; echo  money_format('%i', $ivareal)?></td>
          </tr>
          <?php } // Show if recordset not empty ?>
           <?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td>TOTAL:</td>
            <td align="left">$<?php echo money_format('%i', $subtotal+$iva)?></td>
            <td align="right">TOTAL REAL:</td>
            <td>&nbsp;</td>
            <td colspan="2">$<?php echo money_format('%i', $subtotalreal+$ivareal)?></td>
          </tr>
          <?php } // Show if recordset not empty ?>
         <?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
            <tr>
              <td colspan="11">CANTIDAD CON LETRA:<? echo num2letras(money_format('%i',$subtotal+$iva),false,true,$row_RsSub['moneda']); ?>               </td>
            </tr>
            <?php } // Show if recordset not empty ?>
            <tr>
              <td colspan="11">CANTIDAD CON LETRA REAL:<? echo num2letras(money_format('%i',$subtotalreal+$ivareal),false,true,$row_RsSub['moneda']); ?></td>
            </tr>
          <tr>
            <td colspan="11"><fieldset>
            <legend>NOTAS O COMENTARIOS</legend>
            <span class="Estilo1"><?php echo $row_RsSub['notas'];?></span>
            </fieldset></td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td colspan="3" align="center"><input type="button" name="Submit" value="Cerrar" onclick="window.location='close.php'" /></td>
          <td>&nbsp;</td>
          <td colspan="5" align="right">&nbsp;</td>
      </tr>
</table>

<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($RsArticulos);

mysql_free_result($RsSub);
?>