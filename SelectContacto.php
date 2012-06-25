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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE subcotizacion SET contacto=%s WHERE idsubcotizacion=%s",
                       GetSQLValueString($_POST['select'], "int"),
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$ide_RsContacto = "-1";
if (isset($_GET['idcliente'])) {
  $ide_RsContacto = $_GET['idcliente'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsContacto = sprintf("SELECT * FROM contactoclientes WHERE idcliente=%s", GetSQLValueString($ide_RsContacto, "int"));
$RsContacto = mysql_query($query_RsContacto, $tecnocomm) or die(mysql_error());
$row_RsContacto = mysql_fetch_assoc($RsContacto);
$totalRows_RsContacto = mysql_num_rows($RsContacto);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body><form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
<table width="300" border="0" align="center" class="wrapper">
  <tr>
    <td colspan="6" align="center" class="titulos">SELECCIONAR CONTACTO</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="6" align="center"><label>CONTACTOS
        <select name="select" id="select" class="form">
          <?php
do {  
?>
          <option value="<?php echo $row_RsContacto['idcontacto']?>"><?php echo $row_RsContacto['nombre']?></option>
          <?php
} while ($row_RsContacto = mysql_fetch_assoc($RsContacto));
  $rows = mysql_num_rows($RsContacto);
  if($rows > 0) {
      mysql_data_seek($RsContacto, 0);
	  $row_RsContacto = mysql_fetch_assoc($RsContacto);
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
    <td>
      <label>
        <input type="submit" name="button" id="button" value="ACEPTAR" />
        </label>
    
    </td>
    <td>&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="idcontacto" value="<?php echo $_GET['idcontacto'];?>"/>
<input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion'];?>"/>
<input type="hidden" name="MM_update" value="form1" />
</form>
</body>
</html>
<?php
mysql_free_result($RsContacto);
?>
