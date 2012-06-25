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


$query = sprintf("SELECT *,(SELECT (sum(punitario*cantidad)*1.15) FROM detallefactura d WHERE d.idfactura = f.idfactura) AS total FROM factura f WHERE f.idcliente = %s", GetSQLValueString($_GET['idcliente'], "int"));

if(isset($_GET['filtrar'])){

	switch($_GET['filtrar']){
	
		case "Buscar":
					if(isset($_GET['buscar'])){
						//$query = sprintf("SELECT * FROM cuentasporpagar WHERE idproveedor = %s AND nofactura = %s",GetSQLValueString($_GET['idproveedor'], "int"),GetSQLValueString($_GET['buscar'], "int"));
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
	default: $query= $query." ORDER BY idfactura DESC".$orden[$_GET['formaordenar']];
}


$colname_rsCliente = "-1";
if (isset($_GET['idcliente'])) {
  $colname_rsCliente = $_GET['idcliente'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCliente = sprintf("SELECT * FROM cliente WHERE idcliente = %s", GetSQLValueString($colname_rsCliente, "int"));
$rsCliente = mysql_query($query_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);
$totalRows_rsCliente = mysql_num_rows($rsCliente);

$colname_rsContacto = "-1";
if (isset($_GET['idcliente'])) {
  $colname_rsContacto = $_GET['idcliente'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsContacto = sprintf("SELECT * FROM contactoclientes WHERE idcontacto = %s", GetSQLValueString($colname_rsContacto, "int"));
$rsContacto = mysql_query($query_rsContacto, $tecnocomm) or die(mysql_error());
$row_rsContacto = mysql_fetch_assoc($rsContacto);
$totalRows_rsContacto = mysql_num_rows($rsContacto);

$maxRows_rsFacturas = 30;
$pageNum_rsFacturas = 0;
if (isset($_GET['pageNum_rsFacturas'])) {
  $pageNum_rsFacturas = $_GET['pageNum_rsFacturas'];
}
$startRow_rsFacturas = $pageNum_rsFacturas * $maxRows_rsFacturas;

$colname_rsFacturas = "-1";
if (isset($_GET['idcliente'])) {
  $colname_rsFacturas = $_GET['idcliente'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas =$query;// sprintf("SELECT *,(SELECT SUM(punitario * cantidad) FROM detallefactura d WHERE d.idfactura = f.idfactura) AS total FROM factura f WHERE f.idcliente = %s", GetSQLValueString($colname_rsFacturas, "int"));
$query_limit_rsFacturas = sprintf("%s LIMIT %d, %d", $query_rsFacturas, $startRow_rsFacturas, $maxRows_rsFacturas);
$rsFacturas = mysql_query($query_limit_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);

if (isset($_GET['totalRows_rsFacturas'])) {
  $totalRows_rsFacturas = $_GET['totalRows_rsFacturas'];
} else {
  $all_rsFacturas = mysql_query($query_rsFacturas);
  $totalRows_rsFacturas = mysql_num_rows($all_rsFacturas);
}
$totalPages_rsFacturas = ceil($totalRows_rsFacturas/$maxRows_rsFacturas)-1;


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
$meses = array (1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$signo = array("$","US$");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>DETALLE POR PROVEEDOR</title>
<script src="js/funciones.js"></script>

<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body onload="print();">
<?php 

$monedadestino = (isset($_GET['mostraren']))?$_GET['mostraren'] : 0;

$concepto = array("Factura: ","Nota de Credito: ");

?>
<form name="detalle" method="get">
<table border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center" width="100%">
  <!--DWLayoutTable-->
  <tr>
    <td colspan="11" valign="top" class="titulos">CUENTAS POR COBRAR:</td>
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
  </tr>
  <tr>
    <td></td>
    <td colspan="2" valign="top">CLIENTE:</td>
    <td colspan="5" valign="top"><?php echo $row_rsCliente['nombre']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
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
  </tr>
  <tr>
    <td></td>
    <td colspan="2" valign="top">DIRECCION:</td>
    <td colspan="7" valign="top"><?php echo $row_rsCliente['direccionfacturacion']; ?></td>
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
  </tr>
  <tr>
    <td></td>
    <td valign="top">CONTACTO(S):</td>
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
  </tr>
  <tr class="titleTabla">
    <td></td>
    <td colspan="4" valign="top">NOMBRE</td>
    <td colspan="3" valign="top">TELEFONOS</td>
    <td colspan="2" valign="top">EMAIL</td>
    <td></td>
  </tr>
  <?php do { ?>
    <tr>
      <td></td>
      <td colspan="4" valign="top"><?php echo $row_rsContacto['nombre']; ?></td>
      <td colspan="3" valign="top"><?php echo $row_rsContacto['telefono']; ?>/ <?php echo $row_rsContacto['telefono2']; ?></td>
      <td colspan="2" valign="top"><?php echo $row_rsContacto['correo']; ?></td>
      <td></td>
    </tr>
    <?php } while ($row_rsContacto = mysql_fetch_assoc($rsContacto)); ?>
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
  </tr>
  



  <tr>
    <td></td>
    <td colspan="9" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td  valign="top" width="25%">FACTURA:</td>
          <td  valign="top" width="20%">Fecha</td>
          <td width="20%" colspan="2"  valign="top">Monto</td>
          <td  valign="top" width="20%" align="right">Fecha Pago</td>
    
      </tr>
      <tr>
        <td></td>
          <td></td>
          <td colspan="2"></td>
          <td></td>
          
      </tr>
      <?php $totalpesos=$totaldolasres = 0;?>
      <?php do { ?>
        <tr>
          <td height="24" valign="top"><?php echo $row_rsFacturas['numfactura']; ?></td>
          <td valign="top" align="left"><?php echo formatDate($row_rsFacturas['fecha']); ?></td>
          <td align="left" valign="top" width="5%"><?php echo $signo[$row_rsFacturas['moneda']]; ?></td>
          <td align="right" valign="top" width="15%"><?php echo format_money($row_rsFacturas['total']); ?></td>
          <td  valign="top" align="right" ><?php echo formatDate($row_rsFacturas['fechapago']); ?></td>
          
        </tr>
        <?php 
			if($row_rsFacturas['estado'] != 2)
			if($row_rsFacturas['moneda']==0){
						$totalpesos = $totalpesos + $row_rsFacturas['total'];
						}else{
						$totaldolares = $totaldolares + $row_rsFacturas['total'];
						}
				?>
        <?php } while ($row_rsFacturas = mysql_fetch_assoc($rsFacturas)); ?>
<tr>
        <td height="6"></td>
          <td></td>
          <td colspan="2"></td>
          <td></td>
     
      </tr>
      
      
      
      
      
      
      
    </table></td>
  <td>&nbsp;</td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td valign="top" align="right">TOTAL $:</td>
    <td align="right" valign="top"><?php echo format_money($totalpesos);?></td>
    <td>&nbsp;</td>
    <td colspan="2" valign="top">TOTAL: US$</td>
    <td valign="top"><?php echo format_money($totaldolares);?></td>
    <td></td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="idcliente" value="<?php echo $_GET['idcliente'];?>" />
</form>
</body>
</html>
<?php
mysql_free_result($rsCliente);

mysql_free_result($rsContacto);

mysql_free_result($rsFacturas);
?>