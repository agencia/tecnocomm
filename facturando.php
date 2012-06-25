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
<?php require_once('numtoletras.php');?>
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




$colname_rsFactura = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsFactura = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFactura = sprintf("SELECT * FROM factura f LEFT JOIN cliente c ON f.idcliente = c.idcliente WHERE idfactura = %s", GetSQLValueString($colname_rsFactura, "int"));
$rsFactura = mysql_query($query_rsFactura, $tecnocomm) or die(mysql_error());
$row_rsFactura = mysql_fetch_assoc($rsFactura);
$totalRows_rsFactura = mysql_num_rows($rsFactura);

$maxRows_rsDetalle = 30;
$pageNum_rsDetalle = 0;
if (isset($_GET['pageNum_rsDetalle'])) {
  $pageNum_rsDetalle = $_GET['pageNum_rsDetalle'];
}
$startRow_rsDetalle = $pageNum_rsDetalle * $maxRows_rsDetalle;

$colname_rsDetalle = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsDetalle = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT * FROM detallefactura WHERE idfactura = %s", GetSQLValueString($colname_rsDetalle, "int"));
$query_limit_rsDetalle = sprintf("%s LIMIT %d, %d", $query_rsDetalle, $startRow_rsDetalle, $maxRows_rsDetalle);
$rsDetalle = mysql_query($query_limit_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);

if (isset($_GET['totalRows_rsDetalle'])) {
  $totalRows_rsDetalle = $_GET['totalRows_rsDetalle'];
} else {
  $all_rsDetalle = mysql_query($query_rsDetalle);
  $totalRows_rsDetalle = mysql_num_rows($all_rsDetalle);
}
$totalPages_rsDetalle = ceil($totalRows_rsDetalle/$maxRows_rsDetalle)-1;

$colname_rsCotizacion = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsCotizacion = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT sb.identificador2,sb.idsubcotizacion FROM facturacotizacion f,subcotizacion sb WHERE f.idcotizacion = sb.idsubcotizacion AND f.idfactura = %s", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);


   require_once('lib/eventos.php');
	$evt = new evento(24,$_SESSION['MM_Userid'],"Factura modificada parqa cotizacion :".$row_rsCotizacion['identificador2']);
	$evt->registrar();

$moneda = array(0=>"Pesos",1=>"Dolares");


