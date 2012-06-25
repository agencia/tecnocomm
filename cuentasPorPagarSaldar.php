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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "pagarCuenta")) {
  
   
  $updateSQL = sprintf("UPDATE cuentasporpagar SET estado=%s,fechapago=%s, tipopago=%s,referencia=%s, tipocambio=%s WHERE idcuenta=%s",
                       GetSQLValueString(1, "int"),
					    GetSQLValueString($_POST['fechapago'], "date"),
					   GetSQLValueString($_POST['tipopago'], "int"),
					   GetSQLValueString($_POST['referencia'], "text"),
					   GetSQLValueString($_POST['tipocambio'], "double"),
                       GetSQLValueString($_POST['idcuenta'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
  
  
  


  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsCuenta = "-1";
if (isset($_GET['idcuenta'])) {
  $colname_rsCuenta = $_GET['idcuenta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCuenta = sprintf("SELECT * FROM cuentasporpagar c,proveedor p WHERE idcuenta = %s AND p.idproveedor = c.idproveedor", GetSQLValueString($colname_rsCuenta, "int"));
$rsCuenta = mysql_query($query_rsCuenta, $tecnocomm) or die(mysql_error());
$row_rsCuenta = mysql_fetch_assoc($rsCuenta);
$totalRows_rsCuenta = mysql_num_rows($rsCuenta);

$colname_rsProveedor = "-1";
if (isset($_GET['idcuenta'])) {
  $colname_rsProveedor = $_GET['idcuenta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedor = sprintf("SELECT p.* FROM cuentasporpagar c, proveedor p WHERE idcuenta = %s AND c.idproveedor = p.idproveedor", GetSQLValueString($colname_rsProveedor, "int"));
$rsProveedor = mysql_query($query_rsProveedor, $tecnocomm) or die(mysql_error());
$row_rsProveedor = mysql_fetch_assoc($rsProveedor);
$totalRows_rsProveedor = mysql_num_rows($rsProveedor);

//cargamos tipo de cambio
require_once('tipoCambio.php');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Registrar pago Cuenta</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css"  rel="stylesheet" type="text/css"/>
<script language="javascript" src="js/calendario.js"></script>
</head>

<body>
<?php 
$concepto = array("Factura: ","Nota de Credito: ");
$signo = array("$","US$");
?>
<form action="<?php echo $editFormAction; ?>" name="pagarCuenta" method="POST">
<table width="739" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="22" colspan="12" valign="top">REGISTAR PAGO DE CUENTA</td>
  </tr>
  <tr>
    <td width="25" height="11"></td>
    <td width="120"></td>
    <td width="14"></td>
    <td width="36"></td>
    <td width="87"></td>
    <td width="113"></td>
    <td width="83"></td>
    <td width="106"></td>
    <td width="100"></td>
    <td width="12"></td>
    <td width="24"></td>
    <td width="17"></td>
  </tr>
  
  <tr>
    <td height="22">&nbsp;</td>
    <td valign="top">PROVEEDOR:</td>
    <td>&nbsp;</td>
    <td colspan="7" valign="top"><?php echo $row_rsCuenta['nombrecomercial']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="9"></td>
    <td></td>
    <td></td>
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
    <td valign="top">CONCEPTO:</td>
    <td>&nbsp;</td>
    <td colspan="7" valign="top"><?php echo $concepto[$row_rsCuenta['tipo']]; ?> <?php echo $row_rsCuenta['nofactura']; ?></td>
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
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="21"></td>
    <td valign="top">MONTO:</td>
    <td></td>
    <td valign="top"><?php echo $signo[$row_rsCuenta['moneda']]; ?></td>
    <td align="right" valign="top"><?php echo format_money($row_rsCuenta['monto']); ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td valign="top"></td>
    <td valign="top"></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
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
  </tr>
  
  <tr>
    <td height="20"></td>
    <td valign="top">FECHA:</td>
    <td></td>
    <td colspan="2" valign="top"><?php echo $row_rsCuenta['fecha']; ?></td>
    <td>&nbsp;</td>
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
  </tr>
  
  
  
  
  
  
  <tr>
    <td height="22"></td>
    <td valign="top">FECHA VENCE:</td>
    <td>&nbsp;</td>
    <td colspan="2" valign="top"><?php echo $row_rsCuenta['fechavencimiento']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="6"></td>
    <td></td>
    <td></td>
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
    <td valign="top">TIPO PAGO:</td>
    <td></td>
    <td colspan="3" valign="top"><label>
      <select name="tipopago" id="tipopago">
        <option value="0" selected="selected">Cheque</option>
        <option value="1">Transferencia</option>
        <option value="2">Efectivo</option>
        <option value="3">Otro</option>
      </select>
    </label></td>
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
  </tr>
  <tr>
    <td height="23"></td>
    <td valign="top">REFERNCIA:</td>
    <td></td>
    <td colspan="3" valign="top"><input type="text" name="referencia" id="referencia" /></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="22"></td>
    <td>FECHA DE PAGO:</td>
    <td></td>
    <td colspan="2"><input type="text" name="fechapago" id="referencia2" class="fecha" /></td>
    <td></td>
    <td></td>
    <td><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td colspan="3" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="22"></td>
    <td>TIPO DE CAMBIO:</td>
    <td></td>
    <td colspan="2"><input type="text" name="tipocambio" id="referencia3" /></td>
    <td></td>
    <td></td>
    <td><a href="cuentasPorPagarCancelar.php?idcuenta=<?php echo $_GET['idcuenta'];?>"><input type="button" value="CANCELAR FACTURA" /></a></td>
    <td colspan="3" valign="top"><input type="submit" value="REALIZAR PAGO" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="39"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="idcuenta" value="<?php echo $_GET['idcuenta'];?>" />
<input type="hidden" name="MM_update" value="pagarCuenta" />
<input type="hidden" name="MM_insert" value="pagarCuenta" />
</form>
</body>
</html>
<?php
mysql_free_result($rsCuenta);

mysql_free_result($rsProveedor);
?>
