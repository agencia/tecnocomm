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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "registrar")) {

if($_POST['edo']>4){
$cantidad=0;
}
else{
	$cantidad=$_POST['cantidad'];
}


  $insertSQL = sprintf("INSERT INTO subcotizacionarticulo (idsubcotizacion, idarticulo, descri, precio_cotizacion, cantidad, utilidad, mo, moneda, reall, tipo_cambio, marca1) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idsubcotizacion'], "int"),
                       GetSQLValueString($_POST['idarticulo'], "int"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['precio'], "double"),
                       GetSQLValueString($cantidad, "double"),
                       GetSQLValueString($_POST['utilidad'], "double"),
                       GetSQLValueString($_POST['mo'], "double"),
					   GetSQLValueString($_POST['moneda'], "int"),
					   GetSQLValueString($_POST['cantidad'], "double"),
					   GetSQLValueString($_POST['cambio'], "double"),
					   GetSQLValueString($_POST['marca1'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

	$updateSQL1 = sprintf("UPDATE subcotizacion SET  usercreo=%s WHERE idsubcotizacion=%s",
                       GetSQLValueString($_SESSION['MM_Userid'], "int"),
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result11 = mysql_query($updateSQL1, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$ide_RsArticulo = "-1";
if (isset($_GET['idarticulo'])) {
  $ide_RsArticulo = $_GET['idarticulo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulo = sprintf("select * from articulo where idarticulo=%s", GetSQLValueString($ide_RsArticulo, "int"));
$RsArticulo = mysql_query($query_RsArticulo, $tecnocomm) or die(mysql_error());
$row_RsArticulo = mysql_fetch_assoc($RsArticulo);
$totalRows_RsArticulo = mysql_num_rows($RsArticulo);

$idesub_RsSub = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $idesub_RsSub = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSub = sprintf("select * from subcotizacion where idsubcotizacion=%s", GetSQLValueString($idesub_RsSub, "int"));
$RsSub = mysql_query($query_RsSub, $tecnocomm) or die(mysql_error());
$row_RsSub = mysql_fetch_assoc($RsSub);
$totalRows_RsSub = mysql_num_rows($RsSub);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>AGREGAR ARTICULO A COTIZACION</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript">

function confirmar(elementName,elementValueOld,elementValueNew){

if(!confirm("Usted A Elegido Hacer El Siguiente Cambio, \nConfirme Por Favor, \n Valor Orginial: "+elementValueOld+" \nValor Nuevo: "+elementValueNew)){
	
	document.getElementById(elementName).value =  elementValueOld;

}else{
	document.getElementById(elementName).value =  elementValueNew;

}


}
</script>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>

<body class="wrapper" onload = "document.forms[0].cantidad.focus()">
<form name="registrar" method="POST" action="<?php echo $editFormAction; ?>" >
<table width="500" border="0" align="center" >
  <tr>
    <td width="27">&nbsp;</td>
    <td colspan="2" align="center" background="images/titulo.gif" class="titulos">DATOS ARTICULO</td>
    <td width="30">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><?php if($row_RsSub['tipo']==0){$cad="SUM E INST "; } ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="212">TIPO CAMBIO:<span class="Estilo1"><?php //echo $row_RsSub['tipo_cambio']; ?></span></td>
    <td width="213"><input name="cambio" type="text" class="form" id="cambio" value="<?php echo $row_RsSub['tipo_cambio']; ?>" onchange="confirmar('cambio','<?php echo money_format('%i',$row_RsSub['tipo_cambio']); ?>',this.form.cambio.value);"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">DESCRIPCION:</td>
    <td><label>
    <textarea name="nombre" cols="45" rows="3" class="form" id="nombre" onchange="confirmar('nombre','<?php echo $cad.$row_RsArticulo['nombre']; ?>',this.form.nombre.value);"><?php echo htmlentities($cad.$row_RsArticulo['nombre']); ?></textarea>
    </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">PRECIO:</td>
    <td><input name="precio" type="text" class="form" id="precio" value="<?php echo round($row_RsArticulo['precio'],2); ?>" onchange="confirmar('precio','<?php echo money_format('%i',$row_RsArticulo['precio']); ?>',this.form.precio.value);"/><?php if($row_RsArticulo['moneda']==0){ echo "M.N.";}if($row_RsArticulo['moneda']==1){ echo "USD";} ?>&nbsp;&nbsp;<?php if($row_RsArticulo['tipo']==0){ echo "PL";}if($row_RsArticulo['tipo']==1){ echo "CO";} ?></td>
    <td>&nbsp;</td> 
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">MANO DE OBRA: </td>
    <td><input name="mo" type="text" class="form" id="mo" value="<?php echo $row_RsArticulo['instalacion']; ?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">CANTIDAD:</td>
    <td><input name="cantidad" type="text" class="form" id="cantidad" value="1" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">FACTOR UTILIDAD:</td>
    <td align="left"><input name="utilidad" type="text" class="form" id="utilidad" value="<?php echo $row_RsSub['utilidad_global'];?> " /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right"><label>
      <input type="submit" name="button" id="button" value="Aceptar" />
    </label></td>
    <td>&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="idarticulo" value="<?php echo $row_RsArticulo['idarticulo']; ?>"/>
<input type="hidden" name="marca1" value="<?php echo $row_RsArticulo['marca']; ?>"/>
<input type="hidden" name="moneda" value="<?php echo $row_RsArticulo['moneda']; ?>"/>
<input type="hidden" name="edo" value="<?php echo $row_RsSub['estado']; ?>"/>
<input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion']; ?>"/>
<input type="hidden" name="tipo" value="<?php echo $row_RsSub['tipo']; ?>"/>
<input type="hidden" name="MM_insert" value="registrar" />
</form>
</body>
</html>
<?php
mysql_free_result($RsArticulo);

mysql_free_result($RsSub);
?>
