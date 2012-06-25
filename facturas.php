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

$currentPage = $_SERVER["PHP_SELF"];


$select = "SELECT f.*,c.abreviacion,c.idcliente,  (SELECT (SELECT identificador2 FROM subcotizacion sb WHERE sb.idsubcotizacion = fc.idcotizacion) FROM facturacotizacion fc WHERE fc.idfactura = f.idfactura)  AS idcotizacionfactura FROM factura f, cliente c WHERE f.idcliente = c.idcliente ORDER BY numfactura DESC";

if(isset($_GET['buscar'])){

$bus = "%".$_GET['buscar']."%";

$select = "SELECT f.*,c.abreviacion,c.idcliente, (SELECT (SELECT identificador2 FROM subcotizacion sb WHERE sb.idsubcotizacion = fc.idcotizacion) FROM facturacotizacion fc WHERE fc.idfactura = f.idfactura)  AS idcotizacionfactura FROM factura f, cliente c WHERE f.idcliente = c.idcliente  AND c.nombre like ".GetSQLValueString($bus, "text")."ORDER BY numfactura DESC ";

}

if(isset($_GET['estadoFactura']) && $_GET['estadoFactura'] != -1){

$bus =$_GET['estadoFactura'];

$select = "SELECT f.*,c.abreviacion,c.idcliente , (SELECT (SELECT identificador2 FROM subcotizacion sb WHERE sb.idsubcotizacion = fc.idcotizacion) FROM facturacotizacion fc WHERE fc.idfactura = f.idfactura)  AS idcotizacionfactura FROM factura f, cliente c WHERE f.idcliente = c.idcliente  AND f.estado =".GetSQLValueString($bus, "int")." ORDER BY numfactura DESC ";

}


$maxRows_rsFacutras = 30;
$pageNum_rsFacutras = 0;
if (isset($_GET['pageNum_rsFacutras'])) {
  $pageNum_rsFacutras = $_GET['pageNum_rsFacutras'];
}
$startRow_rsFacutras = $pageNum_rsFacutras * $maxRows_rsFacutras;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacutras = $select;
$query_limit_rsFacutras = sprintf("%s LIMIT %d, %d", $query_rsFacutras, $startRow_rsFacutras, $maxRows_rsFacutras);
$rsFacutras = mysql_query($query_limit_rsFacutras, $tecnocomm) or die(mysql_error());
$row_rsFacutras = mysql_fetch_assoc($rsFacutras);

if (isset($_GET['totalRows_rsFacutras'])) {
  $totalRows_rsFacutras = $_GET['totalRows_rsFacutras'];
} else {
  $all_rsFacutras = mysql_query($query_rsFacutras);
  $totalRows_rsFacutras = mysql_num_rows($all_rsFacutras);
}
$totalPages_rsFacutras = ceil($totalRows_rsFacutras/$maxRows_rsFacutras)-1;

$queryString_rsFacutras = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsFacutras") == false && 
        stristr($param, "totalRows_rsFacutras") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsFacutras = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsFacutras = sprintf("&totalRows_rsFacutras=%d%s", $totalRows_rsFacutras, $queryString_rsFacutras);


$estado =array ("<img src=\"images/Facturacion.png\"  title=\"Activa\"/>","<img src=\"images/Cobrar.png\" title=\"Pagada\" />","cancelada","<img src=\"images/Stacked Documents 24 h p.png\" title=\"incobrable\"/>");
$tipo =  array("ORDEN DE SERVICIO","COTIZACION","OTRO");




?>


<table width="915" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td height="25" colspan="5" valign="top" class="titulos">FACTURACION</td>
  </tr>
  <tr>
    <td width="10" height="7"></td>
    <td width="413"></td>
    <td width="6"></td>
    <td width="411"></td>
    <td width="76"></td>
  </tr>
  <tr>
    <td height="28" colspan="5" align="center" valign="top"><a href="nuevaFactura.php" onclick="NewWindow(this.href,'Nueva Factura',700,300,'yes'); return false;"  ><strong><img src="images/bullet_16.jpg" alt="Nueva" width="26" height="22" border="0" align="middle" /></strong>NUEVA FACTURA</a></td>
  </tr>
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="39"></td>
    <td valign="middle"><form method="get" name="buscar" id="buscar">
      BUSCAR CLIENTE:
          <input name="buscar" type="text" id="buscar" />
      <input type="submit" name="buscar2" id="buscar2" value="BUSCAR" />
      <input type="hidden" name="mod" value="facturacion" />
    </form></td>
    <td>&nbsp;</td>
    <td valign="top"><form method="get" name="buscar2" id="buscar2">
      FILTRAR ESTADO:
        
          <select name="estadoFactura" id="estadoFactura">
          <option value="-1" selected="selected">TODAS</option>
          <option value="0" >ACTIVA</option>
          <option value="1">PAGADA</option>
          <option value="2">CANCELADA</option>
          <option value="3">INCOBRABLE</option> 
        </select>

