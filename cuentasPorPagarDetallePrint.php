<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php'); ?>
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

//95d835abdde8760ef40a110639a196c3:znTUkIbN1H2VpsVmzQMMnXZStYUWbo5B

$query = sprintf("SELECT * FROM cuentasporpagar WHERE idproveedor = %s ", GetSQLValueString($_GET['idproveedor'], "int"));

if(isset($_GET['filtrar'])){

	switch($_GET['filtrar']){
	
		case "Buscar":
					if(isset($_GET['buscar'])){
						$query = sprintf("SELECT * FROM cuentasporpagar WHERE idproveedor = %s AND nofactura = %s",GetSQLValueString($_GET['idproveedor'], "int"),GetSQLValueString($_GET['buscar'], "int"));
					}
		
		case "Filtrar":
				if($_GET['mostrar'] != 0){
						
						switch($_GET['mostrar']){
						
								case 1: $query = $query." AND estado = 0"; 
								break;
								case 2:$query = $query." AND estado = 1"; 
								break;
								case 3:$query = $query." AND estado = 2"; 
								break;
							}
						
						}
				if($_GET['periodo'] != 1){
						
							$datei = $_GET['anoi']."-".$_GET['mesi']."-".$_GET['diai'];
							$datef = $_GET['anof']."-".$_GET['mesf']."-".$_GET['diaf'];
							
						
						switch($_GET['periodo']){
						
								case 2: $query = $query.sprintf(" AND fecha BETWEEN %s AND %s ",GetSQLValueString($datei, "date"),GetSQLValueString($datei, "date")); 
								break;
								case 3:$query = $query.sprintf(" AND fechavencimiento BETWEEN %s AND %s ",GetSQLValueString($datei, "date"),GetSQLValueString($datei, "date")); 
								break;
						}	
				}	
	}
}

$orden = array(1=>"ASC","DESC");

switch($_GET['ordenar']){

	case 2: $query = $query." ORDER BY fecha ".$orden[$_GET['formaordenar']];
	break;
	case 3: $query= $query." ORDER BY fechavencimiento ".$orden[$_GET['formaordenar']];
	break;
	default: $query= $query." ORDER BY nofactura ".$orden[$_GET['formaordenar']];
}



$maxRows_rsProveedor = 1;
$pageNum_rsProveedor = 0;
if (isset($_GET['pageNum_rsProveedor'])) {
  $pageNum_rsProveedor = $_GET['pageNum_rsProveedor'];
}
$startRow_rsProveedor = $pageNum_rsProveedor * $maxRows_rsProveedor;

