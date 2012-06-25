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

$colname_rsFactura = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsFactura = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFactura = sprintf("SELECT * FROM factura WHERE idfactura = %s", GetSQLValueString($colname_rsFactura, "int"));
$rsFactura = mysql_query($query_rsFactura, $tecnocomm) or die(mysql_error());
$row_rsFactura = mysql_fetch_assoc($rsFactura);
$totalRows_rsFactura = mysql_num_rows($rsFactura);


    $tipopago = array ("Cheque","Transferencia","Efectivo","Otro");
	
	

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="pagarFactura" id="pagarFactura">
  <table width="489" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
    <!--DWLayoutTable-->
    <tr class="titleTabla">
      <td height="20" colspan="8" valign="top" >Pago de Factura:</td>
    </tr>
    <tr>
      <td width="8" height="10"></td>
      <td width="111"></td>
      <td width="13"></td>
      <td width="208"></td>
      <td width="49"></td>
      <td width="24"></td>
      <td width="52"></td>
      <td width="24"></td>
    </tr>
    <tr>
      <td height="21">&nbsp;</td>
      <td valign="top">TIPO PAGO</td>
      <td>&nbsp;</td>
      <td valign="top"><label><?php echo $tipopago[$row_rsFactura['tipopago']]; ?></label></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
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
    <tr>
      <td height="20"></td>
      <td valign="top">REFERENCIA</td>
      <td></td>
      <td colspan="3" valign="top"><label><?php echo $row_rsFactura['referenciabnca']; ?></label></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
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
    </tr>
    <tr>
      <td height="20"></td>
      <td valign="top">BANCO</td>
      <td></td>
      <td colspan="3" valign="top"><label><?php echo $row_rsFactura['banco']; ?></label></td>
      <td></td>
      <td></td>
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
    </tr>
    <tr>
      <td height="20"></td>
      <td valign="top">FECHA PAGO</td>
      <td></td>
      <td colspan="3" valign="top"><label></label>
        <label></label>
        <label><?php echo formatDate($row_rsFactura['fechapago']); ?></label></td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td height="15"></td>
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
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td colspan="2" valign="top"><input type="button" value="Aceptar"  onclick="window.close();"/></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="19"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      <td></td>
      <td></td>
    </tr>
  </table>
  <input type="hidden" name="idfactura" value="<?php echo $_GET['idfactura'];?>" />
  <input type="hidden" name="MM_update" value="pagarFactura" />
</form>
</body>
</html>
<?php
mysql_free_result($rsFactura);
?>
