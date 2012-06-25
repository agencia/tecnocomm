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
$query_rsOrden = "SELECT * FROM ordenservicio o,cliente c WHERE o.idcliente = c.idcliente ORDER BY o.estado ASC , o.fecha DESC";
$rsOrden = mysql_query($query_rsOrden, $tecnocomm) or die(mysql_error());
$row_rsOrden = mysql_fetch_assoc($rsOrden);
$totalRows_rsOrden = mysql_num_rows($rsOrden);
?>
<?php require_once('utils.php'); ?>
<link href="style2.css" rel="stylesheet" type="text/css" />


<h1>Ordenes de Servicio</h1>
<div id="submenu">
  <a href="ordenServicio.Nuevo.php" onclick="NewWindow(this.href,'Orden Servicio',800,600,'yes');return false;">Nueva Orden Servicio</a> </div>
<div id="buscar">Buscar: <input type="text" name="buscar"/></div>

<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td>Opciones</td><td>Numero</td><td>Cliente</td><td>Fecha</td><td>Descripcion</td></tr>
</thead>
<tbody>
<tr><td>Opciones</td><td><?php echo $row_rsOrden['numeroorden']; ?></td><td><?php echo $row_rsOrden['idcliente']; ?></td><td><?php echo $row_rsOrden['fecha']; ?></td><td><?php echo $row_rsOrden['descripcionreporte']; ?></td></tr>
</tbody>
</table>

</div>
<?php
mysql_free_result($rsOrden);
?>
