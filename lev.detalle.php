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

$colname_rsLevDetalle = "-1";
if (isset($_GET['idlevantamiento'])) {
  $colname_rsLevDetalle = $_GET['idlevantamiento'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevDetalle = sprintf("SELECT l.*,a.nombre FROM levantamientoipdetalle l LEFT JOIN articulo a ON l.idarticulo = a.idarticulo WHERE idlevantamientoip = %s", GetSQLValueString($colname_rsLevDetalle, "int"));
$rsLevDetalle = mysql_query($query_rsLevDetalle, $tecnocomm) or die(mysql_error());
$row_rsLevDetalle = mysql_fetch_assoc($rsLevDetalle);
$totalRows_rsLevDetalle = mysql_num_rows($rsLevDetalle);

$k=1;
do{
	$partidas[$row_rsLevDetalle['iddetallelevantamientoip']]=$k;
	$k++;
}while($row_rsLevDetalle = mysql_fetch_assoc($rsLevDetalle));
@mysql_data_seek($rsLevDetalle, 0);
$row_rsLevDetalle = mysql_fetch_assoc($rsLevDetalle);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Levantamiento</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js" language="javascript"> </script>
<script language="javascript"  src="js/funciones.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
</head>
<body>
<h1>Levantamiento</h1>
<?php 
include("ip.encabezado.php");
?>
<div id="submenu">
<ul>
<li><a href="lev.editar.php?idlevantamiento=<?php echo $_GET['idlevantamiento'];?>" class="popup">Editar Levantamiento</a></li>
<li><a href="lev.detalle.add.php?idlevantamiento=<?php echo $_GET['idlevantamiento'];?>" class="popup">Agregar Partida</a></li>
</ul>
</div>
<div id="distabla">
<table width="100%" cellpadding="2" cellspacing="0">
<thead>
<tr>
<td>Partida</td>
<td>Descripcion</td>
<td>Cantidad</td>
<td>Opciones</td>
</tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td><?php echo $partidas[$row_rsLevDetalle['iddetallelevantamientoip']];?></td>
      <td><?php echo $row_rsLevDetalle['nombre']; ?></td>
      <td><?php echo $row_rsLevDetalle['cantidad']; ?></td>
      <td><a href="editar.levantamiento.articulo?iddetallelevantamientoip=<?php echo $row_rsLevDetalle['iddetallelevantamientoip'];?>"  onclick="NewWindow(this.href,'Modificar articulo','550','300','yes');return false" ><img src="images/Edit.png" border="0" title="Modificar Articulo" /></a><a href="eliminar.levantamiento.producto?iddetallelevantamientoip=<?php echo $row_rsLevDetalle['iddetallelevantamientoip'];?>"  onclick="NewWindow(this.href,'Eliminar articulo','550','300','yes');return false" ><img src="images/eliminar.gif" border="0" title="Eliminar Articulo" /></a></td>
    </tr>
    <?php } while ($row_rsLevDetalle = mysql_fetch_assoc($rsLevDetalle)); ?>
</tbody>

</table>
</div>

</body>
</html>
<?php
mysql_free_result($rsLevDetalle);
?>
