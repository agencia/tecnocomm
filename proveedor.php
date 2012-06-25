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

$select =  "SELECT * FROM proveedor ORDER BY nombrecomercial ASC";

if(isset($_GET['buscar'])){

$bus = "%".$_GET['buscar']."%";
$campo = array(1=>"nombrecomercial",2=>"razonsocial",3=>"contacto");

$ref = $campo[$_GET['tipoBusqueda']];

$select = "SELECT * FROM proveedor  WHERE ".$ref." like ".GetSQLValueString($bus, "text")." ORDER BY nombrecomercial ASC";

}




$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsProveedor = 30;
$pageNum_rsProveedor = 0;
if (isset($_GET['pageNum_rsProveedor'])) {
  $pageNum_rsProveedor = $_GET['pageNum_rsProveedor'];
}
$startRow_rsProveedor = $pageNum_rsProveedor * $maxRows_rsProveedor;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedor = $select;
$query_limit_rsProveedor = sprintf("%s LIMIT %d, %d", $query_rsProveedor, $startRow_rsProveedor, $maxRows_rsProveedor);
$rsProveedor = mysql_query($query_limit_rsProveedor, $tecnocomm) or die(mysql_error());
$row_rsProveedor = mysql_fetch_assoc($rsProveedor);

if (isset($_GET['totalRows_rsProveedor'])) {
  $totalRows_rsProveedor = $_GET['totalRows_rsProveedor'];
} else {
  $all_rsProveedor = mysql_query($query_rsProveedor);
  $totalRows_rsProveedor = mysql_num_rows($all_rsProveedor);
}
$totalPages_rsProveedor = ceil($totalRows_rsProveedor/$maxRows_rsProveedor)-1;

$queryString_rsProveedor = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsProveedor") == false && 
        stristr($param, "totalRows_rsProveedor") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsProveedor = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsProveedor = sprintf("&totalRows_rsProveedor=%d%s", $totalRows_rsProveedor, $queryString_rsProveedor);



