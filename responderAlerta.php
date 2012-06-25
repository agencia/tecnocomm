<?php require_once('Connections/tecnocomm.php'); ?><?php
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
?><?php
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "responderAlerta")) {
  $insertSQL = sprintf("INSERT INTO msjmensajes (idconversacion, idusuario, mensaje, estado, fecha, prioridad) VALUES (%s, %s, %s, 0,NOW(),%s )",
                       GetSQLValueString($_POST['idconversacion'], "int"),
                       GetSQLValueString($_POST['de'], "int"),
                       GetSQLValueString($_POST['mensaje'], "text"),
					   GetSQLValueString($_POST['prioridad'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



$colname_RsDe = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_RsDe = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsDe = sprintf("SELECT * FROM usuarios WHERE id = %s", GetSQLValueString($colname_RsDe, "int"));
$RsDe = mysql_query($query_RsDe, $tecnocomm) or die(mysql_error());
$row_RsDe = mysql_fetch_assoc($RsDe);
$totalRows_RsDe = mysql_num_rows($RsDe);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Responder Alerta</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
<link href="css/token-input.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Nuevo Aviso </h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="responderAlerta" method="POST">
<div>
<h3> Aviso </h3>
<label>De:

<?php echo $row_RsDe['nombrereal']; ?></label>


</label>
<label>Prioridad:
<select name="prioridad">
  <option value="0">Normal</option>
  <option value="1">urgente</option>
</select>
</label>

<label>Mensaje:
<textarea name="mensaje" cols="50" rows="10" class="mceEditor"></textarea>
</label>

<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="idconversacion" value="<?php echo $_GET['idconversacion'];?>" />
<input type="hidden" name="de" value="<?php echo $row_RsDe['id']; ?>"/>
<input type="hidden" name="MM_insert" value="responderAlerta" />
</form>

</div>
</body>
</html>
<?php
mysql_free_result($RsDe);
?>
