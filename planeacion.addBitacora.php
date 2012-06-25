<?php
session_start();
header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addBitacoraForm")) {
  $insertSQL = sprintf("INSERT INTO tarea_bitacora (idTarea, idUsuario, mensaje, fecha) VALUES (%s, %s, %s, NOW())",
                       GetSQLValueString($_POST['idtarea'], "int"),
                       GetSQLValueString($_POST['idusuario'], "int"),
                       GetSQLValueString($_POST['comentario'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


$colname_rsTareas = "-1";
if (isset($_GET['idtarea'])) {
  $colname_rsTareas = $_GET['idtarea'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTareas = sprintf("SELECT usuarios.username, tarea_bitacora.* FROM tarea_bitacora, usuarios WHERE idTarea = %s AND usuarios.id = tarea_bitacora.idUsuario", GetSQLValueString($colname_rsTareas, "int"));
$rsTareas = mysql_query($query_rsTareas, $tecnocomm) or die(mysql_error());
$row_rsTareas = mysql_fetch_assoc($rsTareas);
$totalRows_rsTareas = mysql_num_rows($rsTareas);

$colname_Recordset1 = "-1";
if (isset($_GET['idtarea'])) {
  $colname_Recordset1 = $_GET['idtarea'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Recordset1 = sprintf("SELECT * FROM tarea_usuario WHERE idtarea = %s AND idusuario = %s", GetSQLValueString($colname_Recordset1, "int"), GetSQLValueString($_SESSION['MM_Userid'], "int"));
$Recordset1 = mysql_query($query_Recordset1, $tecnocomm) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Bitacora</title>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jqueryui.js"></script>
<script language="javascript" type="text/javascript" src="js/planeacion.junta2.js"></script>
<script language="javascript" type="text/ecmascript" src="js/funciones.js"></script>
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css" />
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
window.moveTo(0,0);
window.resizeTo(900,650); 
</script>
</head>

<body>
<form name="addBitacoraForm" method="POST" action="<?php echo $editFormAction; ?>" id="formbitacora">
<h1>Bitacora</h1>
<table width="100%" >
<tr>
<td valign="top" width="50%">
  <?php if ($totalRows_rsTareas > 0) { // Show if recordset not empty ?>
  <table width="100%">
      <?php do { ?>
    <tr class="fdos">
      <td width="80px"><?php echo $row_rsTareas['username']; ?></td>
      <td rowspan="2" valign="top" style="font-size:12px;"><?php echo $row_rsTareas['mensaje']; ?></td>
    </tr>
    <tr>
      <td class="fdos"><?php echo formatDateTimeShort($row_rsTareas['fecha']); ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
      <?php } while ($row_rsTareas = mysql_fetch_assoc($rsTareas)); ?>
    </table>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_rsTareas == 0) { // Show if recordset empty ?>
  <i> No hay registros</i>
  <?php } // Show if recordset empty ?>
<br />
<?php if ($totalRows_Recordset1 > 0) { // Show if recordset not empty ?>
<input type="submit" value="Guardar" />
  <?php } // Show if recordset not empty ?>
</td>
<td valign="top" width="50%" style="font-size:14px;">

<a href="conversacion.nueva.php" class="popup">Nueva Alerta</a>
<br />

<label>Commentario:</label>
<br />
<?php if ($totalRows_Recordset1 > 0) { // Show if recordset not empty ?>
  <textarea name="comentario" style="width:400px;height:300px"></textarea>
<input type="hidden" name="idtarea" value="<?php echo $_GET['idtarea']?>">
<input type="hidden" name="idusuario" value="<?php echo $_SESSION['MM_Userid'];?>">
  <?php } // Show if recordset not empty ?>
<?php if ($totalRows_Recordset1 == 0) { // Show if recordset empty ?>
  No Puede Escribir Bitacora...
  <?php } // Show if recordset empty ?></td>
</tr>
</table>
<input type="hidden" name="MM_insert" value="addBitacoraForm" />
</form>
</body>
</html>
<?php
mysql_free_result($rsTareas);

mysql_free_result($Recordset1);
