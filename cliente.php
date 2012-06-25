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

if(isset($_GET['buscar'])){
		$param = "%".$_GET['buscar']."%";
		$query = "SELECT * FROM cliente WHERE nombre like ".GetSQLValueString($param, "text")." OR abreviacion like ".GetSQLValueString($param, "text")." OR razonsocial like ".GetSQLValueString($param, "text")."  ORDER BY nombre";	
}else
		$query = "SELECT * FROM cliente ORDER BY nombre";
		
		
$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsClientes2 = 1;
$pageNum_rsClientes2 = 0;
if (isset($_GET['pageNum_rsClientes2'])) {
  $pageNum_rsClientes2 = $_GET['pageNum_rsClientes2'];
}
$startRow_rsClientes2 = $pageNum_rsClientes2 * $maxRows_rsClientes2;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsClientes2 = "SELECT * FROM cliente ORDER BY nombre";
$query_limit_rsClientes2 = sprintf("%s LIMIT %d, %d", $query_rsClientes2, $startRow_rsClientes2, $maxRows_rsClientes2);
$rsClientes2 = mysql_query($query_limit_rsClientes2, $tecnocomm) or die(mysql_error());
$row_rsClientes2 = mysql_fetch_assoc($rsClientes2);

if (isset($_GET['totalRows_rsClientes2'])) {
  $totalRows_rsClientes2 = $_GET['totalRows_rsClientes2'];
} else {
  $all_rsClientes2 = mysql_query($query_rsClientes2);
  $totalRows_rsClientes2 = mysql_num_rows($all_rsClientes2);
}
$totalPages_rsClientes2 = ceil($totalRows_rsClientes2/$maxRows_rsClientes2)-1;

$maxRows_rsClientes = 30;
$pageNum_rsClientes = 0;
if (isset($_GET['pageNum_rsClientes'])) {
  $pageNum_rsClientes = $_GET['pageNum_rsClientes'];
}
$startRow_rsClientes = $pageNum_rsClientes * $maxRows_rsClientes;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsClientes = $query;
$query_limit_rsClientes = sprintf("%s LIMIT %d, %d", $query_rsClientes, $startRow_rsClientes, $maxRows_rsClientes);
$rsClientes = mysql_query($query_limit_rsClientes, $tecnocomm) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);

if (isset($_GET['totalRows_rsClientes'])) {
  $totalRows_rsClientes = $_GET['totalRows_rsClientes'];
} else {
  $all_rsClientes = mysql_query($query_rsClientes);
  $totalRows_rsClientes = mysql_num_rows($all_rsClientes);
}
$totalPages_rsClientes = ceil($totalRows_rsClientes/$maxRows_rsClientes)-1;

$queryString_rsClientes = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsClientes") == false && 
        stristr($param, "totalRows_rsClientes") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsClientes = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsClientes = sprintf("&totalRows_rsClientes=%d%s", $totalRows_rsClientes, $queryString_rsClientes);
?>
<table width="915" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td height="25" colspan="4" valign="top" class="titulos">CATALOGO DE CLIENTES</td>
  </tr>
  <tr>
    <td width="10" height="7"></td>
    <td width="515"></td>
    <td width="379"></td>
    <td width="11"></td>
  </tr>
  
  <tr>
    <td height="28" colspan="4" align="center" valign="top"><a href="nuevoCliente.php" onclick="NewWindow(this.href,'Nueva Cotizacion','500','500','no');return false"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" /></strong></a><a href="nuevoCliente.php"  onClick="NewWindow(this.href,'Nuevo Cliente',400,350,'yes');return false;">NUEVO CLIENTE</a><a href="nuevoCliente.php" onclick="NewWindow(this.href,'Nueva Cotizacion','500','500','no');return false"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" /></strong></a><a href="clientestoexcel.php"  onclick="NewWindow(this.href,'Nuevo Cliente',400,350,'yes');return false;">EXPORTAR A EXCEL</a></td>
  </tr>
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="41"></td>
    <td valign="middle">
      <form name="buscar" method="get">
        BUSCAR: 
          <input name="buscar" type="text" id="buscar" value="<?php echo $_GET['buscar'];?>" />
        <input type="submit" name="buscar2" id="buscar2" value="BUSCAR" />
        <input type="hidden" name="mod" value="clientes">
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
    <td height="41" colspan="4" align="right" valign="top"><a href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, 0, $queryString_rsClientes); ?>">
    <?php if ($pageNum_rsClientes > 0) { // Show if not first page ?>
      <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
</a> <a href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, max(0, $pageNum_rsClientes - 1), $queryString_rsClientes); ?>">
<?php if ($pageNum_rsClientes > 0) { // Show if not first page ?>
  <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
  <?php } // Show if not first page ?>
