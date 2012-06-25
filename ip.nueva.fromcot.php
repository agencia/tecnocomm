<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmCrearIp")) {
  $insertSQL = sprintf("INSERT INTO ip (idcliente, idatendio, idcontacto, fecha, hora, descripcion) VALUES (%s, %s, %s,NOW(),NOW(), %s)",
                       GetSQLValueString($_POST['idcliente'], "int"),
                       GetSQLValueString($_POST['idatendio'], "int"),
                       GetSQLValueString($_POST['idcontacto'], "int"),
					   GetSQLValueString($_POST['descripcion'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  
  $idip = mysql_insert_id();
  
    $insertSQL = sprintf("UPDATE factura f JOIN facturacotizacion fc ON f.idfactura = fc.idfactura JOIN subcotizacion sb ON sb.idsubcotizacion = fc.idcotizacion SET f.idip = %s, f.anticipo = fc.numeroanticipo WHERE sb.idcotizacion =  %s ", $idip,GetSQLValueString($_POST['idcotizacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  
  //actualizar cotizacion
  $updateSQL = sprintf("UPDATE cotizacion SET idip = %s WHERE idcotizacion = %s",$idip,GetSQLValueString($_POST['idcotizacion'],"int"));
    mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "closeip.php?idip=".$idip;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rsCotizacion = "-1";
if (isset($_GET['idcotizacion'])) {
  $colname_rsCotizacion = $_GET['idcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT * FROM cotizacion WHERE idcotizacion = %s", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);

$colname_rsSubCotizacion = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsSubCotizacion = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsSubCotizacion = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_rsSubCotizacion, "int"));
$rsSubCotizacion = mysql_query($query_rsSubCotizacion, $tecnocomm) or die(mysql_error());
$row_rsSubCotizacion = mysql_fetch_assoc($rsSubCotizacion);
$totalRows_rsSubCotizacion = mysql_num_rows($rsSubCotizacion);

$colname_rsFacturas = "-1";
if (isset($_GET['idcotizacion'])) {
  $colname_rsFacturas = $_GET['idcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = sprintf("SELECT f . * FROM factura f JOIN facturacotizacion fc ON f.idfactura = fc.idfactura JOIN subcotizacion sb ON sb.idsubcotizacion = fc.idcotizacion WHERE sb.idcotizacion =  %s ", GetSQLValueString($colname_rsFacturas, "int"));
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);

$colname_rsSubCotisaciones = "-1";
if (isset($_GET['idcotizacion'])) {
  $colname_rsSubCotisaciones = $_GET['idcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsSubCotisaciones = sprintf("SELECT * FROM subcotizacion WHERE idcotizacion = %s", GetSQLValueString($colname_rsSubCotisaciones, "int"));
$rsSubCotisaciones = mysql_query($query_rsSubCotisaciones, $tecnocomm) or die(mysql_error());
$row_rsSubCotisaciones = mysql_fetch_assoc($rsSubCotisaciones);
$totalRows_rsSubCotisaciones = mysql_num_rows($rsSubCotisaciones);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Crear Ip De Cotizacion</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Crear Ip De Cotizacion</h1>
<form action="<?php echo $editFormAction; ?>" name="frmCrearIp" method="POST" id="myform">
<div>
<h3>Informacion Encontrada Relacionada </h3>
<p>Las facturas y cotizaciones listadas acontinuacion seran agregadas automaticamente a la ip creada.</p>
<h3>Facturas Encontradas</h3>
<?php if ($totalRows_rsFacturas > 0) { // Show if recordset not empty ?>
  <div class="distabla">
    <table width="80%" cellpadding="0" cellspacing="0">
      <thead>
        <tr>
          <td>Numero Factura</td>
          <td>Fecha Factura</td>
          </tr>
      </thead>
      <tbody>
        <?php do { ?>
          <tr>
            <td><?php echo $row_rsFacturas['numfactura']; ?></td>
            <td><?php echo formatDate($row_rsFacturas['fecha']); ?></td>
          </tr>
          <?php } while ($row_rsFacturas = mysql_fetch_assoc($rsFacturas)); ?>
      </tbody>
    </table>
  </div>
  <?php } // Show if recordset not empty ?>
<?php if ($totalRows_rsFacturas == 0) { // Show if recordset empty ?>
  <p>No se encontro ninguna factura relacionada con esta cotizacion o cotizaciones. Es posible que haya facturas ya creadas para este grupo de cotizaciones de ser asi debera agregarlas manualmente ala ip </p>
  <?php } // Show if recordset empty ?>
<p>Nota: Es posible que no de el total de facturas creadas sobre esta cotizacion...</p>
<h3>Cotizaciones Encontradas</h3>
<div class="distabla">
<table width="80%" cellpadding="0" cellspacing="0">
<thead>
<tr>
<td>Identificador</td>
<td>Fecha Cotizacion</td>
</tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsSubCotisaciones['identificador2']; ?></td>
      <td><?php echo formatDate($row_rsSubCotisaciones['fecha']); ?></td>
    </tr>
    <?php } while ($row_rsSubCotisaciones = mysql_fetch_assoc($rsSubCotisaciones)); ?>
</tbody>
</table>
</div>
<div>
<h3>Descripcion</h3>
<label>Descripcion:<textarea name="descripcion" cols="" rows=""></textarea></label>
</div>
<input type="hidden" name="idcotizacion"  value="<?php echo $_GET['idcotizacion'];?>"/>
<input type="hidden" name="idcliente" value="<?php echo $row_rsCotizacion['idcliente']; ?>" />
<input type="hidden" name="idcontacto" value="<?php echo $row_rsSubCotizacion['contacto']; ?>" />
<input type="hidden" name="idatendio" value="<?php echo $_SESSION['MM_Userid'];?>" />
<button type="submit">Crear</button>
</div>
<input type="hidden" name="MM_insert" value="frmCrearIp" />
</form>
</body>
</html>
<?php
mysql_free_result($rsCotizacion);

mysql_free_result($rsSubCotizacion);

mysql_free_result($rsFacturas);

mysql_free_result($rsSubCotisaciones);
?>
