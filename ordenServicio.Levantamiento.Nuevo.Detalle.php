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

$colname_rsLevantamiento = "-1";
if (isset($_GET['idlevantamiento'])) {
  $colname_rsLevantamiento = $_GET['idlevantamiento'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevantamiento = sprintf("SELECT * FROM levantamiento WHERE idlevantamiento = %s", GetSQLValueString($colname_rsLevantamiento, "int"));
$rsLevantamiento = mysql_query($query_rsLevantamiento, $tecnocomm) or die(mysql_error());
$row_rsLevantamiento = mysql_fetch_assoc($rsLevantamiento);
$totalRows_rsLevantamiento = mysql_num_rows($rsLevantamiento);

$maxRows_rsDetalleLevantamiento = 30;
$pageNum_rsDetalleLevantamiento = 0;
if (isset($_GET['pageNum_rsDetalleLevantamiento'])) {
  $pageNum_rsDetalleLevantamiento = $_GET['pageNum_rsDetalleLevantamiento'];
}
$startRow_rsDetalleLevantamiento = $pageNum_rsDetalleLevantamiento * $maxRows_rsDetalleLevantamiento;

$colname_rsDetalleLevantamiento = "-1";
if (isset($_GET['idlevantamiento'])) {
  $colname_rsDetalleLevantamiento = $_GET['idlevantamiento'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalleLevantamiento = sprintf("SELECT l.*,a.nombre FROM levantamientodetalle l LEFT JOIN articulo a ON l.idarticulo = a.idarticulo  AND idlevantamiento = %s ORDER BY idlevantamientodetalle ASC", GetSQLValueString($colname_rsDetalleLevantamiento, "int"));
$query_limit_rsDetalleLevantamiento = sprintf("%s LIMIT %d, %d", $query_rsDetalleLevantamiento, $startRow_rsDetalleLevantamiento, $maxRows_rsDetalleLevantamiento);
$rsDetalleLevantamiento = mysql_query($query_limit_rsDetalleLevantamiento, $tecnocomm) or die(mysql_error());
$row_rsDetalleLevantamiento = mysql_fetch_assoc($rsDetalleLevantamiento);

if (isset($_GET['totalRows_rsDetalleLevantamiento'])) {
  $totalRows_rsDetalleLevantamiento = $_GET['totalRows_rsDetalleLevantamiento'];
} else {
  $all_rsDetalleLevantamiento = mysql_query($query_rsDetalleLevantamiento);
  $totalRows_rsDetalleLevantamiento = mysql_num_rows($all_rsDetalleLevantamiento);
}
$totalPages_rsDetalleLevantamiento = ceil($totalRows_rsDetalleLevantamiento/$maxRows_rsDetalleLevantamiento)-1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Documento sin título</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/funciones.js">
</script>
</head>

<body>
<h1>Detalle De Tipo Levantamiento</h1>
<h2><?php echo $row_rsLevantamiento['titulo']; ?></h2>
<p>Administre los conceptos que estan asignados ha este tipo de levantamiento....</p>
<div id="submenu"><ul><li><a href="levantamiento.detalle.nuevo.php?idlevantamiento=<?php echo $_GET['idlevantamiento'];?>" onclick="NewWindow(this.href,'Agergar Concepto','800','600','yes');return false;">Agregar Concepto</a></li></ul></div>
<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td>Opciones</td>
<td>Descripcion</td>
<td>&nbsp;</td></tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td>&nbsp;</td><td><?php echo $row_rsDetalleLevantamiento['nombre'];?></td><td>&nbsp;</td></tr>
    <?php } while ($row_rsDetalleLevantamiento = mysql_fetch_assoc($rsDetalleLevantamiento)); ?>
</tbody>
</table>

</div>


</body>
</html>
<?php
mysql_free_result($rsLevantamiento);

mysql_free_result($rsDetalleLevantamiento);
?>
