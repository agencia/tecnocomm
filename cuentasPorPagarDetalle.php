<?php require_once('Connections/tecnocomm.php'); ?>
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

$query = sprintf("SELECT cp.*, (SELECT SUM(a.monto) FROM cuentasporpagar_abonos a WHERE a.idcuenta = cp.idcuenta ) as abonado FROM cuentasporpagar cp WHERE cp.idproveedor = %s ", GetSQLValueString($_GET['idproveedor'], "int"));

if(isset($_GET['filtrar'])){

	switch($_GET['filtrar']){
	
		case "Buscar":
					if(isset($_GET['buscar'])){
						$query = sprintf("SELECT cp.*, (SELECT SUM(a.monto) FROM cuentasporpagar_abonos a WHERE a.idcuenta = cp.idcuenta ) as abonado FROM cuentasporpagar cp WHERE cp.idproveedor = %s AND cp.nofactura = %s",GetSQLValueString($_GET['idproveedor'], "int"),GetSQLValueString($_GET['buscar'], "int"));
					}
		break;
		case "Filtrar":
				if($_GET['mostrar'] != 0){
						
						switch($_GET['mostrar']){
						
								case 1: $query = $query." AND cp.estado = 0"; 
								break;
								case 2:$query = $query." AND cp.estado = 1"; 
								break;
								case 3:$query = $query." AND cp.estado = 2"; 
								break;
							}
						
						}
				if($_GET['periodo'] != 1){
						
							$datei = $_GET['anoi']."-".$_GET['mesi']."-".$_GET['diai'];
							$datef = $_GET['anof']."-".$_GET['mesf']."-".$_GET['diaf'];
							
						
						switch($_GET['periodo']){
						
								case 2: $query = $query.sprintf(" AND cp.fecha BETWEEN %s AND %s ",GetSQLValueString($datei, "date"),GetSQLValueString($datei, "date")); 
								break;
								case 3:$query = $query.sprintf(" AND cp.fechavencimiento BETWEEN %s AND %s ",GetSQLValueString($datei, "date"),GetSQLValueString($datei, "date")); 
								break;
						}	
				}	
	}
}

$orden = array(1=>"ASC","DESC");

