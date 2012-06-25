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
$query_rsSalidas = sprintf("SELECT SUM(cantidad) AS tot,idsubcotizacion FROM proyecto_salida WHERE idproyecto = %s GROUP BY idsubcotizacion ORDER BY fecha ASC", GetSQLValueString($colname_rsSalidas, "int"));
$rsSalidas = mysql_query($query_rsSalidas, $tecnocomm) or die(mysql_error());
$row_rsSalidas = mysql_fetch_assoc($rsSalidas);
$totalRows_rsSalidas = mysql_num_rows($rsSalidas);

$colname_rsAvance = "-1";
if (isset($_GET['idip'])) {
  $colname_rsAvance = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAvance = sprintf("SELECT SUM(cantidad) AS tot,idsubcotizacion FROM proyecto_avance WHERE idproyecto = %s GROUP BY idsubcotizacion ORDER BY fecha ASC", GetSQLValueString($colname_rsAvance, "int"));
$rsAvance = mysql_query($query_rsAvance, $tecnocomm) or die(mysql_error());
$row_rsAvance = mysql_fetch_assoc($rsAvance);
$totalRows_rsAvance = mysql_num_rows($rsAvance);

unset($salidas);
do{	
	$salidas[$row_rsSalidas['idsubcotizacion']] = $row_rsSalidas;
}while($row_rsSalidas = mysql_fetch_assoc($rsSalidas));

unset($avance);
do{	
	$avance[$row_rsAvance['idsubcotizacion']] = $row_rsAvance;
}while($row_rsAvance = mysql_fetch_assoc($rsAvance));


?>
<div id="distabla">
<table>
<thead>
<tr><td>Partida</td><td>Descripcion</td><td>Cotizado</td><td>Salidas</td><td>Avance</td><td>Supervicion</td><td>Conciliacion</td></tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td></td>
      <td><?php echo $row_rsPartidas['descri']; ?></td>
      <td><?php echo $row_rsPartidas['cantidad']; ?></td>
      <td><?php echo $salidas[$row_rsPartidas['idsubcotizacionarticulo']]['tot'];?></td>
      <td><?php echo $avance[$row_rsPartidas['idsubcotizacionarticulo']]['tot'];?></td>
      <td></td>
      <td><?php if($row_rsPartidas['cantidad'] == $salidas[$row_rsPartidas['idsubcotizacionarticulo']]['tot'] && $row_rsPartidas['cantidad'] == $avance[$row_rsPartidas['idsubcotizacionarticulo']]['tot']){ echo $salidas[$row_rsPartidas['idsubcotizacionarticulo']]['tot'];}else{echo "*";}?></td>
    </tr>
    <?php } while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas)); ?>
</tbody>
</table>
</div>
<?php
mysql_free_result($rsPartidas);

mysql_free_result($rsSalidas);

mysql_free_result($rsAvance);

?>