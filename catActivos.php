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

$maxRows_rsActivos = 30;
$pageNum_rsActivos = 0;
if (isset($_GET['pageNum_rsActivos'])) {
  $pageNum_rsActivos = $_GET['pageNum_rsActivos'];
}
$startRow_rsActivos = $pageNum_rsActivos * $maxRows_rsActivos;

if(isset($_GET['buscar']) and ($_GET['buscar']!="")){
	$sql.=" and  descripcion like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR modelo like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR marca like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR numserie like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR proveedor like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR ubicacion like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	
	
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsActivos = "SELECT * FROM activos where 1 $sql ORDER BY id ASC";
$query_limit_rsActivos = sprintf("%s LIMIT %d, %d", $query_rsActivos, $startRow_rsActivos, $maxRows_rsActivos);
$rsActivos = mysql_query($query_limit_rsActivos, $tecnocomm) or die(mysql_error());
$row_rsActivos = mysql_fetch_assoc($rsActivos);

if (isset($_GET['totalRows_rsActivos'])) {
  $totalRows_rsActivos = $_GET['totalRows_rsActivos'];
} else {
  $all_rsActivos = mysql_query($query_rsActivos);
  $totalRows_rsActivos = mysql_num_rows($all_rsActivos);
}
$totalPages_rsActivos = ceil($totalRows_rsActivos/$maxRows_rsActivos)-1;

$queryString_rsActivos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsActivos") == false && 
        stristr($param, "totalRows_rsActivos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsActivos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsActivos = sprintf("&totalRows_rsActivos=%d%s", $totalRows_rsActivos, $queryString_rsActivos);
?>
<h1> Activos </h1>
<div class="submenu"> <a href="nuevoActivo.php" onclick="NewWindow(this.href,'Nuevo Banco',600,800,'yes'); return false;"> Activo Nuevo </a> </div>
<div class="buscar"><form name="buscar" action="" method="get">
<label><span>Buscar</span><input type="text" name="buscar" value="<?php echo $_GET['buscar'];?>"></label> <input name="enviar" type="submit" value="Buscar" />
<input type="hidden" name="mod" value="catactivos"/>
</form>
</div>
<a href="imprimirActivos.php?consulta=<?php echo $query_rsActivos;?>" target="_blank">IMPRIMIR CONSULTA  DE ACTIVOS</a>
<div class="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="9" align="right"><table border="0">
  <tr>	
    <td><?php if ($pageNum_rsActivos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsActivos=%d%s", $currentPage, 0, $queryString_rsActivos); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsActivos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsActivos=%d%s", $currentPage, max(0, $pageNum_rsActivos - 1), $queryString_rsActivos); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsActivos < $totalPages_rsActivos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsActivos=%d%s", $currentPage, min($totalPages_rsActivos, $pageNum_rsActivos + 1), $queryString_rsActivos); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsActivos < $totalPages_rsActivos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsActivos=%d%s", $currentPage, $totalPages_rsActivos, $queryString_rsActivos); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>Opciones</td>
<td>Descripcion</td>
<td>Marca</td>
<td>Modelo</td>
<td>Numserie</td>
<td>Fecha de Compra</td>
<td>Proveedor</td>
<td>Valor Contable</td>
<td>Ubicacion</td>
  </tr>
</thead>
<tbody>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
    <td><a href="modificarActivo.php?id=<?php echo $row_rsActivos['id']; ?>" onclick="NewWindow(this.href,'Modificar Activo',800,800,'yes');return false;"><img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR DATOS DE ACTIVO"/></a><a href="eliminarActivo.php?id=<?php echo $row_rsActivos['id']; ?>" onclick="NewWindow(this.href,'Eliminar Activo',800,800,'yes');return false;"><img src="images/eliminar.gif" width="24" height="24" border="0" title="ELIMINAR ACTIVO"/></a></td>
    <td><?php echo $row_rsActivos['descripcion']; ?>(<?php echo $row_rsActivos['clave']; ?>)</td><td><?php echo $row_rsActivos['marca']; ?></td><td><?php echo $row_rsActivos['modelo']; ?></td>
    <td><?php echo $row_rsActivos['numserie']; ?></td>
    <td><?php echo $row_rsActivos['fechacompra']; ?></td>
    <td><?php echo $row_rsActivos['proveedor']; ?></td>
    <td><?php echo $row_rsActivos['valorcontable']; ?></td>
    <td><?php echo $row_rsActivos['ubicacion']; ?></td>
      </tr>
    <?php } while ($row_rsActivos = mysql_fetch_assoc($rsActivos)); ?>
</tbody>
<tfoot>
<tr><td colspan="9" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsActivos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsActivos=%d%s", $currentPage, 0, $queryString_rsActivos); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsActivos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsActivos=%d%s", $currentPage, max(0, $pageNum_rsActivos - 1), $queryString_rsActivos); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsActivos < $totalPages_rsActivos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsActivos=%d%s", $currentPage, min($totalPages_rsActivos, $pageNum_rsActivos + 1), $queryString_rsActivos); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsActivos < $totalPages_rsActivos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsActivos=%d%s", $currentPage, $totalPages_rsActivos, $queryString_rsActivos); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td></tr></table></td></tr></tfoot>

</table>
</div>
<?php
mysql_free_result($rsActivos);
?>
