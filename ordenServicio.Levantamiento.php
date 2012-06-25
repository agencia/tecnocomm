<?php require_once('Connections/tecnocomm.php'); ?>
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
$query_rsLevantamiento = "SELECT * FROM levantamiento ORDER BY titulo ASC";
$rsLevantamiento = mysql_query($query_rsLevantamiento, $tecnocomm) or die(mysql_error());
$row_rsLevantamiento = mysql_fetch_assoc($rsLevantamiento);
$totalRows_rsLevantamiento = mysql_num_rows($rsLevantamiento);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Documento sin título</title>
</head>

<body>
<h1>Levantamiento</h1>
<p>Administra los diferentes tipos de levantamiento...</p>
<div id="submenu">
  <a href="ordenServicio.Levantamiento.Nuevo.php" onclick="NewWindow(this.href,'Orden Servicio',800,600,'yes');return false;">Nuevo Levantamiento</a> </div>
<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td>Opciones</td><td>Titulo</td><td>Descripcion</td></tr>
</thead>
<tbody>
<tr>
  <td>&nbsp;</td><td><?php echo $row_rsLevantamiento['titulo']; ?></td><td><?php echo substr($row_rsLevantamiento['descripcion'],0,50)."..."; ?></td></tr>
</tbody>
</table>

</div>

</body>
</html>
<?php
mysql_free_result($rsLevantamiento);
?>