$colname_rsProveedor = "-1";
if (isset($_GET['idproveedor'])) {
  $colname_rsProveedor = $_GET['idproveedor'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedor = sprintf("SELECT * FROM proveedor WHERE idproveedor = %s", GetSQLValueString($colname_rsProveedor, "int"));
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

$maxRows_rsCuentas = 30;
$pageNum_rsCuentas = 0;
if (isset($_GET['pageNum_rsCuentas'])) {
  $pageNum_rsCuentas = $_GET['pageNum_rsCuentas'];
}
$startRow_rsCuentas = $pageNum_rsCuentas * $maxRows_rsCuentas;

$colname_rsCuentas = "-1";
if (isset($_GET['idproveedor'])) {
  $colname_rsCuentas = $_GET['idproveedor'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCuentas = $query;//sprintf("SELECT * FROM cuentasporpagar WHERE idproveedor = %s ORDER BY nofactura ASC", GetSQLValueString($colname_rsCuentas, "int"));
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

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>DETALLE POR PROVEEDOR</title>
<script src="js/funciones.js"></script>


<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body onload="print();">
<?php 
$concepto = array("Factura: ","Nota de Credito: ");

$estados = array("<img src=\"images/state1.png\" width=\"24\" height=\"24\" title=\"Abierta\" />","<img src=\"images/Cobrar.png\" alt=\"\" width=\"24\" height=\"24\" title=\"Pagada\"/>","<img src=\"images/Terminar.png\" width=\"24\" height=\"24\"  title=\"Cancelada\"/>");


$meses = array (1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");


//cargamos el tipo de cambio
require_once('tipoCambio.php');

//chequemos si tipo de cambio a cambiado para guardarlo

if(isset($_GET['tipocambio']) && ($_GET['tipocambio'] != $_tipoCambio) ){

$fichero=fopen("tipoCambio.php","w");
$grabar=fwrite($fichero,'<?php $_tipoCambio = 13.5;  ?>');
$cerrar=fclose($fichero);
$_tipoCambio = $_GET['tipocambio'];
}

$monedadestino = (isset($_GET['mostraren']))?$_GET['mostraren'] : 0;

$signo = array(0=>"$",1=>"US$");

?>
<form name="filtros" method="get">
<table border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center" width="100%">
  <!--DWLayoutTable-->
  <tr>
    <td colspan="33" valign="top" class="titulos">CUENTAS POR PAGAR:</td>
    </tr>
  <tr>
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
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <tr>
    <td></td>
    <td colspan="2" valign="top">PROVEEDOR:</td>
    <td>&nbsp;</td>
    <td colspan="15" valign="top"><?php echo $row_rsProveedor['nombrecomercial']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
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
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td colspan="2" valign="top">CONTACTO:</td>
    <td></td>
    <td colspan="15" valign="top"><?php echo $row_rsProveedor['contacto']; ?></td>
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
  <tr>
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
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td colspan="2" valign="top">TELEFONO:</td>
    <td>&nbsp;</td>
    <td colspan="7" valign="top"><?php echo $row_rsProveedor['telefono']; ?></td>
    <td>&nbsp;</td>
    <td colspan="4" valign="top">EMAIL:</td>
    <td colspan="11" valign="top"><?php echo $row_rsProveedor['email']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
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
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td colspan="2" valign="top">BANCO:</td>
    <td>&nbsp;</td>
    <td colspan="5" valign="top"><?php echo $row_rsProveedor['banco']; ?></td>
    <td>&nbsp;</td>
    <td colspan="5" align="right" valign="top">NO.CTA:</td>
    <td>&nbsp;</td>
    <td colspan="9" valign="top"><?php echo $row_rsProveedor['ctabancaria']; ?></td>
    <td>&nbsp;</td>
    <td colspan="3" align="right" valign="top">CLABE:</td>
    <td colspan="3" valign="top"><?php echo $row_rsProveedor['clabe']; ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
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
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  <tr>
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
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <tr>
    <td></td>
    <td colspan="30" valign="top"><table border="0" cellpadding="0" cellspacing="0" class="resaltarTabla" width="100%">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td valign="top" width="10%">Estado</td>
          <td valign="top" width="25%">Concepto:</td>
          <td align="center" valign="top" width="15%">Fecha</td>
          <td align="center" valign="top" width="15%">Fecha Vence</td>
          <td colspan="2" align="center" valign="top" width="20%">Monto</td>
          <td valign="top" width="15%">Fecha Pago</td>
        </tr>
      <tr>
        <td height="5"></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <?php $totaldolares=0;$totalpesos=0;?>
      <?php do { ?>
        <tr>
          <td valign="top"><?php echo $estados[$row_rsCuentas['estado']];?></td>
          <td valign="top"><?php echo $concepto[$row_rsCuentas['tipo']]; ?><?php echo $row_rsCuentas['nofactura']; ?></td>
          <td align="center" valign="top"><?php echo formatDate($row_rsCuentas['fecha']); ?></td>
          <td align="center" valign="top"><?php echo formatDate($row_rsCuentas['fechavencimiento']); ?></td>
          <td valign="top"><?php echo  $signo[$row_rsCuentas['moneda']];?></td>
          <td align="right" valign="top"><?php echo format_money($row_rsCuentas['monto']); ?> </td>
          <td align="right" valign="top"><?php echo formatDate($row_rsCuentas['fechapago']); ?></td>
        </tr>
        <?php 
			if($row_rsCuentas['moneda'] == 0){
				if($row_rsCuentas['tipo']==1)
					$totalpesos = $totalpesos - $row_rsCuentas['monto'];
				else	
					$totalpesos = $totalpesos + $row_rsCuentas['monto'];
				}else{
					if($row_rsCuentas['tipo']==1)
					$totaldolares = $totaldolares - $row_rsCuentas['monto'];
					else
					$totaldolares = $totaldolares + $row_rsCuentas['monto'];
				}
				
				?>
        <?php } while ($row_rsCuentas = mysql_fetch_assoc($rsCuentas)); ?>
      
      <tr>
        <td height="13"></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      
      
      
      
      
      
      
    </table></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
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
    <td colspan="5" align="right" valign="top">TOTAL: $</td>
    <td colspan="4" align="right" valign="top"><?php echo format_money($totalpesos);?></td>
    <td>&nbsp;</td>
    <td colspan="5" align="right" valign="top">TOTAL: US$</td>
    <td colspan="3" valign="top" align="right"><?php echo format_money($totaldolares);?></td>
    <td></td>
    <td></td>
  </tr>
 
  
  <tr>
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
    <td colspan="7" valign="top"></td>
    <td></td>
    <td></td>
  </tr>
</table>

<input type="hidden" name="idproveedor" value="<?php echo $_GET['idproveedor'];?>" />
</form>
</body>
</html>
<?php
mysql_free_result($rsProveedor);

mysql_free_result($rsCuentas);
?>
