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
}if (!function_exists("GetSQLValueString")) {
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevoIp")) {
  $insertSQL = sprintf("INSERT INTO ip (idcliente, fecha, idatendio, idcontacto,descripcion, hora) VALUES (%s, NOW(), %s, %s,%s, NOW() )",
                       GetSQLValueString($_POST['idcliente'], "int"),
					   GetSQLValueString($_POST['idatendio'], "int"),
					   GetSQLValueString($_POST['idcontacto'], "int"),
					    GetSQLValueString($_POST['descripcion'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());


$idip = mysql_insert_id();

switch($_POST['mod']){
		
		case "cotizacion": $link = "cotizaciones_nueva_cliente_ip.php?idip=".$idip."&idcliente=".$_POST['idcliente']."&idcontacto=".$_POST['idcontacto'];
			break;
		case "factura": $link = "nuevaFactura.php?idip=".$idip."&idcliente=".$_POST['idcliente']."&idcontacto=".$_POST['idcontacto'];
			break;

		
		default :
			$link = "closeip.php?idip=".$idip;
	}


  $insertGoTo = $link;
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nuevo Ip</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
</head>

<body>
<div id="myform">
<h1>Nuevo IP</h1>
<?php if(!isset($_GET['idcliente'])) { ?>
<form method="GET" action="">
<div>
<h3>Cliente</h3>
<label>Seleccionar
  <select name="idcliente">
    <?php
do {  
?>
    <option value="<?php echo $row_rsCliente['idcliente']?>" onclick="this.form.submit();"><?php echo $row_rsCliente['nombre']?></option>
  <?php
} while ($row_rsCliente = mysql_fetch_assoc($rsCliente));
  $rows = mysql_num_rows($rsCliente);
  if($rows > 0) {
      mysql_data_seek($rsCliente, 0);
	  $row_rsCliente = mysql_fetch_assoc($rsCliente);
  }
?>
</select>
</label>

<h3>Nuevo Cliente</h3>
<a href="nuevoCliente.php" class="popup">Crear Nuevo Cliente</a>
</div>
<input type="hidden" name="mod"  value="<?php echo $_GET['mod']?>"/>
</form>
<?php } else { ?>
<form method="POST" action="<?php echo $editFormAction; ?>" name="nuevoIp">
<div>
<h3>Contacto</h3>
<label>Persona de Contacto
<select name="idcontacto">
  <?php
do {  
?>
  <option value="<?php echo $row_rsContacto['idcontacto']?>"><?php echo $row_rsContacto['nombre']?></option>
  <?php
} while ($row_rsContacto = mysql_fetch_assoc($rsContacto));
  $rows = mysql_num_rows($rsContacto);
  if($rows > 0) {
      mysql_data_seek($rsContacto, 0);
	  $row_rsContacto = mysql_fetch_assoc($rsContacto);
  }
?>
</select>
</label>
<h3>Nuevo Contacto</h3>
<a href="nuevoContacto.php?idcliente=<?php echo $_GET['idcliente'];?>" class="popup">Crear Nuevo Contacto</a>
</div>
<div>
<label>
Descripcion
<textarea name="descripcion"></textarea>
</label>
</div>
<div>
<fieldset>
<label><input type="checkbox" />Otro</label>
<label><input type="checkbox" />Orden. Servicio</label>
<label><input type="checkbox" />Levantamiento</label>
<label><input type="checkbox" />Cotizacion</label>
</fieldset>
</div>
<?php } ?>
<div class="botones">
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="idatendio" value="<?php echo $_SESSION['MM_Userid']?>" /> 
<input type="hidden" name="idcliente" value="<?php echo $_GET['idcliente']?>" /> 
<input type="hidden" name="mod"  value="<?php echo $_GET['mod']?>"/>
<input type="hidden" name="MM_insert" value="nuevoIp" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($rsCliente);

mysql_free_result($rsContacto);
?>
