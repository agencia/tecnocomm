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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "myform")) {
  $insertSQL = sprintf("INSERT INTO proyecto_herramienta (idherramienta, idip, responsable, presto, fechasalida) VALUES (%s, %s, %s, %s, NOW())",
                       GetSQLValueString($_POST['idherramienta'], "int"),
                       GetSQLValueString($_POST['idip'], "int"),
                       GetSQLValueString($_POST['responsable'], "int"),
					   GetSQLValueString($_SESSION['MM_Userid'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  //poner herramienta como prestada.
  
  $updateSQL = sprintf('UPDATE herramienta SET prestada = 1 WHERE id = %s',GetSQLValueString($_POST['idherramienta'],'int'));
  mysql_select_db($database_tecnocomm,$tecnocomm);
  $result = mysql_query($updateSQL,$tecnocomm) or die(mysql_error());
  

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuarios = "SELECT * FROM usuarios ORDER BY username ASC";
$rsUsuarios = mysql_query($query_rsUsuarios, $tecnocomm) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

$colname_rsHerramienta = "-1";
if (isset($_GET['idh'])) {
  $colname_rsHerramienta = $_GET['idh'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsHerramienta = sprintf("SELECT * FROM herramienta WHERE id = %s", GetSQLValueString($colname_rsHerramienta, "int"));
$rsHerramienta = mysql_query($query_rsHerramienta, $tecnocomm) or die(mysql_error());
$row_rsHerramienta = mysql_fetch_assoc($rsHerramienta);
$totalRows_rsHerramienta = mysql_num_rows($rsHerramienta);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Prestamo De Herramienta</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Prestamo De Herramienta</h1>
<form action="<?php echo $editFormAction; ?>" name="myform" method="POST" id="myform">

<div>
<h3>Seleccione Usuario</h3>
<select name="responsable">
  <?php
do {  
?>
  <option value="<?php echo $row_rsUsuarios['id']?>"><?php echo $row_rsUsuarios['username']?></option>
  <?php
} while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios));
  $rows = mysql_num_rows($rsUsuarios);
  if($rows > 0) {
      mysql_data_seek($rsUsuarios, 0);
	  $row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
  }
?>
</select>
<input type="submit" value="Aceptar" />
</div>

<input type="hidden" name="idip" value="<?php echo $_GET['idip'];?>"  />
<input type="hidden" name="idherramienta" value="<?php echo $_GET['idh']?>" />
<input type="hidden" name="MM_insert" value="myform" />
</form>
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsHerramienta);
?>
