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

$maxRows_rsPuestos = 30;
$pageNum_rsPuestos = 0;
if (isset($_GET['pageNum_rsPuestos'])) {
  $pageNum_rsPuestos = $_GET['pageNum_rsPuestos'];
}
$startRow_rsPuestos = $pageNum_rsPuestos * $maxRows_rsPuestos;

if(isset($_GET['buscar']) and ($_GET['buscar']!="")){
	$sql.=" and  clave like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR idpuesto like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR descripcion like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR sueldo like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	
	
	
	
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPuestos = "SELECT * FROM puesto WHERE 1 $sql";
$rsPuestos = mysql_query($query_rsPuestos, $tecnocomm) or die(mysql_error());
$row_rsPuestos = mysql_fetch_assoc($rsPuestos);
$totalRows_rsPuestos = mysql_num_rows($rsPuestos);

$queryString_rsPuestos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsPuestos") == false && 
        stristr($param, "totalRows_rsPuestos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsPuestos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsPuestos = sprintf("&totalRows_rsPuestos=%d%s", $totalRows_rsPuestos, $queryString_rsPuestos);
?>
<h1> Puestos </h1>
<div class="submenu"> <a href="nuevo.puesto.php" onclick="NewWindow(this.href,'Nuevo Puesto',600,800,'yes'); return false;"> Puesto Nuevo </a> </div>
<div class="buscar"><form name="buscar" action="" method="get">
<label><span>Buscar</span><input type="text" name="buscar" value="<?php echo $_GET['buscar'];?>"></label> <input name="enviar" type="submit" value="Buscar" />
<input type="hidden" name="mod" value="listapuestos"/>
</form></div>
<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="6" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsPuestos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsPuestos=%d%s", $currentPage, 0, $queryString_rsPuestos); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsPuestos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsPuestos=%d%s", $currentPage, max(0, $pageNum_rsPuestos - 1), $queryString_rsPuestos); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsPuestos < $totalPages_rsPuestos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsPuestos=%d%s", $currentPage, min($totalPages_rsPuestos, $pageNum_rsPuestos + 1), $queryString_rsPuestos); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsPuestos < $totalPages_rsPuestos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsPuestos=%d%s", $currentPage, $totalPages_rsPuestos, $queryString_rsPuestos); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>Opciones</td>
<td>Clave</td>
<td>Nombre</td>
<td>Descripcion</td>
  <td>Funciones</td>
  <td>Sueldo</td>
</tr>
</thead>
<tbody>
 <?php if ($totalRows_rsPuestos > 0) { // Show if recordset not empty ?>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
     
        <td valign="top"><a href="editar.puesto.php?idpuesto=<?php echo $row_rsPuestos['idpuesto']; ?>" onclick="NewWindow(this.href,'Modificar Banco',800,800,'yes');return false;"><img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR DATOS DE BANCO"/></a><a href="eliminar.puesto.php?idpuesto=<?php echo $row_rsPuestos['idpuesto']; ?>" onclick="NewWindow(this.href,'Eliminar Banco',800,800,'yes');return false;"><img src="images/eliminar.gif" width="24" height="24" border="0" title="ELIMINAR BANCO"/></a></td>
        <td valign="top"><?php echo $row_rsPuestos['clave']; ?></td>
        <td valign="top"><?php echo $row_rsPuestos['nombre']; ?></td>
        <td valign="top"><?php echo $row_rsPuestos['descripcion']; ?></td>
        <td valign="top"><?php echo $row_rsPuestos['funciones']; ?></td>
        <td valign="top"><?php echo format_money($row_rsPuestos['sueldo']); ?></td>
        
</tr>
    <?php } while ($row_rsPuestos = mysql_fetch_assoc($rsPuestos)); ?>
    <?php } // Show if recordset not empty ?>
</tbody>
<tfoot>
<tr><td colspan="6" align="right">
    <table border="0">
      <tr>
        <td><?php if ($pageNum_rsPuestos > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsPuestos=%d%s", $currentPage, 0, $queryString_rsPuestos); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsPuestos > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsPuestos=%d%s", $currentPage, max(0, $pageNum_rsPuestos - 1), $queryString_rsPuestos); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsPuestos < $totalPages_rsPuestos) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsPuestos=%d%s", $currentPage, min($totalPages_rsPuestos, $pageNum_rsPuestos + 1), $queryString_rsPuestos); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsPuestos < $totalPages_rsPuestos) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsPuestos=%d%s", $currentPage, $totalPages_rsPuestos, $queryString_rsPuestos); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr></tfoot>
</table>
</div>
<?php
mysql_free_result($rsPuestos);
?>
