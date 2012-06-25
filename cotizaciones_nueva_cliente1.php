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

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsClientes = "select * from cliente";
$RsClientes = mysql_query($query_RsClientes, $tecnocomm) or die(mysql_error());
$row_RsClientes = mysql_fetch_assoc($RsClientes);
$totalRows_RsClientes = mysql_num_rows($RsClientes);


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO cotizacion (idcliente, consecutivo) VALUES (%s, %s)",
                       GetSQLValueString($_POST['idcliente'], "int"),
                       GetSQLValueString($_POST['textfield'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCoti = "select idcotizacion from cotizacion order by idcotizacion desc";
$RsCoti = mysql_query($query_RsCoti, $tecnocomm) or die(mysql_error());
$row_RsCoti = mysql_fetch_assoc($RsCoti);
$totalRows_RsCoti = mysql_num_rows($RsCoti);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsClientesX = "select * from cliente where idcliente=".$_POST['idcliente'];
$RsClientesX = mysql_query($query_RsClientesX, $tecnocomm) or die(mysql_error());
$row_RsClientesX = mysql_fetch_assoc($RsClientesX);
$totalRows_RsClientesX = mysql_num_rows($RsClientesX);

$l=strlen($_POST['textfield']);
$sa="";
if($l==1){$sa="00";}

if($l==2){$sa="0";}

$identi="C-".$sa.$_POST['textfield']."-".date("y").$row_RsClientesX['abreviacion'];

if($_POST['suministro']==1){
	$det="SUMINISTRO E INSTALACION GLOBAL ";
	$monto=1;
}
else{
	$det="0";
	$monto=1;
}


 $insertSQL = sprintf("INSERT INTO subcotizacion (idcotizacion, identificador,identificador2, fecha, formapago, moneda, vigencia, tipoentrega, garantia,tipo_cambio,nombre,utilidad_global,notas,contacto, usercreo, estado, tipo, descrimano, monto, descuento, cantidad, unidad, codigo, marca) VALUES (%s, %s, '%s', now(), %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 1, %s, '%s', %s, %s, 1.00, 'SERV', 'TECNOCOMM', 'TECNOCOMM')",$row_RsCoti['idcotizacion'],
                       GetSQLValueString($_POST['textfield'], "int"),
					   $identi,
                       GetSQLValueString($_POST['forma'], "text"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['vigencia'], "text"),
                       GetSQLValueString($_POST['entrega'], "text"),
                       GetSQLValueString($_POST['garantia'], "text"),
					   $_POST['TIPO'],
					   GetSQLValueString($_POST['nombre'], "text"),
					   $_POST['utilidad'],
					   GetSQLValueString($_POST['notas'], "text"),
					   1,$_SESSION['MM_Userid'],
					   GetSQLValueString($_POST['suministro'], "int"),
					   $det,
					   $monto,
					   $_POST['descuento']);

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
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
}
else
{
$cotext=-1;
}

  $insertGoTo = "cotizaciones_nueva.php?idcliente=".$_POST['idcliente']."&moneda=".$_POST['moneda']."&consecutivo=".$_POST['textfield']."&idsubcotizacion=".$row_RsSubCoti['idsubcotizacion']."&cotext=".$cotext."&utilidad=".$_POST['utilidad'];
  $_SESSION['tipo']=$_POST['TIPO'];
  $_SESSION['moneda']=$_POST['moneda'];
  $_SESSION['utilidad']=$_POST['utilidad'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsNumCotizacion = "select max(consecutivo) as sig from cotizacion ";
$RsNumCotizacion = mysql_query($query_RsNumCotizacion, $tecnocomm) or die(mysql_error());
$row_RsNumCotizacion = mysql_fetch_assoc($RsNumCotizacion);
$totalRows_RsNumCotizacion = mysql_num_rows($RsNumCotizacion);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCotiExt = "select * from subcotizacion";
$RsCotiExt = mysql_query($query_RsCotiExt, $tecnocomm) or die(mysql_error());
$row_RsCotiExt = mysql_fetch_assoc($RsCotiExt);
$totalRows_RsCotiExt = mysql_num_rows($RsCotiExt);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nueva Cotizacion</title>
<link href="style.css" rel="stylesheet" type="text/css" />
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

var win= null;
function NewWindow(mypage,myname,w,h,scroll){
  var winl = (screen.width-w)/2;
  var wint = (screen.height-h)/2;
  var settings  ='height='+h+',';
      settings +='width='+w+',';
      settings +='top='+wint+',';
      settings +='left='+winl+',';
      settings +='scrollbars='+scroll+',';
      settings +='resizable=yes';
  win=window.open(mypage,myname,settings);
  if(parseInt(navigator.appVersion) >= 4){win.window.focus();}
}
//-->

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
	
}/////fin valida

</script>

</head>

<body class="wrapper" onload="activar(this,true);"><form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>" onsubmit="return valida();">
<table width="550" border="0" align="center">
  <tr>
    <td width="16">&nbsp;</td>
    <td colspan="2" align="center" class="titulos" background="images/titulo.gif">SELECCIONA CLIENTE</td>
    <td width="33">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="173">&nbsp;</td>
    <td width="160">&nbsp;</td>
    <td><a href="nuevoCliente.php" onclick="NewWindow(this.href,'NUevo Cliente','500','500','yes');return false"><img src="images/AddUser.png" alt="Agregar Usuario" width="24" height="24" border="0" title="AGREGAR NUEVO CLIENTE" /></a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">CLIENTE:
      <label>
      <select name="idcliente" class="form" id="idcliente">
        <option value="-1">Selecciona Cliente</option>
        <?php
do {  
?>
        <option value="<?php echo $row_RsClientes['idcliente']?>"><?php echo $row_RsClientes['nombre']?></option>
        <?php
} while ($row_RsClientes = mysql_fetch_assoc($RsClientes));
  $rows = mysql_num_rows($RsClientes);
  if($rows > 0) {
      mysql_data_seek($RsClientes, 0);
	  $row_RsClientes = mysql_fetch_assoc($RsClientes);
  }
?>
      </select>
      </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><label></label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">IDENTIFICADOR:
      <input name="nombre" type="text" class="form" id="nombre" size="40" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">MONEDA:
      <label>
      <select name="moneda" class="form" id="moneda">
        <option value="0">PESOS</option>
        <option value="1">DOLARES</option>
      </select>
      </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">TIPO DE CAMBIO:
      <label>
      <input name="TIPO" type="text" class="form" id="TIPO" size="10" />
      </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">FORMA DE PAGO:
      <label id="fo">
      <select name="forma" id="forma" class="form">
        <option value="CONTADO">CONTADO</option>
        <option value="50 % ANTICIPO y 50% CONTRAENTREGA">50 % ANTICIPO y 50% CONTRAENTREGA</option>
        <option value="50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE">50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE</option>
        <option value="30 DIAS">30 DIAS</option>
        <option onclick="change('forma',40,'fo');">Otro...</option>
      </select>
      </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">VIGENCIA:
      <label id="vig">
      <select name="vigencia" id="vigencia" class="form">
        <option value="-1">Seleccionar</option>
        <option value="PRECIOS SUJETOS A CAMBIO SIN PREVIO AVISO">PRECIOS SUJETOS A CAMBIO SIN PREVIO AVISO</option>
        <option value="30 DIAS">30 DIAS</option>
        <option onclick="change('vigencia',40,'vig');">Otro...</option>
      </select>
      </label> 
      &nbsp;&nbsp;&nbsp;<BR />
      <label></label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">TIEMPO ENTREGA:
      <input name="entrega" type="text" class="form" id="entrega" size="40" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">GARANTIA:
      <label id="gar">
      <select name="garantia" id="garantia" class="form">
        <option value="-1">SELECCIONAR</option>
        <option value="1 AÑO MATERIAL Y MANO DE OBRA">1 AÑO MATERIAL Y MANO DE OBRA</option>
        <option value="25 AÑOS PANDUIT">25 AÑOS PANDUIT</option>
        <option onclick="change('garantia',40,'gar');">Otro...</option>
      </select>
      </label>&nbsp;&nbsp;&nbsp;<BR /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">FACTOR DE UTILIDAD:
      <input name="utilidad" type="text" class="form" id="utilidad" value="1" size="10" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">DESCUENTO:
      <input name="descuento" type="text" class="form" id="descuento" value="0" size="10" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><label>CONSECUTIVO:
      <input name="textfield" type="text" class="form" id="textfield" value="<?php echo $row_RsNumCotizacion['sig']+1; ?>" size="6" />
          </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">NOTAS O COMENTARIOS:<br />
      <label>
      <textarea name="notas" id="notas" cols="45" rows="5" class="form"></textarea>
      </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">TIPO DE SUMINISTRO:
      <select name="suministro" id="suministro">
        <option value="0">SUMINISTRO DE INSTALACION</option>
        <option value="1">SUMINISTRO GLOBAL</option>
        <option value="2" selected="selected">SOLO SUMINISTRO</option>
        </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><p>OBTENER DATOS DE OTRA COTIZACION:  &nbsp;&nbsp;
      <label>
      <input name="radiobutton" type="radio"  onclick="activar(this,false);" value="radiobutton"/>
      SI</label>
      <label>
      <input name="radiobutton" type="radio" value="radiobutton" checked="checked" onclick="activar(this,true);"/>
      NO</label>
    </p>      </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><label>COTIZACION:
        <select name="cotext" id="cotext">
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
        </select>
    </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="button2" id="button2" value="Cancelar"  onclick="window.close();"/></td>
    <td align="right"><label>
      <input type="submit" name="button" id="button" value="Aceptar" />
    </label></td>
    <td>&nbsp;</td>
  </tr>
</table> 
<input type="hidden" name="MM_insert" value="form1" />
</form>
</body>
</html>
<?php
mysql_free_result($RsClientes);

mysql_free_result($RsNumCotizacion);

mysql_free_result($RsCotiExt);
?>