</a> <a href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, min($totalPages_rsClientes, $pageNum_rsClientes + 1), $queryString_rsClientes); ?>">
<?php if ($pageNum_rsClientes < $totalPages_rsClientes) { // Show if not last page ?>
  <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
  <?php } // Show if not last page ?>
</a> <a href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, $totalPages_rsClientes, $queryString_rsClientes); ?>">
<?php if ($pageNum_rsClientes < $totalPages_rsClientes) { // Show if not last page ?>
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
    <td height="69"></td>
    <td colspan="3" align="center" valign="top"><?php if ($totalRows_rsClientes > 0) { // Show if recordset not empty ?>
        <table border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <tr class="titleTabla">
            <td width="10" height="24">&nbsp;</td>
            <td width="99" valign="top">OPCIONES</td>
            <td width="302" valign="middle">CLIENTE</td>
            <td width="29">&nbsp;</td>
            <td width="258" valign="middle">DIRECCION</td>
            <td width="197" valign="middle">TELEFONO</td>
            <td width="9">&nbsp;</td>
          </tr>
          <?php do { ?>
            <tr  onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
              <td height="28"></td>
              <td valign="top"><a  name="<?php echo $row_rsClientes['idcliente']; ?>" href="#<?php echo $row_rsClientes['idcliente']; ?>" onclick="NewWindow('contactos.php?idcliente=<?php echo $row_rsClientes['idcliente']; ?>','Contactos','850','500','no');"><img src="images/Clientes.png" width="24" height="24" title="Administrar Contactos"></a><a href="#<?php echo $row_rsClientes['idcliente']; ?>" onclick="NewWindow('eliminarCliente.php?idcliente=<?php echo $row_rsClientes['idcliente']; ?>','Eliminar Cliente','400','200','no');"><img src="images/eliminar.gif" title="Eliminar Cliente"></a><a href="#<?php echo $row_rsClientes['idcliente']; ?>" onclick="NewWindow('modificarCliente.php?idcliente=<?php echo $row_rsClientes['idcliente']; ?>','Modificar Cliente','500','400','no');"><img src="images/Edit.png" title="Modificar Datos de Cliente"></a></td>
              <td valign="top"><?php echo valida("cliente","idcliente",$row_rsClientes['idcliente']);echo $row_rsClientes['nombre']; ?>(<?php echo $row_rsClientes['clave'];?>)</td>
              <td>&nbsp;</td>
              <td valign="top"><?php echo $row_rsClientes['direccion']; ?></td>
              <td valign="top"><?php echo $row_rsClientes['telefono']; ?></td>
              <td></td>
            </tr>
            <tr>
              <td height="0"></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            
            <?php } while ($row_rsClientes = mysql_fetch_assoc($rsClientes)); ?>
                    </table>
        <?php } // Show if recordset not empty ?></td>
  </tr>
  <?php if ($totalRows_rsClientes== 0) { // Show if recordset empty ?>
    <tr>
      <td height="43"></td>
      <td colspan="2" align="center" valign="top">NO SE ENCONTRO NINCUN RESULTADO CON LA PALABRA: <?php echo $_GET['buscar']; ?></td>
      <td>&nbsp;</td>
    </tr>
	 <?php } // Show if recordset empty ?>
    <tr>
      <td height="43"></td>
      <td colspan="2" align="center" valign="top"><a href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, 0, $queryString_rsClientes); ?>">
        <?php if ($pageNum_rsClientes > 0) { // Show if not first page ?>
        <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
        <?php } // Show if not first page ?>
      </a> <a href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, max(0, $pageNum_rsClientes - 1), $queryString_rsClientes); ?>">
      <?php if ($pageNum_rsClientes > 0) { // Show if not first page ?>
      <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
      </a> <a href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, min($totalPages_rsClientes, $pageNum_rsClientes + 1), $queryString_rsClientes); ?>">
      <?php if ($pageNum_rsClientes < $totalPages_rsClientes) { // Show if not last page ?>
      <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
      </a> <a href="<?php printf("%s?pageNum_rsClientes=%d%s", $currentPage, $totalPages_rsClientes, $queryString_rsClientes); ?>">
      <?php if ($pageNum_rsClientes < $totalPages_rsClientes) { // Show if not last page ?>
      <img src="images/Last.gif" alt="ultimo" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
      </a></td>
      <td>&nbsp;</td>
    </tr>
   
</table>


<?php
mysql_free_result($rsClientes);

mysql_free_result($rsClientes2);
?>
