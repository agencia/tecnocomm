<?php require_once('Connections/tecnocomm.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "modAviso")) {
  $updateSQL = sprintf("UPDATE avisos SET realizado=%s, fecharealizado=now(),respuesta=%s WHERE id=%s",
                       GetSQLValueString($_POST['realizado'], "int"),
					   GetSQLValueString($_POST['respuesta'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_RsMensaje = "-1";
if (isset($_GET['id'])) {
  $colname_RsMensaje = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsMensaje = sprintf("SELECT *,(select nombrereal from usuarios where id=de) as dee, (select nombrereal from usuarios where id=para) as par FROM avisos WHERE id = %s", $colname_RsMensaje);
$RsMensaje = mysql_query($query_RsMensaje, $tecnocomm) or die(mysql_error());
$row_RsMensaje = mysql_fetch_assoc($RsMensaje);
$totalRows_RsMensaje = mysql_num_rows($RsMensaje);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Banco</title>
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
<h1>Modificar Aviso </h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="modAviso" method="POST">
<div>
<h3>Datos de Aviso </h3>
<label>De:<?php echo $row_RsMensaje['dee']; ?></label>

<label>Para:<?php echo $row_RsMensaje['par']; ?></label>

<label>Fecha:<?php echo $row_RsMensaje['fecha']; ?></label>

<label>Hora:<?php echo $row_RsMensaje['hora']; ?></label>


<label>Realizado:
<select name="realizado">
  <option value="0" <?php if (!(strcmp(0, $row_RsMensaje['realizado']))) {echo "selected=\"selected\"";} ?>>No</option>
  <option value="1" <?php if (!(strcmp(1, $row_RsMensaje['realizado']))) {echo "selected=\"selected\"";} ?>>Si</option>
</select>
</label><label>Mensaje:<br />


<?php echo $row_RsMensaje['mensaje']; ?></label>
</div>
<div>
<h3>Respuesta</h3>
<label>
<textarea name="respuesta" cols="50" rows="10" class="mceEditor"></textarea>
</label>

</div>

<div>
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>"/>
<input type="hidden" name="MM_update" value="modAviso">

</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsMensaje);
?>