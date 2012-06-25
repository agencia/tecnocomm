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


if(isset($_POST['guardar']) && $_POST['guardar'] == true){
	
	
	foreach($_POST['cantidad'] as $kcant => $cant)if($cant > 0){
	mysql_select_db($database_tecnocomm, $tecnocomm);
	$query_insert = sprintf("INSERT INTO proyecto_salida(idproyecto,entrega,recibe,cantidad,fecha,hora,idsubcotizacion) VALUES(%s,%s,%s,%s,NOW(),NOW(),%s)",GetSQLValueString($_POST['idip'],"int"),GetSQLValueString($_SESSION['MM_Userid'],"int"),GetSQLValueString($_POST['recibe'],"int"),GetSQLValueString($cant,"int"),GetSQLValueString($kcant,"int"));
	$result = mysql_query($query_insert,$tecnocomm)or die(mysql_error());
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

$colname_rsEmpleados = "-1";
if (isset($_POST['recibe'])) {
  $colname_rsEmpleados = $_POST['recibe'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsEmpleados = sprintf("SELECT * FROM usuarios WHERE id = %s ORDER BY nombrereal ASC", GetSQLValueString($colname_rsEmpleados, "int"));
$rsEmpleados = mysql_query($query_rsEmpleados, $tecnocomm) or die(mysql_error());
$row_rsEmpleados = mysql_fetch_assoc($rsEmpleados);
$totalRows_rsEmpleados = mysql_num_rows($rsEmpleados);


do{
	
	$partidas[$row_rsPartidas['idsubcotizacionarticulo']] = $row_rsPartidas; 	

}while($row_rsPartidas = mysql_fetch_assoc($rsPartidas));



?>
<h1>Confirmar Salida</h1>
<h2>Proyecto: <?php echo $_GET['idip'];?></h2>

<div id="distabla">
<form name="salida" method="post" action="index.php?mod=confsalida">

<div id="rec">
Mercancia Para: <?php echo $row_rsEmpleados['nombrereal']; ?>
</div>

    <table width="100%" class="0" cellpadding="2">
      <thead>
        <tr><td>Partida</td><td>Descripcion</td><td>Cantidad Cotizada</td><td>Cantidad Entregada</td><td>Cantidad Entregando</td></tr>
      </thead>
      <tbody>
  <?php foreach($_POST['cantidad'] as $kcant => $cant)if($cant > 0){?>
  <tr>
    <td></td>
    <td><?php echo $partidas[$kcant]['descri']; ?></td>
    <td><?php echo $partidas[$kcant]['cantidad']; ?></td>
    <td><?php echo $partidas[$kcant]['cantidad']; ?></td>
    <td><?php echo $cant; ?><input type="hidden" name="cantidad[<?php echo $partidas[$kcant]['idsubcotizacionarticulo']; ?>]" value="<?php echo $cant;?>"></td>
  </tr>
  <?php }//enforeach ?>
      </tbody>
    </table>
   <input type="hidden" name="idip" value="<?php echo $_GET['idip'];?>">
   <input type="hidden"  name="guardar"  value="true">
   <button type="submit">Guadar</button>
   </form>

</div>
<?php
mysql_free_result($rsPartidas);


mysql_free_result($rsEmpleados);
?>
