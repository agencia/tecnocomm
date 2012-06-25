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

if(isset($_GET['buscar'])) {

 $query = "SELECT p.*,c.*  FROM cuentasporpagar c, proveedor p WHERE c.idproveedor = p.idproveedor AND p.nombrecomercial like ". GetSQLValueString("%".$_GET['buscar']."%","text")." GROUP BY p.idproveedor";


}
else{
$query = "SELECT p.*,c.*  FROM cuentasporpagar c, proveedor p WHERE c.idproveedor = p.idproveedor  GROUP BY p.idproveedor";

}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsCuentas = 30;
$pageNum_rsCuentas = 0;
if (isset($_GET['pageNum_rsCuentas'])) {
  $pageNum_rsCuentas = $_GET['pageNum_rsCuentas'];
}
$startRow_rsCuentas = $pageNum_rsCuentas * $maxRows_rsCuentas;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCuentas = $query; //
$query_limit_rsCuentas = sprintf("%s LIMIT %d, %d", $query_rsCuentas, $startRow_rsCuentas, $maxRows_rsCuentas);
$rsCuentas = mysql_query($query_limit_rsCuentas, $tecnocomm) or die(mysql_error());
$row_rsCuentas = mysql_fetch_assoc($rsCuentas);

if (isset($_GET['totalRows_rsCuentas'])) {
  $totalRows_rsCuentas = $_GET['totalRows_rsCuentas'];
} else {
  $all_rsCuentas = mysql_query($query_rsCuentas);
  $totalRows_rsCuentas = mysql_num_rows($all_rsCuentas);
}
$totalPages_rsCuentas = ceil($totalRows_rsCuentas/$maxRows_rsCuentas)-1;

$queryString_rsCuentas = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsCuentas") == false && 
        stristr($param, "totalRows_rsCuentas") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsCuentas = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsCuentas = sprintf("&totalRows_rsCuentas=%d%s", $totalRows_rsCuentas, $queryString_rsCuentas);
?><table width="924" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td height="25" colspan="3" valign="top" class="titulos">CUENTAS POR PAGAR</td>
    <td width="9"></td>
  </tr>
  <tr>
    <td width="10" height="7"></td>
    <td width="520"></td>
    <td width="385"></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="28" colspan="3" align="center" valign="top"><a href="agregarCuenta.php" onClick="NewWindow(this.href,'Nueva Cotizacion','500','500','no');return false"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" /></strong>Agregar Cuenta</a> <a href="cuentasxpagar.reporte.php" class="popup"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" /></strong>Ver Detalle</a></td>
    <td></td>
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
        Buscar: <input name="buscar" type="text" id="buscar" />
        <input type="submit" name="buscar2" id="buscar2" value="Buscar" />
        <input type="hidden" name="mod" value="porpagar">
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
    <td height="27" colspan="3" align="right" valign="top"><a href="<?php printf("%s?pageNum_rsCuentas=%d%s", $currentPage, 0, $queryString_rsCuentas); ?>">
    <?php if ($pageNum_rsCuentas > 0) { // Show if not first page ?>
      <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
</a> <a href="<?php printf("%s?pageNum_rsCuentas=%d%s", $currentPage, max(0, $pageNum_rsCuentas - 1), $queryString_rsCuentas); ?>">
    <?php if ($pageNum_rsCuentas > 0) { // Show if not first page ?>
      <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
</a> <a href="<?php printf("%s?pageNum_rsCuentas=%d%s", $currentPage, min($totalPages_rsCuentas, $pageNum_rsCuentas + 1), $queryString_rsCuentas); ?>">
    <?php if ($pageNum_rsCuentas < $totalPages_rsCuentas) { // Show if not last page ?>
      <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
</a> <a href="<?php printf("%s?pageNum_rsCuentas=%d%s", $currentPage, $totalPages_rsCuentas, $queryString_rsCuentas); ?>">
    <?php if ($pageNum_rsCuentas < $totalPages_rsCuentas) { // Show if not last page ?>
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
    <td height="56"></td>
    <td colspan="2" align="center" valign="top"><table border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="10" height="24">&nbsp;</td>
        <td width="80" valign="top">Opciones</td>
        <td width="194" valign="middle">Proveedor</td>
        <td width="238" valign="middle">Contacto</td>
        <td width="126" valign="middle">Telefono</td>
        <td width="178" valign="top">email</td>
        <td width="79">&nbsp;</td>
      </tr>
          
           <?php do { ?>
             <tr  onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onMouseOut="this.style.backgroundColor = '';">
               <td height="24"></td>
               <td valign="top"><a name="<?php echo $row_rsCuentas['idproveedor']; ?>" href="#<?php echo $row_rsCuentas['idproveedor']; ?>" onClick="NewWindow('cuentasPorPagarDetalle.php?idproveedor=<?php echo $row_rsCuentas['idproveedor']; ?>','Modificar Cliente','500','300','no');"><img src="images/Stacked Documents 24 h p.png" width="24" height="24" title="Ver Detalle y Abonar"/></a>
</td>
               <td valign="top"><?php echo $row_rsCuentas['nombrecomercial']; ?></td>
               <td valign="top"><?php echo $row_rsCuentas['contacto']; ?></td>
               <td valign="top"><?php echo $row_rsCuentas['telefono']; ?></td>
               <td valign="top"><?php echo $row_rsCuentas['email']; ?></td>
               <td>&nbsp;</td>
             </tr>
             <?php } while ($row_rsCuentas = mysql_fetch_assoc($rsCuentas)); ?>
          
<tr>
        <td height="4"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      
    </table></td>
    <td></td>
  </tr>
  
    <tr>
      <td height="27"></td>
      <td colspan="2" align="right" valign="top"><a href="<?php printf("%s?pageNum_rsCuentas=%d%s", $currentPage, 0, $queryString_rsCuentas); ?>">
        <?php if ($pageNum_rsCuentas > 0) { // Show if not first page ?>
        <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
        <?php } // Show if not first page ?>
      </a> <a href="<?php printf("%s?pageNum_rsCuentas=%d%s", $currentPage, max(0, $pageNum_rsCuentas - 1), $queryString_rsCuentas); ?>">
      <?php if ($pageNum_rsCuentas > 0) { // Show if not first page ?>
      <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
      </a> <a href="<?php printf("%s?pageNum_rsCuentas=%d%s", $currentPage, min($totalPages_rsCuentas, $pageNum_rsCuentas + 1), $queryString_rsCuentas); ?>">
      <?php if ($pageNum_rsCuentas < $totalPages_rsCuentas) { // Show if not last page ?>
      <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
      </a> <a href="<?php printf("%s?pageNum_rsCuentas=%d%s", $currentPage, $totalPages_rsCuentas, $queryString_rsCuentas); ?>">
      <?php if ($pageNum_rsCuentas < $totalPages_rsCuentas) { // Show if not last page ?>
      <img src="images/Last.gif" alt="ultimo" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
      </a></td>
      <td></td>
    </tr>
    <tr>
      <td height="20"></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
    </tr>
</table>
<?php
mysql_free_result($rsCuentas);
?>