if(isset($_POST['guardar']) && $_POST['guardar'] == "true"){
		
		$query = sprintf("SELECT f.*,SUM(df.punitario * df.cantidad) AS montofactura FROM factura f LEFT JOIN detallefactura df ON f.idfactura = df.idfactura WHERE f.idfactura <> %s AND f.idcliente = %s GROUP BY f.idfactura ",GetSQLValueString($_POST['idfactura'],"int"),GetSQLValueString($row_rsFactura['idcliente'],"int"));
		mysql_select_db($database_tecnocomm, $tecnocomm) or die(mysql_error());
		$rs_query = mysql_query($query,$tecnocomm);
		
		$alertar = false;
		
		while($row = mysql_fetch_assoc($rs_query)){
			if($row['montofactura'] == $_POST['monto']){ $alertar = true;
			
			}
			
		}
		
		
		if(!$alertar || isset($_POST['siguardar'])){
		
		mysql_select_db($database_tecnocomm, $tecnocomm);
		$query_guardar = sprintf("UPDATE factura SET guardada = 1 WHERE idfactura = %s",
								 GetSQLValueString($_POST['idfactura'],"int"));
		$rs_guardar = mysql_query($query_guardar,$tecnocomm) or die(mysql_error());
		
		header("Location: printFacturaPDF.php?idfactura=".$_POST['idfactura']);
		}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Detalle de Facutra</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>
<script src="js/jquery.js" language="javascript"> </script>
<script language="javascript"  src="js/funciones.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
<body>
<?php if($alertar == true){?>
<p class="Estilo1"> Ya existe una factura por esta cantidad para este cliente, Desea continuar con la factura..</p>
     <form name="guardarFactura" method="post">
      <input type="submit" value="Guardar Factura" />
      <input type="hidden" name="idfactura" value="<?php echo $row_rsFactura['idfactura'];?>" />
      <input type="hidden" name="guardar" value="true" />
      <input type="hidden" name="siguardar" value="true" />
      </form>
<?php } ?>

<table width="100%" border="0" cellpadding="4" cellspacing="0" align="center" class="wrapper">
    <!--DWLayoutTable-->
    <tr>
      <td height="28" colspan="17"  valign="top" class="titulos">Factura: <?php echo $row_rsFactura['numfactura']; ?></td>
    </tr>
    <tr>
      <td width="70" height="16"></td>
      <td width="23"></td>
      <td width="60"></td>
      <td width="70"></td>
      <td width="88"></td>
      <td width="79"></td>
      <td width="60"></td>
      <td width="70"></td>
      <td width="88"></td>
      <td width="23"></td>
      <td width="98"></td>
      <td width="32"></td>
      <td width="23"></td>
      <td width="14"></td>
      <td width="51"></td>
      <td width="74"></td>
      <td width="14"></td>
    </tr>
    
    
    <tr>
      <td height="24" colspan="2" align="right"  valign="middle">Nombre:</td>
      <td colspan="11"  valign="middle"><?php echo $row_rsFactura['razonsocial']; ?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="24" colspan="2" align="right" valign="middle">Direccion:</td>
      <td colspan="11" valign="middle"><?php echo $row_rsFactura['direccionfacturacion']; ?></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="24" colspan="2" align="right" valign="middle">Telefono:</td>
      <td colspan="3" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
      <td>&nbsp;</td>
      <td colspan="2" valign="middle">Lugar de Expidicion:</td>
      <td colspan="6" valign="middle">Aguascalients, Ags.</td>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="24" colspan="2" align="right" valign="middle">Ciudad:</td>
      <td colspan="4" valign="middle"><?php echo $row_rsFactura['ciudadfacturacion']; ?></td>
      <td valign="middle">RFC:</td>
      <td colspan="7" valign="middle"><?php echo $row_rsFactura['rfc']; ?></td>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="24">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
    </tr>
    
    
    
    <tr>
      <td height="21" colspan="8" align="left" valign="middle"></td>
      <td><?php if($row_rsFactura['guardada'] == 0){?><a href="factura.addreferencia.php?idfactura=<?php echo $row_rsFactura['idfactura']; ?>" class="popup">Referencicas</a><?php } ?></td>
      <td>&nbsp;</td>
      <td colspan="2" valign="top"><?php if($row_rsFactura['guardada'] == 0){?><a href="facturandoAgregarConcepto.php?idfactura=<?php echo $row_rsFactura['idfactura']; ?>" class="popup">Agregar Producto</a><?php } ?></td>
      <td>&nbsp;</td>
      <td colspan="3" valign="top"><?php if($row_rsFactura['guardada'] == 0){?><a href="facturaAddConcepto.php?idfactura=<?php echo $row_rsFactura['idfactura']; ?>" class="popup">Agregar Concepto</a><?php } ?></td>
      <td>&nbsp;</td>
    </tr>
    
    <tr>
      <td height="16"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
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
      <td height="24" valign="top">Partida</td>
      <td colspan="2" valign="top">Cantidad</td>
      <td valign="top">Unidad</td>
      <td colspan="5" valign="top">Concepto</td>
      <td colspan="2" valign="top">Precio Unitario</td>
      <td colspan="4" valign="top">Importe</td>
      <td colspan="2" valign="top">Opcion</td>
    </tr>
    <?php $subtotal=0;?>
    <?php do { ?>
      <tr>
<?php if ($totalRows_rsDetalle > 0) { // Show if recordset not empty ?>
                <td height="29" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
          <td colspan="2" valign="top"><?php echo $row_rsDetalle['cantidad']; ?></td>
          <td valign="top"><?php echo $row_rsDetalle['unidad']; ?></td>
          <td colspan="5" valign="top"><?php echo $row_rsDetalle['concepto']; ?></td>
          <td colspan="2" valign="top"><?php echo format_money($row_rsDetalle['punitario']); ?></td>
          <td colspan="4" valign="top"><?php echo format_money($row_rsDetalle['punitario'] * $row_rsDetalle['cantidad']); ?> </td>
          <td colspan="2" valign="top"><a href="modificarConcepto.php?iddetalle=<?php echo $row_rsDetalle['iddetalle'];?>" onclick="NewWindow(this.href,'Asignar Concepto',400,250,'yes');return false;"><img src="images/Edit.png" width="24" height="24" title="Modifcar Concepto"/></a><a href="quitarConceptoFactura.php?idconcepto=<?php echo $row_rsDetalle['iddetalle']?>" onclick="NewWindow(this.href,'Quitar Concepto',350,250,'yes'); return false;"><img src="images/eliminar.gif" width="19" height="19" /></a></td>
          <?php } // Show if recordset not empty ?>
    </tr>
      <?php $subtotal = $subtotal + round($row_rsDetalle['punitario'] * $row_rsDetalle['cantidad'],2);?>
  
      <?php } while ($row_rsDetalle = mysql_fetch_assoc($rsDetalle)); ?>
<?php if ($totalRows_rsDetalle == 0) { // Show if recordset empty ?>
        <tr>
          <td height="22">&nbsp;</td>
          <td></td>
          <td colspan="13" align="center" valign="top"><span class="Estilo1">Agregue Uno o Mas Conceptos Utilizando las Opciones Agregar Producto o Concepto</span></td>
          <td></td>
          <td></td>
        </tr>
        <?php } // Show if recordset empty ?>
<tr>
      <td height="24">&nbsp;</td>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td colspan="5"><?php if(strlen( $row_rsFactura['referencia1'])>3){?>SEGUN COTIZACION: <?php echo $row_rsFactura['referencia1']; ?><?php } ?></td>
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
  <td height="24">&nbsp;</td>
  <td></td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td colspan="5"><?php if(strlen( $row_rsFactura['referencia2'])>3){?>SEGUN ORDEN DE SERVICIO: <?php echo $row_rsFactura['referencia2']; ?><?php } ?></td>
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
  <td height="24">&nbsp;</td>
  <td></td>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
  <td colspan="5"><?php if(strlen( $row_rsFactura['referencia3'])>3){?>SEGUN ORDEN DE COMPRA: <?php echo $row_rsFactura['referencia3']; ?><?php } ?></td>
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
  <td height="24">&nbsp;</td>
  <td></td>
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
  <td>&nbsp;</td>
  <td></td>
  <td></td>
  <td></td>
</tr>
    
    
    
    
    <tr>
      <td height="24">&nbsp;</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td colspan="2" align="right" valign="middle">SUB-TOTAL: $</td>
      <td colspan="4" align="right" valign="middle"><?php echo format_money($subtotal);?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="24">&nbsp;</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td colspan="2" align="right" valign="middle">I.V.A.</td>
      <td colspan="4" align="right" valign="middle"><?php $iva =  round($subtotal * .16); echo format_money($iva);?></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="27" colspan="9"><span class="style1">CANTIDAD CON LETRA: <? $t = $subtotal*1.16; echo num2letras(money_format('%i',($subtotal + $iva)),false,true,$row_rsFactura['moneda']); ?>
      </span></td>
      <td colspan="2" align="right" valign="middle">TOTAL: $</td>
      <td colspan="4" align="right" valign="middle"><span class="style1"><?php echo format_money($subtotal + $iva);?></span></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="35">&nbsp;</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="31">&nbsp;</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      <td colspan="3" valign="top">
      <?php if($row_rsFactura['guardada'] == 1){?>
      <a href="printFacturaPDF.php?idfactura=<?php echo $row_rsFactura['idfactura']; ?>" target="_blank"><img src="images/Imprimir2.png" width="24" height="24"  title="Imprimir Factura"/>IMPRIMIR</a>
      <?php }else{ ?>
      <form name="guardarFactura" method="post">
      <input type="submit" value="Guardar Factura" />
      <input type="hidden" name="monto" value="<?php echo $subtotal;?>" />
      <input type="hidden" name="idfactura" value="<?php echo $row_rsFactura['idfactura'];?>" />
      <input type="hidden" name="guardar" value="true" />
      </form>
	  <?php 
	  }
	  ?>
      
      </td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="28">&nbsp;</td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
    </tr>
</table>

</body></html>

<?php
mysql_free_result($rsFactura);

mysql_free_result($rsDetalle);

mysql_free_result($rsCotizacion);
?>