<input type="submit" value="FILTRAR" />
      <input type="hidden" name="mod" value="facturacion" />
    </form></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="10"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  

  
  <tr>
    <td height="41" colspan="5" align="right" valign="top"><?php if ($pageNum_rsFacutras > 0) { ?><a href="<?php printf("%s?pageNum_rsFacutras=%d%s", $currentPage, 0, $queryString_rsFacutras); ?>"><img src="images/First.gif" alt="Primero" width="24" height="24" border="0" /></a> <?php } // Show if not first page ?> <?php if ($pageNum_rsFacutras > 0) { ?><a href="<?php printf("%s?pageNum_rsFacutras=%d%s", $currentPage, max(0, $pageNum_rsFacutras - 1), $queryString_rsFacutras); ?>"><img src="images/Back.gif" alt="Atras" width="24" height="24" border="0" /></a> <?php } // Show if not first page ?> <?php if ($pageNum_rsFacutras < $totalPages_rsFacutras) { // Show if not last page ?><a href="<?php printf("%s?pageNum_rsFacutras=%d%s", $currentPage, min($totalPages_rsFacutras, $pageNum_rsFacutras + 1), $queryString_rsFacutras); ?>"><img src="images/Forward.gif" alt="Siguiente" width="24" height="24" border="0" /></a><?php } // Show if not first page ?><?php if ($pageNum_rsFacutras < $totalPages_rsFacutras) { // Show if not last page ?><a href="<?php printf("%s?pageNum_rsFacutras=%d%s", $currentPage, $totalPages_rsFacutras, $queryString_rsFacutras); ?>"><img src="images/Last.gif" alt="Ultimo" width="24" height="24" border="0" /></a><?php } // Show if not first page ?> </td>
  </tr>
  <tr>
    <td height="5"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="67"></td>
    <td colspan="4" align="center" valign="top"><table border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="10" height="20">&nbsp;</td>
        <td colspan="2" valign="middle">OPCIONES</td>
        <td width="107" valign="top">NO. FACTURA</td>
        <td width="223" valign="middle">CLIENTE</td>
        <td width="258" valign="middle">REFERENCIA</td>
        <td width="156" valign="middle">FECHA</td>
        </tr>
      <?php do { ?>
        <tr  onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
            <td height="20"></td>
          <td width="61" valign="top"><?php echo $estado[$row_rsFacutras['estado']];?></td>
          <td width="93" valign="top"><a href="printFacturaPDF.php?idfactura=<?php echo $row_rsFacutras['idfactura']; ?>" target="_blank"><img src="images/Imprimir2.png" width="24" height="24" border="0"  title="Imprimir Factura"/>
          </a>
            <a href="index.php?mod=facturando&idfactura=<?php echo $row_rsFacutras['idfactura']; ?>"><img src="images/Edit.png" width="24" height="24" border="0" title="EDITAR FACTURA" /></a><a href="eliminarFactura.php?idfactura=<?php echo $row_rsFacutras['idfactura']; ?>" onclick="NewWindow(this.href,'Elimnar factura','850','600','YES');return false"><img src="images/eliminar.gif" alt="eliminar" width="19" height="19" border="0" title="ELIMINAR FACTURA" /></a></td>
          <td valign="top"><?php echo $row_rsFacutras['numfactura']; ?></td>
          <td valign="top"><?php echo $row_rsFacutras['abreviacion']; ?></td>
          <td valign="top"><?php echo $tipo[$row_rsFacutras['tipo']]; ?>: <?php if($row_rsFacutras['tipo']!=1){ echo $row_rsFacutras['referencia']; } if($row_rsFacutras['tipo']==1){ echo $row_rsFacutras['idcotizacionfactura'];}?></td>
          <td valign="top"><?php echo formatDate($row_rsFacutras['fecha']); ?></td>
        </tr>
        <?php } while ($row_rsFacutras = mysql_fetch_assoc($rsFacutras)); ?>
<tr>
        <td height="0"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        </tr>
    </table></td>
  </tr>
  <?php if ($totalRows_rsClientes== 0) { // Show if recordset empty ?>
  <tr>
    <td height="43"></td>
    <td colspan="4" align="center" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <?php } ?>
</table>
<div id="hola">

 <div class="multiBoxDesc mb10"></div>
<script type="text/javascript">
			var box = {};
			window.addEvent('domready', function(){
				box = new MultiBox('mb', {descClassName: 'multiBoxDesc', useOverlay: true,contentColor:'#00427F'});
			});
			var pag = {};
			var his = {};
			window.addEvent('domready', function(){
				pag = new pageLoader({loadInTo:'ajaxContent'});
				his = new History();
			});
			
		</script>
 </div>
<?php
mysql_free_result($rsFacutras);
?>
