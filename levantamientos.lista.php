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

$maxRows_rsLevantamientos = 30;
$pageNum_rsLevantamientos = 0;
if (isset($_GET['pageNum_rsLevantamientos'])) {
  $pageNum_rsLevantamientos = $_GET['pageNum_rsLevantamientos'];
}
$startRow_rsLevantamientos = $pageNum_rsLevantamientos * $maxRows_rsLevantamientos;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevantamientos = "SELECT l.*, c.nombre, c.abreviacion FROM levantamientoip  l  LEFT JOIN ip ON  l.idip = ip.idip JOIN cliente c ON c.idcliente = ip.idcliente ORDER BY idlevantamientoip DESC";
$query_limit_rsLevantamientos = sprintf("%s LIMIT %d, %d", $query_rsLevantamientos, $startRow_rsLevantamientos, $maxRows_rsLevantamientos);
$rsLevantamientos = mysql_query($query_limit_rsLevantamientos, $tecnocomm) or die(mysql_error());
$row_rsLevantamientos = mysql_fetch_assoc($rsLevantamientos);

if (isset($_GET['totalRows_rsLevantamientos'])) {
  $totalRows_rsLevantamientos = $_GET['totalRows_rsLevantamientos'];
} else {
  $all_rsLevantamientos = mysql_query($query_rsLevantamientos);
  $totalRows_rsLevantamientos = mysql_num_rows($all_rsLevantamientos);
}
$totalPages_rsLevantamientos = ceil($totalRows_rsLevantamientos/$maxRows_rsLevantamientos)-1;

