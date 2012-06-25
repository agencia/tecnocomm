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

$colname_rsPartidas = "-1";
if (isset($_GET['idlevantamientoip'])) {
  $colname_rsPartidas = $_GET['idlevantamientoip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT ld.*, a.nombre FROM levantamientoipdetalle ld, articulo a WHERE idlevantamientoip = %s AND a.idarticulo = ld.idarticulo", GetSQLValueString($colname_rsPartidas, "int"));
$rsPartidas = mysql_query($query_rsPartidas, $tecnocomm) or die(mysql_error());
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);
$totalRows_rsPartidas = mysql_num_rows($rsPartidas);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Levantamiento Nuevo</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Nuevo Levantamiento</h1>
<div id="submenu">
<ul>
<li>Agregar Partida</li>
</ul>
</div>
<div id="distabla">
<table width="100%" cellpadding="1" cellspacing="0">
<thead>
<tr><td>Opciones</td><td>Partida</td><td>Descripcion</td><td>Cantidad</td></tr>
</thead>
<tbody>
  <?php do { ?>
    <tr><td></td><td><?php echo ++$i;?></td><td><?php echo $row_rsPartidas['nombre']; ?></td><td><?php echo $row_rsPartidas['cantidad']; ?></td></tr>
    <?php } while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas)); ?>
</tbody>

</table>
</div>
</body>
</html>
<?php
mysql_free_result($rsPartidas);
?>
