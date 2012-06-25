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

$maxRows_RsEmpleados = 30;
$pageNum_RsEmpleados = 0;
if (isset($_GET['pageNum_RsEmpleados'])) {
  $pageNum_RsEmpleados = $_GET['pageNum_RsEmpleados'];
}
$startRow_RsEmpleados = $pageNum_RsEmpleados * $maxRows_RsEmpleados;


if(isset($_GET['buscar']) and ($_GET['buscar']!="")){
	$sql.=" and clave like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR nombre like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR puesto like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR telefono like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR celular like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	
	
}


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsEmpleados = "SELECT *,datediff(fechatermino,now()) as termina, (select nombre from puesto where idpuesto=empleado.idpuesto) as puesto FROM empleado where 1 $sql  ORDER BY idempleado ASC";
$query_limit_RsEmpleados = sprintf("%s LIMIT %d, %d", $query_RsEmpleados, $startRow_RsEmpleados, $maxRows_RsEmpleados);
$RsEmpleados = mysql_query($query_limit_RsEmpleados, $tecnocomm) or die(mysql_error());
$row_RsEmpleados = mysql_fetch_assoc($RsEmpleados);

if (isset($_GET['totalRows_RsEmpleados'])) {
  $totalRows_RsEmpleados = $_GET['totalRows_RsEmpleados'];
} else {
  $all_RsEmpleados = mysql_query($query_RsEmpleados);
  $totalRows_RsEmpleados = mysql_num_rows($all_RsEmpleados);
}
$totalPages_RsEmpleados = ceil($totalRows_RsEmpleados/$maxRows_RsEmpleados)-1;

$queryString_RsEmpleados = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_RsEmpleados") == false && 
        stristr($param, "totalRows_RsEmpleados") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_RsEmpleados = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_RsEmpleados = sprintf("&totalRows_RsEmpleados=%d%s", $totalRows_RsEmpleados, $queryString_RsEmpleados);
?>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>

<h1> Empleados </h1>
<div class="submenu"> <a href="nuevoEmpleadoPersonales.php" onclick="NewWindow(this.href,'Nuevo Empleado',600,800,'yes'); return false;"> Empleado Nuevo </a> </div>
<div class="buscar">
<form name="buscar" action="" method="get">
<label><span>Buscar</span><input type="text" name="buscar" value="<?php echo $_GET['buscar'];?>"></label> <input name="enviar" type="submit" value="Buscar" />
<input type="hidden" name="mod" value="catempleados"/>
</form>

</div>
<a href="imprimirEmpleado.php?consulta=<?php echo $query_RsEmpleados;?>" target="_blank">IMPRIMIR CONSULTA DE EMPLEADOS</a>
<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr>
  <td colspan="6" align="right">
    <table border="0">
  <tr>
    <td><?php if ($pageNum_RsEmpleados > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_RsEmpleados=%d%s", $currentPage, 0, $queryString_RsEmpleados); ?>"><img src="images/First.gif" border="0"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_RsEmpleados > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_RsEmpleados=%d%s", $currentPage, max(0, $pageNum_RsEmpleados - 1), $queryString_RsEmpleados); ?>"><img src="images/Previous.gif" border="0"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_RsEmpleados < $totalPages_RsEmpleados) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_RsEmpleados=%d%s", $currentPage, min($totalPages_RsEmpleados, $pageNum_RsEmpleados + 1), $queryString_RsEmpleados); ?>"><img src="images/Next.gif" border="0"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_RsEmpleados < $totalPages_RsEmpleados) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_RsEmpleados=%d%s", $currentPage, $totalPages_RsEmpleados, $queryString_RsEmpleados); ?>"><img src="images/Last.gif" border="0"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>Opciones</td>
<td>Clave</td>

<td>Nombre</td>

<td>Puesto</td>
  <td>Telefono</td>
  <td>Domicilio</td>
  </tr>
</thead>
<tbody>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
    <td><a href="modificarEmpleado.php?idempleado=<?php echo $row_RsEmpleados['idempleado']; ?>" onclick="NewWindow(this.href,'Modificar Empleado',800,800,'yes');return false;"><img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR DATOS DE Empleado"/></a><a href="eliminarEmpleado.php?idempleado=<?php echo $row_RsEmpleados['idempleado']; ?>" onclick="NewWindow(this.href,'Eliminar Empleado',800,800,'yes');return false;"><img src="images/eliminar.gif" width="24" height="24" border="0" title="ELIMINAR Empleado"/></a></td>
    <td><?php echo $row_RsEmpleados['clave']; ?></td><td><?php echo $row_RsEmpleados['nombre']; ?></td>
    <td><?php echo $row_RsEmpleados['puesto']; ?></td>
      <td><?php echo $row_RsEmpleados['telefono']; ?></td>
      <td><?php echo $row_RsEmpleados['domicilio']; ?></td>
      </tr>
    <?php } while ($row_RsEmpleados = mysql_fetch_assoc($RsEmpleados)); ?>
</tbody>
<tfoot>
<tr><td colspan="6" align="right">
    <table border="0">
      <tr>
        <td><?php if ($pageNum_RsEmpleados > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_RsEmpleados=%d%s", $currentPage, 0, $queryString_RsEmpleados); ?>"><img src="images/First.gif" border="0" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_RsEmpleados > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_RsEmpleados=%d%s", $currentPage, max(0, $pageNum_RsEmpleados - 1), $queryString_RsEmpleados); ?>"><img src="images/Previous.gif" border="0" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_RsEmpleados < $totalPages_RsEmpleados) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_RsEmpleados=%d%s", $currentPage, min($totalPages_RsEmpleados, $pageNum_RsEmpleados + 1), $queryString_RsEmpleados); ?>"><img src="images/Next.gif" border="0" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_RsEmpleados < $totalPages_RsEmpleados) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_RsEmpleados=%d%s", $currentPage, $totalPages_RsEmpleados, $queryString_RsEmpleados); ?>"><img src="images/Last.gif" border="0" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr></tfoot>
</table>
</div>
<?php
mysql_free_result($RsEmpleados);
?>