?>
<table width="915" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td height="25" colspan="4" valign="top" class="titulos">CATALOGO DE PROVEEDORES</td>
  </tr>
  <tr>
    <td width="10" height="7"></td>
    <td width="581"></td>
    <td width="313"></td>
    <td width="11"></td>
  </tr>
  
  <tr>
    <td height="28" colspan="4" align="center" valign="top"><a href="nuevoProveedor.php" onClick="NewWindow(this.href,'Nueva Proveedor','700','500','no');return false"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" /></strong></a><a href="nuevoProveedor.php"  onClick="NewWindow(this.href,'Nuevo Proveedor',700,330,'yes');return false;">NUEVO PROVEEDOR</a><a href="nuevoProveedor.php" onclick="NewWindow(this.href,'Nueva Proveedor','700','500','no');return false"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" /></strong></a><a href="proveedorestoexcel.php"  onclick="NewWindow(this.href,'Nuevo Proveedor',700,330,'yes');return false;">EXPORTAR EXCEL</a></td>
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
      <form name="buscar2" method="get">
        BUSCAR: 
          <input name="buscar" type="text" id="buscar" value="<?php echo $_GET['buscar'];?>" />
        <label>
        <select name="tipoBusqueda" id="tipoBusqueda">
          <option value="1" <?php if (!(strcmp(1, $_GET['tipoBusqueda']))) {echo "selected=\"selected\"";} ?>>NOMBRE COMERCIAL</option>
          <option value="2" <?php if (!(strcmp(2, $_GET['tipoBusqueda']))) {echo "selected=\"selected\"";} ?>>RAZON SOCIAL</option>
          <option value="3" <?php if (!(strcmp(3, $_GET['tipoBusqueda']))) {echo "selected=\"selected\"";} ?>>CONTACTO</option>
        </select>
        </label>
        <input type="submit" name="buscar2" id="buscar2" value="BUSCAR" />
        <input type="hidden" name="mod" value="proveedores">
         </form></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="5"></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  
  
  

  <tr>
    <td height="27" colspan="4" align="right" valign="top"><a href="<?php printf("%s?pageNum_rsProveedor=%d%s", $currentPage, 0, $queryString_rsProveedor); ?>">
    <?php if ($pageNum_rsProveedor > 0) { // Show if not first page ?>
      <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
</a> <a href="<?php printf("%s?pageNum_rsProveedor=%d%s", $currentPage, max(0, $pageNum_rsProveedor - 1), $queryString_rsProveedor); ?>">
<?php if ($pageNum_rsProveedor > 0) { // Show if not first page ?>
  <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
  <?php } // Show if not first page ?>
</a> <a href="<?php printf("%s?pageNum_rsProveedor=%d%s", $currentPage, min($totalPages_rsProveedor, $pageNum_rsProveedor + 1), $queryString_rsProveedor); ?>">
<?php if ($pageNum_rsProveedor >= $totalPages_rsProveedor) { // Show if last page ?>
  <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
  <?php } // Show if last page ?>
</a> <a href="<?php printf("%s?pageNum_rsProveedor=%d%s", $currentPage, $totalPages_rsProveedor, $queryString_rsProveedor); ?>">
<?php if ($pageNum_rsProveedor < $totalPages_rsProveedor) { // Show if not last page ?>
  <img src="images/Last.gif" alt="ultimo" width="24" height="24" border="0" />
  <?php } // Show if not last page ?>
</a> </td>
  </tr>
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="52"></td>
    <td colspan="3" align="center" valign="top"><table border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="10" height="24">&nbsp;</td>
        <td width="108" valign="top">OPCIONES</td>
        <td width="221" valign="middle">NOMBRE COMERCIAL</td>
        <td width="258" valign="middle">CONTACTO</td>
        <td width="146" valign="middle">TELEFONO</td>
        <td width="161" valign="top">E-MAIL</td>
      </tr>
      <?php do { ?>
        <tr  onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onMouseOut="this.style.backgroundColor = '';">
          <td height="28"></td>
          <td valign="top"><a name="p<?php echo $row_rsProveedor['idproveedor']; ?>"> </A><a href="modificarProveedor.php?idproveedor=<?php echo $row_rsProveedor['idproveedor']; ?>" onClick="NewWindow(this.href,'Modificar Proveedor','700','300','no');return false" ><img src="images/Edit.png" border="0" title="Modificar Datos de Proveedor"></a><a href="#p<?php echo $row_rsProveedor['idproveedor']; ?>" onClick="NewWindow('detalleProveedor.php?idproveedor=<?php echo $row_rsProveedor['idproveedor']; ?>','Detalle Proveedor','700','300','no');" ><img  src="images/View Details 16 n g.gif" border="0" title="Ver Detalles"></a>     <a href="eliminarProveedor.php?idproveedor=<?php echo $row_rsProveedor['idproveedor']; ?>" onClick="NewWindow(this.href,'Detalle Proveedor','700','300','no');return false" >     <img src="images/eliminar.gif" width="19" height="19" /></a></td>
          <td valign="top"><?php echo valida("proveedor","idproveedor",$row_rsProveedor['idproveedor']); echo $row_rsProveedor['nombrecomercial']; ?>(<?php echo $row_rsProveedor['clave'];?>)</td>
          <td valign="top"><?php echo $row_rsProveedor['contacto']; ?></td>
          <td valign="top"><?php echo $row_rsProveedor['telefono']; ?></td>
          <td valign="top"><?php echo $row_rsProveedor['email']; ?></td>
        </tr>
        <?php } while ($row_rsProveedor = mysql_fetch_assoc($rsProveedor)); ?>
      <tr>
        <td height="0"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </table></td>
  </tr>
  <?php if ($totalRows_rsProveedor== 0) { // Show if recordset empty ?>
    <tr>
      <td height="18"></td>
      <td colspan="2" align="center" valign="top">NO SE ENCONTRO NINGUN RESULTADO CON LA PALABRA: <?php echo $_GET['buscar'];?>     </td>
      <td>&nbsp;</td>
    </tr>
       <?php } ?>
    <tr>
      <td height="15"></td>
      <td colspan="3" align="center"><a href="<?php printf("%s?pageNum_rsProveedor=%d%s", $currentPage, 0, $queryString_rsProveedor); ?>">
        <?php if ($pageNum_rsProveedor > 0) { // Show if not first page ?>
        <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
        <?php } // Show if not first page ?>
      </a> <a href="<?php printf("%s?pageNum_rsProveedor=%d%s", $currentPage, max(0, $pageNum_rsProveedor - 1), $queryString_rsProveedor); ?>">
      <?php if ($pageNum_rsProveedor > 0) { // Show if not first page ?>
      <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
      </a> <a href="<?php printf("%s?pageNum_rsProveedor=%d%s", $currentPage, min($totalPages_rsProveedor, $pageNum_rsProveedor + 1), $queryString_rsProveedor); ?>">
      <?php if ($pageNum_rsProveedor >= $totalPages_rsProveedor) { // Show if last page ?>
      <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
      <?php } // Show if last page ?>
      </a> <a href="<?php printf("%s?pageNum_rsProveedor=%d%s", $currentPage, $totalPages_rsProveedor, $queryString_rsProveedor); ?>">
      <?php if ($pageNum_rsProveedor < $totalPages_rsProveedor) { // Show if not last page ?>
      <img src="images/Last.gif" alt="ultimo" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
      </a></td>
    </tr>
</table>





<?php

mysql_free_result($rsProveedor);
?>
