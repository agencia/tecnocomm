<?php require_once('Connections/tecnocomm.php'); ?>
<?php
session_start();
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

if ((isset($_POST['id'])) && ($_POST['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM usuarios WHERE id=%s",
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($deleteSQL, $tecnocomm) or die(mysql_error());

  $deleteGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$ide_RsUsr = "-1";
if (isset($_GET['id'])) {
  $ide_RsUsr = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUsr = sprintf("SELECT *,(select nombre from nombres_accesos where nombres_accesos.id=usuarios.responsabilidad) as nivel  FROM usuarios WHERE id=%s", $ide_RsUsr);
$RsUsr = mysql_query($query_RsUsr, $tecnocomm) or die(mysql_error());
$row_RsUsr = mysql_fetch_assoc($RsUsr);
$totalRows_RsUsr = mysql_num_rows($RsUsr);

if ((isset($_POST['id'])) && ($_POST['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM medicosygrupos WHERE idmedico=%s and idgrupo=%s",
                       GetSQLValueString($_POST['idmedico'], "int"),
					   GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_papsystem, $papsystem);
  $Result1 = mysql_query($deleteSQL, $papsystem) or die(mysql_error());
  
 require_once('lib/eventos.php');
	$evt = new evento(20,$_SESSION['MM_Userid'],"Usuario eliminado:".$row_RsUsr['username']);
	$evt->registrar();

  $deleteGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>

<body>
<form name="registrar" method="post">
<table width="400" border="0" align="center" cellspacing="3" class="wrapper">
  <tr>
    <td colspan="3" align="center" class="titulos">Eliminar  Clasificacion </td>
  </tr>
  <tr>
    <td width="120">&nbsp;</td>
    <td width="114">&nbsp;</td>
    <td width="148">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3">Confirmas que deseas eliminar al Usuario:<span class="Estilo1"><?php echo $row_RsUsr['username']; ?></span> con nivel de acceso:<span class="Estilo1"><?php echo $row_RsUsr['nivel']; ?></span> ?</td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td><label>
      <input type="submit" name="Submit" value="Aceptar" />
    </label></td>
    <td><label>
      <input type="submit" name="Submit2" value="Cancelar"  onclick="window.close();"/>
    </label></td>
  </tr>
</table>
<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>

</form>
</body>
</html>
<?php
mysql_free_result($RsUsr);
?>
