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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevaSalida")) {
  $insertSQL = sprintf("INSERT INTO subcotizacionsalida (idsub, autoriza, creo, fecha, hora, responsable) VALUES (%s, %s, %s, now(), now(), %s)",
                       GetSQLValueString($_POST['idsub'], "int"),
                       GetSQLValueString($_POST['autoriza'], "int"),
                       GetSQLValueString($_POST['creo'], "int"),
					   GetSQLValueString($_POST['responsable'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "detalleSalida.php?idsalida=".mysql_insert_id();
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_RsSubcotizacion = "-1";
if (isset($_GET['idsub'])) {
  $colname_RsSubcotizacion = $_GET['idsub'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSubcotizacion = sprintf("SELECT *,(select count(*) FROM subcotizacionarticulo a WHERE a.idsubcotizacion=subcotizacion.idsubcotizacion) as cant FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_RsSubcotizacion, "int"));
$RsSubcotizacion = mysql_query($query_RsSubcotizacion, $tecnocomm) or die(mysql_error());
$row_RsSubcotizacion = mysql_fetch_assoc($RsSubcotizacion);
$totalRows_RsSubcotizacion = mysql_num_rows($RsSubcotizacion);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUsuario = "SELECT * FROM usuarios ORDER BY nombrereal ASC";
$RsUsuario = mysql_query($query_RsUsuario, $tecnocomm) or die(mysql_error());
$row_RsUsuario = mysql_fetch_assoc($RsUsuario);
$totalRows_RsUsuario = mysql_num_rows($RsUsuario);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nueva Salida</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Nueva Salida</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevaSalida" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Cotizacion:<?php echo $row_RsSubcotizacion['identificador2']; ?>

</label>
<label>Autoriza:
<select name="autoriza">
  <?php
do {  
?>
  <option value="<?php echo $row_RsUsuario['id']?>"><?php echo $row_RsUsuario['nombrereal']?></option>
  <?php
} while ($row_RsUsuario = mysql_fetch_assoc($RsUsuario));
  $rows = mysql_num_rows($RsUsuario);
  if($rows > 0) {
      mysql_data_seek($RsUsuario, 0);
	  $row_RsUsuario = mysql_fetch_assoc($RsUsuario);
  }
?>
</select>
</label>
<label>Responsable:
<select name="responsable">
  <?php
do {  
?>
  <option value="<?php echo $row_RsUsuario['id']?>"><?php echo $row_RsUsuario['nombrereal']?></option>
  <?php
} while ($row_RsUsuario = mysql_fetch_assoc($RsUsuario));
  $rows = mysql_num_rows($RsUsuario);
  if($rows > 0) {
      mysql_data_seek($RsUsuario, 0);
	  $row_RsUsuario = mysql_fetch_assoc($RsUsuario);
  }
?>
</select>
</label>
<label>Cantidad de Partidas:<?php echo $row_RsSubcotizacion['cant']; ?></label>

<label></label>


</div>

<div>
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="idsub" value="<?php echo $_GET['idsub'];?>" />

<input type="hidden" name="creo" value="<?php echo $_SESSION['MM_Userid'];?>" />
<input type="hidden" name="MM_insert" value="nuevaSalida" />
</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsSubcotizacion);

mysql_free_result($RsUsuario);
?>