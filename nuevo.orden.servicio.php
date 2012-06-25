
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevaOrden")) {
	
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsAbre = "SELECT abreviacion, idcliente FROM cliente WHERE idcliente = (SELECT idcliente FROM ip WHERE idip = " . $_POST['ip'] . ")";
$RsAbre = mysql_query($query_RsAbre, $tecnocomm) or die(mysql_error());
$row_RsAbre = mysql_fetch_assoc($RsAbre);
$totalRows_RsAbre = mysql_num_rows($RsAbre);
	
	
  $insertSQL = sprintf("INSERT INTO ordenservicio (idcliente, idusuario, descripcionreporte, observaciones, numeroorden, estado, fecha, identificador, idip,hora, cargo, pendiente) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s,now(),2,2)",
                       GetSQLValueString($row_RsAbre['idcliente'], "int"),
                       GetSQLValueString($_POST['idusuario'], "int"),
                       GetSQLValueString($_POST['descripcionreporte'], "text"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($_POST['numeroorden'], "int"),
                       GetSQLValueString($_POST['estado'], "int"),
                       GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['identificador'] . ' ' . $row_RsAbre['abreviacion'], "text"),
                       GetSQLValueString($_POST['ip'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
$id=mysql_insert_id();
  $insertGoTo = "editar.orden.detalle.php?idordenservicio=".$id."&idip=".$_POST['ip'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsNumOrden = "SELECT MAX(numeroorden) as numeroorden FROM ordenservicio WHERE EXTRACT(YEAR from fecha) = YEAR(NOW())";
$RsNumOrden = mysql_query($query_RsNumOrden, $tecnocomm) or die(mysql_error());
$row_RsNumOrden = mysql_fetch_assoc($RsNumOrden);
$totalRows_RsNumOrden = mysql_num_rows($RsNumOrden);

/*
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsAbre = "SELECT abreviacion FROM clientes WHERE idcliente = " . $_GET['idcliente'];
$RsAbre = mysql_query($query_RsAbre, $tecnocomm) or die(mysql_error());
$row_RsAbre = mysql_fetch_assoc($RsAbre);
$totalRows_RsAbre = mysql_num_rows($RsAbre);
*/

$num_orden = sprintf("OS-%03d-%s",$row_RsNumOrden['numeroorden'] + 1, date("y"));
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Nueva Orden de Servicio</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Nueva Orden de Servicio</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevaOrden" method="POST">
<div>
<h3>Datos Generales</h3>
<?php if(isset($_GET['ip'])&&($_GET['ip']!='')){  ?>
<input type="hidden" name="ip" value="<?php echo $_GET['ip'];?>" />
<? }else{?>
<label>Ip:</label>
<input type="text" name="ip" value="<?php echo $_GET['ip'];?>" />
<? }?>
<label>Identificador:
<input name="nombre" type="text" class="form" id="nombre" size="40" />
</label>
<label>Numero Orden:
  <input name="numeroorden" type="text" value="<?php echo $row_RsNumOrden['numeroorden'] + 1; ?>" />
</label>
<label>Descripcion Reporte:
  <textarea name="descripcionreporte">
  </textarea>
</label>
<label>Observaciones:
  <textarea name="observaciones">
  </textarea>
</label>
</div>
<div class="button">
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="idcliente" value="<?php echo $_GET['idcliente']?>"/>
<input type="hidden" name="estado" value="0"/>
<input type="hidden" name="idusuario" value="<?php echo $_SESSION['MM_Userid']?>"/>
<input type="hidden" name="fecha" value="<?php echo date('Y-m-d')?>"/>
<input type="hidden" name="identificador" value="<?php echo $num_orden; ?>"/>
<input type="hidden" name="MM_insert" value="nuevaOrden" />
</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsNumOrden);
?>