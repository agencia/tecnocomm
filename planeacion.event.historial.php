<?php header("Content-Type: text/html; charset=iso-8859-1"); ?>
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

$colname_rsTareas = "-1";
if (isset($_GET['idtarea'])) {
  $colname_rsTareas = $_GET['idtarea'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTareas = sprintf("SELECT usuarios.username, tarea_comentarios.* FROM tarea_comentarios, usuarios WHERE idTarea = %s AND usuarios.id = tarea_comentarios.idUsuario", GetSQLValueString($colname_rsTareas, "int"));
$rsTareas = mysql_query($query_rsTareas, $tecnocomm) or die(mysql_error());
$row_rsTareas = mysql_fetch_assoc($rsTareas);
$totalRows_rsTareas = mysql_num_rows($rsTareas);

?>
<div>
  <?php if ($totalRows_rsTareas > 0) { // Show if recordset not empty ?>
  <table>
      <?php do { ?>
    <tr>
      <td width="150px"><?php echo $row_rsTareas['username']; ?></td>
      <td rowspan="2" valign="top" style="font-size:12px;"><?php echo $row_rsTareas['comentario']; ?></td>
    </tr>
    <tr>
      <td><?php echo formatDateTimeShort($row_rsTareas['fecha']); ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
      <?php } while ($row_rsTareas = mysql_fetch_assoc($rsTareas)); ?>
    </table>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_rsTareas == 0) { // Show if recordset empty ?>
  <i> No hay comentarios</i>
  <?php } // Show if recordset empty ?>
</div>
<?php
mysql_free_result($rsTareas);
?>