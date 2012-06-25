<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
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

$query = "SELECT cl.idcliente, cl.nombre, cl.abreviacion, f.numfactura, f.tipo, f.moneda, f.fecha, f.fechapago, f.estado,(select (sum(punitario*cantidad)*1.15) from detallefactura de where de.idfactura=f.idfactura) as tot,  (SELECT (SELECT identificador2 FROM subcotizacion sb WHERE sb.idsubcotizacion = fc.idcotizacion) FROM facturacotizacion fc WHERE fc.idfactura = f.idfactura)  AS idcotizacionfactura FROM cliente cl,factura f WHERE cl.idcliente = f.idcliente";

if(isset($_GET['buscar'])){
						$query = sprintf("SELECT cl.idcliente, cl.nombre, cl.abreviacion, f.numfactura, f.tipo, f.moneda, f.fecha, f.fechapago, f.estado, (select (sum(punitario*cantidad)*1.15) from detallefactura de where de.idfactura=f.idfactura) as tot,  (SELECT (SELECT identificador2 FROM subcotizacion sb WHERE sb.idsubcotizacion = fc.idcotizacion) FROM facturacotizacion fc WHERE fc.idfactura = f.idfactura)  AS idcotizacionfactura FROM cliente cl,factura f WHERE cl.idcliente = f.idcliente AND cl.nombre like %s",GetSQLValueString('%'.$_GET['buscar'].'%', "text"));
					}

if(isset($_GET['filtrar'])){

				if($_GET['mostrar'] != 0){
						
						switch($_GET['mostrar']){
						
								case 1: $query = $query." AND f.estado = 0"; 
								break;
								case 2:$query = $query." AND f.estado = 1"; 
								break;
								case 3:$query = $query." AND f.estado = 2"; 
								break;
							}
						
						}
				if($_GET['periodo'] != 1){
						
							$datei = $_GET['anoi']."-".$_GET['mesi']."-".$_GET['diai'];
							$datef = $_GET['anof']."-".$_GET['mesf']."-".$_GET['diaf'];
							
						
						switch($_GET['periodo']){
						
								case 2: $query = $query.sprintf(" AND fecha BETWEEN %s AND %s ",GetSQLValueString($datei, "date"),GetSQLValueString($datei, "date")); 
								break;
								
								case 4:$query = $query.sprintf(" AND fechapago BETWEEN %s AND %s ",GetSQLValueString($datei, "date"),GetSQLValueString($datei, "date")); 
								break;
						}	
				}	
	}


$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsProveedores = 30;
$pageNum_rsProveedores = 0;
if (isset($_GET['pageNum_rsProveedores'])) {
  $pageNum_rsProveedores = $_GET['pageNum_rsProveedores'];
}
$startRow_rsProveedores = $pageNum_rsProveedores * $maxRows_rsProveedores;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedores =$query;// "SELECT * FROM proveedor p,cuentasporpagar c WHERE p.idproveedor = c.idproveedor";
$query_limit_rsProveedores = sprintf("%s LIMIT %d, %d", $query_rsProveedores, $startRow_rsProveedores, $maxRows_rsProveedores);
$rsProveedores = mysql_query($query_limit_rsProveedores, $tecnocomm) or die(mysql_error());
$row_rsProveedores = mysql_fetch_assoc($rsProveedores);

if (isset($_GET['totalRows_rsProveedores'])) {
  $totalRows_rsProveedores = $_GET['totalRows_rsProveedores'];
} else {
  $all_rsProveedores = mysql_query($query_rsProveedores);
  $totalRows_rsProveedores = mysql_num_rows($all_rsProveedores);
}
$totalPages_rsProveedores = ceil($totalRows_rsProveedores/$maxRows_rsProveedores)-1;

$queryString_rsProveedores = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsProveedores") == false && 
        stristr($param, "totalRows_rsProveedores") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsProveedores = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsProveedores = sprintf("&totalRows_rsProveedores=%d%s", $totalRows_rsProveedores, $queryString_rsProveedores);

$concepto = array("Segun orden de Servicio: ","Segun Cotizacion: ");

$estados = array("<img src=\"images/state1.png\" width=\"24\" height=\"24\" title=\"Abierta\" />","<img src=\"images/Cobrar.png\" alt=\"\" width=\"24\" height=\"24\" title=\"Pagada\"/>","<img src=\"images/Terminar.png\" width=\"24\" height=\"24\"  title=\"Cancelada\"/>");

