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

$colname_rsUsuario = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsUsuario = $_SESSION['MM_Username'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuario = sprintf("SELECT * FROM usuarios WHERE username = %s", GetSQLValueString($colname_rsUsuario, "text"));
$rsUsuario = mysql_query($query_rsUsuario, $tecnocomm) or die(mysql_error());
$row_rsUsuario = mysql_fetch_assoc($rsUsuario);
$totalRows_rsUsuario = mysql_num_rows($rsUsuario);

$colname_rsCotizacion = "-1";
if (isset($_POST['idordencompra'])) {
  $colname_rsCotizacion = $_POST['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT * FROM ordencompra o,proveedor p WHERE o.idordencompra = %s AND o.idproveedor = p.idproveedor", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);
?><?php 
require_once "mail/lib/Swift.php";
require_once "mail/lib/Swift/Connection/SMTP.php";

//print_r($_POST);

//echo $message." - ".$row_rsCotizacion['correo']." - ".$row_rsUsuario['email'];
$error = "CORREO ENVIADO";
try{
		$server  = $row_rsUsuario['servidorsmtp'];
		$pwd     =  $row_rsUsuario['passsmtp'];
		$username = $row_rsUsuario['usernamesmtp'];

		$smtp = "";	
	if($row_rsUsuario['usettlssl'] == 0){
			$smpt = new Swift_Connection_SMTP($row_rsUsuario['servidorsmtp'],$row_rsUsuario['puertosmtp']);
	}
	else{
		$smtp = new Swift_Connection_SMTP( $row_rsUsuario['servidorsmtp'], Swift_Connection_SMTP::PORT_SECURE, Swift_Connection_SMTP::ENC_TLS);
	}
	
	//	$swift =& new Swift(new Swift_Connection_NativeMail());
	$smtp->setUsername($username);
	 $smtp->setpassword($pwd);
	
	$swift =& new Swift($smtp);
 
 $file ="http://localhost/PrintOrdenCompraPDF.php?idordencompra=".$_POST['idordencompra'];
 $filename = $row_rsCotizacion['identificador'].".pdf";
 
$message =& new Swift_Message($_POST['asunto']);
$message->attach(new Swift_Message_Part(stripslashes($_POST['mensaje']),"text/html"));
$message->attach(new Swift_Message_Attachment(file_get_contents($file),$filename,"application/pdf"));


$recipients =& new Swift_RecipientList();
$recipients->addTo($row_rsCotizacion['email']);
//Use addCc()
if($_POST['concopia']){
	$recipients->addCc($row_rsUsuario['email']);
}

$correos = split(",",$_POST['cc']);
if(is_array($correos)){
	foreach($correos as $c){
			$recipients->addCc($c);
	}
}

if($swift->send($message,$recipients,$row_rsUsuario['email'] )){
	$mensaje =  "Mensaje Enviado Exitosamente";
	}else{
    $mensaje = "El Mensaje no pudo ser enviado, revise la configuracion de su cuenta...";
	}

}catch(Exception $e){
	$error = "EL CORREO NO HA PODIDO SER ENTREGADO PORFAVOR REVISE LA CONFIGURACION DE SU CUENTA, LAS DIRECCIONES DE CORREO...".$e;

}	
?>
<link href="style.css" rel="stylesheet" type="text/css" />


<table width="490" border="0" cellpadding="0" cellspacing="0" align="center" class="wrapper">
  <!--DWLayoutTable-->
  <tr>
    <td height="18" colspan="4" valign="top"><?php echo $error; ?></td>
  </tr>
  <tr>
    <td width="12" height="17"></td>
    <td width="99"></td>
    <td width="353"></td>
    <td width="26"></td>
  </tr>
  <tr>
    <td height="23"></td>
    <td valign="top">Cotizacion:</td>
    <td valign="top"><?php echo $row_rsCotizacion['identificador']; ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="26"></td>
    <td valign="top">Correo:</td>
    <td valign="top"><?php echo $row_rsCotizacion['email']; ?></td>
    <td></td>
  </tr>
  <tr>
    <td height="25"></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="25"></td>
    <td colspan="2" valign="top"><?php echo $mensaje;?></td>
    <td></td>
  </tr>
  <tr>
    <td height="17"></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
<?php
mysql_free_result($rsUsuario);

mysql_free_result($rsCotizacion);
?>