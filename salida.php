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
$query_rsFechas = sprintf("SELECT fecha FROM proyecto_salida WHERE idproyecto = %s AND tipo=0 GROUP BY fecha ORDER BY fecha ASC", GetSQLValueString($colname_rsFechas, "int"));
$rsFechas = mysql_query($query_rsFechas, $tecnocomm) or die(mysql_error());
$row_rsFechas = mysql_fetch_assoc($rsFechas);
$totalRows_rsFechas = mysql_num_rows($rsFechas);

$colname_rsSalidas = "-1";
if (isset($_GET['idip'])) {
  $colname_rsSalidas = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsSalidas = sprintf("SELECT * FROM proyecto_salida WHERE idproyecto = %s AND tipo=0 ORDER BY fecha ASC", GetSQLValueString($colname_rsSalidas, "int"));
$rsSalidas = mysql_query($query_rsSalidas, $tecnocomm) or die(mysql_error());
$row_rsSalidas = mysql_fetch_assoc($rsSalidas);
$totalRows_rsSalidas = mysql_num_rows($rsSalidas);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsEmpleados = "SELECT * FROM usuarios ORDER BY nombrereal ASC";
$rsEmpleados = mysql_query($query_rsEmpleados, $tecnocomm) or die(mysql_error());
$row_rsEmpleados = mysql_fetch_assoc($rsEmpleados);
$totalRows_rsEmpleados = mysql_num_rows($rsEmpleados);

do{
	
	$fechas[]=$row_rsFechas['fecha'];
	
}while ($row_rsFechas = mysql_fetch_assoc($rsFechas));


do{	
	$salidas[$row_rsSalidas['idsubcotizacion']][$row_rsSalidas['fecha']] = $row_rsSalidas;
}while($row_rsSalidas = mysql_fetch_assoc($rsSalidas));

?>
<h1>Salidas De Material</h1>
<h2>Proyecto: <?php echo $_GET['idip'];?></h2>

<div id="distabla">
<form name="salida" method="post" action="index.php?mod=confsalida&idip=<?php echo $_GET['idip'];?>">

<div id="rec">
Mercancia Para:<select name="recibe">
  <?php
do {  
?>
  <option value="<?php echo $row_rsEmpleados['id']?>"><?php echo $row_rsEmpleados['nombrereal']?></option>
  <?php
} while ($row_rsEmpleados = mysql_fetch_assoc($rsEmpleados));
  $rows = mysql_num_rows($rsEmpleados);
  if($rows > 0) {
      mysql_data_seek($rsEmpleados, 0);
	  $row_rsEmpleados = mysql_fetch_assoc($rsEmpleados);
  }
?>
</select>
</div>

    <table width="100%" class="0" cellpadding="2">
      <thead>
        <tr><td>Partida</td><td>Descripcion</td><td>Cantidad</td><?php foreach($fechas as $fecha){?><td><?php echo $fecha;?></td><?php }//fin de foreach?><td>Hoy</td>
          <td>Entregado/Cotizado</td></tr>
      </thead>
      <tbody>
        <?php do { ?>
  <tr>
    <td></td>
    <td><?php echo substr($row_rsPartidas['descri'],0,25); ?></td>
    <td><?php echo $row_rsPartidas['cantidad']; ?></td>
    <?php foreach($fechas as $fecha){?> <td><?php echo $salidas[$row_rsPartidas['idsubcotizacionarticulo']][$fecha]['cantidad']; $suma[$row_rsPartidas['idsubcotizacionarticulo']] = $suma[$row_rsPartidas['idsubcotizacionarticulo']] +$salidas[$row_rsPartidas['idsubcotizacionarticulo']][$fecha]['cantidad'];?></td><?php }//fin de foreach?>
    
    <td><input type="text" size="4" name="cantidad[<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]"></td>
    <td><?php echo $suma[$row_rsPartidas['idsubcotizacionarticulo']];?>/<?php echo $row_rsPartidas['cantidad']; ?></td>
  </tr>
  <?php } while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas)); ?>
      </tbody>
    </table>
   <input type="hidden" name="idip" value="<?php echo $_GET['idip'];?>">
   <button type="submit">Aceptar</button>
    </form>

</div>
<?php
mysql_free_result($rsPartidas);

mysql_free_result($rsFechas);

mysql_free_result($rsSalidas);

mysql_free_result($rsEmpleados);
?>
