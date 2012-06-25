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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmNuevaAlerta")) {
  $insertSQL = sprintf("INSERT INTO conversacion_mensaje (idconversacion, mensaje, remitente, fecha) VALUES (%s, %s, %s, NOW())",
                       GetSQLValueString($_POST['idconversacion'], "int"),
                       GetSQLValueString($_POST['mensaje'], "text"),
					   GetSQLValueString($_SESSION['MM_Userid'],"int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  	$updateSQL = sprintf("UPDATE conversacion_destinatario SET estado = 0 WHERE idconversacion = %s AND destinatario != %s",
						 GetSQLValueString($_POST['idconversacion'], "int"),
						 GetSQLValueString($_SESSION['MM_Userid'], "int"));
	 mysql_select_db($database_tecnocomm, $tecnocomm);
  	$Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
  	
  //insertar destinatarios
  if(is_array($_POST['destinatarios']))
  foreach($_POST['destinatarios'] as $destinatario => $on){
	  $insertSQL = sprintf("INSERT INTO conversacion_destinatario (idconversacion, destinatario, estado) VALUES (%s, %s, 0)",
                       GetSQLValueString($_POST['idconversacion'], "int"),
                       GetSQLValueString($destinatario, "int"));

	  mysql_select_db($database_tecnocomm, $tecnocomm);
  	$Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  }

if(isset($_POST['crRealizada'])){
	//cambiar valor a conversacion
	$updateSQL = sprintf("UPDATE conversacion SET estado = 1 WHERE idconversacion = %s",
						 GetSQLValueString($_POST['idconversacion'], "int"));
	 mysql_select_db($database_tecnocomm, $tecnocomm);
  	$Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
}

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuarios = sprintf("SELECT * FROM usuarios WHERE id not in (SELECT destinatario FROM conversacion_destinatario WHERE idconversacion = %s) ORDER BY username ASC", GetSQLValueString($_GET['idconversacion'], "int"));
$rsUsuarios = mysql_query($query_rsUsuarios, $tecnocomm) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

$idconver_UsuarioEnConver = "-1";
if (isset($_GET['idconversacion'])) {
  $idconver_UsuarioEnConver = $_GET['idconversacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_UsuarioEnConver = sprintf("SELECT * FROM usuarios WHERE usuarios.id in (SELECT destinatario FROM conversacion_destinatario WHERE idconversacion = %s)", GetSQLValueString($idconver_UsuarioEnConver, "int"));
$UsuarioEnConver = mysql_query($query_UsuarioEnConver, $tecnocomm) or die(mysql_error());
$row_UsuarioEnConver = mysql_fetch_assoc($UsuarioEnConver);
$totalRows_UsuarioEnConver = mysql_num_rows($UsuarioEnConver);

$colname_ConvAnteriores = "-1";
if (isset($_GET['idconversacion'])) {
  $colname_ConvAnteriores = $_GET['idconversacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_ConvAnteriores = sprintf("(SELECT mensaje, fecha FROM conversacion_mensaje WHERE idconversacion = %s) UNION (SELECT mensaje, fechacreado as fecha FROM conversacion WHERE idconversacion = %s) ORDER BY fecha DESC", GetSQLValueString($colname_ConvAnteriores, "int"), GetSQLValueString($colname_ConvAnteriores, "int"));
$ConvAnteriores = mysql_query($query_ConvAnteriores, $tecnocomm) or die(mysql_error());
$row_ConvAnteriores = mysql_fetch_assoc($ConvAnteriores);
$totalRows_ConvAnteriores = mysql_num_rows($ConvAnteriores);

$colname_ConvAnteriores2 = "-1";
if (isset($_GET['idconversacion'])) {
  $colname_ConvAnteriores2 = $_GET['idconversacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_ConvAnteriores2 = sprintf("(SELECT mensaje, fecha FROM conversacion_mensaje WHERE idconversacion = %s) UNION (SELECT mensaje, fechacreado as fecha FROM conversacion WHERE idconversacion = %s) ORDER BY fecha DESC", GetSQLValueString($colname_ConvAnteriores2, "int"), GetSQLValueString($colname_ConvAnteriores2, "int"));
$ConvAnteriores2 = mysql_query($query_ConvAnteriores2, $tecnocomm) or die(mysql_error());
$row_ConvAnteriores2 = mysql_fetch_assoc($ConvAnteriores2);
$totalRows_ConvAnteriores2 = mysql_num_rows($ConvAnteriores2);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Responder Alerta</title>
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
<h1>Responder Alerta</h1>

<p></p>
<form action="<?php echo $editFormAction; ?>" name="frmNuevaAlerta" method="POST" id="myform">

<div>
<div>
<h3>Respuesta</h3>
<label>
<textarea name="mensaje"><?php if ($totalRows_UsuarioEnConver == 1) { ?>
<?php echo $row_ConvAnteriores2['mensaje']; ?><?php }?>
</textarea>
</label>

<label>
<input type="checkbox" name="crRealizada" />
Marcar como realizado
</label>
</div>
<div class="botones">
<button type="submit">Enviar </button>
</div>

</div>

<div>
<h3>Agregar Destinatarios</h3>
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
</fieldset>
</div></div>
<?php if ($totalRows_UsuarioEnConver == 1) { ?>
<div>
<h3>Respuestas Anteriores</h3>
<?php do { ?>
  <div class="altAsunto"><?php echo $row_ConvAnteriores['fecha']; ?></div>
  <br />
  <div><?php echo $row_ConvAnteriores['mensaje']; ?></div>
  <br />
  <br />
  <?php } while ($row_ConvAnteriores = mysql_fetch_assoc($ConvAnteriores)); ?>
</div>
<?php } ?>
<input type="hidden" name="MM_insert" value="frmResponder" />
<input type="hidden" name="idconversacion" value="<?php echo $_GET['idconversacion']?>" />
<input type="hidden" name="MM_insert" value="frmNuevaAlerta" />
</form>
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($UsuarioEnConver);

mysql_free_result($ConvAnteriores);
?>
