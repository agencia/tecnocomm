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
if (isset($_GET['idip'])) {
  $colname_rsPartidas = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT s.* FROM subcotizacionarticulo s RIGHT JOIN ip i ON s.idsubcotizacion = i.cotizacion WHERE i.idip = %s", GetSQLValueString($colname_rsPartidas, "int"));
$rsPartidas = mysql_query($query_rsPartidas, $tecnocomm) or die(mysql_error());
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);
$totalRows_rsPartidas = mysql_num_rows($rsPartidas);


$colname_rsSalidas = "-1";
if (isset($_GET['idip'])) {
  $colname_rsSalidas = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsSalidas = sprintf("SELECT * FROM proyecto_salida WHERE idproyecto = %s AND tipo = 0 ORDER BY fecha ASC", GetSQLValueString($colname_rsSalidas, "int"));
$rsSalidas = mysql_query($query_rsSalidas, $tecnocomm) or die(mysql_error());
$row_rsSalidas = mysql_fetch_assoc($rsSalidas);
$totalRows_rsSalidas = mysql_num_rows($rsSalidas);

$colname_rsDevolucion = "-1";
if (isset($_GET['idip'])) {
  $colname_rsDevolucion = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDevolucion = sprintf("SELECT * FROM proyecto_salida WHERE idproyecto = %s AND tipo = 1 ORDER BY fecha ASC", GetSQLValueString($colname_rsDevolucion, "int"));
$rsDevolucion = mysql_query($query_rsDevolucion, $tecnocomm) or die(mysql_error());
$row_rsDevolucion = mysql_fetch_assoc($rsDevolucion);
$totalRows_rsDevolucion = mysql_num_rows($rsDevolucion);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsEmpleados = "SELECT * FROM usuarios ORDER BY nombrereal ASC";
$rsEmpleados = mysql_query($query_rsEmpleados, $tecnocomm) or die(mysql_error());
$row_rsEmpleados = mysql_fetch_assoc($rsEmpleados);
$totalRows_rsEmpleados = mysql_num_rows($rsEmpleados);



do{	
	$salidas[$row_rsSalidas['idsubcotizacion']] = $salidas[$row_rsSalidas['idsubcotizacion']]  + $row_rsSalidas['cantidad'];
}while($row_rsSalidas = mysql_fetch_assoc($rsSalidas));

do{	
	$salidas[$row_rsDevolucion['idsubcotizacion']] = $salidas[$row_rsDevolucion['idsubcotizacion']]  -  $row_rsDevolucion['cantidad'];
}while($row_rsDevolucion = mysql_fetch_assoc($rsSalidas));

?>
<h1>Solicitud Interna De Materiales</h1>
<p>
Muestra un reporte del material solicitado a la fecha.
para hacer una solicitud de material de click en Crear Solicitud De Material
</p>
<div id="opciones">
<ul><li><a href="requesicionmaterial.php?idip=<?php echo $_GET['idip']; ?>" class="popup">Crear Solicitud De Material</a></li></ul>
</div>
<div id="distabla">
    <table width="100%" cellspacing="0" cellpadding="2">
      <thead>
        <tr>
        <td>Partida</td>
        <td>Codigo</td>
        <td>Marca</td>
        <td>Descripcion</td>
        <td>Cantidad<br> Cotizada</td>
        <td>Cantidad<br> Entregada</td>
         </tr>
      </thead>
      <tbody>
        <?php do { ?>
  <tr>
    <td></td>
    <td><?php echo $row_rsPartidas['codigo']; ?></td>
    <td><?php echo $row_rsPartidas['marca']; ?></td>
    <td><?php echo $row_rsPartidas['descri']; ?></td>
    <td><?php echo $row_rsPartidas['cantidad']; ?></td>
    <td><?php echo $salidas[$row_rsPartidas['idsubcotizacionarticulo']]; ?></td>
  </tr>
  <?php } while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas)); //añadir el material extra para este proeycto?>
      </tbody>
    </table>


</div>
<?php
mysql_free_result($rsPartidas);

mysql_free_result($rsSalidas);

mysql_free_result($rsEmpleados);
?>
