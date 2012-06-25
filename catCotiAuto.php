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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsCoti = 30;
$pageNum_rsCoti = 0;
if (isset($_GET['pageNum_rsCoti'])) {
  $pageNum_rsCoti = $_GET['pageNum_rsCoti'];
}
$startRow_rsCoti = $pageNum_rsCoti * $maxRows_rsCoti;

if(isset($_GET['buscar']) and ($_GET['buscar']!="")){
	$sql.=" and  identificador2 like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR identificador like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	
	
	
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCoti = "SELECT idsubcotizacion,idcotizacion, identificador, identificador2, contacto, fecha, numero, moneda, nombre, tipo_cambio, vigencia, tipoentrega,(select nombre from cliente,cotizacion where cliente.idcliente=cotizacion.idcliente and idcotizacion=subcotizacion.idcotizacion ) as nomcliente FROM subcotizacion WHERE estado = 3 $sql ORDER BY identificador ASC";
$query_limit_rsCoti = sprintf("%s LIMIT %d, %d", $query_rsCoti, $startRow_rsCoti, $maxRows_rsCoti);
$rsCoti = mysql_query($query_limit_rsCoti, $tecnocomm) or die(mysql_error());
$row_rsCoti = mysql_fetch_assoc($rsCoti);

if (isset($_GET['totalRows_rsCoti'])) {
  $totalRows_rsCoti = $_GET['totalRows_rsCoti'];
} else {
  $all_rsCoti = mysql_query($query_rsCoti);
  $totalRows_rsCoti = mysql_num_rows($all_rsCoti);
}
$totalPages_rsCoti = ceil($totalRows_rsCoti/$maxRows_rsCoti)-1;

$queryString_rsCoti = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsCoti") == false && 
        stristr($param, "totalRows_rsCoti") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsCoti = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsCoti = sprintf("&totalRows_rsCoti=%d%s", $totalRows_rsCoti, $queryString_rsCoti);
?>
<h1>Administracion de Proyectos </h1>
<div class="submenu"> <a href="nuevoBanco.php" onclick="NewWindow(this.href,'Nuevo Banco',600,800,'yes'); return false;"></a> </div>
<div class="buscar"><form name="buscar" action="" method="get">
<label><span>Buscar</span><input type="text" name="buscar" value="<?php echo $_GET['buscar'];?>"></label> <input name="enviar" type="submit" value="Buscar" />
<input type="hidden" name="mod" value="catAutorizados"/>
</form></div>

<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="5" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsCoti > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsCoti=%d%s", $currentPage, 0, $queryString_rsCoti); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsCoti > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsCoti=%d%s", $currentPage, max(0, $pageNum_rsCoti - 1), $queryString_rsCoti); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsCoti < $totalPages_rsCoti) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsCoti=%d%s", $currentPage, min($totalPages_rsCoti, $pageNum_rsCoti + 1), $queryString_rsCoti); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsCoti < $totalPages_rsCoti) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsCoti=%d%s", $currentPage, $totalPages_rsCoti, $queryString_rsCoti); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>Opciones</td>
<td>Cotizacion</td>
<td>Cliente</td>
<td>Descripcion de Cotizacion </td>
  <td>Fecha</td>
  </tr>
</thead>
<tbody>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
    <td><a href="AdmonProyecto.php?id=<?php echo $row_rsCoti['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Admon Proyecto',600,800,'yes'); return false;"><img src="images/Checkmark.png" width="24" height="24" border="0" title="Administrar Proyecto" /></a><a href="catSalidas.php?idsub=<?php echo $row_rsCoti['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Admon salidas',600,800,'yes'); return false;"><img src="images/Administracion.png" width="24" height="24" border="0" /></a></td>
    <td><?php echo $row_rsCoti['identificador2']; ?></td>
    <td><?php echo $row_rsCoti['nomcliente']; ?></td>
    <td><?php echo $row_rsCoti['nombre']; ?></td>
      <td><?php echo $row_rsCoti['fecha']; ?></td>
      </tr>
    <?php } while ($row_rsCoti = mysql_fetch_assoc($rsCoti)); ?>
</tbody>
<tfoot>
<tr><td colspan="5" align="right">
    <table border="0">
      <tr>
        <td><?php if ($pageNum_rsCoti > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsCoti=%d%s", $currentPage, 0, $queryString_rsCoti); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsCoti > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsCoti=%d%s", $currentPage, max(0, $pageNum_rsCoti - 1), $queryString_rsCoti); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsCoti < $totalPages_rsCoti) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsCoti=%d%s", $currentPage, min($totalPages_rsCoti, $pageNum_rsCoti + 1), $queryString_rsCoti); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsCoti < $totalPages_rsCoti) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsCoti=%d%s", $currentPage, $totalPages_rsCoti, $queryString_rsCoti); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr></tfoot>
</table>
</div>
<?php
mysql_free_result($rsCoti);
?>
