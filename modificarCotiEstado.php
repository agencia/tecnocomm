<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('Connections/tecnocomm.php'); ?>
<?php
session_start();
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
  $updateSQL = sprintf("UPDATE subcotizacion SET estado=%s WHERE idsubcotizacion=%s",
                       GetSQLValueString($_POST['estado'], "int"),
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




$colname_Rs = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_Rs = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Rs = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_Rs, "int"));
$Rs = mysql_query($query_Rs, $tecnocomm) or die(mysql_error());
$row_Rs = mysql_fetch_assoc($Rs);
$totalRows_Rs = mysql_num_rows($Rs);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
.Estilo2 {color: #FFFFFF}
-->
</style>
</head>

<body>
<form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
<table width="350" border="0" align="center" class="wrapper">
  <tr class="titulos">
    <td colspan="3" align="center"><span class="Estilo2">MODIFICAR ESTADO DE COTIZACION</span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center"><p>Selecciona el estado de la cotizacion.</p>
      <p>COTIZACION:<?php echo $row_Rs['identificador2']; ?></p>
      <p>
        <select name="estado" id="estado">
          <option value="1" <?php if (!(strcmp(1, $row_Rs['estado']))) {echo "selected=\"selected\"";} ?>>ABIERTA</option>
          <option value="2" <?php if (!(strcmp(2, $row_Rs['estado']))) {echo "selected=\"selected\"";} ?>>ENVIADA</option>
          <option value="3" <?php if (!(strcmp(3, $row_Rs['estado']))) {echo "selected=\"selected\"";} ?>>AUTORIZADA</option>
          <option value="4" <?php if (!(strcmp(4, $row_Rs['estado']))) {echo "selected=\"selected\"";} ?>>CONCILIADA</option>
          <option value="5" <?php if (!(strcmp(5, $row_Rs['estado']))) {echo "selected=\"selected\"";} ?>>PAGADA</option>
          <option value="6" <?php if (!(strcmp(6, $row_Rs['estado']))) {echo "selected=\"selected\"";} ?>>CONC ABIERTA</option>
          <option value="7" <?php if (!(strcmp(7, $row_Rs['estado']))) {echo "selected=\"selected\"";} ?>>CONC ENVIADA</option>
          <option value="8" <?php if (!(strcmp(8, $row_Rs['estado']))) {echo "selected=\"selected\"";} ?>>CONC AUTORIZADA</option>
          <option value="9" <?php if (!(strcmp(9, $row_Rs['estado']))) {echo "selected=\"selected\"";} ?>>CONC PAGADA</option>
        </select>
      </p>
      <p>&nbsp;</p></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">
      <label>
        <input type="submit" name="button" id="button" value="Aceptar" />
        </label>    </td>
    <td>&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="id" value="<?php echo $row_Rs['idsubcotizacion']; ?>"/>
<input type="hidden" name="MM_update" value="form1" />
 </form>
</body>
</html>
<?php
mysql_free_result($Rs);
?>
