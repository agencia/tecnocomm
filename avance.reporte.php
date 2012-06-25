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

$colname_rsFechas = "-1";
if (isset($_GET['idip'])) {
  $colname_rsFechas = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFechas = sprintf("SELECT fecha FROM proyecto_avance WHERE idproyecto = %s GROUP BY fecha ORDER BY fecha ASC", GetSQLValueString($colname_rsFechas, "int"));
$rsFechas = mysql_query($query_rsFechas, $tecnocomm) or die(mysql_error());
$row_rsFechas = mysql_fetch_assoc($rsFechas);
$totalRows_rsFechas = mysql_num_rows($rsFechas);

$colname_rsAvance = "-1";
if (isset($_GET['idip'])) {
  $colname_rsAvance = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAvance = sprintf("SELECT * FROM proyecto_avance WHERE idproyecto = %s ORDER BY fecha ASC", GetSQLValueString($colname_rsSalidas, "int"));
$rsAvance = mysql_query($query_rsAvance, $tecnocomm) or die(mysql_error());
$row_rsAvance = mysql_fetch_assoc($rsAvance);
$totalRows_rsAvance = mysql_num_rows($rsAvance);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsEmpleados = "SELECT * FROM usuarios ORDER BY nombrereal ASC";
$rsEmpleados = mysql_query($query_rsEmpleados, $tecnocomm) or die(mysql_error());
$row_rsEmpleados = mysql_fetch_assoc($rsEmpleados);
$totalRows_rsEmpleados = mysql_num_rows($rsEmpleados);


unset($fechas);
do{
	
	$fechas[]=$row_rsFechas['fecha'];
	
}while ($row_rsFechas = mysql_fetch_assoc($rsFechas));


do{	
	$avances[$row_rsAvance['idsubcotizacion']][$row_rsAvance['fecha']] = $row_rsAvance;
}while($row_rsAvance = mysql_fetch_assoc($rsAvance));

unset($suma);

?>
<h1>Reporte De Avance</h1>
<h2>Proyecto: <?php echo $_GET['idip'];?></h2>
<div id="opciones">
<ul><li><a href="avance.php?idip=<?php echo $_GET['idip'];?>" class="popup">Capturar Avance</a></li></ul>
</div>
<div id="distabla">
    <table width="100%" class="0" cellpadding="2">
      <thead>
        <tr><td>Partida</td><td>Descripcion</td><td>Cantidad</td><?php foreach($fechas as $fecha){?><td><?php echo $fecha;?></td><?php }//fin de foreach?>
          <td>Instalado/Cotizado</td></tr>
      </thead>
      <tbody>
        <?php do { ?>
  <tr>
    <td></td>
    <td><?php echo substr($row_rsPartidas['descri'],0,25); ?></td>
    <td><?php echo $row_rsPartidas['cantidad']; ?></td>
    <?php foreach($fechas as $fecha){?> <td><?php echo $avances[$row_rsPartidas['idsubcotizacionarticulo']][$fecha]['cantidad']; $suma[$row_rsPartidas['idsubcotizacionarticulo']] = $suma[$row_rsPartidas['idsubcotizacionarticulo']] +$avances[$row_rsPartidas['idsubcotizacionarticulo']][$fecha]['cantidad'];?></td><?php }//fin de foreach?>
    
   
    <td><?php echo $suma[$row_rsPartidas['idsubcotizacionarticulo']];?>/<?php echo $row_rsPartidas['cantidad']; ?></td>
  </tr>
  <?php } while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas)); ?>
      </tbody>
    </table>
</div>
<?php
mysql_free_result($rsPartidas);

mysql_free_result($rsFechas);

mysql_free_result($rsAvance);

mysql_free_result($rsEmpleados);
?>
