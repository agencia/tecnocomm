<?php require_once('../Connections/tecnocomm.php'); 
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
?>
<?php
$VARtipo_Recordset1 = "1,2,3";
if (isset($_GET['tipo'])) {
  $VARtipo_Recordset1 = $_GET['tipo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Recordset1 = sprintf("SELECT mn_estados.estado, mn_actividades.actividad, mn_actividades.idActividad, mn_actividades.inicio, mn_actividades.ultimo FROM mn_actividades, mn_estados WHERE mn_actividades.estado = mn_estados.idEstado AND mn_actividades.estado in (%s) ", $VARtipo_Recordset1);
$Recordset1 = mysql_query($query_Recordset1, $tecnocomm) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Documento sin título</title>
<script src="../js/jquery.js" type="text/javascript"></script>
<script src="../js/funciones.js" type="text/javascript"></script>
<link href="../style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="800" border="0" align="center" cellpadding="1" cellspacing="1" class="resaltarTabla">
  <tr>
    <td width="84">&nbsp;</td>
    <td width="595"><a href="nueva.php" class="popup"><img src="../images/Agregar.png" alt="nueva" width="24" height="24" border="0" />Nueva</a> -::- Actividades: <a href="index.php"> Todas</a> <a href="index.php?tipo=1">Pendientes</a> <a href="index.php?tipo=2"> Para revision</a> <a href="index.php?tipo=3">Realizado</a></td>
    <td width="111">&nbsp;</td>
  </tr>
  <tr class="titleTabla">
    <td>Estado</td>
    <td>Actividad</td>
    <td>Opciones</td>
  </tr>
    <?php if ($totalRows_Recordset1 > 0) { // Show if recordset not empty ?>
      <?php do { ?>
  <tr <?php echo ($a%2) ? 'bgcolor="#FBFBFF"': ""; $a++; ?>>
        <td><?php echo $row_Recordset1['estado']; ?></td>
        <td><?php echo $row_Recordset1['actividad']; ?><br />
          Creado: <?php echo $row_Recordset1['inicio']; ?> - Modificado: <?php echo $row_Recordset1['ultimo']; ?></td>
        <td><a href="eliminar.php?idactividad=<?php echo $row_Recordset1['idActividad']; ?>" class="popup"><img src="../images/eliminar.gif" width="20" height="20" border="0" /></a><a href="editar.php?idactividad=<?php echo $row_Recordset1['idActividad']; ?>" class="popup"><img src="../images/ico_edit.png" width="20" height="20" border="0" /></a></td>
  </tr>
        <?php } while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); ?>
      <?php } // Show if recordset not empty ?>
  <tr>
    <?php if ($totalRows_Recordset1 == 0) { // Show if recordset empty ?>
  <td>&nbsp;</td>
      <td>No hay actividades</td>
      <td>&nbsp;</td>
      <?php } // Show if recordset empty ?>
  </tr>
</table>

</body>
</html>
<?php
mysql_free_result($Recordset1);
?>
