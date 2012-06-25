<?php require_once('Connections/tecnocomm.php'); ?>
<?php
require_once('lib/validacion.php');
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

$sql = "SELECT * FROM articulo ORDER BY nombre ASC";
if(isset($_GET['buscar'])){


$sql = "SELECT * FROM articulo WHERE nombre like \"%".$_GET['buscar']."%\" OR  marca like \"%".$_GET['buscar']."%\" OR codigo like \"%".$_GET['buscar']."%\" OR idarticulo like \"%".$_GET['buscar']."%\" ORDER BY nombre ASC";
			}
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsProductos = 30;
$pageNum_rsProductos = 0;
if (isset($_GET['pageNum_rsProductos'])) {
  $pageNum_rsProductos = $_GET['pageNum_rsProductos'];
}
$startRow_rsProductos = $pageNum_rsProductos * $maxRows_rsProductos;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProductos = $sql;
$query_limit_rsProductos = sprintf("%s LIMIT %d, %d", $query_rsProductos, $startRow_rsProductos, $maxRows_rsProductos);
$rsProductos = mysql_query($query_limit_rsProductos, $tecnocomm) or die(mysql_error());
$row_rsProductos = mysql_fetch_assoc($rsProductos);

if (isset($_GET['totalRows_rsProductos'])) {
  $totalRows_rsProductos = $_GET['totalRows_rsProductos'];
} else {
  $all_rsProductos = mysql_query($query_rsProductos);
  $totalRows_rsProductos = mysql_num_rows($all_rsProductos);
}
$totalPages_rsProductos = ceil($totalRows_rsProductos/$maxRows_rsProductos)-1;

