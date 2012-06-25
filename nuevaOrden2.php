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

$colname_rsAbreviacion = "-1";
if (isset($_POST['idproveedor'])) {
  $colname_rsAbreviacion = $_POST['idproveedor'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAbreviacion = sprintf("SELECT * FROM proveedor WHERE idproveedor = %s", GetSQLValueString($colname_rsAbreviacion, "int"));
$rsAbreviacion = mysql_query($query_rsAbreviacion, $tecnocomm) or die(mysql_error());
$row_rsAbreviacion = mysql_fetch_assoc($rsAbreviacion);
$totalRows_rsAbreviacion = mysql_num_rows($rsAbreviacion);
//generamos identificador


function zerofill ($num,$zerofill) {
    while (strlen($num)<$zerofill) {
        $num = "0".$num;
    }
    return $num;
}

$identificador = "OC-".zerofill($_POST['consecutivo'],4)."-".date("y").$row_rsAbreviacion ['abreviacion'];


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO ordencompra (idcotizacion, formapago, moneda, vigencia, tiempoentrega, notas, usercreo, consecutivo, idproveedor, descuento, tipoorden, tituloconcepto,fecha,identificador) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,NOW(),%s)",
                       GetSQLValueString($_POST['idcotizacion'], "int"),
                       GetSQLValueString($_POST['formapago'], "text"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['vigencia'], "text"),
                       GetSQLValueString($_POST['tiempoentrega'], "text"),
                       GetSQLValueString($_POST['notas'], "text"),
                       GetSQLValueString($_POST['usercreo'], "int"),
                       GetSQLValueString($_POST['consecutivo'], "int"),
                       GetSQLValueString($_POST['idproveedor'], "int"),
                       GetSQLValueString($_POST['descuento'], "text"),
                       GetSQLValueString($_POST['tipoorden'], "int"),
                       GetSQLValueString($_POST['tituloconcepto'], "text"),
					   GetSQLValueString($identificador, "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
$id = mysql_insert_id();
  $insertGoTo = "detalleOrden2.php?idordencompra=".$id;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedor = "SELECT * FROM proveedor ORDER BY nombrecomercial ASC";
$rsProveedor = mysql_query($query_rsProveedor, $tecnocomm) or die(mysql_error());
$row_rsProveedor = mysql_fetch_assoc($rsProveedor);
$totalRows_rsProveedor = mysql_num_rows($rsProveedor);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacioens = "SELECT * FROM subcotizacion ORDER BY identificador2 DESC";
$rsCotizacioens = mysql_query($query_rsCotizacioens, $tecnocomm) or die(mysql_error());
$row_rsCotizacioens = mysql_fetch_assoc($rsCotizacioens);
$totalRows_rsCotizacioens = mysql_num_rows($rsCotizacioens);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConsecutivo = "SELECT MAX(consecutivo) AS siguiente FROM ordencompra where consecutivo < 956 AND EXTRACT(YEAR FROM fecha) = YEAR(NOW()) GROUP BY EXTRACT(YEAR FROM fecha) ORDER BY EXTRACT(YEAR FROM fecha) desc";
$rsConsecutivo = mysql_query($query_rsConsecutivo, $tecnocomm) or die(mysql_error());
$row_rsConsecutivo = mysql_fetch_assoc($rsConsecutivo);
$totalRows_rsConsecutivo = mysql_num_rows($rsConsecutivo);

$colname_rsAbreviacion = "-1";
if (isset($_POST['idproveedor'])) {
  $colname_rsAbreviacion = $_POST['idproveedor'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAbreviacion = sprintf("SELECT * FROM proveedor WHERE idproveedor = %s", GetSQLValueString($colname_rsAbreviacion, "int"));
$rsAbreviacion = mysql_query($query_rsAbreviacion, $tecnocomm) or die(mysql_error());
$row_rsAbreviacion = mysql_fetch_assoc($rsAbreviacion);
$totalRows_rsAbreviacion = mysql_num_rows($rsAbreviacion);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<script language="javascript">
function change(name,size,lab){
s = document.getElementById(name);
obj = document.createElement('input')
obj.type = 'text'
obj.id = name;
obj.name = name;
obj.size = size;
document.getElementById(lab).replaceChild(obj,s)
}

function activar(obj,val) {
    //dis = obj.selectedIndex==0 ? false : true;
    document.getElementById(obj).disabled=val;
    
} 
</script>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center" class="wrapper">
  <tr valign="baseline"><td>TIPO ORDEN DE COMPRA</td><td><label><input type="radio" name="tipoorden" value="0"  checked="checked" onclick="activar('idcotizacion',false); activar('tituloconcepto',true);"/>Ligada a Cotizacion</label><label><input type="radio" name="tipoorden" value="1"  onclick="activar('idcotizacion',true); activar('tituloconcepto',false);"/> Abierta</label></td></tr>
    <tr valign="baseline"><td align="right" valign="top">CONCEPTO</td>
    <td><textarea name="tituloconcepto" rows="4" cols="36" id="tituloconcepto" disabled="disabled"></textarea> </td></tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">COTIZACION: </td>
      <td><select name="idcotizacion" id="idcotizacion" >
      
        <?php
do {  
?>
        <option value="<?php echo $row_rsCotizacioens['idsubcotizacion']?>"><?php echo $row_rsCotizacioens['identificador2']?></option>
        <?php
} while ($row_rsCotizacioens = mysql_fetch_assoc($rsCotizacioens));
  $rows = mysql_num_rows($rsCotizacioens);
  if($rows > 0) {
      mysql_data_seek($rsCotizacioens, 0);
	  $row_rsCotizacioens = mysql_fetch_assoc($rsCotizacioens);
  }
?>
      </select>
      </td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">PROVEEDOR:</td>
      <td><select name="idproveedor">
        <?php
do {  
?>
        <option value="<?php echo $row_rsProveedor['idproveedor']?>"><?php echo $row_rsProveedor['nombrecomercial']?></option>
        <?php
} while ($row_rsProveedor = mysql_fetch_assoc($rsProveedor));
  $rows = mysql_num_rows($rsProveedor);
  if($rows > 0) {
      mysql_data_seek($rsProveedor, 0);
	  $row_rsProveedor = mysql_fetch_assoc($rsProveedor);
  }
?>
      </select>
      </td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">FORMA DE PAGO:</td>
      <td><label id="fo"><select name="formapago" id="formapago" class="form">
        <option value="CONTADO">CONTADO</option>
        <option value="50 % ANTICIPO y 50% CONTRAENTREGA">50 % ANTICIPO y 50% CONTRAENTREGA</option>
        <option value="50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE">50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE</option>
        <option value="30 DIAS">30 DIAS</option>
        <option onclick="change('formapago',40,'fo');">Otro...</option>
      </select></label></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">MONEDA:</td>
      <td><select name="moneda">
        <option value="0" <?php if (!(strcmp(0, ""))) {echo "SELECTED";} ?>>PESOS</option>
        <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>DOLARES</option>
      </select>
      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">VIGENCIA:</td>
      <td>  <label id="vig">
      <select name="vigencia" id="vigencia" class="form">
        <option value="-1">Seleccionar</option>
        <option value="PRECIOS SUJETOS A CAMBIO SIN PREVIO AVISO">PRECIOS SUJETOS A CAMBIO SIN PREVIO AVISO</option>
        <option value="30 DIAS">30 DIAS</option>
        <option onclick="change('vigencia',40,'vig');">Otro...</option>
      </select>
      </label> </td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">TIEMPO DE ENTREGA:</td>
      <td><input type="text" name="tiempoentrega" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">DESCUENTO:</td>
      <td><input type="text" name="descuento" value="" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">NOTAS:</td>
      <td><textarea name="notas" rows="3" cols="35"></textarea></td>
    </tr>

    <tr valign="baseline">
      <td nowrap="nowrap" align="right">CONSECUTIVO:</td>
      <td><input type="text" name="consecutivo" value="<?php echo $row_rsConsecutivo['siguiente'] + 1; ?>" size="10" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td align="right"><input type="submit" value="CREAR ORDEN DE COMPRA" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1" />
  <input type="hidden" name="usercreo" value="<?php echo $_SESSION['MM_Userid']?>" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rsProveedor);

mysql_free_result($rsCotizacioens);

mysql_free_result($rsConsecutivo);

mysql_free_result($rsAbreviacion);
?>
