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

$maxRows_rsLevantamientoIp = 25;
$pageNum_rsLevantamientoIp = 0;
if (isset($_GET['pageNum_rsLevantamientoIp'])) {
  $pageNum_rsLevantamientoIp = $_GET['pageNum_rsLevantamientoIp'];
}
$startRow_rsLevantamientoIp = $pageNum_rsLevantamientoIp * $maxRows_rsLevantamientoIp;

$colname_rsLevantamientoIp = "-1";
if (isset($_GET['idip'])) {
  $colname_rsLevantamientoIp = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevantamientoIp = sprintf("SELECT * FROM levantamientoip WHERE idip = %s ORDER BY idip DESC", GetSQLValueString($colname_rsLevantamientoIp, "int"));
$query_limit_rsLevantamientoIp = sprintf("%s LIMIT %d, %d", $query_rsLevantamientoIp, $startRow_rsLevantamientoIp, $maxRows_rsLevantamientoIp);
$rsLevantamientoIp = mysql_query($query_limit_rsLevantamientoIp, $tecnocomm) or die(mysql_error());
$row_rsLevantamientoIp = mysql_fetch_assoc($rsLevantamientoIp);

if (isset($_GET['totalRows_rsLevantamientoIp'])) {
  $totalRows_rsLevantamientoIp = $_GET['totalRows_rsLevantamientoIp'];
} else {
  $all_rsLevantamientoIp = mysql_query($query_rsLevantamientoIp);
  $totalRows_rsLevantamientoIp = mysql_num_rows($all_rsLevantamientoIp);
}
$totalPages_rsLevantamientoIp = ceil($totalRows_rsLevantamientoIp/$maxRows_rsLevantamientoIp)-1;
?>
<h1>Levantamientos</h1>
<div id="submenu">
<ul><li><a href="lev.nuevo.php?idip=<?php echo $_GET['idip'];?>" class="popup">Nuevo Levantamiento</a></li>

<li><a href="lev.print.formatos.php?idip=<?php echo $_GET['idip'];?>" class="popup">Imprimir Formato</a></li></ul>
</div>
<?php if ($totalRows_rsLevantamientoIp > 0) { // Show if recordset not empty ?>
  <div id="distabla">
    <table width="100%" cellpadding="1" cellspacing="0">
      <thead>
        <tr><td>Opciones</td><td>Folio
        
        </td><td>Fecha</td><td>Descripcion</td></tr>
      </thead>
      <tbody>
        <?php do { ?>
  <tr><td><a href="lev.detalle.php?idip=<?php echo $_GET['idip'];?>&idlevantamiento=<?php echo $row_rsLevantamientoIp['idlevantamientoip']; ?> " class="popup"><img src="images/Edit.png"  border="0" title="DETALLE LEVANTAMIENTO"/></a>
    <a href="lev.print.formatos.php?idip=<?php echo $_GET['idip'];?>&idlevantamiento=<?php echo $row_rsLevantamientoIp['idlevantamientoip']; ?>" class="popup"><img src="images/Imprimir2.png" width="24" height="24" border="0"  title="IMPRIMIR LEVANTAMIENTO"/></a>
  </td>
  <td><?php echo $row_rsLevantamientoIp['consecutivo'];?></td>
  <td><?php echo formatDate($row_rsLevantamientoIp['fecha']); ?></td><td><?php echo $row_rsLevantamientoIp['descripcion']; ?></td></tr>
  <?php } while ($row_rsLevantamientoIp = mysql_fetch_assoc($rsLevantamientoIp)); ?>
      </tbody>
    </table>
  </div>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_rsLevantamientoIp == 0) { // Show if recordset empty ?>
  <p>No hay levantamientos creados</p>
  <?php } // Show if recordset empty ?>
<?php
mysql_free_result($rsLevantamientoIp);
?>
