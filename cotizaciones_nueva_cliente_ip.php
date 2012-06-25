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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsClientes = "SELECT * FROM cliente order by nombre";
$RsClientes = mysql_query($query_RsClientes, $tecnocomm) or die(mysql_error());
$row_RsClientes = mysql_fetch_assoc($RsClientes);
$totalRows_RsClientes = mysql_num_rows($RsClientes);


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
$colname_rsCliente = "-1";
if (isset($_POST['idip'])) {
  $colname_rsCliente = $_POST['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCliente = sprintf("SELECT * FROM ip WHERE idip = %s", GetSQLValueString($colname_rsCliente, "int"));
//echo $query_rsCliente;
$rsCliente = mysql_query($query_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);
$totalRows_rsCliente = mysql_num_rows($rsCliente);
  ////////////////////////////////actualizamos el consecutivo
   $update2 = "UPDATE cot_consecutivo set numero=numero+1 where anio=YEAR(NOW())";
                      
  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result2 = mysql_query($update2, $tecnocomm) or die(mysql_error());
  //////////////////////////////////////////////////////////
  
  //Conse
    
  mysql_select_db($database_tecnocomm, $tecnocomm);
	$query_RsConse = "select numero from cot_consecutivo where anio=YEAR(NOW())";
	$RsConse = mysql_query($query_RsConse, $tecnocomm) or die(mysql_error());
	$row_RsConse = mysql_fetch_array($RsConse);
	$totalRows_RsConse = mysql_num_rows($RsConse);

	
  $insertSQL = sprintf("INSERT INTO cotizacion (idcliente, consecutivo,idip) VALUES (%s, %s, %s)",
                       GetSQLValueString($row_rsCliente['idcliente'], "int"),
                       GetSQLValueString($row_RsConse['numero'], "int"),
					     GetSQLValueString($_POST['idip'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCoti = "select idcotizacion from cotizacion order by idcotizacion desc";
$RsCoti = mysql_query($query_RsCoti, $tecnocomm) or die(mysql_error());
$row_RsCoti = mysql_fetch_assoc($RsCoti);
$totalRows_RsCoti = mysql_num_rows($RsCoti);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsClientesX = "select * from cliente where idcliente=".$row_rsCliente['idcliente'];
$RsClientesX = mysql_query($query_RsClientesX, $tecnocomm) or die(mysql_error());
$row_RsClientesX = mysql_fetch_assoc($RsClientesX);
$totalRows_RsClientesX = mysql_num_rows($RsClientesX);

$l=strlen($row_RsConse['numero']);
$sa="";
if($l==1){$sa="00";}

if($l==2){$sa="0";}

$identi="C-".$sa.$row_RsConse['numero']."-".date("y").$row_RsClientesX['abreviacion'];

if(($_POST['suministro']==1) || ($_POST['suministro']==3)){
	$det="SERVICIO DE INSTALACION ";
	$monto=1;
}
else{
	$det="0";
	$monto=1;
}


 $insertSQL = sprintf("INSERT INTO subcotizacion (
     idcotizacion, 
     identificador,
     identificador2, 
     fecha, 
     formapago, 
     moneda, 
     vigencia, 
     tipoentrega, 
     garantia,
     tipo_cambio,
     nombre,
     utilidad_global,
     notas,
     contacto, 
     usercreo, 
     estado, 
     tipo, 
     descrimano, 
     monto, 
     descuento, 
     cantidad, 
     unidad, 
     codigo, 
     marca,
     descuentoreal
) VALUES (%s, %s, %s, now(), %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 1, %s, %s, %s, %s, 1.00, 'SERV', 'TECNOCOMM', 'TECNOCOMM', %s)",$row_RsCoti['idcotizacion'],
                       GetSQLValueString($_POST['textfield'], "int"),
					   GetSQLValueString($identi, "text"),
                       GetSQLValueString($_POST['forma'], "text"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['vigencia'], "text"),
                       GetSQLValueString($_POST['entrega'], "text"),
                       GetSQLValueString($_POST['garantia'], "text"),
					   GetSQLValueString($_POST['TIPO'], "text"),
					   GetSQLValueString($_POST['nombre'], "text"),
					   GetSQLValueString($_POST['utilidad'], "double"),
					   GetSQLValueString($_POST['notas'], "text"),
					   GetSQLValueString(1,"int"),
					   GetSQLValueString($_SESSION['MM_Userid'], "int"),
					   GetSQLValueString($_POST['suministro'], "int"),
					   GetSQLValueString($det, "text"),
					   GetSQLValueString($monto, "int"),
					   GetSQLValueString($_POST['descuento'], "double"),
					    GetSQLValueString($_POST['descuento'], "double"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  $idsub=mysql_insert_id();
  require_once('lib/eventos.php');
	$evt = new evento(10,$_SESSION['MM_Userid'],"Cotizacion creada para el cliente :".$row_RsClientes['abreviacion']." con el consecutivo:".$identi);
	$evt->registrar();
  
   mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSubCoti = "select idsubcotizacion from subcotizacion order by idsubcotizacion desc";
$RsSubCoti = mysql_query($query_RsSubCoti, $tecnocomm) or die(mysql_error());
$row_RsSubCoti = mysql_fetch_assoc($RsSubCoti);
$totalRows_RsSubCoti = mysql_num_rows($RsSubCoti);

if(isset($_POST['cotext'])&&($_POST['cotext']!="")){
$cotext=$_POST['cotext'];
$insertGoTo = "cotizacion.ext.php?idsubcotizacion1=".$idsub."&idsubcotizacion2=".$cotext;
}
else
{
  	$insertGoTo = "cotizacion.detalle.ip.php?idsubcotizacion=".$row_RsSubCoti['idsubcotizacion']."&idip=".$_POST['idip'];
	$cotext=-1;
}

  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsNumCotizacion ="SELECT max(numero) as sig  FROM cot_consecutivo where anio = YEAR(NOW())";
$RsNumCotizacion = mysql_query($query_RsNumCotizacion, $tecnocomm) or die(mysql_error());
$row_RsNumCotizacion = mysql_fetch_assoc($RsNumCotizacion);
$totalRows_RsNumCotizacion = mysql_num_rows($RsNumCotizacion);

if($row_RsNumCotizacion['sig'] == null)
{
	  $insertSQL = "INSERT INTO cot_consecutivo (numero, anio) VALUES (0, YEAR(NOW()))";

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCotiExt = "SELECT * FROM subcotizacion order by identificador2";
$RsCotiExt = mysql_query($query_RsCotiExt, $tecnocomm) or die(mysql_error());
$row_RsCotiExt = mysql_fetch_assoc($RsCotiExt);
$totalRows_RsCotiExt = mysql_num_rows($RsCotiExt);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsIP = "SELECT * FROM ip ORDER BY idip DESC";
$rsIP = mysql_query($query_rsIP, $tecnocomm) or die(mysql_error());
$row_rsIP = mysql_fetch_assoc($rsIP);
$totalRows_rsIP = mysql_num_rows($rsIP);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nueva Cotizacion</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/funciones.js"></script>
<script type=text/javascript>
function change(name,size,lab){
s = document.getElementById(name);
obj = document.createElement('input')
obj.type = 'text'
obj.id = name;
obj.name = name;
obj.size = size;
document.getElementById(lab).replaceChild(obj,s)
}



function mover(posy,posx)
{
  var winl = (screen.width-posy)/2;
  var wint = (screen.height-posx)/2;
  
if (parseInt(navigator.appVersion)>3)
  top.resizeTo(posy,posx);
  top.moveTo(winl,wint);
}
//mover('1035','400');
function activar(obj,val) {
    //dis = obj.selectedIndex==0 ? false : true;
    document.getElementById("cotext").disabled=val;
    
} 

function valida(){
    
    if (document.form1.idcliente.value==-1){
       alert("Tiene que seleccionar un cliente ")
       document.form1.idcliente.focus()
       return false;
    } 
	
	if (document.form1.nombre.value.length==0){
       alert("Tiene que escribir un identificador ")
       document.form1.nombre.focus()
       return false;
    } 
	
	if (document.form1.idip.value.length==0){
       alert("Tiene que escribir una  ip ")
       document.form1.idip.focus()
       return false;
    } 
	
	if (document.form1.TIPO.value.length==0){
       alert("Tiene que escribir un tipo de cambio ")
       document.form1.TIPO.focus()
       return false;
    } 
	
	if (document.form1.entrega.value.length==0){
       alert("Tiene que escribir un tiempo de entrega ")
       document.form1.entrega.focus()
       return false;
    } 
	
	if (document.form1.suministro.value==-1){
       alert("Tiene que seleccionar un tipo de suministro ")
       document.form1.suministro.focus()
       return false;
    } 
	
}/////fin valida

</script>

</head>

<body class="wrapper" onload="activar(this,true)">
    
        <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>" onsubmit="return valida();">
<table width="800" border="0" align="center">
  <tr>
    <td width="1">&nbsp;</td>
    <td colspan="3" align="center" class="titulos" background="images/titulo.gif">SELECCIONA CLIENTE</td>
 
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="10">&nbsp;</td>
    <td width="200">&nbsp;</td>
    <td width="500"></td>
  </tr>
 <?php if(!isset($_GET['idip'])){?>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">IP</td>
    <td><input name="idip" type="text" class="form" id="idip" size="10" value="<?php echo (isset($_GET['idip'])) ? isset($_GET['idip']) : "";?>"/></td>
  </tr>
  <?php }else {?><input type="hidden" name="idip" value="<?php echo $_GET['idip']?>" />
<?php  } ?>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">IDENTIFICADOR:</td>
    <td><input name="nombre" type="text" class="form" id="nombre" size="40" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">MONEDA:</td>
    <td><select name="moneda" class="form" id="moneda">
      <option value="0">PESOS</option>
      <option value="1">DOLARES</option>
    </select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">TIPO DE CAMBIO:</td>
    <td><input name="TIPO" type="text" class="form" id="TIPO" size="10" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">FORMA DE PAGO:</td>
    <td><label id="fo"><select name="forma" id="forma" class="form">
      <option value="CONTADO">CONTADO</option>
      <option value="50 % ANTICIPO y 50% CONTRAENTREGA">50 % ANTICIPO y 50% CONTRAENTREGA</option>
      <option value="50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE">50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE</option>
      <option value="30 DIAS">30 DIAS</option>
      <option onclick="change('forma',40,'fo');">Otro...</option>
    </select></label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">VIGENCIA:&nbsp;&nbsp;&nbsp;<BR />
      </td>
    <td><label id="vig"><select name="vigencia" id="vigencia" class="form">
      <option value="-1">Seleccionar</option>
      <option value="PRECIOS SUJETOS A CAMBIO SIN PREVIO AVISO">PRECIOS SUJETOS A CAMBIO SIN PREVIO AVISO</option>
      <option value="30 DIAS">30 DIAS</option>
      <option onclick="change('vigencia',40,'vig');">Otro...</option>
    </select></label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">TIEMPO ENTREGA:      </td>
    <td><input name="entrega" type="text" class="form" id="entrega" size="40" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">GARANTIA:&nbsp;&nbsp;&nbsp;<BR /></td>
    <td><label id="gar"><select name="garantia" id="garantia" class="form">
      <option value="-1">SELECCIONAR</option>
      <option value="1 AÑO MATERIAL Y MANO DE OBRA">1 AÑO MATERIAL Y MANO DE OBRA</option>
      <option value="25 AÑOS PANDUIT">25 AÑOS PANDUIT</option>
      <option onclick="change('garantia',40,'gar');">Otro...</option>
    </select></label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">FACTOR DE UTILIDAD:      </td>
    <td><input name="utilidad" type="text" class="form" id="utilidad" value="1" size="10" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">DESCUENTO:      </td>
    <td><input name="descuento" type="text" class="form" id="descuento" value="0" size="10" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right"><label>CONSECUTIVO:
      
          </label></td>
    <td><input name="textfield" type="text" class="form" id="textfield" value="<?php echo ($row_RsNumCotizacion['sig']+1); ?>" size="6" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right" valign="top">NOTAS O COMENTARIOS:<br /></td>
    <td><textarea name="notas" id="notas" cols="45" rows="5" class="form"></textarea> </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right" >TIPO DE SUMINISTRO:      </td>
    <td><select name="suministro"  id="suministro">
      <option value="-1" selected="selected" >SELECCIONAR</option>
      <option value="0">SUMINISTRO E INSTALACION</option>
      <option value="1">INSTALACION GLOBAL</option>
      <option value="2" >SOLO SUMINISTRO</option>
      <option value="3" >SOLO INSTALACION</option>
      </select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right">OBTENER DATOS DE OTRA COTIZACION: </td>
    <td><label>
      <input name="radiobutton" type="radio"  onclick="activar(this,false);" value="radiobutton"/>
      SI</label>
      <label>
        <input name="radiobutton" type="radio" value="radiobutton" checked="checked" onclick="activar(this,true);"/>
      NO</label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="right"><label>COTIZACION:
        
    </label></td>
    <td><select name="cotext" id="cotext">
      <option value="-1">Ninguna</option>
      <?php
do {  
?>
      <option value="<?php echo $row_RsCotiExt['idsubcotizacion']?>"><?php echo $row_RsCotiExt['identificador2']?></option>
      <?php
} while ($row_RsCotiExt = mysql_fetch_assoc($RsCotiExt));
  $rows = mysql_num_rows($RsCotiExt);
  if($rows > 0) {
      mysql_data_seek($RsCotiExt, 0);
	  $row_RsCotiExt = mysql_fetch_assoc($RsCotiExt);
  }
?>
    </select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td><input type="button" name="button2" id="button2" value="Cancelar"  onclick="window.close();"/>      
      <input type="submit" name="button" id="button" value="Aceptar" /></td>
  </tr>
</table> 
<input type="hidden" name="idcliente" value="<?php echo $_GET['idcliente'];?>" />
<input type="hidden" name="MM_insert" value="form1" />
</form>
</body>
</html>
<?php
mysql_free_result($RsClientes);

mysql_free_result($RsNumCotizacion);

mysql_free_result($RsCotiExt);

mysql_free_result($rsIP);

@mysql_free_result($rsCliente);
?>
