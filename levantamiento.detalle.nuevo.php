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

$maxRows_rsConceptos = 30;
$pageNum_rsConceptos = 0;
if (isset($_GET['pageNum_rsConceptos'])) {
  $pageNum_rsConceptos = $_GET['pageNum_rsConceptos'];
}
$startRow_rsConceptos = $pageNum_rsConceptos * $maxRows_rsConceptos;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConceptos = "SELECT * FROM articulo ORDER BY clave ASC";
$query_limit_rsConceptos = sprintf("%s LIMIT %d, %d", $query_rsConceptos, $startRow_rsConceptos, $maxRows_rsConceptos);
$rsConceptos = mysql_query($query_limit_rsConceptos, $tecnocomm) or die(mysql_error());
$row_rsConceptos = mysql_fetch_assoc($rsConceptos);

if (isset($_GET['totalRows_rsConceptos'])) {
  $totalRows_rsConceptos = $_GET['totalRows_rsConceptos'];
} else {
  $all_rsConceptos = mysql_query($query_rsConceptos);
  $totalRows_rsConceptos = mysql_num_rows($all_rsConceptos);
}
$totalPages_rsConceptos = ceil($totalRows_rsConceptos/$maxRows_rsConceptos)-1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Agregar Concepto</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Agregar Concepto</h1>
<div id="opciones">
<label>Buscar <input type="text" /></label> <button type="submit">Buscar</button>
<label></label>
</div>
<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead><tr><td>Clave</td><td>Descripcion</td><td>Marca</td><td>Codigo</td><td>Opciones</td></tr></thead>
<tbody>
  <?php do { ?>
    <tr><td><?php echo $row_rsConceptos['clave']; ?></td><td><?php echo $row_rsConceptos['nombre']; ?></td><td><?php echo $row_rsConceptos['marca']; ?></td><td><?php echo $row_rsConceptos['codigo']; ?></td><td>
    <a href="levantamiento.detalle.nuevo.agregarArticulo.php?idlevantamiento=<?php echo $_GET['idlevantamiento'];?>&idarticulo=<?php echo $row_rsConceptos['idarticulo']; ?>" onclick="NewWindow(this.href,'Agregar Concepto','800','600','yes'); return false;"><img src="images/Checkmark.png" alt="agregar" title="AGREGAR ARTICULO A COTIZACION" border="0"></a>
    </td></tr>
    <?php } while ($row_rsConceptos = mysql_fetch_assoc($rsConceptos)); ?>
</tbody>
</table>
</div>
</body>
</html>
<?php
mysql_free_result($rsConceptos);
?>
