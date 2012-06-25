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

$colname_rsFavoritos = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsFavoritos = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFavoritos = sprintf("SELECT f.idfavorito, l.nombre,l.link, l.mod,l.imagen FROM favoritos f, link l WHERE idusuario = %s AND f.idlink = l.id", GetSQLValueString($colname_rsFavoritos, "int"));
$rsFavoritos = mysql_query($query_rsFavoritos, $tecnocomm) or die(mysql_error());
$row_rsFavoritos = mysql_fetch_assoc($rsFavoritos);
$totalRows_rsFavoritos = mysql_num_rows($rsFavoritos);

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsAvisos = 10;
$pageNum_rsAvisos = 0;
if (isset($_GET['pageNum_rsAvisos'])) {
  $pageNum_rsAvisos = $_GET['pageNum_rsAvisos'];
}
$startRow_rsAvisos = $pageNum_rsAvisos * $maxRows_rsAvisos;

$colname_rsAvisos = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsAvisos = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAvisos = sprintf("SELECT * FROM avisos WHERE para = %s ORDER BY fecha,realizado DESC", GetSQLValueString($colname_rsAvisos, "int"));
$query_limit_rsAvisos = sprintf("%s LIMIT %d, %d", $query_rsAvisos, $startRow_rsAvisos, $maxRows_rsAvisos);
$rsAvisos = mysql_query($query_limit_rsAvisos, $tecnocomm) or die(mysql_error());
$row_rsAvisos = mysql_fetch_assoc($rsAvisos);

if (isset($_GET['totalRows_rsAvisos'])) {
  $totalRows_rsAvisos = $_GET['totalRows_rsAvisos'];
} else {
  $all_rsAvisos = mysql_query($query_rsAvisos);
  $totalRows_rsAvisos = mysql_num_rows($all_rsAvisos);
}
$totalPages_rsAvisos = ceil($totalRows_rsAvisos/$maxRows_rsAvisos)-1;$maxRows_rsAvisos = 30;
$pageNum_rsAvisos = 0;
if (isset($_GET['pageNum_rsAvisos'])) {
  $pageNum_rsAvisos = $_GET['pageNum_rsAvisos'];
}
$startRow_rsAvisos = $pageNum_rsAvisos * $maxRows_rsAvisos;

$colname_rsAvisos = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsAvisos = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAvisos = sprintf("SELECT *,(select nombrereal from usuarios where id=de ) as dee, (select nombrereal from usuarios where id=para ) as par FROM avisos WHERE (para = %s or de=%s) and padre is null and liberado=0 ORDER BY prioridad,fecha DESC", GetSQLValueString($colname_rsAvisos, "int"),GetSQLValueString($colname_rsAvisos, "int"));
$query_limit_rsAvisos = sprintf("%s LIMIT %d, %d", $query_rsAvisos, $startRow_rsAvisos, $maxRows_rsAvisos);
$rsAvisos = mysql_query($query_limit_rsAvisos, $tecnocomm) or die(mysql_error());
$row_rsAvisos = mysql_fetch_assoc($rsAvisos);

if (isset($_GET['totalRows_rsAvisos'])) {
  $totalRows_rsAvisos = $_GET['totalRows_rsAvisos'];
} else {
  $all_rsAvisos = mysql_query($query_rsAvisos);
  $totalRows_rsAvisos = mysql_num_rows($all_rsAvisos);
}
$totalPages_rsAvisos = ceil($totalRows_rsAvisos/$maxRows_rsAvisos)-1;



$queryString_rsAvisos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsAvisos") == false && 
        stristr($param, "totalRows_rsAvisos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsAvisos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsAvisos = sprintf("&totalRows_rsAvisos=%d%s", $totalRows_rsAvisos, $queryString_rsAvisos);
$realizado=array(0=>"No",1=>"Si");
?>
<div id="portada">

<div id="favoritos">
<h1>Mis Enlaces [<a href="nuevoFavorito.php" onClick="NewWindow(this.href,'Nuevo Favorito',800,600,'yes');return false;" style="color:#FFF;">agregar</a>]</h1>
<ul>
  <?php do { ?>
    <li><a href="index.php?mod=<?php echo $row_rsFavoritos['mod']; ?>"><img src="<?php echo $row_rsFavoritos['imagen']; ?>" border="0" align="absmiddle"> <?php echo $row_rsFavoritos['nombre']; ?></a><a href="eliminarFavorito.php?idfavorito=<?php echo $row_rsFavoritos['idfavorito'];?>" onClick="NewWindow(this.href,'Nuevo Favorito',800,600,'yes');return false;" ><img src="images/eliminar.gif" border="0" align="middle" title="Eliminar Favorito" /></a></li>
    <?php } while ($row_rsFavoritos = mysql_fetch_assoc($rsFavoritos)); ?>
</ul>
</div>
</div>
<?php
mysql_free_result($rsFavoritos);
?>
