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

$maxRows_rsProyectos = 30;
$pageNum_rsProyectos = 0;
if (isset($_GET['pageNum_rsProyectos'])) {
  $pageNum_rsProyectos = $_GET['pageNum_rsProyectos'];
}
$startRow_rsProyectos = $pageNum_rsProyectos * $maxRows_rsProyectos;

$ide_rsProyectos = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $ide_rsProyectos = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProyectos = sprintf("SELECT *, (select nombre FROM subcotizacion WHERE idsubcotizacion= subcotizacionpersonal.idsubcotizacion) as nomproy FROM subcotizacionpersonal where idempleado=%s", GetSQLValueString($ide_rsProyectos, "int"));
$query_limit_rsProyectos = sprintf("%s LIMIT %d, %d", $query_rsProyectos, $startRow_rsProyectos, $maxRows_rsProyectos);
$rsProyectos = mysql_query($query_limit_rsProyectos, $tecnocomm) or die(mysql_error());
$row_rsProyectos = mysql_fetch_assoc($rsProyectos);

if (isset($_GET['totalRows_rsProyectos'])) {
  $totalRows_rsProyectos = $_GET['totalRows_rsProyectos'];
} else {
  $all_rsProyectos = mysql_query($query_rsProyectos);
  $totalRows_rsProyectos = mysql_num_rows($all_rsProyectos);
}
$totalPages_rsProyectos = ceil($totalRows_rsProyectos/$maxRows_rsProyectos)-1;

$queryString_rsProyectos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsProyectos") == false && 
        stristr($param, "totalRows_rsProyectos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsProyectos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsProyectos = sprintf("&totalRows_rsProyectos=%d%s", $totalRows_rsProyectos, $queryString_rsProyectos);
?>
<h1> Reporte de Avance</h1>
<div class="submenu"> </div>
<div class="buscar"><label><span>Buscar</span><input type="text" name="buscar"></label></div>

<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="3" align="right">z
  <table border="0">
  <tr>
    <td><?php if ($pageNum_rsProyectos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsProyectos=%d%s", $currentPage, 0, $queryString_rsProyectos); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsProyectos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsProyectos=%d%s", $currentPage, max(0, $pageNum_rsProyectos - 1), $queryString_rsProyectos); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsProyectos < $totalPages_rsProyectos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsProyectos=%d%s", $currentPage, min($totalPages_rsProyectos, $pageNum_rsProyectos + 1), $queryString_rsProyectos); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsProyectos < $totalPages_rsProyectos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsProyectos=%d%s", $currentPage, $totalPages_rsProyectos, $queryString_rsProyectos); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td width="11%">Opciones</td>
<td width="81%">Proyecto</td>
  <td width="8%">&nbsp;</td>
  </tr>
</thead>
<tbody>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
    <td><?php if ($totalRows_rsProyectos > 0) { // Show if recordset not empty ?>
  <a href="nuevoAvance.php?id=<?php echo $rsProyectos['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Nuevo Banco',600,800,'yes'); return false;"> Nuevo Reporte</a>
  <?php } // Show if recordset not empty ?></td>
    <td><?php echo $rsProyectos['nomproy'];?></td>
      <td>&nbsp;</td>
      </tr>
    <?php } while ($row_rsProyectos = mysql_fetch_assoc($rsProyectos)); ?>
</tbody>
<tfoot>
<tr><td colspan="3" align="right">
    <table border="0">
      <tr>
        <td><?php if ($pageNum_rsProyectos > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsProyectos=%d%s", $currentPage, 0, $queryString_rsProyectos); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsProyectos > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsProyectos=%d%s", $currentPage, max(0, $pageNum_rsProyectos - 1), $queryString_rsProyectos); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsProyectos < $totalPages_rsProyectos) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsProyectos=%d%s", $currentPage, min($totalPages_rsProyectos, $pageNum_rsProyectos + 1), $queryString_rsProyectos); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsProyectos < $totalPages_rsProyectos) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsProyectos=%d%s", $currentPage, $totalPages_rsProyectos, $queryString_rsProyectos); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr></tfoot>

</table>
</div>
<?php
mysql_free_result($rsProyectos);
?>
