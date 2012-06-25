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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "registrar")) {
  $insertSQL = sprintf("INSERT INTO autorizacion (idlink, nivel) VALUES (%s, %s)",
                       GetSQLValueString($_POST['select'], "int"),
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  require_once('lib/eventos.php');
	$evt = new evento(5,$_SESSION['MM_Userid'],"Permiso asignado al nivel:".$_POST['select']);
	$evt->registrar();

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Liks = "select * from link ";
$Liks = mysql_query($query_Liks, $tecnocomm) or die(mysql_error());
$row_Liks = mysql_fetch_assoc($Liks);
$totalRows_Liks = mysql_num_rows($Liks);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />

<style type="text/css">
<!--
.Estilo1 {
	color: #455678;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" name="registrar" method="POST">
<table width="300" border="0" align="center" class="wrapper">
  <tr>
    <td colspan="4" align="center" class="titulos">Agregar Permiso </td>
  </tr>
  <tr>
    <td width="27">&nbsp;</td>
    <td width="59">&nbsp;</td>
    <td width="118">&nbsp;</td>
    <td width="28">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" align="center"><label><span class="Estilo1">Acceso a Conceder:</span>
        <select name="select" class="form">
          <option value="-1">Seleccionar</option>
          <?php
do {  
?>
          <option value="<?php echo $row_Liks['id']?>"><?php echo $row_Liks['nombre']?></option>
          <?php
} while ($row_Liks = mysql_fetch_assoc($Liks));
  $rows = mysql_num_rows($Liks);
  if($rows > 0) {
      mysql_data_seek($Liks, 0);
	  $row_Liks = mysql_fetch_assoc($Liks);
  }
?>
      </select>
    </label></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><label>
      <input type="submit" name="Submit" value="Aceptar" />
    </label></td>
    <td>&nbsp;</td>
  </tr>
</table>
<input name="id" type="hidden" value="<?php echo $_GET['id'];?>"/>
<input type="hidden" name="MM_insert" value="registrar">
</form>
</body>
</html>
<?php
mysql_free_result($Liks);
?>