$i=0;
do{

$proveedor[$row_rsProveedores['idcliente']]['nombre'] =  $row_rsProveedores['abreviacion'];
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['estado'] = $estados[$row_rsProveedores['estado']];
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['concepto'] = $row_rsProveedores['numfactura'];
/*
if($row_rsProveedores['tipo']!=1){ 
		$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['concepto'] =$concepto[$row_rsProveedores['tipo']].		
		$row_rsProveedores['referencia'];
} 
if($row_rsProveedores['tipo']==1){
	$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['concepto'] =$concepto[$row_rsProveedores['tipo']].$row_rsProveedores
	['idcotizacionfactura']; 
}
*/
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['moneda'] = $row_rsProveedores['moneda'];
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['monto'] = $row_rsProveedores['tot'];
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['fecha'] = formatDate($row_rsProveedores['fecha']);
$proveedor[$row_rsProveedores['idcliente']]['cuentas'][$i]['fechapago'] = formatDate($row_rsProveedores['fechapago']);

$i++;
}while($row_rsProveedores = mysql_fetch_assoc($rsProveedores));

$meses = array (1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");


$signo = array("$","US$");
?>





<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
</head>

<body>
<form name="reporteproveedores" method="get">
<table width="840" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="19" colspan="12" valign="top" class="titulos">CUENTAS POR COBRAR</td>
  </tr>
  <tr>
    <td width="81" height="8"></td>
    <td width="215"></td>
    <td width="14"></td>
    <td width="22"></td>
    <td width="47"></td>
    <td width="22"></td>
    <td width="98"></td>
    <td width="31"></td>
    <td width="73"></td>
    <td width="106"></td>
    <td width="31"></td>
    <td width="98"></td>
  </tr>
  <tr>
    <td height="21" colspan="3" valign="top"><select name="periodo" id="periodo">
        <option value="1" selected="selected">No Filtrar Por Periodo</option>
        <option value="2">Fecha</option>
        <option value="3">Fecha de Pago</option>
        </select></td>
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
  <tr>
    <td height="24" colspan="12" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="resaltarTabla">
      <!--DWLayoutTable-->
      <tr>
        <td width="39" height="21" valign="top">Del</td>
          <td width="39" valign="top">Dia:</td>
          <td width="76" valign="top"><select name="diai" id="diai">
              
              <?php for($i=1;$i<=31;$i++){?>
              
              <option value="<?php echo $i;?>" <?php if($i == date("j")) echo "selected=\"selected\"";?> ><?php echo $i;?></option>
              
              <?php } ?>
          </select>        </td>
          <td width="46" valign="top">Mes:</td>
          <td width="100" valign="top"><select name="mesi" id="mesi">
              
              <?php  foreach($meses as $mes => $nombremes){ ?>
              <option value="<?php echo $mes ?>" <?php if($mes == date("n")) echo "selected=\"selected\"";?> ><?php echo $nombremes;?></option>
              <?php } ?>
              
          </select>        </td>
          <td width="43" valign="top">A&ntilde;o:</td>
          <td width="74" valign="top"><select name="anoi" id="anoi">
              <?php for($i=2000;$i<=2050;$i++){?>
              
              <option value="<?php echo $i;?>" <?php if($i == date("Y")) echo "selected=\"selected\"";?>><?php echo $i;?></option>
              
              <?php } ?>
          </select>        </td>
          <td width="23" align="center" valign="top">Al</td>
          <td width="39" valign="top">Dia:</td>
          <td width="76" valign="top"><select name="diaf" id="diaf">
              <?php for($i=1;$i<=31;$i++){?>
              
              <option value="<?php echo $i;?>" <?php if($i == date("j")) echo "selected=\"selected\"";?>><?php echo $i;?></option>
              
              <?php } ?>
          </select>        </td>
          <td width="41" valign="top">Mes:</td>
          <td width="100" valign="top"><select name="mesf" id="mesf">
              <?php  foreach($meses as $mes => $nombremes){ ?>
              <option value="<?php echo $mes ?>" <?php if($mes == date("n")) echo "selected=\"selected\"";?> ><?php echo $nombremes;?></option>
              <?php } ?>
          </select>        </td>
          <td width="36" valign="top">A&ntilde;o:</td>
          <td width="62" valign="top"><select name="anof" id="anof">
              <?php for($i=2000;$i<=2050;$i++){?>
              
              <option value="<?php echo $i;?>" <?php if($i == date("Y")) echo "selected=\"selected\"";?>><?php echo $i;?></option>
              
              <?php } ?>
              
          </select>        </td>
          <td width="56" valign="top"><input type="submit" name="filtrar" id="filtrar" value="Filtrar" /></td>
        </tr>
      <tr>
        <td height="3"></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
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
  <tr>
    <td height="1"></td>
    <td></td>
    <td></td>
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
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="24" valign="top">Estado:</td>
    <td colspan="2" valign="top"><select name="mostrar" id="mostrar">
      <option value="0" selected="selected" <?php if (!(strcmp(1, $_GET['mostrar']))) {echo "selected=\"selected\"";} ?>>Todas</option>
      <option value="1" <?php if (!(strcmp(2, $_GET['mostrar']))) {echo "selected=\"selected\"";} ?>>Abiertas</option>
      <option value="2" <?php if (!(strcmp(3, $_GET['mostrar']))) {echo "selected=\"selected\"";} ?>>Pagadas</option>
      <option value="3" <?php if (!(strcmp(4, $_GET['mostrar']))) {echo "selected=\"selected\"";} ?>>Canceladas</option>
    </select>
      <input type="submit" name="filtrar" id="filtrar" value="Filtrar" /></td>
    <td>&nbsp;</td>
    <td colspan="2" valign="top">Buscar:</td>
    <td colspan="4" valign="top"><input name="buscar" type="text" id="buscar" value="<?php echo $_GET['buscar'] ?>" />
    <input type="submit" name="filtrar2" id="filtrar2" value="Buscar" /></td>
    <td><a href="reporteClientesPrint.php?<?php echo $_SERVER['QUERY_STRING']; ?>" onclick="NewWindow(this.href,'Imprimir',800,800,'yes');return false;"><img src="images/Imprimir2.png" width="24" height="24" /></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="41">&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="4" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="305" height="29" align="right" valign="top"><a href="<?php printf("%s?pageNum_rsProveedores=%d%s", $currentPage, 0, $queryString_rsProveedores); ?>">
        <?php if ($pageNum_rsProveedores > 0) { // Show if not first page ?>
          <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
          <?php } // Show if not first page ?>
</a> <a href="<?php printf("%s?pageNum_rsProveedores=%d%s", $currentPage, max(0, $pageNum_rsProveedores - 1), $queryString_rsProveedores); ?>">
        <?php if ($pageNum_rsProveedores > 0) { // Show if not first page ?>
          <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
          <?php } // Show if not first page ?>
</a> <a href="<?php printf("%s?pageNum_rsProveedores=%d%s", $currentPage, min($totalPages_rsProveedores, $pageNum_rsProveedores + 1), $queryString_rsProveedores); ?>">
        <?php if ($pageNum_rsProveedores < $totalPages_rsProveedores) { // Show if not last page ?>
          <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
          <?php } // Show if not last page ?>
</a> <a href="<?php printf("%s?pageNum_rsProveedores=%d%s", $currentPage, $totalPages_rsProveedores, $queryString_rsProveedores); ?>">
        <?php if ($pageNum_rsProveedores < $totalPages_rsProveedores) { // Show if not last page ?>
          <img src="images/Last.gif" alt="ultimo" width="24" height="24" border="0" />
          <?php } // Show if not last page ?>
</a> </td>
      </tr>
    </table>    </td>
  </tr>
 <?php foreach($proveedor as $pro){?>
  <tr>
    <td height="98" colspan="12" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td height="18" colspan="2" valign="top" class="realte">Cliente:</td>
          <td colspan="10" valign="top" class="realte"><?php echo $pro['nombre'];?></td>
        </tr>
      <tr>
        <td width="74" height="4"></td>
          <td width="50"></td>
          <td width="96"></td>
          <td width="100"></td>
          <td width="75"></td>
          <td width="58"></td>
          <td width="35"></td>
          <td width="102"></td>
          <td width="78"></td>
          <td width="49"></td>
          <td width="91"></td>
          <td width="30"></td>
        </tr>
      <tr>
        <td height="18" colspan="3" valign="top">Detalle De Cuentas:</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      <tr>
        <td height="18" valign="top">Estado</td>
          <td colspan="3" valign="top">No. Factura</td>
          <td colspan="3" align="center" valign="top">Monto</td>
          <td colspan="2" align="center" valign="top">Fecha</td>
          <td colspan="2" align="center" valign="top">Fecha Pago</td>
          <td>&nbsp;</td>
        </tr>
        <?php $tpesos = $tdolares = 0; foreach($pro['cuentas'] as $cuenta){ ?>
      <tr>
        <td height="20" valign="top"><?php echo $cuenta['estado'];?></td>
          <td colspan="3" valign="top"><?php echo $cuenta['concepto'];?></td>
          <td valign="top"><?php echo $signo[$cuenta['moneda']];?></td>
          <td colspan="2" align="right" valign="top"><?php echo $cuenta['monto'];?></td>
          <td colspan="2" align="center" valign="top"><?php echo $cuenta['fecha'];?></td>
          <td colspan="2" align="center" valign="top"><?php echo $cuenta['fechapago'];?></td>
          <td></td>
        </tr>
        <?php if($cuenta['estado'] != 2)if($cuenta['moneda'] == 0){$tpesos = $tpesos +$cuenta['monto']; } else {$tdolares = $tdolares +$cuenta['monto'];}?>
        <?php  } ?>
      <tr>
        <td height="20"></td>
          <td></td>
          <td></td>
          <td></td>
          <td colspan="2" align="right" valign="top">TOTAL $:</td>
          <td colspan="2" align="right" valign="top"><?php echo format_money($tpesos);?></td>
          <td align="right" valign="top">TOTAL US$:</td>
          <td align="right" valign="top"><?php echo format_money($tdolares);?></td>
          <td>&nbsp;</td>
          <td></td>
        </tr>
      
      
      
      
      
      
      
      
      
    </table></td>
  </tr>
  <?php $totalpesos = $totalpesos + $tpesos; $totaldolars = $totaldolars + $tdolares;?>
  <?php } ?>
  <tr>
    <td height="15" colspan="12" valign="top" class="resaltarTabla">TOTAL</td>
    </tr>
  <tr>
    <td height="21"></td>
    <td></td>
    <td colspan="3" valign="top"><span class="style1">TOTAL $:</span></td>
    <td colspan="2" align="right" valign="top"><span class="style1"><?php echo format_money($totalpesos);?></span></td>
    <td colspan="2" align="right" valign="top"><span class="style1">TOTAL US$:</span></td>
    <td colspan="2" align="right" valign="top"><span class="style1"><?php echo format_money($totaldolares);?></span></td>
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
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  <tr>
    <td height="41"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="305" height="33" align="right" valign="top"><a href="<?php printf("%s?pageNum_rsProveedores=%d%s", $currentPage, 0, $queryString_rsProveedores); ?>">
          <?php if ($pageNum_rsProveedores > 0) { // Show if not first page ?>
            <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
            <?php } // Show if not first page ?>
          </a> <a href="<?php printf("%s?pageNum_rsProveedores=%d%s", $currentPage, max(0, $pageNum_rsProveedores - 1), $queryString_rsProveedores); ?>">
            <?php if ($pageNum_rsProveedores > 0) { // Show if not first page ?>
              <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
              <?php } // Show if not first page ?>
            </a> <a href="<?php printf("%s?pageNum_rsProveedores=%d%s", $currentPage, min($totalPages_rsProveedores, $pageNum_rsProveedores + 1), $queryString_rsProveedores); ?>">
              <?php if ($pageNum_rsProveedores < $totalPages_rsProveedores) { // Show if not last page ?>
                <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
                <?php } // Show if not last page ?>
              </a> <a href="<?php printf("%s?pageNum_rsProveedores=%d%s", $currentPage, $totalPages_rsProveedores, $queryString_rsProveedores); ?>">
              <?php if ($pageNum_rsProveedores < $totalPages_rsProveedores) { // Show if not last page ?>
                <img src="images/Last.gif" alt="ultimo" width="24" height="24" border="0" />
                <?php } // Show if not last page ?>
            </a></td>
          </tr>
    </table></td>
  </tr>
</table>
</form>
</body>
</html>
<?php
mysql_free_result($rsProveedores);
?>
