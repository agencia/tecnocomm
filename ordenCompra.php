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

$query = "SELECT *,o.identificador AS identorden,o.idcotizacion AS idencoti FROM ordencompra o LEFT JOIN subcotizacion s ON o.idcotizacion = s.idsubcotizacion ORDER BY EXTRACT(YEAR FROM o.fecha) desc, o.consecutivo DESC ";

if(isset($_GET['buscar']) && $_GET['buscar']!=""){
	$query = sprintf("SELECT *,o.identificador AS identorden,o.idcotizacion AS idencoti FROM ordencompra o LEFT JOIN subcotizacion s ON o.idcotizacion = s.idsubcotizacion JOIN proveedor p ON o.idproveedor = p.idproveedor WHERE s.identificador2 like %s OR p.nombrecomercial like %s OR o.tituloconcepto like %s OR p.abreviacion like %s ORDER BY EXTRACT(YEAR FROM o.fecha) desc, o.consecutivo DESC",GetSQLValueString("%".$_GET['buscar']."%","text"),GetSQLValueString("%".$_GET['buscar']."%","text"),GetSQLValueString("%".$_GET['buscar']."%","text"),GetSQLValueString("%".$_GET['buscar']."%","text"));
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_ordenCompra = 30;
$pageNum_ordenCompra = 0;
if (isset($_GET['pageNum_ordenCompra'])) {
  $pageNum_ordenCompra = $_GET['pageNum_ordenCompra'];
}
$startRow_ordenCompra = $pageNum_ordenCompra * $maxRows_ordenCompra;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_ordenCompra = $query;
$query_limit_ordenCompra = sprintf("%s LIMIT %d, %d", $query_ordenCompra, $startRow_ordenCompra, $maxRows_ordenCompra);
$ordenCompra = mysql_query($query_limit_ordenCompra, $tecnocomm) or die(mysql_error());
$row_ordenCompra = mysql_fetch_assoc($ordenCompra);

if (isset($_GET['totalRows_ordenCompra'])) {
  $totalRows_ordenCompra = $_GET['totalRows_ordenCompra'];
} else {
  $all_ordenCompra = mysql_query($query_ordenCompra);
  $totalRows_ordenCompra = mysql_num_rows($all_ordenCompra);
}
$totalPages_ordenCompra = ceil($totalRows_ordenCompra/$maxRows_ordenCompra)-1;

$queryString_ordenCompra = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_ordenCompra") == false && 
        stristr($param, "totalRows_ordenCompra") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_ordenCompra = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_ordenCompra = sprintf("&totalRows_ordenCompra=%d%s", $totalRows_ordenCompra, $queryString_ordenCompra);


$colname_rsCotx = "-1";
if (isset($_GET['idpartida'])) {
  $colname_rsCotx = $_GET['idpartida'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotx = "SELECT * FROM subcotizacion WHERE idsubcotizacion IN (SELECT idcotizacion FROM ordencompra WHERE idcotizacion is not null) ORDER BY identificador2";
$rsCotx = mysql_query($query_rsCotx, $tecnocomm) or die(mysql_error());
$row_rsCotx = mysql_fetch_assoc($rsCotx);
$totalRows_rsCotx = mysql_num_rows($rsCotx);
?>

  <table width="940" border="0" cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <tr>
      <td height="25" colspan="8" valign="top" class="titulos">ORDENES DE COMPRA</td>
    </tr>
    <tr>
      <td width="8" height="16"></td>
      <td width="100"></td>
      <td width="120"></td>
      <td width="135"></td>
      <td width="209"></td>
      <td width="82"></td>
      <td width="54"></td>
      <td width="169"></td>
    </tr>
    
    <tr>
      <td height="26" colspan="8" align="center" valign="top"><a href="nuevaOrden2.php" onclick="NewWindow(this.href,'Nueva Orden de Compra',760,450,'yes'); return false;"><img src="images/bullet_16.jpg" width="26" height="22" align="texttop">Nueva Orden De Compra</a></td>
    </tr>
    <tr>
      <td height="7"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="45"></td>
      <td colspan="2">
          <form method="get" name="buscarorden">Buscar:
      <input name="buscar" type="text" id="buscar" value="<?php echo $_GET['buscar'];?>" />
      <input type="submit" name="button" id="button" value="buscar" />
      <input type="hidden" name="mod" value="ordenCompra" /></form>
      </td>
      <td>
          <form method="get" name="buscarorden2">Buscar:
                  <select name="buscar">
                      <option value="">Seleccione una cotizacion</option>
                      <?php do { ?>
                      <option><?php echo $row_rsCotx['identificador2']; ?></option>
                      <?php } while($row_rsCotx = mysql_fetch_assoc($rsCotx)); ?>
                  </select>
      <input type="submit" name="button" id="button" value="Buscar Cotizacion" />
      <input type="hidden" name="mod" value="ordenCompra" /></form>
      </td>
      <td colspan="2" align="right" valign="top"><a href="<?php printf("%s?pageNum_ordenCompra=%d%s", $currentPage, 0, $queryString_ordenCompra); ?>">
        <?php if ($pageNum_ordenCompra > 0) { // Show if not first page ?>
          <img src="images/First.gif" width="24" height="24" />
<?php } // Show if not first page ?>
            </a><a href="<?php printf("%s?pageNum_ordenCompra=%d%s", $currentPage, max(0, $pageNum_ordenCompra - 1), $queryString_ordenCompra); ?>">
            <?php if ($pageNum_ordenCompra > 0) { // Show if not first page ?>
              <img src="images/Back.gif" width="24" height="24" />
              <?php } // Show if not first page ?>
            </a><a href="<?php printf("%s?pageNum_ordenCompra=%d%s", $currentPage, min($totalPages_ordenCompra, $pageNum_ordenCompra + 1), $queryString_ordenCompra); ?>">
            <?php if ($pageNum_ordenCompra < $totalPages_ordenCompra) { // Show if not last page ?>
              <img src="images/Forward.gif" width="24" height="24" />
              <?php } // Show if not last page ?>
            </a><a href="<?php printf("%s?pageNum_ordenCompra=%d%s", $currentPage, $totalPages_ordenCompra, $queryString_ordenCompra); ?>">
            <?php if ($pageNum_ordenCompra < $totalPages_ordenCompra) { // Show if not last page ?>
              <img src="images/Last.gif" width="24" height="24" />
              <?php } // Show if not last page ?>
          </a></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="10"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    
    
    
    
    
    
    
    
    <tr class="titleTabla">
      <td height="18"></td>
      <td  width="30px" valign="top">Opciones</td>
      <td width="200px" valign="top">Orden de Compra</td>
      <td colspan="2" valign="top">Concepto</td>
      <td colspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    
    
    
    
    
    <tr>
      <td height="4"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
    
    
    
    <?php do { ?>
      <tr onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
        <td height="36"></td>
        <?php if ($totalRows_ordenCompra > 0) { // Show if recordset not empty ?>
          <td valign="top"><a href="detalleOrden2.php?idordencompra=<?php echo $row_ordenCompra['idordencompra'];?>" onclick="NewWindow(this.href,'Detalle Orden',800,800,'yes'); return false;" ><img src="images/Edit.png" width="24" height="24" /></a><a href="printOrdenCompraGeneral.php?idordencompra=<?php echo $row_ordenCompra['idordencompra'];?>" onclick="NewWindow(this.href,'Imprimir Orden Compra','900','800','yes'); return false;"><img src="images/Imprimir2.png"  title="Imprimir Orden Compra"/></a></td>
          <td valign="top"><?php echo $row_ordenCompra['identorden'];?></td>
          <td colspan="2" valign="top"><?php if($row_ordenCompra['tipoorden'] == 0){ ?>
            <a href="detalleCotizacion.php?idcotizacion=<?php echo $row_ordenCompra['idencoti']; ?>" 
            onclick="NewWindow(this.href,'Detalle Cotizacion',800,800,'yes');return false;">Cotizacion: <?php echo $row_ordenCompra['identificador2'];?></a>
            <?php } else { ?> 
        <?php echo $row_ordenCompra['tituloconcepto']; ?>            <?php } ?></td>
          <td valign="top"></td>
          <?php } // Show if recordset not empty ?>
        <td></td>
        <td></td>
      </tr>
      <?php } while ($row_ordenCompra = mysql_fetch_assoc($ordenCompra)); ?>
    
    
    
    
    
    
    
    
    <?php if ($totalRows_ordenCompra == 0) { // Show if recordset empty ?>
    
    <tr>
      <td height="18" colspan="8" align="center" valign="top">NO SE ENCONTRO NINGUNA ORDEN DE COMPRA</td>
    </tr>
      <?php } // Show if recordset empty ?>
    <tr>
      <td height="45">&nbsp;</td>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td colspan="2" align="right" valign="top"><a href="<?php printf("%s?pageNum_ordenCompra=%d%s", $currentPage, 0, $queryString_ordenCompra); ?>">
        <?php if ($pageNum_ordenCompra > 0) { // Show if not first page ?>
        <img src="images/First.gif" width="24" height="24" />
        <?php } // Show if not first page ?>
      </a><a href="<?php printf("%s?pageNum_ordenCompra=%d%s", $currentPage, max(0, $pageNum_ordenCompra - 1), $queryString_ordenCompra); ?>">
      <?php if ($pageNum_ordenCompra > 0) { // Show if not first page ?>
      <img src="images/Back.gif" width="24" height="24" />
      <?php } // Show if not first page ?>
      </a><a href="<?php printf("%s?pageNum_ordenCompra=%d%s", $currentPage, min($totalPages_ordenCompra, $pageNum_ordenCompra + 1), $queryString_ordenCompra); ?>">
      <?php if ($pageNum_ordenCompra < $totalPages_ordenCompra) { // Show if not last page ?>
      <img src="images/Forward.gif" width="24" height="24" />
      <?php } // Show if not last page ?>
      </a><a href="<?php printf("%s?pageNum_ordenCompra=%d%s", $currentPage, $totalPages_ordenCompra, $queryString_ordenCompra); ?>">
      <?php if ($pageNum_ordenCompra < $totalPages_ordenCompra) { // Show if not last page ?>
      <img src="images/Last.gif" width="24" height="24" />
      <?php } // Show if not last page ?>
      </a></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="27">&nbsp;</td>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
<script type="text/javascript">
			var box = {};
			window.addEvent('domready', function(){
				box = new MultiBox('mb', {descClassName: 'multiBoxDesc', useOverlay: true});
			});
		</script>
<?php
mysql_free_result($ordenCompra);
?>