switch($_GET['ordenar']){

	case 2: $query = $query." ORDER BY cp.fecha ".$orden[$_GET['formaordenar']];
	break;
	case 3: $query= $query." ORDER BY cp.fechavencimiento ".$orden[$_GET['formaordenar']];
	break;
	default: $query= $query." ORDER BY cp.nofactura ".$orden[$_GET['formaordenar']];
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
<link href="css/redmond/jquery.css"  rel="stylesheet" type="text/css"/>
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jqueryui.js"></script>
<script language="javascript" src="js/calendario.js"></script>
<script src="js/funciones.js"></script>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
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
<table width="854" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="28" colspan="33" valign="top" class="titulos">CUENTAS POR PAGAR:</td>
    </tr>
  <tr>
    <td width="2" height="11"></td>
    <td width="70"></td>
    <td width="38"></td>
    <td width="20"></td>
    <td width="25"></td>
    <td width="67"></td>
    <td width="20"></td>
    <td width="28"></td>
    <td width="13"></td>
    <td width="14"></td>
    <td width="29"></td>
    <td width="7"></td>
    <td width="5"></td>
    <td width="19"></td>
    <td width="10"></td>
    <td width="19"></td>
    <td width="10"></td>
    <td width="24"></td>
    <td width="28"></td>
    <td width="20"></td>
    <td width="28"></td>
    <td width="15"></td>
    <td width="19"></td>
    <td width="27"></td>
    <td width="28"></td>
    <td width="20"></td>
    <td width="11"></td>
    <td width="23"></td>
    <td width="37"></td>
    <td width="45"></td>
    <td width="45"></td>
    <td width="53"></td>
    <td width="30"></td>
  </tr>
  
  <tr>
    <td height="20"></td>
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
    <td height="5"></td>
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
    <td height="20"></td>
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
    <td height="5"></td>
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
    <td height="20"></td>
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
    <td height="5"></td>
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
    <td height="20"></td>
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
    <td height="25"></td>
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
    <td height="21"></td>
    <td colspan="2" valign="middle">Tipo Cambio:</td>
    <td colspan="3" valign="top"><input type="text" name="tipocambio" id="tipocambio" size="10" value="<?php echo $_tipoCambio;?>"/></td>
    <td>&nbsp;</td>
    <td colspan="6" valign="middle">Mostrar En:</td>
    <td colspan="7" valign="middle"><select name="mostraren" id="mostraren">
      <option value="0" <?php if (!(strcmp(0, $_GET['mostraren']))) {echo "selected=\"selected\"";} ?>>Pesos</option>
      <option value="1" <?php if (!(strcmp(1, $_GET['mostraren']))) {echo "selected=\"selected\"";} ?>>Dolares</option>
    </select>    </td>
    <td colspan="5" valign="top"><input type="submit" name="modificartipo" id="modificartipo" value="Actualizar" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
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
    <td height="24"></td>
    <td valign="top">Mostrar:</td>
    <td colspan="7" valign="top"><select name="mostrar" id="mostrar">
      <option value="0" <?php if (!(strcmp(0, $_GET['mostrar']))) {echo "selected=\"selected\"";} ?>>Todas</option>
      <option value="1" <?php if (!(strcmp(1, $_GET['mostrar']))) {echo "selected=\"selected\"";} ?>>Abiertas</option>
      <option value="2" <?php if (!(strcmp(2, $_GET['mostrar']))) {echo "selected=\"selected\"";} ?>>Pagadas</option>
      <option value="3" <?php if (!(strcmp(3, $_GET['mostrar']))) {echo "selected=\"selected\"";} ?>>Canceladas</option>
    </select>
      <input type="submit" name="filtrar" id="filtrar" value="Filtrar" />    </td>
    <td colspan="21" valign="top">Buscar:
      <input name="buscar" type="text" id="buscar" value="<?php echo $_GET['buscar'] ?>" />
      <select name="buscarpor" id="buscarpor">
        <option selected="selected" value="1" <?php if (!(strcmp(1, $_GET['buscarpor']))) {echo "selected=\"selected\"";} ?>>No. Factura</option>
        <option value="2" <?php if (!(strcmp(2, $_GET['buscarpor']))) {echo "selected=\"selected\"";} ?>>Fecha</option>
<option value="3" <?php if (!(strcmp(3, $_GET['buscarpor']))) {echo "selected=\"selected\"";} ?>>Vence</option>
    </select>
    <input type="submit" name="filtrar" id="filtrar" value="Buscar" /></td>
    <td>&nbsp;</td>
    <td><a href="cuentasPorPagarDetallePrint.php?<?php echo $_SERVER['QUERY_STRING'];?>" onclick="NewWindow(this.href,'Imprimir',800,800,'yes');return false;"><img src="images/Imprimir2.png" width="24" height="24" /></a></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="8"></td>
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
    <td height="21"></td>
    <td colspan="4" valign="middle">Filtrar Por Periodo:</td>
    <td colspan="9" valign="top"><select name="periodo" id="periodo">
      <option value="1" selected="selected">No Filtrar Por Periodo</option>
      <option value="2">Fecha</option>
      <option value="3">Fecha de Vencimiento</option>
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
    <td height="5"></td>
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
    <td height="26"></td>
    <td colspan="32" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="resaltarTabla">
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
    <td height="44"></td>
    <td colspan="2" valign="top">Ordenar Por:</td>
    <td colspan="5" valign="top"><select name="ordenar" id="ordenar">
      <option value="1" selected="selected" <?php if (!(strcmp(1, $_GET['ordenar']))) {echo "selected=\"selected\"";} ?>>Concepto</option>
      <option value="2" <?php if (!(strcmp(2, $_GET['ordenar']))) {echo "selected=\"selected\"";} ?>>Fecha</option>
<option value="3" <?php if (!(strcmp(3, $_GET['ordenar']))) {echo "selected=\"selected\"";} ?>>Fecha Vencimiento</option>
    </select></td>
    <td colspan="9" valign="top"><select name="formaordenar" id="formaordenar">
      <option value="1" selected="selected" <?php if (!(strcmp(1, $_GET['formaordenar']))) {echo "selected=\"selected\"";} ?>>Ascendente</option>
      <option value="2" <?php if (!(strcmp(2, $_GET['formaordenar']))) {echo "selected=\"selected\"";} ?>>Descendente</option>
                </select></td>
    <td colspan="4" valign="top"><input type="submit" name="filtrar2" id="filtrar2" value="Ordenar" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="7" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="219" height="27" align="right" valign="top"><a href="<?php printf("%s?pageNum_rsCuentas=%d%s", $currentPage, 0, $queryString_rsCuentas); ?>">
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
      </tr>
    </table>    </td>
    <td></td>
    <td></td>
  </tr>
  
  
  <tr>
    <td height="85"></td>
    <td colspan="30" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="resaltarTabla">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="96" height="18" valign="top">Estado</td>
          <td width="242" valign="top">Concepto:</td>
          <td width="133" align="center" valign="top">Fecha</td>
          <td width="131" align="center" valign="top">Fecha Vence</td>
          <td colspan="2" align="center" valign="top">Monto</td>
          <td colspan="2" align="center" valign="top">Saldo</td>
          <td width="107" valign="top">Opciones</td>
        </tr>
      <tr>
        <td height="5"></td>
          <td></td>
          <td></td>
          <td></td>
          <td width="22"></td>
          <td width="74"></td>
          <td width="22"></td>
          <td width="74"></td>
          <td></td>
        </tr>
        <?php $totaldolares=0;$totalpesos=0;?>
      <?php do { ?>
        <tr>
          <td height="20" valign="top"><?php echo $estados[$row_rsCuentas['estado']];?></td>
          <td valign="top"><?php echo $concepto[$row_rsCuentas['tipo']]; ?><?php echo $row_rsCuentas['nofactura']; ?></td>
          <td align="center" valign="top"><?php echo formatDate($row_rsCuentas['fecha']); ?></td>
          <td align="center" valign="top"><?php echo formatDate($row_rsCuentas['fechavencimiento']); ?></td>
          <td valign="top"><?php echo  $signo[$row_rsCuentas['moneda']];?></td>
          <td align="right" valign="top"><?php echo format_money($row_rsCuentas['monto']); ?> </td>
          <td align="right" valign="top"><?php echo  $signo[$row_rsCuentas['moneda']];?></td>
          <td align="right" valign="top"><?php echo format_money($row_rsCuentas['monto']-$row_rsCuentas['abonado']); ?> </td>
          <td align="right" valign="top"><a href="editar.Cuenta.php?idcuenta=<?php echo $row_rsCuentas['idcuenta']; ?>" class="popup"><img src="images/Edit.png" title="EDITAR CUENTA" width="24" border="0" height="24" /></a>
              <?php if($row_rsCuentas['estado'] == 1){ ?><a href="#" onclick="NewWindow('cuentasPorPagarDetallePago.php?idcuenta=<?php echo $row_rsCuentas['idcuenta']; ?>','Pagar Cuenta',800,800,'yes');" > <img src="images/Cobrar.png" width="24" height="24" /></a><?php } ?>  <?php if($row_rsCuentas['estado'] == 0){ ?><a href="#" onclick="NewWindow('ci/index.php/cuentas/porpagar/detalle/<?php echo $row_rsCuentas['idcuenta']; ?>','Pagar Cuenta',800,800,'yes');" ><img src="images/Pagar.png" /></a><?php } ?>
              <a href="eliminarCuenta.php?idcuenta=<?php echo $row_rsCuentas['idcuenta']; ?>" onclick="if(confirm('¿Eliminar la factura?')) { NewWindow(this.href,'eliminar Cuenta',300,300,'yes'); } return false;"><img src="images/eliminar.gif" title="ELIMINAR CUENTA" width="24" border="0" height="24"></a></td>
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
				$saldofinal += $row_rsCuentas['monto']-$row_rsCuentas['abonado'];
				?>
        <?php } while ($row_rsCuentas = mysql_fetch_assoc($rsCuentas)); ?>
      
      <tr>
        <td height="13"></td>
          <td></td>
    <td align="right" valign="top">&nbsp;</td>
    <td align="right" valign="top">&nbsp;</td>
    <td valign="top">&nbsp;$</td>
    <td align="right"><b><?php echo format_money($totalpesos);?></b></td>
    <td valign="top">&nbsp;$</td>
    <td align="right"><b><?php echo format_money($saldofinal);?></b></td>
    <td valign="top" align="right">&nbsp;</td>
        </tr>
      
      
      
      
      
      
      
    </table></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
<!--  </tr>
  <tr>
    <td height="27"></td>
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
  </tr>-->
 
  
  <tr>
    <td height="44"></td>
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
    <td colspan="7" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="219" height="29" align="right" valign="top"><a href="<?php printf("%s?pageNum_rsCuentas=%d%s", $currentPage, 0, $queryString_rsCuentas); ?>">
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
          </tr>
    </table></td>
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
