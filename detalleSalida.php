<?php require_once('Connections/tecnocomm.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

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
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
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

$ide_RsSalida = "-1";
if (isset($_GET['idsalida'])) {
  $ide_RsSalida = $_GET['idsalida'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSalida = sprintf("SELECT *,(select nombrereal FROM usuarios WHERE id=creo) as crea, (select nombrereal from usuarios where id=autoriza)  as aut, (select count(*) from salidamaterial where idsalida=id) as cant,(select identificador2 from subcotizacion where idsubcotizacion=idsub) as nombre FROM subcotizacionsalida where id=%s ORDER BY fecha ASC", GetSQLValueString($ide_RsSalida, "int"));
$RsSalida = mysql_query($query_RsSalida, $tecnocomm) or die(mysql_error());
$row_RsSalida = mysql_fetch_assoc($RsSalida);
$totalRows_RsSalida = mysql_num_rows($RsSalida);

$maxRows_rsPartidas = 30;
$pageNum_rsPartidas = 0;
if (isset($_GET['pageNum_rsPartidas'])) {
  $pageNum_rsPartidas = $_GET['pageNum_rsPartidas'];
}
$startRow_rsPartidas = $pageNum_rsPartidas * $maxRows_rsPartidas;

$ide_rsPartidas = "-1";
if (isset($_GET['idsalida'])) {
  $ide_rsPartidas = $_GET['idsalida'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT (select descri FROM subcotizacionarticulo WHERE idsubcotizacionarticulo=salidamaterial.idarticulo) as partida, (select cantidad from subcotizacionarticulo where idsubcotizacionarticulo=salidamaterial.idarticulo) as total, cantidad, responsable, fecha, (select nombrereal from usuarios where id=responsable)  as resp from salidamaterial where idsalida=%s", GetSQLValueString($ide_rsPartidas, "int"));
$query_limit_rsPartidas = sprintf("%s LIMIT %d, %d", $query_rsPartidas, $startRow_rsPartidas, $maxRows_rsPartidas);
$rsPartidas = mysql_query($query_limit_rsPartidas, $tecnocomm) or die(mysql_error());
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);

if (isset($_GET['totalRows_rsPartidas'])) {
  $totalRows_rsPartidas = $_GET['totalRows_rsPartidas'];
} else {
  $all_rsPartidas = mysql_query($query_rsPartidas);
  $totalRows_rsPartidas = mysql_num_rows($all_rsPartidas);
}
$totalPages_rsPartidas = ceil($totalRows_rsPartidas/$maxRows_rsPartidas)-1;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Detalle Salida</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript"  src="js/funciones.js"></script>
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>

<div>
<h1>Datos Generales</h1>
<label>Cotizacion:<?php echo $row_RsSalida['nombre'];?></label>
<label><br />
Autoriza:<?php echo $row_RsSalida['aut'];?><br />
</label>

<label>Crea:<?php echo $row_RsSalida['crea'];?><br />
</label>
<label>Fecha:<?php echo $row_RsSalida['fecha'];?><br />
</label>
<label>Cantidad de Partidas:<?php echo $row_RsSalida['cant'];?></label>
</div>
<div>
<h1>Agregar Partida</h1>
<a href="buscarPartida.php?idsalida=<?php echo $_GET['idsalida']; ?>&idsub=<?php echo $_GET['idsub'];?>" onclick="NewWindow(this.href,'buscar partida',800,800,'yes');return false;"><label>
<img src="images/Agregar.png" width="24" height="24" border="0" align="middle" />Agregar</label>	
</a>
</div>

<br /><br />
<div>
<div id="distabla">
  <table width="100%" cellpadding="0" cellspacing="0">
  <thead>
  <tr><td colspan="6" align="right"><table width="100%" cellpadding="0" cellspacing="0">
    
      <tr>
        <td colspan="5" align="right"><table border="0">
            <tr>
              <td><?php if ($pageNum_rsPartidas > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, 0, $queryString_rsPartidas); ?>"><img src="images/First.gif" /></a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_rsPartidas > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, max(0, $pageNum_rsPartidas - 1), $queryString_rsPartidas); ?>"><img src="images/Previous.gif" /></a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_rsPartidas < $totalPages_rsPartidas) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, min($totalPages_rsPartidas, $pageNum_rsPartidas + 1), $queryString_rsPartidas); ?>"><img src="images/Next.gif" /></a>
                  <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_rsPartidas < $totalPages_rsPartidas) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, $totalPages_rsPartidas, $queryString_rsPartidas); ?>"><img src="images/Last.gif" /></a>
                  <?php } // Show if not last page ?></td>
            </tr>
        </table></td>
      </tr>
      
      <tr>
        <td>Opciones</td>
        <td>Partida</td>
        <td>Cantidad</td>
        <td>Restante</td>
        <td>Fecha</td>
      </tr>
  </thead>
    <tbody>
      <?php do { ?>
      <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
        <td><a href="" onclick="NewWindow(this.href,'Modificar partida',800,800,'yes');return false;"><img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR DATOS DE PARTIDA"/></a><a href="" onclick="NewWindow(this.href,'Eliminar pARTIDA',800,800,'yes');return false;"><img src="images/eliminar.gif" width="24" height="24" border="0" title="ELIMINAR PARTIDA"/></a></td>
        <td><?php echo $row_rsPartidas['partida']; ?></td>
        <td><?php echo $row_rsPartidas['cantidad']; ?></td>
        <td><?php echo $row_rsPartidas['total']-$row_rsPartidas['cantidad']; ?></td>
        <td><?php echo $row_rsPartidas['fecha']; ?></td>
      </tr>
      <?php } while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas)); ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5" align="right"><table border="0">
            <tr>
              <td><?php if ($pageNum_rsPartidas > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, 0, $queryString_rsPartidas); ?>"><img src="images/First.gif" /></a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_rsPartidas > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, max(0, $pageNum_rsPartidas - 1), $queryString_rsSalidarsPartidas); ?>"><img src="images/Previous.gif" /></a>
                  <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_rsPartidas < $totalPages_rsPartidas) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, min($totalPages_rsPartidas, $pageNum_rsPartidas + 1), $queryString_rsPartidas); ?>"><img src="images/Next.gif" /></a>
                  <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_rsPartidas < $totalPages_rsPartidas) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, $totalPages_rsPartidas, $queryString_rsPartidas); ?>"><img src="images/Last.gif" /></a>
                  <?php } // Show if not last page ?></td>
            </tr>
        </table></td>
      </tr>
    </tfoot>
  </table></td></tr>
</thead>
</table>
</div>
</div>
</body>
</html>
<?php
mysql_free_result($RsSalida);

mysql_free_result($rsPartidas);
?>
