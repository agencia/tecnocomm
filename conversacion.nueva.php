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

//print_r($_POST['destinatarios']);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmNuevaAlerta")) {
  $insertSQL = sprintf("INSERT INTO conversacion (mensaje, remitente, prioridad, estado, fechacreado, idip, asunto) VALUES (%s, %s, %s, 0, NOW(), %s, %s)",
                       GetSQLValueString($_POST['mensaje'], "text"),
                       GetSQLValueString($_POST['remitente'], "int"),
                       GetSQLValueString($_POST['prioridad'], "int"),
					   GetSQLValueString($_POST['idip'], "int"),
					   GetSQLValueString($_POST['asunto'],"text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  $idconversacion = mysql_insert_id();  
  
  //insertar destinatarios
  if(is_array($_POST['destinatarios']))
  foreach($_POST['destinatarios'] as $destinatario => $on){
	  $insertSQL = sprintf("INSERT INTO conversacion_destinatario (idconversacion, destinatario, estado) VALUES (%s, %s, 0)",
                       GetSQLValueString($idconversacion, "int"),
                       GetSQLValueString($destinatario, "int"));

	  mysql_select_db($database_tecnocomm, $tecnocomm);
  	$Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  }



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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nueva Alerta</title>
<script src="js/jquery.js" language="javascript"> </script>
<script src="js/jqueryui.js" language="javascript"></script>
<script language="javascript"  src="js/funciones.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
<script language="javascript">
$(function(){
		   
		   	$("#selall").click(function(e){
										if( $(this).val() == "on"){
											$(".cu").attr("checked","checked");
										}else{
											$(".cu").removeAttr("checked");
										}
										});
		 	});
</script>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Nueva Alerta</h1>
<p></p>
<form action="<?php echo $editFormAction; ?>" name="frmNuevaAlerta" method="POST" id="myform">
<div>
<h3>Asunto: </h3>
<label>
(titulo de alerta)
<input type="text" name="asunto" value="" />
</label>
<h3>Ip</h3>
<label>
(es opcional) <a href="ip.buscar.php" class="popup">Buscar</a>
<input type="text" name="idip" value="<?php echo isset($_GET['idip'])?$_GET['idip']:"";?>" />
</label>

<h3>Destinatarios</h3>
<small>Si no selecciona ningun destinatario solo usted podra ver la alerta</small>
<div>
<input type="checkbox" id="selall"/> Todos <br /><br />
<fieldset>
<ul id="usersAl">
<?php
do {
	if ($row_rsUsuarios['id'] != $_SESSION['MM_Userid']) {
?>
<li>
<label>
<input type="checkbox" name="destinatarios[<?php echo $row_rsUsuarios['id']?>]" class="cu"  />
<?php echo $row_rsUsuarios['username']?>
</label>
</li>
<?php
	}
} while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios));?>
</ul>
<input type="hidden" name="destinatarios[<?php echo $_SESSION['MM_Userid']?>]" />
</fieldset>

</div>
<h3>Prioridad</h3>
<select name="prioridad">
<option value="0">Normal</option>
<option value="1">Alta</option>
</select>
<h3>Mensaje</h3>
<label>
<textarea name="mensaje">

</textarea>
</label>
</div>
<div class="botones">
<button type="submit">Enviar Alerta</button>
</div>
<input type="hidden" name="remitente" value="<?php echo $_SESSION['MM_Userid'];?>" />
<input type="hidden" name="MM_insert" value="frmNuevaAlerta" />
</form>
</body>
</html>
<?php
mysql_free_result($rsUsuarios);
?>
