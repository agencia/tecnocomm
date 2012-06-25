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

$maxRows_rsBancos = 30;
$pageNum_rsBancos = 0;
if (isset($_GET['pageNum_rsBancos'])) {
  $pageNum_rsBancos = $_GET['pageNum_rsBancos'];
}
$startRow_rsBancos = $pageNum_rsBancos * $maxRows_rsBancos;

$ide_rsBancos = "-1";
if (isset($_GET['idsub'])) {
  $ide_rsBancos = $_GET['idsub'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsBancos = sprintf("SELECT *,(select nombre from articulo where idarticulo=partidaextra.idarticulo) as nomart FROM partidaextra where idsubcotizacion=%s", GetSQLValueString($ide_rsBancos, "int"));
$query_limit_rsBancos = sprintf("%s LIMIT %d, %d", $query_rsBancos, $startRow_rsBancos, $maxRows_rsBancos);
$rsBancos = mysql_query($query_limit_rsBancos, $tecnocomm) or die(mysql_error());
$row_rsBancos = mysql_fetch_assoc($rsBancos);

if (isset($_GET['totalRows_rsBancos'])) {
  $totalRows_rsBancos = $_GET['totalRows_rsBancos'];
} else {
  $all_rsBancos = mysql_query($query_rsBancos);
  $totalRows_rsBancos = mysql_num_rows($all_rsBancos);
}
$totalPages_rsBancos = ceil($totalRows_rsBancos/$maxRows_rsBancos)-1;

$queryString_rsBancos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsBancos") == false && 
        stristr($param, "totalRows_rsBancos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsBancos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsBancos = sprintf("&totalRows_rsBancos=%d%s", $totalRows_rsBancos, $queryString_rsBancos);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Detalle de Avance</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/valid.js"></script>
<script language="javascript"  src="js/funciones.js"></script>
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
.style1 {
	color: #990000;
	font-size: 10px;
}
.ocultar tbody{
	display:none;
}

</style>
</head>

<body>

<h1>Partidas Extras Agregadas</h1>
<div class="submenu">  </div>


<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="4" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsBancos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, 0, $queryString_rsBancos); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsBancos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, max(0, $pageNum_rsBancos - 1), $queryString_rsBancos); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsBancos < $totalPages_rsBancos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, min($totalPages_rsBancos, $pageNum_rsBancos + 1), $queryString_rsBancos); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsBancos < $totalPages_rsBancos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, $totalPages_rsBancos, $queryString_rsBancos); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>Partida</td>

<td>Cantidad</td>

<td>Fecha</td>
  <td>Comentario</td>
  </tr>
</thead>
<tbody>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
    <td><?php echo $rsBancos['nomart'];?></td>
	<td><?php echo $rsBancos['cantidad_a'];?></td>
	<td><?php echo $rsBancos['fecha'];?></td>
    <td><?php echo $rsBancos['comentario'];?></td>
    </tr>
    <?php } while ($row_rsBancos = mysql_fetch_assoc($rsBancos)); ?>
</tbody>
<tfoot>
<tr><td colspan="4" align="right">
    <table border="0">
      <tr>
        <td><?php if ($pageNum_rsBancos > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, 0, $queryString_rsBancos); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsBancos > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, max(0, $pageNum_rsBancos - 1), $queryString_rsBancos); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsBancos < $totalPages_rsBancos) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, min($totalPages_rsBancos, $pageNum_rsBancos + 1), $queryString_rsBancos); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsBancos < $totalPages_rsBancos) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, $totalPages_rsBancos, $queryString_rsBancos); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr></tfoot>
</table>
</div>
  </body>
</html>
<?php
mysql_free_result($rsBancos);
?>
