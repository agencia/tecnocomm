<?php require_once('Connections/tecnocomm.php'); ?>
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "actualizar")) {
  $updateSQL = sprintf("UPDATE usuarios SET email=%s, servidorsmtp=%s, usernamesmtp=%s, passsmtp=%s, loginsmtp=%s, usettlssl=%s, puertosmtp=%s,firmacorreo=%s WHERE id=%s",
                       GetSQLValueString($_POST['correo'], "text"),
                       GetSQLValueString($_POST['servidor'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString(isset($_POST['requierelogin']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString(isset($_POST['usettlssl']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['puerto'], "int"),
					   GetSQLValueString($_POST['firmacorreo'], "text"),
                       GetSQLValueString($_POST['idusuario'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsUsuario = "-1";
if (isset($_GET['idusuario'])) {
  $colname_rsUsuario = $_GET['idusuario'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuario = sprintf("SELECT * FROM usuarios WHERE id = %s", GetSQLValueString($colname_rsUsuario, "int"));
$rsUsuario = mysql_query($query_rsUsuario, $tecnocomm) or die(mysql_error());
$row_rsUsuario = mysql_fetch_assoc($rsUsuario);
$totalRows_rsUsuario = mysql_num_rows($rsUsuario);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>DATOS SMTP</title>
<link href="style.css" rel="stylesheet" type="text/css" />
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
<form action="<?php echo $editFormAction; ?>" method="POST" name="actualizar" id="actualizar">
  <table align="center" class="wrapper">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Correo:</td>
      <td><input type="text" name="correo" value="<?php echo $row_rsUsuario['email']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Servidor:</td>
      <td><input type="text" name="servidor" value="<?php echo $row_rsUsuario['servidorsmtp']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Username:</td>
      <td><input type="text" name="username" value="<?php echo $row_rsUsuario['usernamesmtp']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Password:</td>
      <td><input type="text" name="password" value="<?php echo $row_rsUsuario['passsmtp']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Puerto:</td>
      <td><input type="text" name="puerto" value="<?php echo $row_rsUsuario['puertosmtp']; ?>" size="10" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Requiere Login:</td>
      <td><input <?php if (!(strcmp($row_rsUsuario['loginsmtp'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="requierelogin"checked="checked" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Usa TTL o SSL</td>
      <td><input <?php if (!(strcmp($row_rsUsuario['usettlssl'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="usettlssl" value="" /></td>
    </tr>
      <tr valign="baseline">
      <td align="right" valign="top" nowrap="nowrap">FIRMA:</td>
      <td><textarea name="firmacorreo" cols="60" rows="15" class="mceEditor"><?php echo htmlentities($row_rsUsuario['firmacorreo']);?></textarea></td>
    </tr>
    
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td align="right"><input type="submit" value="Guardar" /></td>
    </tr>
  </table>
  <input type="hidden" name="idusuario" value="<?php echo $_GET['idusuario']; ?>" />
  <input type="hidden" name="MM_insert" value="form1" />
  <input type="hidden" name="MM_update" value="actualizar" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rsUsuario);
?>