$queryString_rsProductos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsProductos") == false && 
        stristr($param, "totalRows_rsProductos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsProductos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsProductos = sprintf("&totalRows_rsProductos=%d%s", $totalRows_rsProductos, $queryString_rsProductos);
?>
<?php 

$moneda =  array(1=>"US$",0=>"$");

?><script language="javascript" type="text/javascript">
			$(document).ready(function(){
				$("#buscar").focus();
			});
            </script>
<table width="100%"border="0" cellpadding="0" cellspacing="0" >
  <!--DWLayoutTable-->
  <tr>
    <td height="25" colspan="4" valign="top" class="titulos">Catalago Productos y Servicios</td>
  </tr>
  <tr>
    <td width="10" height="7"></td>
    <td width="772"></td>
    <td width="141"></td>
    <td width="13"></td>
  </tr>
  
  <tr>
    <td height="28" colspan="4" align="center" valign="top"><a href="nuevoProducto.php" onclick="NewWindow(this.href,'Nueva Cotizacion','500','500','no');return false"><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" /></a><a href="nuevoProducto.php"  onClick="NewWindow(this.href,'Nuevo Cliente',400,230,'yes');return false;">Nuevo Producto</a> <a href="toexcel.php"><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" />Exportar a Excel</a></td>
  </tr>
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="24"></td>
    <td valign="middle">
      <form name="buscar" method="get">
        Buscar: <input name="buscar" type="text" id="buscar"  size="40" value="<?php echo $_GET['buscar'];?>" tabindex="1"/>
       
        <input type="submit" name="buscar2" id="buscar2" value="Buscar" />
        <input type="hidden" name="mod" value="prodyserv">
     </form></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="10"></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="41" colspan="3" align="right" valign="top"><?php if ($totalRows_rsProductos > 0) { // Show if recordset not empty ?>
        Estas viendo del <?php echo ($startRow_rsProductos + 1) ?>al <?php echo min($startRow_rsProductos + $maxRows_rsProductos, $totalRows_rsProductos) ?>de un total de <?php echo $totalRows_rsProductos ?>
  <?php } // Show if recordset not empty ?>
      
      <a href="<?php printf("%s?pageNum_rsProductos=%d%s", $currentPage, 0, $queryString_rsProductos); ?>">
      <?php if ($pageNum_rsProductos > 0) { // Show if not first page ?>
      <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
</a> <a href="<?php printf("%s?pageNum_rsProductos=%d%s", $currentPage, max(0, $pageNum_rsProductos - 1), $queryString_rsProductos); ?>">
<?php if ($pageNum_rsProductos > 0) { // Show if not first page ?>
  <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
  <?php } // Show if not first page ?>
</a> <a href="<?php printf("%s?pageNum_rsProductos=%d%s", $currentPage, min($totalPages_rsProductos, $pageNum_rsProductos + 1), $queryString_rsProductos); ?>">
<?php if ($pageNum_rsProductos < $totalPages_rsProductos) { // Show if not last page ?>
  <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
  <?php } // Show if not last page ?>
</a> <a href="<?php printf("%s?pageNum_rsProductos=%d%s", $currentPage, $totalPages_rsProductos, $queryString_rsProductos); ?>">
<?php if ($pageNum_rsProductos < $totalPages_rsProductos) { // Show if not last page ?>
  <img src="images/Last.gif" alt="ultimo" width="24" height="24" border="0" />
  <?php } // Show if not last page ?>
</a> </td>
  <td></td>
  </tr>
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  <tr>
    
    <td colspan="4" align="center" valign="top"><table border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="5" height="24">&nbsp;</td>
        <td width="72" valign="top">Opciones</td>
        <td width="147" valign="middle">Clave</td>
        <td width="133" valign="middle">Codigo</td>
        <td width="129" valign="middle">Marca</td>
        <td width="264" valign="middle">Descripcion</td>
        <td width="86" valign="middle">Costo Inst.</td>
        <td valign="middle" colspan="2">Precio</td>
        <td width="32">Tipo moneda</td>
      </tr><?php if ($totalRows_rsProductos > 0) { // Show if recordset empty ?>
      <?php do { ?>
        <tr  onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
          <td height="28"></td>
          <td valign="top"><a href="#" name="<?php echo $row_rsProductos['idarticulo'];?>"><a  href="#<?php echo $row_rsProductos['idarticulo'];?>"  onclick="NewWindow('modificarProducto.php?idarticulo=<?php echo $row_rsProductos['idarticulo'];?>','Modificar Cliente',800,800,'yes');"><img src="images/Edit.png" width="19" title="Modificar Producto"></a><a href="#<?php echo $row_rsProductos['idarticulo'];?>" onclick="NewWindow('eliminarProducto.php?idarticulo=<?php echo $row_rsProductos['idarticulo'];?>','Modificar Cliente','800','800','yes');"><img src="images/eliminar.gif" width="19" height="19" /></a></td>
          <td valign="top"><?php echo valida("articulo","idarticulo",$row_rsProductos['idarticulo']);echo $row_rsProductos['clave']; ?></td>
          <td valign="top"><?php echo $row_rsProductos['codigo']; ?></td>
          <td valign="top"><?php echo $row_rsProductos['marca']; ?></td>
          <td valign="top"><?php echo $row_rsProductos['nombre']; ?></td>
          <td valign="top"><?php echo $row_rsProductos['instalacion']; ?></td>
          <td width="80" align="left" valign="top"><?php echo $row_rsProductos['precio']; ?></td>
          <td width="30" align="right" valign="top">  <?php echo $moneda[$row_rsProductos['moneda']]; ?></td>
          <td width="30" align="right" valign="top">  <?php echo ($row_rsProductos['tipo'] == 0) ? "PL":"C"; ?></td>
        </tr>
        <?php } while ($row_rsProductos = mysql_fetch_assoc($rsProductos)); ?>
        <?php } ?>
      <tr>
        <td height="0"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      
    </table></td>
  </tr>
  <?php if ($totalRows_rsProductos == 0) { // Show if recordset empty ?>
      <tr>
        <td height="43"></td>
        <td colspan="2" align="center" valign="top">No se encontro ningun resultado con la palabra: en:</td>
        <td>&nbsp;</td>
      </tr> <?php } // Show if recordset empty ?>
    <tr>
      <td height="43"></td>
      <td colspan="2" align="right" valign="top"><a href="<?php printf("%s?pageNum_rsProductos=%d%s", $currentPage, 0, $queryString_rsProductos); ?>">
      <?php if ($pageNum_rsProductos > 0) { // Show if not first page ?>
        <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
        <?php } // Show if not first page ?>
      </a> <a href="<?php printf("%s?pageNum_rsProductos=%d%s", $currentPage, max(0, $pageNum_rsProductos - 1), $queryString_rsProductos); ?>">
      <?php if ($pageNum_rsProductos > 0) { // Show if not first page ?>
      <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
      </a> <a href="<?php printf("%s?pageNum_rsProductos=%d%s", $currentPage, min($totalPages_rsProductos, $pageNum_rsProductos + 1), $queryString_rsProductos); ?>">
      <?php if ($pageNum_rsProductos < $totalPages_rsProductos) { // Show if not last page ?>
      <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
      </a> <a href="<?php printf("%s?pageNum_rsProductos=%d%s", $currentPage, $totalPages_rsProductos, $queryString_rsProductos); ?>">
      <?php if ($pageNum_rsProductos < $totalPages_rsProductos) { // Show if not last page ?>
      <img src="images/Last.gif" alt="ultimo" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
      </a></td>
      <td>&nbsp;</td>
    </tr>
</table>

<?php
mysql_free_result($rsProductos);
?>
