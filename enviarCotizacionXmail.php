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

$MM_restrictGoTo = "index.php";
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

$colname_rsCotizacion = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsCotizacion = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT * FROM ordencompra o,proveedor p WHERE o.idordencompra = %s AND o.idproveedor = p.idproveedor", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);

$colname_rsAdmin = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsAdmin = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAdmin = sprintf("SELECT * FROM usuarios WHERE id = %s", GetSQLValueString($colname_rsAdmin, "int"));
$rsAdmin = mysql_query($query_rsAdmin, $tecnocomm) or die(mysql_error());
$row_rsAdmin = mysql_fetch_assoc($rsAdmin);
$totalRows_rsAdmin = mysql_num_rows($rsAdmin);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Enviar Cotizacion</title>
<script language="javascript" type="text/javascript" src="js/codigo.js"></script>
<script language="javascript" type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
// General options
mode : "textareas",
theme : "advanced",
editor_selector : "mceEditor",
editor_deselector : "mceNoEditor",
plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
 
// Theme options
theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
theme_advanced_toolbar_location : "top",
theme_advanced_toolbar_align : "left",
theme_advanced_statusbar_location : "bottom",
theme_advanced_resizing : true,

});
</script>

<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="contenido">
<form name="enviarmail" id="enviarmail" method="post" action="enviarordencorreo.php">
<table width="841" border="0" cellpadding="0" cellspacing="0" class="wrapper resaltarTabla" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="28" colspan="12" valign="top" class="titulos">ENVIAR COTIZACION</td>
  </tr>
  <tr>
    <td width="28" height="4"></td>
    <td width="36"></td>
    <td width="42"></td>
    <td width="40"></td>
    <td width="238"></td>
    <td width="32"></td>
    <td width="104"></td>
    <td width="162"></td>
    <td width="33"></td>
    <td width="54"></td>
    <td width="45"></td>
    <td width="27"></td>
  </tr>
  
  <tr>
    <td height="20"></td>
    <td colspan="3" valign="top">COTIZACION:</td>
    <td colspan="3" valign="top"><?php echo $row_rsCotizacion['identificador']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="6"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td colspan="4" valign="top" class="realte">PARA:</td>
    <td></td>
    <td colspan="5" valign="top" class="realte">DE:</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="6"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td colspan="4" valign="top"><?php echo $row_rsCotizacion['contacto']; ?></td>
    <td></td>
    <td colspan="5" valign="top"><?php echo $row_rsAdmin['nombrereal']; ?></td>
    <td>&nbsp;</td>
  </tr>
  
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td colspan="4" valign="top"><?php echo $row_rsCotizacion['email']; ?></td>
    <td></td>
    <td colspan="5" valign="top"><?php echo $row_rsAdmin['email']; ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td valign="top">CC:</td>
    <td colspan="7" valign="top"><input name="cc" type="text" id="cc" size="45" title="Separados por Comas (,)" /> 
      Separados por comas</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="3"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="22"></td>
    <td colspan="2" valign="top">ASUNTO:</td>
    <td colspan="4" valign="top"><label>
      <input name="asunto" type="text" id="asunto" value="Cotizacion: <?php echo htmlentities($row_rsCotizacion['identificador']); ?>" size="45"/>
    </label></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  <tr>
    <td height="21"></td>
    <td colspan="3" valign="top">MENSAJE:</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="300"></td>
    <td colspan="10" valign="top"><label>
      <textarea name="mensaje" id="mensaje" cols="90" rows="17" class="mceEditor"><?php echo $row_rsAdmin['firmacorreo']; ?></textarea>
    </label></td>
    <td></td>
  </tr>
  <tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="20"></td>
    <td colspan="9" valign="top">NOTA: LA COTIZACION SE ADJUNTARA AL MENSAJE AUTOMAITCAMENTE</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="14"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="22"></td>
    <td colspan="5" valign="top"><label>
      <input name="concopia" type="checkbox" id="concopia" checked="checked" />
    ENVIARME UNA COPIA A MI CORREO</label></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="4"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="21"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3" align="right" valign="top"><input type="submit" value="Enviar Mail"/></td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="idordencompra" value="<?php echo $_GET['idordencompra'];?>" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($rsCotizacion);

mysql_free_result($rsAdmin);
?>
