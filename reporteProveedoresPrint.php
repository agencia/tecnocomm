<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "systemFail.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

$query = "SELECT p.idproveedor, p.nombrecomercial, c.nofactura, c.tipo, c.monto, c.moneda, c.fecha, c.fechapago, c.fechavencimiento, c.estado FROM proveedor p,cuentasporpagar c WHERE p.idproveedor = c.idproveedor";

if(isset($_GET['buscar'])){
						$query = sprintf("SELECT p.idproveedor, p.nombrecomercial, c.nofactura, c.tipo, c.monto, c.moneda, c.fecha, c.fechapago, c.fechavencimiento, c.estado FROM proveedor p,cuentasporpagar c WHERE p.idproveedor = c.idproveedor AND p.nombrecomercial like %s",GetSQLValueString('%'.$_GET['buscar'].'%', "text"));
					}

if(isset($_GET['filtrar'])){

				if($_GET['mostrar'] != 0){
						
						switch($_GET['mostrar']){
						
								case 1: $query = $query." AND c.estado = 0"; 
								break;
								case 2:$query = $query." AND c.estado = 1"; 
								break;
								case 3:$query = $query." AND c.estado = 2"; 
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
								case 4:$query = $query.sprintf(" AND fechapago BETWEEN %s AND %s ",GetSQLValueString($datei, "date"),GetSQLValueString($datei, "date")); 
								break;
						}	
				}	
	}




mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedores = $query;//"SELECT * FROM proveedor p,cuentasporpagar c WHERE p.idproveedor = c.idproveedor";
$rsProveedores = mysql_query($query_rsProveedores, $tecnocomm) or die(mysql_error());
$row_rsProveedores = mysql_fetch_assoc($rsProveedores);


$concepto = array("Factura: ","Nota de Credito: ");

$estados = array("<img src=\"images/state1.png\" width=\"24\" height=\"24\" title=\"Abierta\" />","<img src=\"images/Cobrar.png\" alt=\"\" width=\"24\" height=\"24\" title=\"Pagada\"/>","<img src=\"images/Terminar.png\" width=\"24\" height=\"24\"  title=\"Cancelada\"/>");

