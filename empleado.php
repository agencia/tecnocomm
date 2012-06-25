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

$maxRows_rsEmpleado = 30;
$pageNum_rsEmpleado = 0;
if (isset($_GET['pageNum_rsEmpleado'])) {
  $pageNum_rsEmpleado = $_GET['pageNum_rsEmpleado'];
}
$startRow_rsEmpleado = $pageNum_rsEmpleado * $maxRows_rsEmpleado;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsEmpleado = "SELECT * FROM empleado ORDER BY nombre ASC";
$query_limit_rsEmpleado = sprintf("%s LIMIT %d, %d", $query_rsEmpleado, $startRow_rsEmpleado, $maxRows_rsEmpleado);
$rsEmpleado = mysql_query($query_limit_rsEmpleado, $tecnocomm) or die(mysql_error());
$row_rsEmpleado = mysql_fetch_assoc($rsEmpleado);

if (isset($_GET['totalRows_rsEmpleado'])) {
  $totalRows_rsEmpleado = $_GET['totalRows_rsEmpleado'];
} else {
  $all_rsEmpleado = mysql_query($query_rsEmpleado);
  $totalRows_rsEmpleado = mysql_num_rows($all_rsEmpleado);
}
$totalPages_rsEmpleado = ceil($totalRows_rsEmpleado/$maxRows_rsEmpleado)-1;

$queryString_rsEmpleado = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsEmpleado") == false && 
        stristr($param, "totalRows_rsEmpleado") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsEmpleado = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsEmpleado = sprintf("&totalRows_rsEmpleado=%d%s", $totalRows_rsEmpleado, $queryString_rsEmpleado);
?>
<h1> Empleados </h1>
<div class="submenu">Nuevo Empleado</div>
<div class="buscar"><label><span>Buscar</span><input type="text" name="buscar"></label></div>

<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="4" align="right">&nbsp;
    <table border="0">
      <tr>
        <td><?php if ($pageNum_rsEmpleado > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsEmpleado=%d%s", $currentPage, 0, $queryString_rsEmpleado); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsEmpleado > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsEmpleado=%d%s", $currentPage, max(0, $pageNum_rsEmpleado - 1), $queryString_rsEmpleado); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsEmpleado < $totalPages_rsEmpleado) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsEmpleado=%d%s", $currentPage, min($totalPages_rsEmpleado, $pageNum_rsEmpleado + 1), $queryString_rsEmpleado); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsEmpleado < $totalPages_rsEmpleado) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsEmpleado=%d%s", $currentPage, $totalPages_rsEmpleado, $queryString_rsEmpleado); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr>
<tr>
<td>Opciones</td>
<td>Nombre</td>
<td>Estado de Contrato</td>
<td>Telefono(s)</td>
  </tr>
</thead>
<tbody>
  <?php do { ?>
  <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
    <td><a href="emplado.modificar.php?idbanco=<?php echo $row_rsBancos['idbanco']; ?>" onclick="NewWindow(this.href,'Modificar Datos de Empleado',800,800,'yes');return false;"><img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR DATOS DE BANCO"/></a><a href="#"><img src="images/eliminar.gif" width="24" height="24" border="0" title="ELIMINAR BANCO"/></a></td>
    <td><?php echo $row_rsEmpleado['nombre']; ?></td><td>&nbsp;</td>
    <td><?php echo $row_rsEmpleado['telefono']; ?> / <?php echo $row_rsEmpleado['celular']; ?></td>
  </tr>
  <?php } while ($row_rsEmpleado = mysql_fetch_assoc($rsEmpleado)); ?>
</tbody>
<tfoot>
<tr><td colspan="4" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsEmpleado > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsEmpleado=%d%s", $currentPage, 0, $queryString_rsEmpleado); ?>"><img src="images/First.gif" /></a>
      <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsEmpleado > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsEmpleado=%d%s", $currentPage, max(0, $pageNum_rsEmpleado - 1), $queryString_rsEmpleado); ?>"><img src="images/Previous.gif" /></a>
      <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsEmpleado < $totalPages_rsEmpleado) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsEmpleado=%d%s", $currentPage, min($totalPages_rsEmpleado, $pageNum_rsEmpleado + 1), $queryString_rsEmpleado); ?>"><img src="images/Next.gif" /></a>
      <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsEmpleado < $totalPages_rsEmpleado) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsEmpleado=%d%s", $currentPage, $totalPages_rsEmpleado, $queryString_rsEmpleado); ?>"><img src="images/Last.gif" /></a>
      <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr></tfoot>

</table>
</div>
<?php
mysql_free_result($rsEmpleado);
?>
