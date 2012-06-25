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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO reporteavance (idempleado, reporte, idsubcotizacion, fecha, hora) VALUES (%s, %s, %s, now(), now())",
                       GetSQLValueString($_POST['idempleado'], "int"),
                       GetSQLValueString($_POST['reprote'], "text"),
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$ide_RsProyecto = "-1";
if (isset($_GET['id'])) {
  $ide_RsProyecto = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsProyecto = sprintf("select * from subcotizacion where idsubcotizacion=%s", GetSQLValueString($ide_RsProyecto, "int"));
$RsProyecto = mysql_query($query_RsProyecto, $tecnocomm) or die(mysql_error());
$row_RsProyecto = mysql_fetch_assoc($RsProyecto);
$totalRows_RsProyecto = mysql_num_rows($RsProyecto);

$colname_RsReportado = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_RsReportado = $_SESSION['MM_Userid'];
}
$ide_RsReportado = "-1";
if (isset($_GET['id'])) {
  $ide_RsReportado = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsReportado = sprintf("SELECT * FROM reporteavance WHERE idempleado = %s and idsubcotizacion= %s",GetSQLValueString($colname_RsReportado, "int"),GetSQLValueString($ide_RsReportado, "int"));
$RsReportado = mysql_query($query_RsReportado, $tecnocomm) or die(mysql_error());
$row_RsReportado = mysql_fetch_assoc($RsReportado);
$totalRows_RsReportado = mysql_num_rows($RsReportado);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Banco</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Proyecto:<?php echo $row_RsProyecto['nombre']; ?>(<?php echo $row_RsProyecto['identificador2']; ?>)</h1>
<p></p>

<div id="myform">
 <form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">

<div>
<h3>Reporte de Avance</h3>
<label>
Fecha:<?php echo date("d-M-Y"); ?>
</label>

<label>
Reporte:<textarea name="reprote" cols="45" rows="15"></textarea>
</label>


</div>
<div>
<h3>Reportado Anteriormente:</h3>
<?php do { ?>
  <label>
    En la Fecha:   <?php echo $row_RsReportado['fecha']; ?><br />
    Se reporto:
  <?php echo $row_RsReportado['reporte']; ?></label>
  <?php } while ($row_RsReportado = mysql_fetch_assoc($RsReportado)); ?>

</div>
<div>
<label>
      <input type="submit" name="button" id="button" value="Aceptar" />
    </label>
  
</div>

<input type="hidden" name="idempleado" value="<?php echo $_SESSION['MM_Userid'];?>"/>
<input type="hidden" name="idsubcotizacion" value="<?php echo $row_RsProyecto['idsubcotizacion'];?>"/>
<input type="hidden" name="MM_insert" value="form1" />
 </form>

</div>

</body>
</html>
<?php
mysql_free_result($RsProyecto);

mysql_free_result($RsReportado);
?>
