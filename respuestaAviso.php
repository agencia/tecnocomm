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

$update = sprintf("update  avisos  set realizado=1 where id=%s or padre=%s",                     
                       GetSQLValueString($_GET['id'], "int"),
					   GetSQLValueString($_GET['padre'], "int"));
echo $update;
  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($update, $tecnocomm) or die(mysql_error());

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "modAviso")) {
  $insertSQL = sprintf("INSERT INTO avisos (de, para, mensaje, prioridad, padre, fecha, hora) VALUES (%s, %s, %s, %s, %s, now(), now())",
                       GetSQLValueString($_POST['de'], "int"),
                       GetSQLValueString($_POST['para'], "int"),
                       GetSQLValueString($_POST['respuesta'], "text"),
                       GetSQLValueString($_POST['prioridad'], "int"),
                       GetSQLValueString($_POST['padre'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



$colname_RsMensaje = "-1";
if (isset($_GET['padre'])) {
  $colname_RsMensaje = $_GET['padre'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsMensaje = sprintf("SELECT *,(select nombrereal FROM usuarios WHERE id=de) as dee, (select nombrereal from usuarios where id=para) as par FROM avisos WHERE padre = %s order by id DESC", GetSQLValueString($colname_RsMensaje, "int"));
$RsMensaje = mysql_query($query_RsMensaje, $tecnocomm) or die(mysql_error());
$row_RsMensaje = mysql_fetch_assoc($RsMensaje);
$totalRows_RsMensaje = mysql_num_rows($RsMensaje);

$ide_RsOriginal = "-1";
if (isset($_GET['id'])) {
  $ide_RsOriginal = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsOriginal = sprintf("SELECT *,(select nombrereal FROM usuarios WHERE id=de) as dee, (select nombrereal from usuarios where id=para) as par FROM avisos WHERE id= %s ", GetSQLValueString($ide_RsOriginal, "int"));
$RsOriginal = mysql_query($query_RsOriginal, $tecnocomm) or die(mysql_error());
$row_RsOriginal = mysql_fetch_assoc($RsOriginal);
$totalRows_RsOriginal = mysql_num_rows($RsOriginal);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nueva Respuesta</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
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
</head>

<body>
<h1>Respuesta Aviso </h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="modAviso" method="POST">
<div>
<h3>Mensaje Original </h3>
<?php echo $row_RsOriginal['mensaje']; ?>

<h3>Ultimo Mensaje </h3>
<?php if($totalRows_RsMensaje>0){

 $paranew=$row_RsMensaje['dee'];
?>


<label>De:<?php echo $row_RsMensaje['dee']; ?></label>



<label>Fecha:<?php echo $row_RsMensaje['fecha']; ?></label>

<label>Hora:<?php echo $row_RsMensaje['hora']; ?></label>
<label>Mensaje:<br />


<?php echo $row_RsMensaje['mensaje']; ?></label>
<?php }
 if($totalRows_RsMensaje==0){
 
 $paranew=$row_RsOriginal['de'];
	echo "No hay mensajes anteriores";
}
?>

</div>
<div>
<h3>Respuesta</h3>
<label>Prioridad:
<select name="prioridad">
  <option value="0">Normal</option>
  <option value="1">urgente</option>
</select>
</label>
<label>
<textarea name="respuesta" cols="50" rows="10" class="mceEditor"></textarea>
</label>

</div>

<div>
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="de" value="<?php echo $_SESSION['MM_Userid']; ?>"/>
<input type="hidden" name="para" value="<?php echo $paranew; ?>"/>
<input type="hidden" name="padre" value="<?php echo $_GET['padre']; ?>"/>
<input type="hidden" name="MM_insert" value="modAviso" />


</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsMensaje);

mysql_free_result($RsOriginal);
?>