$i=0;
do{

$proveedor[$row_rsProveedores['idproveedor']]['nombre'] =  $row_rsProveedores['nombrecomercial'];
$proveedor[$row_rsProveedores['idproveedor']]['cuentas'][$i]['estado'] = $estados[$row_rsProveedores['estado']];
$proveedor[$row_rsProveedores['idproveedor']]['cuentas'][$i]['concepto'] =$concepto[$row_rsProveedores['tipo']].$row_rsProveedores['nofactura'];
$proveedor[$row_rsProveedores['idproveedor']]['cuentas'][$i]['tipo'] = $row_rsProveedores['tipo'];
$proveedor[$row_rsProveedores['idproveedor']]['cuentas'][$i]['moneda'] = $row_rsProveedores['moneda'];
$proveedor[$row_rsProveedores['idproveedor']]['cuentas'][$i]['monto'] = $row_rsProveedores['monto'];
$proveedor[$row_rsProveedores['idproveedor']]['cuentas'][$i]['fecha'] = formatDate($row_rsProveedores['fecha']);
$proveedor[$row_rsProveedores['idproveedor']]['cuentas'][$i]['fechavence'] = formatDate($row_rsProveedores['fechavencimiento']);
$proveedor[$row_rsProveedores['idproveedor']]['cuentas'][$i]['fechapago'] = formatDate($row_rsProveedores['fechapago']);

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

<body onload="print();">
<form name="reporteproveedores" method="get">
<table border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center" width="100%">
  <!--DWLayoutTable-->
  <tr>
    <td colspan="12" valign="top" class="titulos">CUENTAS POR PAGAR</td>
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
  </tr>

  <tr>
    <td colspan="4">Fecha de Impresion:<?php echo date("d/m/Y G:i");?></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="4" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="305" height="29" align="right" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
      </tr>
    </table>    </td>
  </tr>
 <?php foreach($proveedor as $pro){?>
  <tr>
    <td colspan="12" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td height="18" colspan="2" valign="top" class="realte">Proveedor:</td>
          <td colspan="12" valign="top" class="realte"><?php echo $pro['nombre'];?></td>
        </tr>
      <tr>
        <td width="64" height="4"></td>
          <td width="43"></td>
          <td width="83"></td>
          <td width="125"></td>
          <td width="26"></td>
          <td width="50"></td>
          <td width="58"></td>
          <td width="60"></td>
          <td width="58"></td>
          <td width="44"></td>
          <td width="87"></td>
          <td width="42"></td>
          <td width="78"></td>
          <td width="22"></td>
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
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      <tr>
        <td height="18" valign="top" width="15%">Estado</td>
          <td colspan="3" valign="top" width="25%">Concepto</td>
          <td colspan="3" align="center" valign="top" width="15%">Monto</td>
          <td colspan="2" align="center" valign="top" width="15%">Fecha</td>
          <td colspan="2" align="center" valign="top" width="15%">Fecha Vence</td>
          <td colspan="2" align="center" valign="top" width="15%">Fecha Pago</td>
          <td>&nbsp;</td>
        </tr>
        <?php $tpesos = $tdolares = 0; foreach($pro['cuentas'] as $cuenta){ ?>
      <tr>
        <td height="20" valign="top"><?php echo $cuenta['estado'];?></td>
          <td colspan="3" valign="top"><?php echo $cuenta['concepto'];?></td>
          <td valign="top"><?php echo $signo[$cuenta['moneda']];?></td>
          <td colspan="2" align="right" valign="top"><?php echo format_money($cuenta['monto']);?></td>
          <td colspan="2" align="center" valign="top"><?php echo $cuenta['fecha'];?></td>
          <td colspan="2" align="center" valign="top"><?php echo $cuenta['fechavence'];?></td>
          <td colspan="2" align="center" valign="top"><?php echo $cuenta['fechapago'];?></td>
          <td></td>
        </tr>   <?php if($cuenta['moneda'] == 0){
		
		if(	$cuenta['tipo'] == 1)
			$tpesos = $tpesos -$cuenta['monto']; 
			else
			$tpesos = $tpesos +$cuenta['monto']; 
		
		} else {
			if(	$cuenta['tipo'] == 1)
			$tdolares = $tdolares -$cuenta['monto'];
			else
			$tdolares = $tdolares +$cuenta['monto'];
			}
			
		?>
        
        <?php  } ?>
      <tr>
        <td height="20"></td>
          <td></td>
          <td></td>
          <td></td>
          <td colspan="2" align="right" valign="top">TOTAL $:</td>
          <td colspan="2" align="right" valign="top"><?php echo format_money($tpesos);?></td>
          <td colspan="2" align="right" valign="top">TOTAL US$:</td>
          <td colspan="2" align="right" valign="top"><?php echo format_money($tdolares);?></td>
          <td>&nbsp;</td>
          <td></td>
        </tr>
      
      
      
      
      
      
      
      
      
    </table></td>
  </tr>
  <?php $totalpesos = $totalpesos + $tpesos; $totaldolares = $totaldolares + $tdolares;?>
  <?php } ?>
  <tr>
    <td colspan="12" valign="top" class="resaltarTabla">TOTAL</td>
    </tr>
  <tr>
    <td></td>
    <td></td>
    <td colspan="3" valign="top" align="right"><span class="style1">TOTAL $:</span></td>
    <td colspan="2" align="right" valign="top"><span class="style1"><?php echo format_money($totalpesos);?></span></td>
    <td colspan="2" align="right" valign="top"><span class="style1">TOTAL US$:</span></td>
    <td colspan="2" align="right" valign="top"><span class="style1"><?php echo format_money($totaldolares);?></span></td>
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
    <td colspan="4" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="305" height="33" align="right" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
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