$queryString_rsLevantamientos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsLevantamientos") == false && 
        stristr($param, "totalRows_rsLevantamientos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsLevantamientos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsLevantamientos = sprintf("&totalRows_rsLevantamientos=%d%s", $totalRows_rsLevantamientos, $queryString_rsLevantamientos);
?>
<h1>Levantamientos</h1>

<div id="buscar">
<form name="frmBuscar" method="get">
	<label>Buscar: </label>
    <input type="text" name="buscar" size="45">
    <input type="submit" value="Buscar...">
    <input type="hidden" name="mod" value="levantamientos">
</form>
</div>


<div class="distabla">
<table width="100%" cellpadding="2" cellspacing="0">
	<thead>
    	<tr>
        	<td colspan="8" align="right"><table border="0">
        	  <tr>
              	<td> Levantamientos del  <?php echo ($startRow_rsLevantamientos + 1) ?> al <?php echo min($startRow_rsLevantamientos + $maxRows_rsLevantamientos, $totalRows_rsLevantamientos) ?> de <?php echo $totalRows_rsLevantamientos ?></td>
        	    <td><?php if ($pageNum_rsLevantamientos > 0) { // Show if not first page ?>
        	        <a href="<?php printf("%s?pageNum_rsLevantamientos=%d%s", $currentPage, 0, $queryString_rsLevantamientos); ?>"><img src="images/First.gif"></a>
       	        <?php } // Show if not first page ?></td>
        	    <td><?php if ($pageNum_rsLevantamientos > 0) { // Show if not first page ?>
        	        <a href="<?php printf("%s?pageNum_rsLevantamientos=%d%s", $currentPage, max(0, $pageNum_rsLevantamientos - 1), $queryString_rsLevantamientos); ?>"><img src="images/Previous.gif"></a>
        	        <?php } // Show if not first page ?></td>
        	    <td><?php if ($pageNum_rsLevantamientos < $totalPages_rsLevantamientos) { // Show if not last page ?>
        	        <a href="<?php printf("%s?pageNum_rsLevantamientos=%d%s", $currentPage, min($totalPages_rsLevantamientos, $pageNum_rsLevantamientos + 1), $queryString_rsLevantamientos); ?>"><img src="images/Next.gif"></a>
        	        <?php } // Show if not last page ?></td>
        	    <td><?php if ($pageNum_rsLevantamientos < $totalPages_rsLevantamientos) { // Show if not last page ?>
        	        <a href="<?php printf("%s?pageNum_rsLevantamientos=%d%s", $currentPage, $totalPages_rsLevantamientos, $queryString_rsLevantamientos); ?>"><img src="images/Last.gif"></a>
        	        <?php } // Show if not last page ?></td>
      	    </tr>
      	  </table></td>
        </tr>
    	<tr>
         	<td>Opciones</td>
        	<td>Ip</td>
            <td>Cliente</td>
            <td>Concecutivo</td>
            <td>Descripcion</td>
            <td>Responsable</td>
            <td>Estado</td>
            <td>Cotizacion</td>
        </tr>
    </thead>
	<tbody>
      <?php do { ?>
  <tr>
  	<td>
    	<a href="lev.detalle.php?idip=<?php echo $row_rsLevantamientos['idip']; ?>&idlevantamiento=<?php echo $row_rsLevantamientos['idlevantamientoip']; ?>" class="popup">
        <img src="images/Edit.png"  border="0" title="DETALLE LEVANTAMIENTO"/></a>
    <a href="lev.print.formatos.php?idip=<?php echo $row_rsLevantamientos['idip']; ?>&idlevantamiento=<?php echo $row_rsLevantamientos['idlevantamientoip']; ?>" class="popup">
    <img src="images/Imprimir2.png" width="24" height="24" border="0"  title="IMPRIMIR LEVANTAMIENTO"/></a></td>
    <td><?php echo $row_rsLevantamientos['idip']; ?></td>
    <td><?php echo $row_rsLevantamientos['nombre']; ?></td>
    <td><?php echo $row_rsLevantamientos['consecutivo']; ?></td>
    <td><?php echo $row_rsLevantamientos['descripcion']; ?></td>
    <td></td>
    <td>En Proceso</td>
    <td></td>
  </tr>
  <?php } while ($row_rsLevantamientos = mysql_fetch_assoc($rsLevantamientos)); ?>
    </tbody>  
   <tfoot>
   <tr>
        	<td colspan="8" align="right"><table border="0">
        	  <tr>
              	<td> Levantamientos del  <?php echo ($startRow_rsLevantamientos + 1) ?> al <?php echo min($startRow_rsLevantamientos + $maxRows_rsLevantamientos, $totalRows_rsLevantamientos) ?> de <?php echo $totalRows_rsLevantamientos ?></td>
        	    <td><?php if ($pageNum_rsLevantamientos > 0) { // Show if not first page ?>
        	        <a href="<?php printf("%s?pageNum_rsLevantamientos=%d%s", $currentPage, 0, $queryString_rsLevantamientos); ?>"><img src="images/First.gif"></a>
       	        <?php } // Show if not first page ?></td>
        	    <td><?php if ($pageNum_rsLevantamientos > 0) { // Show if not first page ?>
        	        <a href="<?php printf("%s?pageNum_rsLevantamientos=%d%s", $currentPage, max(0, $pageNum_rsLevantamientos - 1), $queryString_rsLevantamientos); ?>"><img src="images/Previous.gif"></a>
        	        <?php } // Show if not first page ?></td>
        	    <td><?php if ($pageNum_rsLevantamientos < $totalPages_rsLevantamientos) { // Show if not last page ?>
        	        <a href="<?php printf("%s?pageNum_rsLevantamientos=%d%s", $currentPage, min($totalPages_rsLevantamientos, $pageNum_rsLevantamientos + 1), $queryString_rsLevantamientos); ?>"><img src="images/Next.gif"></a>
        	        <?php } // Show if not last page ?></td>
        	    <td><?php if ($pageNum_rsLevantamientos < $totalPages_rsLevantamientos) { // Show if not last page ?>
        	        <a href="<?php printf("%s?pageNum_rsLevantamientos=%d%s", $currentPage, $totalPages_rsLevantamientos, $queryString_rsLevantamientos); ?>"><img src="images/Last.gif"></a>
        	        <?php } // Show if not last page ?></td>
      	    </tr>
      	  </table></td>
        </tr>
   </tfoot> 
</table>
</div>
<?php
mysql_free_result($rsLevantamientos);
?>
