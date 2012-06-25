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

$maxRows_Recordset1 = 30;
$pageNum_Recordset1 = 0;
if (isset($_GET['pageNum_Recordset1'])) {
  $pageNum_Recordset1 = $_GET['pageNum_Recordset1'];
}
$startRow_Recordset1 = $pageNum_Recordset1 * $maxRows_Recordset1;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Recordset1 = "SELECT * FROM levantamiento ORDER BY titulo ASC";
$query_limit_Recordset1 = sprintf("%s LIMIT %d, %d", $query_Recordset1, $startRow_Recordset1, $maxRows_Recordset1);
$Recordset1 = mysql_query($query_limit_Recordset1, $tecnocomm) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);

if (isset($_GET['totalRows_Recordset1'])) {
  $totalRows_Recordset1 = $_GET['totalRows_Recordset1'];
} else {
  $all_Recordset1 = mysql_query($query_Recordset1);
  $totalRows_Recordset1 = mysql_num_rows($all_Recordset1);
}
$totalPages_Recordset1 = ceil($totalRows_Recordset1/$maxRows_Recordset1)-1;
?>

<h1> Tipos de Levantamiento</h1>
<div id="submenu">
<ul>
<li><a href="ordenServicio.Levantamiento.Nuevo.php" onClick="NewWindow(this.href,'Nuevo Tipo',800,600,'yes');return false;">Nuevo Tipo</a></li>

</ul>
<div id="distabla">
<table width="100%" cellpadding="1" cellspacing="0">
<thead>
<tr><td>opciones</td><td>Titulo</td><td>Descripcion</td></tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td><a href="ordenServicio.Levantamiento.Nuevo.Detalle.php?idlevantamiento=<?php echo $row_Recordset1['idlevantamiento']; ?>" onClick="NewWindow(this.href,'Detalle Levantamiento',800,600,'yes'); return false;"><img src="images/Edit.png" width="24" height="24"></a></td>
      <td><?php echo $row_Recordset1['titulo']; ?></td>
      <td><?php echo $row_Recordset1['descripcion']; ?></td>
    </tr>
    <?php } while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); ?>
</tbody>
</table>
</div>
</div>

<?
mysql_free_result($Recordset1);
?>
