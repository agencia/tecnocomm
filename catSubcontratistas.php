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

$maxRows_RsSub = 30;
$pageNum_RsSub = 0;
if (isset($_GET['pageNum_RsSub'])) {
  $pageNum_RsSub = $_GET['pageNum_RsSub'];
}
$startRow_RsSub = $pageNum_RsSub * $maxRows_RsSub;

if(isset($_GET['buscar']) and ($_GET['buscar']!="")){
	$sql.=" and  nombre like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR abreviacion like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	
	
	
}


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSub = "SELECT * FROM subcontratistas WHERE 1 $sql ORDER BY id ASC";
$query_limit_RsSub = sprintf("%s LIMIT %d, %d", $query_RsSub, $startRow_RsSub, $maxRows_RsSub);
$RsSub = mysql_query($query_limit_RsSub, $tecnocomm) or die(mysql_error());
$row_RsSub = mysql_fetch_assoc($RsSub);

if (isset($_GET['totalRows_RsSub'])) {
  $totalRows_RsSub = $_GET['totalRows_RsSub'];
} else {
  $all_RsSub = mysql_query($query_RsSub);
  $totalRows_RsSub = mysql_num_rows($all_RsSub);
}
$totalPages_RsSub = ceil($totalRows_RsSub/$maxRows_RsSub)-1;

$queryString_RsSub = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_RsSub") == false && 
        stristr($param, "totalRows_RsSub") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_RsSub = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_RsSub = sprintf("&totalRows_RsSub=%d%s", $totalRows_RsSub, $queryString_RsSub);
?>
<h1> SubContratistas </h1>
<div class="submenu"> <a href="nuevoSubcontratista.php" onclick="NewWindow(this.href,'Nuevo Banco',600,800,'yes'); return false;"> Subcontratista Nuevo </a> </div>
<div class="buscar"><form name="buscar" action="" method="get">
<label><span>Buscar</span><input type="text" name="buscar" value="<?php echo $_GET['buscar'];?>"></label> <input name="enviar" type="submit" value="Buscar" />
<input type="hidden" name="mod" value="catsubcontratistas"/>
</form></div>
<a href="imprmirSubcontratistas.php?consulta=<?php echo $query_RsSub;?>" target="_blank">IMPRIMIR CONSULTA DE SUBCONTRATISTAS</a>
<div class="distabla">
<table width="100%">
<thead>
<tr><td colspan="8" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_RsSub > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_RsSub=%d%s", $currentPage, 0, $queryString_RsSub); ?>"><img src="First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_RsSub > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_RsSub=%d%s", $currentPage, max(0, $pageNum_RsSub - 1), $queryString_RsSub); ?>"><img src="Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_RsSub < $totalPages_RsSub) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_RsSub=%d%s", $currentPage, min($totalPages_RsSub, $pageNum_RsSub + 1), $queryString_RsSub); ?>"><img src="Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_RsSub < $totalPages_RsSub) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_RsSub=%d%s", $currentPage, $totalPages_RsSub, $queryString_RsSub); ?>"><img src="Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>Opciones</td>
<td>Nombre</td>
<td>Abreviacion</td>
<td>Direccion</td>
<td>Telefonos</td>
<td>Celulares</td>
<td>Correos</td>
<td>Fecha de Inico</td>
</tr>
</thead>
<tbody>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
    <td><a href="modificarSubcontratista.php?id=<?php echo $row_RsSub['id']; ?>" onclick="NewWindow(this.href,'Modificar Subcontratista',800,800,'yes');return false;"><img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR DATOS DE SUBCONTRATISTA"/></a><a href="eliminarSubcontratista.php?id=<?php echo $row_RsSub['id']; ?>" onclick="NewWindow(this.href,'Eliminar Subcontratista',800,800,'yes');return false;"><img src="images/eliminar.gif" width="24" height="24" border="0" title="ELIMINAR SUBCONTRATISTA"/></a></td>
    <td><?php echo $row_RsSub['nombre']; ?>(<?php echo $row_RsSub['clave'];?>)</td><td><?php echo $row_RsSub['abreviacion']; ?></td><td><?php echo $row_RsSub['calle']; ?>,Col:<?php echo $row_RsSub['colonia']; ?>,<?php echo $row_RsSub['ciudad']; ?>,<?php echo $row_RsSub['estado']; ?></td>
    <td><?php echo $row_RsSub['tel1']; ?>/<?php echo $row_RsSub['tel2']; ?></td>
    <td><?php echo $row_RsSub['cel1']; ?>/<?php echo $row_RsSub['cel2']; ?></td>
    <td><?php echo $row_RsSub['correo1']; ?>/<?php echo $row_RsSub['correo2']; ?></td>
    <td><?php echo $row_RsSub['fecha_inicio']; ?></td>
    </tr>
    <?php } while ($row_RsSub = mysql_fetch_assoc($RsSub)); ?>
</tbody>
<tfoot>
<tr><td colspan="8" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_RsSub > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_RsSub=%d%s", $currentPage, 0, $queryString_RsSub); ?>"><img src="First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_RsSub > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_RsSub=%d%s", $currentPage, max(0, $pageNum_RsSub - 1), $queryString_RsSub); ?>"><img src="Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_RsSub < $totalPages_RsSub) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_RsSub=%d%s", $currentPage, min($totalPages_RsSub, $pageNum_RsSub + 1), $queryString_RsSub); ?>"><img src="Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_RsSub < $totalPages_RsSub) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_RsSub=%d%s", $currentPage, $totalPages_RsSub, $queryString_RsSub); ?>"><img src="Last.gif"></a>
        <?php } // Show if not last page ?></td></tr></table></td></tr></tfoot>

</table>
</div>
<?php
mysql_free_result($RsSub);
?>
