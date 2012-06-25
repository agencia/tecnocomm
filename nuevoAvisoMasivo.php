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
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUsuarios = "SELECT * FROM usuarios";
$RsUsuarios = mysql_query($query_RsUsuarios, $tecnocomm) or die(mysql_error());
$row_RsUsuarios = mysql_fetch_assoc($RsUsuarios);
$totalRows_RsUsuarios = mysql_num_rows($RsUsuarios);

$colname_RsDe = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_RsDe = (get_magic_quotes_gpc()) ? $_SESSION['MM_Userid'] : addslashes($_SESSION['MM_Userid']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsDe = sprintf("SELECT * FROM usuarios WHERE id = %s", $colname_RsDe);
$RsDe = mysql_query($query_RsDe, $tecnocomm) or die(mysql_error());
$row_RsDe = mysql_fetch_assoc($RsDe);
$totalRows_RsDe = mysql_num_rows($RsDe);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) 
{
	$valor = join(',',$_POST['empleado']);
	
	$pc = split(',',$valor);
	
	$count = count($pc);
	
  foreach ($pc as $id)
  {
  $updateSQL = sprintf("INSERT INTO avisos (de, para, fecha, hora, prioridad, mensaje) VALUES(%s, %s, now(), now(), %s, %s)",
                       GetSQLValueString($_POST['de'], "int"),
					    GetSQLValueString($id, "int"),
                        GetSQLValueString($_POST['prioridad'], "int"),
						 GetSQLValueString($_POST['mensaje'], "text"));
 
  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
}
  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript">
function setCheckboxes(the_form, do_check)
{
    var elts      = (typeof(document.forms[the_form].elements['empleado[]']) != 'undefined')
          ? document.forms[the_form].elements['empleado[]']
          : document.forms[the_form].elements['selected_fld[]'];
    var elts_cnt  = (typeof(elts.length) != 'undefined')
                  ? elts.length
                  : 0;

    if (elts_cnt) {
        for (var i = 0; i < elts_cnt; i++) {
            elts[i].checked = do_check;
        } // end for
    } else {
        elts.checked        = do_check;
    } // end if... else

    return true;
} // end of the 'setCheckboxes()' function

function setCheckboxes1(the_form, do_check)
{
    var elts      = (typeof(document.forms[the_form].elements['empleado[]']) != 'undefined')
          ? document.forms[the_form].elements['empleado[]']
          : document.forms[the_form].elements['selected_fld[]'];
    var elts_cnt  = (typeof(elts.length) != 'undefined')
                  ? elts.length
                  : 0;

    if (elts_cnt) {
        for (var i = 0; i < elts_cnt; i++) {
            elts[i].checked = do_check;
        } // end for
    } else {
        elts.checked        = do_check;
    } // end if... else

    return true;
} // end of the 'setCheckboxes()' function
</script>
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
<h1>Aviso Masivo </h1>


<div id="myform">

<form id="form1" name="form1" method="post">
<div>
<h3>Seleccionar Personal </h3>
<label>De:
<?php echo $row_RsDe['nombrereal']; ?>
</label>

   <label>Prioridad:
<select name="prioridad">
  <option value="0">Normal</option>
  <option value="1">urgente</option>
</select>
</label>
     <label>Opciones:
	 <label>
<input type="button" name="button" id="button" value="Marcar Todo " onclick="setCheckboxes('form1', true); return false;"  /><br />
</label>
<label>
 <input type="button" name="button2" id="button2" value="Desmarcar Todo" onclick="setCheckboxes('form1', false); return true;" />
 </label>
</label>
      
      <?php do { ?>
	  <label>
        <input type="checkbox" name="empleado[]" id="empleado[]" value="<?php echo $row_RsUsuarios['id']; ?>" />
        <?php echo $row_RsUsuarios['nombrereal']; ?>
		</label>
        <?php } while ($row_RsUsuarios = mysql_fetch_assoc($RsUsuarios)); ?>

     


</div>
 
<div>
<h3>Mensaje </h3>
<textarea name="mensaje" cols="50" rows="10" class="mceEditor"></textarea>
  <input type="submit" name="Submit" value="Aceptar" />
<input type="hidden" name="de" value="<?php echo $_SESSION['MM_Userid'];?>"/>
<input type="hidden" name="MM_update" value="form1">

</div>
</form>
</div>
  
  
</body>
</html>
<?php
mysql_free_result($RsUsuarios);

mysql_free_result($RsDe);
?>