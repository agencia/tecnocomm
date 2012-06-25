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

<body>
<form name="reporteproveedores" method="get">
<table width="840" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="19" colspan="12" valign="top" class="titulos">CUENTAS POR PAGAR</td>
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
        <option value="3">Fecha de Vencimiento</option>
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
    <td>&nbsp;</td>
    <td><a href="reporteProveedoresPrint.php?<?php echo $_SERVER['QUERY_STRING'];?>" onclick="NewWindow(this.href,'Imprimir',800,800,'yes');return false;"><img src="images/Imprimir2.png" width="24" height="24" /></a></td>
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
        <td height="18" valign="top">Estado</td>
          <td colspan="3" valign="top">Concepto</td>
          <td colspan="3" align="center" valign="top">Monto</td>
          <td colspan="2" align="center" valign="top">Fecha</td>
          <td colspan="2" align="center" valign="top">Fecha Vence</td>
          <?php if(isset($_GET['mostrar'])&&($_GET['mostrar']!=1)){?>
          <td colspan="2" align="center" valign="top">Fecha Pago</td><?php } ?>
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
          <?php if(isset($_GET['mostrar'])&&($_GET['mostrar']!=1)){?>
          <td colspan="2" align="center" valign="top"><?php echo $cuenta['fechapago'];?></td>
          <?php } ?>
          <td></td>
        </tr>
        <?php if($cuenta['moneda'] == 0){
		
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
