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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE subcotizacion SET formapago=%s, nombre=%s, vigencia=%s, tipoentrega=%s, garantia=%s, notas=%s,  descuento=%s, descuentoreal=%s, usercreo=%s WHERE idsubcotizacion=%s",
                       GetSQLValueString($_POST['forma'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['vigencia'], "text"),
                       GetSQLValueString($_POST['entrega'], "text"),
                       GetSQLValueString($_POST['garantia'], "text"),
                       GetSQLValueString($_POST['notas'], "text"),
                       GetSQLValueString($_POST['descuento'], "double"),
                       GetSQLValueString($_POST['descuentoreal'], "double"),
                       GetSQLValueString($_SESSION['MM_Userid'], "int"),
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
  
 require_once('lib/eventos.php');
	$evt = new evento(22,$_SESSION['MM_Userid'],"Datos de cotizacion modificados cotizacion  :".$_POST['idsubcotizacion']);
	$evt->registrar();
	
	
  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$ide_RsSub = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $ide_RsSub = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSub = sprintf("SELECT *,(select nombre FROM cliente WHERE cliente.idcliente= cotizacion.idcliente) as clie  from cotizacion,subcotizacion where cotizacion.idcotizacion=subcotizacion.idcotizacion AND idsubcotizacion=%s", GetSQLValueString($ide_RsSub, "int"));
$RsSub = mysql_query($query_RsSub, $tecnocomm) or die(mysql_error());
$row_RsSub = mysql_fetch_assoc($RsSub);
$totalRows_RsSub = mysql_num_rows($RsSub);

$moneda=array(0=>"Pesos",1=>"Dolares");
$tipo=array(0=>"SUMINISTRO E INSTLACION",1=>"SERVICIO DE INSTALACION",2=>"SOLO SUMINISTRO",3=>"SOLO INSTALACION");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nueva Cotizacion</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/funciones.js"></script>

</head>

<body class="wrapper"><form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
<table width="400" border="0" align="center">
  <tr>
    <td width="16">&nbsp;</td>
    <td colspan="2" align="center" class="titulos" background="images/titulo.gif">MODIFICAR DATOS COTIZACION</td>
    <td width="33">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="173">&nbsp;</td>
    <td width="160">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">CLIENTE:
      <label><?php echo $row_RsSub['clie']; ?></label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">IDENTIFICADOR:
      <input name="nombre" type="text" class="form" id="nombre" size="40"  value="<?php echo $row_RsSub['nombre']; ?>"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">TIPO DE COTIZACION: <?php echo $tipo[$row_RsSub['tipo']]; ?><a href="modificarTipoCoti.php?idsub=<?php echo $_GET['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Mod tipo','1000','700','no');return false"><img src="images/Edit.png" alt="f" width="24" height="24" border="0" title="MODIFICAR FACTOR DE UTILIDAD" /></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">MONEDA:
      <label><?php echo $moneda[$row_RsSub['moneda']]; ?><a href="modificarTipoMoneda.php?idsub=<?php echo $_GET['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Mod Moneda','1000','700','no');return false"><img src="images/Edit.png" alt="f" width="24" height="24" border="0" title="MODIFICAR FACTOR DE UTILIDAD" /></a></label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">TIPO DE CAMBIO:
      <label><?php echo $row_RsSub['tipo_cambio']; ?><a href="modificarTipoCambio.php?idsub=<?php echo $_GET['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Mod Factor','1000','700','no');return false"><img src="images/Edit.png" alt="f" width="24" height="24" border="0" title="MODIFICAR FACTOR DE UTILIDAD" /></a></label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">FORMA DE PAGO:
      <input type="text" name="forma" value="<?php echo $row_RsSub['formapago'];?>" >    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">VIGENCIA:
      <label></label>&nbsp;&nbsp;&nbsp;
      <input name="vigencia" type="text" class="form" id="vigencia" size="40"  value="<?php echo $row_RsSub['vigencia']; ?>"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">TIEMPO ENTREGA:
      <input name="entrega" type="text" class="form" id="entrega" size="40" value="<?php echo $row_RsSub['tipoentrega']; ?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">GARANTIA:
      <label></label>&nbsp;&nbsp;&nbsp;
      <input name="garantia" type="text" class="form" id="garantia" size="40" value="<?php echo $row_RsSub['garantia']; ?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">DESCUENTO
      <input name="descuento" type="text" class="form" id="descuento" size="10"  value="<?php echo $row_RsSub['descuento']; ?>"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">DESCUENTO REAL
      <input name="descuentoreal" type="text" class="form" id="descuento" size="10"  value="<?php echo $row_RsSub['descuentoreal']; ?>"/></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">FACTOR DE UTILIDAD:
     <?php echo $row_RsSub['utilidad_global']; ?><a href="modificarFactor.php?idsub=<?php echo $_GET['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Mod Factor','1000','700','no');return false"><img src="images/Edit.png" width="24" height="24" border="0" title="MODIFICAR FACTOR DE UTILIDAD" /></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center">NOTAS O COMENTARIOS:
      <label>
      <textarea name="notas" id="notas" cols="45" rows="5"><?php echo $row_RsSub['notas']; ?></textarea>
      </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
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
<input type="hidden" name="idsubcotizacion" value="<? echo $_GET['idsubcotizacion'];?>" />
<input type="hidden" name="MM_update" value="form1" />
</form>
</body>
</html>
<?php
mysql_free_result($RsSub);


?>
