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

$maxRows_rsSalida = 30;
$pageNum_rsSalida = 0;
if (isset($_GET['pageNum_rsSalida'])) {
  $pageNum_rsSalida = $_GET['pageNum_rsSalida'];
}
$startRow_rsSalida = $pageNum_rsSalida * $maxRows_rsSalida;

$ide_rsSalida = "-1";
if (isset($_GET['idsub'])) {
  $ide_rsSalida = $_GET['idsub'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsSalida = sprintf("SELECT *,(select nombrereal FROM usuarios WHERE id=creo) as crea, (select nombrereal from usuarios where id=autoriza)  as aut, (select nombrereal from usuarios where id=responsable)  as respo, (select count(*) from salidamaterial where idsalida=id) as cant FROM subcotizacionsalida where idsub=%s ORDER BY fecha ASC", GetSQLValueString($ide_rsSalida, "int"));
$rsSalida = mysql_query($query_rsSalida, $tecnocomm) or die(mysql_error());
$row_rsSalida = mysql_fetch_assoc($rsSalida);
$totalRows_rsSalida = mysql_num_rows($rsSalida);

$colname_RsCoti = "-1";
if (isset($_GET['idsub'])) {
  $colname_RsCoti = $_GET['idsub'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCoti = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_RsCoti, "int"));
$RsCoti = mysql_query($query_RsCoti, $tecnocomm) or die(mysql_error());
$row_RsCoti = mysql_fetch_assoc($RsCoti);
$totalRows_RsCoti = mysql_num_rows($RsCoti);

$ide_RsTot = "-1";
if (isset($_GET['idsub'])) {
  $ide_RsTot = $_GET['idsub'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsTot = sprintf("SELECT * , sum( b.cantidad ) AS tot, b.idarticulo as ideart FROM subcotizacionsalida a, salidamaterial b, subcotizacionarticulo c WHERE a.idsub = c.idsubcotizacion AND b.idarticulo = c.idsubcotizacionarticulo AND a.idsub =%s GROUP BY c.idsubcotizacionarticulo", GetSQLValueString($ide_RsTot, "int"));
$RsTot = mysql_query($query_RsTot, $tecnocomm) or die(mysql_error());
$row_RsTot = mysql_fetch_assoc($RsTot);
$totalRows_RsTot = mysql_num_rows($RsTot);



$queryString_rsSalida = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsSalida") == false && 
        stristr($param, "totalRows_rsSalida") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsSalida = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsSalida = sprintf("&totalRows_rsSalida=%d%s", $totalRows_rsSalida, $queryString_rsSalida);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Salida de Material</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript"  src="js/funciones.js"></script>
</head>

<body>
<h1>Salidas de Material</h1>
<div class="submenu"> <a href="nuevaSalida.php?idsub=<?php echo $_GET['idsub'];?>" onclick="NewWindow(this.href,'Nuevo Salida',600,800,'yes'); return false;"> Nueva Salida </a> </div>


<div>
<h3>Datos Generales</h3>
<label>Cotizacion:

<?php echo $row_RsCoti['identificador2']; ?></label><br />
<label>Nombre:<?php echo $row_RsCoti['nombre']; ?></label>
</div>

<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="6" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsSalida > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, 0, $queryString_rsSalida); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsSalida > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, max(0, $pageNum_rsSalida - 1), $queryString_rsSalida); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsSalida < $totalPages_rsSalida) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, min($totalPages_rsSalida, $pageNum_rsSalida + 1), $queryString_rsSalida); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsSalida < $totalPages_rsSalida) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, $totalPages_rsSalida, $queryString_rsSalida); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>Opciones</td>
<td>Creo</td>
<td>Autorizo</td>
<td>Responsable</td>
<td>Cantidad de Partidas</td>
<td>Fecha</td>
  </tr>
</thead>
<tbody>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
    <td><a href="detalleSalida?idsalida=<?php echo $row_rsSalida['id']; ?>&idsub=<?php echo $_GET['idsub'];?>" onclick="NewWindow(this.href,'Modificar salida',800,800,'yes');return false;"><img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR SALIDA"/></a><a href="" onclick="NewWindow(this.href,'Eliminar salida',800,800,'yes');return false;"><img src="images/eliminar.gif" width="24" height="24" border="0" title="ELIMINAR SALIDA"/></a></td>
    <td><?php echo $row_rsSalida['crea']; ?></td>
    <td><?php echo $row_rsSalida['aut']; ?></td>
    <td><?php echo $row_rsSalida['respo']; ?></td>
    <td><?php echo $row_rsSalida['cant']; ?></td><td><?php echo $row_rsSalida['fecha']; ?></td>
      </tr>
    <?php } while ($row_rsSalida = mysql_fetch_assoc($rsSalida)); ?>
</tbody>
<tfoot>
<tr><td colspan="6" align="right">
    <table border="0">
      <tr>
        <td><?php if ($pageNum_rsSalida > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, 0, $queryString_rsSalida); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsSalida > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, max(0, $pageNum_rsSalida - 1), $queryString_rsSalida); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsSalida < $totalPages_rsSalida) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, min($totalPages_rsSalida, $pageNum_rsSalida + 1), $queryString_rsSalida); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsSalida < $totalPages_rsSalida) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsSalida=%d%s", $currentPage, $totalPages_rsSalida, $queryString_rsSalida); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr></tfoot>
</table>
</div>
<h3>Informacion Global</h3>
<div id="distabla">
<table width="100%" cellspacing="0">
<thead>
	<tr>
    	<td colspan="4">&nbsp;</td>
   	  </tr>
    <tr>
    	<td>Partida</td>
        <td>Decripcion</td>
        <td>Cantidad Cot.</td>
        <td>Salidas</td>
      </tr>
</thead>
<tbody>
    <?php $j=0; do { 
	$j++;
	$idear_RsDetTot = "-1";
if (isset($row_RsTot['ideart'])) {
  $idear_RsDetTot = $row_RsTot['ideart'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsDetTot = sprintf("select sum(cantidad) as tot from salidamaterial where idarticulo=%s", GetSQLValueString($idear_RsDetTot, "int"));
$RsDetTot = mysql_query($query_RsDetTot, $tecnocomm) or die(mysql_error());
$row_RsDetTot = mysql_fetch_assoc($RsDetTot);
$totalRows_RsDetTot = mysql_num_rows($RsDetTot);
	?>
      <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
       
          <td><?php echo $j;?></td>
          <td><?php echo $row_RsTot['descri']; ?></td>
          <td><?php echo $row_RsTot['cantidad']; ?></td>
          <td><?php echo $row_RsDetTot['tot'];?></td>
          
      </tr>
      <?php } while ($row_RsTot = mysql_fetch_assoc($RsTot)); ?>
      <?php if ($totalRows_RsTot == 0) { // Show if recordset empty ?>
      <tr>
        <td colspan="4" align="center">No hay resultados a mostrar</td>
      </tr>
       <?php } // Show if recordset empty ?>
</tbody>
<tfoot>
	<tr>
    	<td colspan="4" align="right"></td>
    </tr>
</tfoot>
</table>
</div>
</body>
</html>
<?php
mysql_free_result($rsSalida);

mysql_free_result($RsCoti);

mysql_free_result($RsTot);

mysql_free_result($RsDetTot);
?>
