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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevaFactura")) {
  $insertSQL = sprintf("INSERT INTO factura (idcliente, fecha, moneda, idip,numfactura) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idcliente'], "int"),
                       GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['idip'], "int"),
					   GetSQLValueString($_POST['concecutivo'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "facturando.php?idfactura=".mysql_insert_id();
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCliente = "SELECT * FROM cliente ORDER BY nombre ASC";
$rsCliente = mysql_query($query_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);
$totalRows_rsCliente = mysql_num_rows($rsCliente);

$colname_rsContacto = "-1";
if (isset($_GET['idcliente'])) {
  $colname_rsContacto = $_GET['idcliente'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsContacto = sprintf("SELECT * FROM contactoclientes WHERE idcliente = %s ORDER BY nombre ASC", GetSQLValueString($colname_rsContacto, "int"));
$rsContacto = mysql_query($query_rsContacto, $tecnocomm) or die(mysql_error());
$row_rsContacto = mysql_fetch_assoc($rsContacto);
$totalRows_rsContacto = mysql_num_rows($rsContacto);

$col1_rsCotizaciones = "-1";
if (isset($_GET['idip'])) {
  $col1_rsCotizaciones = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = sprintf("SELECT s.idsubcotizacion, s.identificador2 FROM subcotizacion s, cotizacion c  WHERE c.idcotizacion = s.idcotizacion AND c.idip = %s AND s.estado > 2", GetSQLValueString($col1_rsCotizaciones, "int"));
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);

$colname_rsIp = "-1";
if (isset($_GET['idip'])) {
  $colname_rsIp = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsIp = sprintf("SELECT i.*,cc.nombre AS nombrecontacto, c.nombre FROM ip i, cliente c, contactoclientes cc WHERE i.idip = %s AND  i.idcliente = c.idcliente AND cc.idcliente = i.idcliente", GetSQLValueString($colname_rsIp, "int"));
$rsIp = mysql_query($query_rsIp, $tecnocomm) or die(mysql_error());
$row_rsIp = mysql_fetch_assoc($rsIp);
$totalRows_rsIp = mysql_num_rows($rsIp);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsNumFactura = "SELECT (MAX(numfactura)+1)  AS numerofactura FROM factura";
$rsNumFactura = mysql_query($query_rsNumFactura, $tecnocomm) or die(mysql_error());
$row_rsNumFactura = mysql_fetch_assoc($rsNumFactura);
$totalRows_rsNumFactura = mysql_num_rows($rsNumFactura);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nuevo Ip</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="myform">
<h1>Nueva Factura</h1>
<form method="POST" action="<?php echo $editFormAction; ?>" name="nuevaFactura">
<div>
<h3>Ip : <?php echo $row_rsIp['idip']; ?></h3>
<ul class="info">
<li>Cliente: <?php echo $row_rsIp['nombre']; ?></li>
<li>Contacto: <?php echo $row_rsIp['nombrecontacto']; ?></li>
</ul>
</div>
<div>
<h3>Datos de Factura</h3>
<label>
Consecutivo:
<input name="consecutivo" type="text" value="<?php echo $row_rsNumFactura['numerofactura']; ?>" />
</label>
<label>
Fecha:
<input type="text" name="fecha"  class="datepicker"/>
</label>
<label>
Moneda:
<select name="moneda">
        <option value="0">PESOS</option>
        <option value="1">DOLAR</option>
      </select>
</label>
<label>
Servicio a Facturar:
<select name="serviciofacutrar" >
<optgroup label="Cotizaciones">
<?php
do {  
?>
<option value="<?php echo $row_rsCotizaciones['idsubcotizacion']?>"><?php echo $row_rsCotizaciones['identificador2']?></option>
<?php
} while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones));
  $rows = mysql_num_rows($rsCotizaciones);
  if($rows > 0) {
      mysql_data_seek($rsCotizaciones, 0);
	  $row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
  }
?>
</optgroup>
<optgroup label="Orden de Servicio">
<option>12dds1</option>
</optgroup>
<optgroup label="Otros">
<option>Varios</option>
</optgroup>
</select>
</label>
</div>
<div class="botones">
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="idatendio" value="<?php echo $_SESSION['MM_Userid']?>" /> 
<input type="hidden" name="idip" value="<?php echo $_GET['idip'];?>"  />
<input type="hidden" name="idcliente" value="<?php echo $_GET['idcliente']?>" />
<input type="hidden" name="MM_insert" value="nuevaFactura" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($rsCliente);

mysql_free_result($rsContacto);

mysql_free_result($rsCotizaciones);

mysql_free_result($rsIp);
?>
