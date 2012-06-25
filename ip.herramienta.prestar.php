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

$maxRows_rsHerramienta = 30;
$pageNum_rsHerramienta = 0;
if (isset($_GET['pageNum_rsHerramienta'])) {
  $pageNum_rsHerramienta = $_GET['pageNum_rsHerramienta'];
}
$startRow_rsHerramienta = $pageNum_rsHerramienta * $maxRows_rsHerramienta;

if(isset($_GET['buscar']) and ($_GET['buscar']!="")){
	$sql.=" and  descripcion like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR modelo like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR marca like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR numserie like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR proveedor like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR ubicacion like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	
	
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsHerramienta = "SELECT * FROM herramienta where prestada = 0 $sql";
$query_limit_rsHerramienta = sprintf("%s LIMIT %d, %d", $query_rsHerramienta, $startRow_rsHerramienta, $maxRows_rsHerramienta);
$rsHerramienta = mysql_query($query_limit_rsHerramienta, $tecnocomm) or die(mysql_error());
$row_rsHerramienta = mysql_fetch_assoc($rsHerramienta);

if (isset($_GET['totalRows_rsHerramienta'])) {
  $totalRows_rsHerramienta = $_GET['totalRows_rsHerramienta'];
} else {
  $all_rsHerramienta = mysql_query($query_rsHerramienta);
  $totalRows_rsHerramienta = mysql_num_rows($all_rsHerramienta);
}
$totalPages_rsHerramienta = ceil($totalRows_rsHerramienta/$maxRows_rsHerramienta)-1;

$queryString_rsHerramienta = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsHerramienta") == false && 
        stristr($param, "totalRows_rsHerramienta") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsHerramienta = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsHerramienta = sprintf("&totalRows_rsHerramienta=%d%s", $totalRows_rsHerramienta, $queryString_rsHerramienta);
?>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js" language="javascript"> </script>
<script src="js/jqueryui.js" language="javascript"></script>
<script language="javascript"  src="js/funciones.js"></script>

<h1> Herramienta </h1>
<div class="buscar"><form name="buscar" action="" method="get">
<label><span>Buscar</span><input type="text" name="buscar" value="<?php echo $_GET['buscar'];?>"></label> <input name="enviar" type="submit" value="Buscar" />
<input type="hidden" name="mod" value="catherramientas"/>
</form></div>
<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="8" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsHerramienta > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsHerramienta=%d%s", $currentPage, 0, $queryString_rsHerramienta); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsHerramienta > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsHerramienta=%d%s", $currentPage, max(0, $pageNum_rsHerramienta - 1), $queryString_rsHerramienta); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsHerramienta < $totalPages_rsHerramienta) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsHerramienta=%d%s", $currentPage, min($totalPages_rsHerramienta, $pageNum_rsHerramienta + 1), $queryString_rsHerramienta); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsHerramienta < $totalPages_rsHerramienta) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsHerramienta=%d%s", $currentPage, $totalPages_rsHerramienta, $queryString_rsHerramienta); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>Descripcion</td>
<td>Grupo</td>
<td>Marca</td>
<td>Modelo</td>
<td>Ubicacion</td>
<td>Opciones</td>
 </tr>
</thead>
<tbody>
  <?php do { ?>
    <tr >
 
    <td><?php echo $row_rsHerramienta['descripcion']; ?></td>
     <td><?php echo $row_rsHerramienta['grupo']; ?></td>
    <td><?php echo $row_rsHerramienta['marca']; ?></td>
    <td><?php echo $row_rsHerramienta['modelo']; ?></td>
      <td><?php echo $row_rsHerramienta['ubicacion']; ?></td>
      <td><a href="ip.herramienta.add.php?opc=add&idh=<?php echo $row_rsHerramienta['id']; ?>&idip=<?php echo $_GET['idip']; ?>" class="popup"><img src="images/state3.png" border="none"></a></td>
      </tr>
    <?php } while ($row_rsHerramienta = mysql_fetch_assoc($rsHerramienta)); ?>
</tbody>
<tfoot>
<tr><td colspan="8" align="right">
    <table border="0">
      <tr>
        <td><?php if ($pageNum_rsHerramienta > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsHerramienta=%d%s", $currentPage, 0, $queryString_rsHerramienta); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsHerramienta > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsHerramienta=%d%s", $currentPage, max(0, $pageNum_rsHerramienta - 1), $queryString_rsHerramienta); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsHerramienta < $totalPages_rsHerramienta) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsHerramienta=%d%s", $currentPage, min($totalPages_rsHerramienta, $pageNum_rsHerramienta + 1), $queryString_rsHerramienta); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsHerramienta < $totalPages_rsHerramienta) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsHerramienta=%d%s", $currentPage, $totalPages_rsHerramienta, $queryString_rsHerramienta); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr></tfoot>
</table>
</div>
<?php
mysql_free_result($rsHerramienta);
?>
