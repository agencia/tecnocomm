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
$sql="";
if(isset($_GET['buscar']) && $_GET['buscar']!=""){
		$sql.="  nombre like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR codigo like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR marca like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	
	$query = sprintf("SELECT * FROM articulo WHERE %s",GetSQLValueString($sql,"text"));
}else{
	$query =  "SELECT * FROM articulo ORDER BY nombre ASC";
	}

$maxRows_rsArticulos = 30;
$pageNum_rsArticulos = 0;
if (isset($_GET['pageNum_rsArticulos'])) {
  $pageNum_rsArticulos = $_GET['pageNum_rsArticulos'];
}
$startRow_rsArticulos = $pageNum_rsArticulos * $maxRows_rsArticulos;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsArticulos =$query;
$query_limit_rsArticulos = sprintf("%s LIMIT %d, %d", $query_rsArticulos, $startRow_rsArticulos, $maxRows_rsArticulos);
$rsArticulos = mysql_query($query_limit_rsArticulos, $tecnocomm) or die(mysql_error());
$row_rsArticulos = mysql_fetch_assoc($rsArticulos);

if (isset($_GET['totalRows_rsArticulos'])) {
  $totalRows_rsArticulos = $_GET['totalRows_rsArticulos'];
} else {
  $all_rsArticulos = mysql_query($query_rsArticulos);
  $totalRows_rsArticulos = mysql_num_rows($all_rsArticulos);
}
$totalPages_rsArticulos = ceil($totalRows_rsArticulos/$maxRows_rsArticulos)-1;

$queryString_rsArticulos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsArticulos") == false && 
        stristr($param, "totalRows_rsArticulos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsArticulos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsArticulos = sprintf("&totalRows_rsArticulos=%d%s", $totalRows_rsArticulos, $queryString_rsArticulos);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Agregar Partida A Levantamiento</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js" language="javascript"> </script>
<script language="javascript"  src="js/funciones.js"></script>
</head>

<body>
<h1>Catalago De Conceptos</h1>
<div id="opciones">
<form name="buscar" method="get">
<label>Buscar <input type="text" name="buscar" /></label> <button type="submit">Buscar</button>
<input type="hidden" name="idlevantamiento" value="<?php echo $_GET['idlevantamiento']; ?>" />
</form>
<button type="button" onclick="javascript:window.location = 'close.php'">Cerrar Ventana</button>
</div>

<div id="distabla">
<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr><td colspan="5" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsArticulos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsArticulos=%d%s", $currentPage, 0, $queryString_rsArticulos); ?>"><img src="images/First.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsArticulos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsArticulos=%d%s", $currentPage, max(0, $pageNum_rsArticulos - 1), $queryString_rsArticulos); ?>"><img src="images/Previous.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsArticulos < $totalPages_rsArticulos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsArticulos=%d%s", $currentPage, min($totalPages_rsArticulos, $pageNum_rsArticulos + 1), $queryString_rsArticulos); ?>"><img src="images/Next.gif" /></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsArticulos < $totalPages_rsArticulos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsArticulos=%d%s", $currentPage, $totalPages_rsArticulos, $queryString_rsArticulos); ?>"><img src="images/Last.gif" /></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>&nbsp;</td>
<td>
Descripcion
</td>
<td>Marca</td>
<td> Codigo </td>
<td>
Opciones
</td>
</tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td>&nbsp;</td>
      <td><?php echo $row_rsArticulos['nombre']; ?></td>
      <td><?php echo $row_rsArticulos['marca']; ?></td>
      <td><?php echo $row_rsArticulos['codigo']; ?></td>
      <td><a href="lev.detalle.add.ok.php?idlevantamiento=<?php echo $_GET['idlevantamiento'];?>&idarticulo=<?php echo $row_rsArticulos['idarticulo']; ?>" class="popup"><img src="images/Checkmark.png"  border="0"/></a></td>
    </tr>
    <?php } while ($row_rsArticulos = mysql_fetch_assoc($rsArticulos)); ?>
</tbody>
<tfoot>
<tr><td colspan="5" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsArticulos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsArticulos=%d%s", $currentPage, 0, $queryString_rsArticulos); ?>"><img src="images/First.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsArticulos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsArticulos=%d%s", $currentPage, max(0, $pageNum_rsArticulos - 1), $queryString_rsArticulos); ?>"><img src="images/Previous.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsArticulos < $totalPages_rsArticulos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsArticulos=%d%s", $currentPage, min($totalPages_rsArticulos, $pageNum_rsArticulos + 1), $queryString_rsArticulos); ?>"><img src="images/Next.gif" /></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsArticulos < $totalPages_rsArticulos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsArticulos=%d%s", $currentPage, $totalPages_rsArticulos, $queryString_rsArticulos); ?>"><img src="images/Last.gif" /></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
</tfoot>
</table>
</div>
</body>
</html>
<?php
mysql_free_result($rsArticulos);
?>
