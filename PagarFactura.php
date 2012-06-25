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

$colname_rsFacturas = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsFacturas = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = sprintf("SELECT *,(SELECT SUM(punitario * cantidad) FROM detallefactura d WHERE d.idfactura = f.idfactura) AS total FROM factura f WHERE f.idfactura = %s", GetSQLValueString($colname_rsFacturas, "int"));
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);

$fecha = $_POST['ano']."-".$_POST['mes']."-".$_POST['dia'];

if(isset($_POST['hacer']) && $_POST['hacer'] == "Cancelar Factura"){
$estado = 2;
}else
$estado = 1;

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "pagarFactura")) {
  $updateSQL = sprintf("UPDATE factura SET referenciabnca=%s, banco=%s, fechapago=%s, tipopago=%s,estado=%s WHERE idfactura=%s",
                       GetSQLValueString($_POST['referenciabnca'], "text"),
                       GetSQLValueString($_POST['banco'], "text"),
                       GetSQLValueString($fecha, "date"),
                       GetSQLValueString($_POST['tipopago'], "int"),
					   GetSQLValueString($estado, "int"),
                       GetSQLValueString($_POST['idfactura'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
  
  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
$signo = array("$","US$");
//cargamos tipo de cambio
require_once('tipoCambio.php');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Pagar Factura</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" name="pagarFactura" method="POST">
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
    <td valign="top"><label>
      <select name="tipopago" id="tipopago">
        <option value="0">Cheque</option>
        <option value="1">Transferencia</option>
        <option value="2">Efectivo</option>
        <option value="3">Otro</option>
      </select>
    </label></td>
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
    <td colspan="3" valign="top"><label>
      <input type="text" name="referenciabnca" id="referenciabnca" />
    </label></td>
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
    <td colspan="3" valign="top"><label>
      <input type="text" name="banco" id="banco" />
    </label></td>
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
    <td colspan="3" valign="top"><label>
        <select name="dia" id="dia">
          <option value="1" <?php if (!(strcmp(1, date("j")))) {echo "selected=\"selected\"";} ?>>1</option>
          <option value="2" <?php if (!(strcmp(2, date("j")))) {echo "selected=\"selected\"";} ?>>2</option>
          <option value="3" <?php if (!(strcmp(3, date("j")))) {echo "selected=\"selected\"";} ?>>3</option>
          <option value="4" <?php if (!(strcmp(4, date("j")))) {echo "selected=\"selected\"";} ?>>4</option>
          <option value="5" <?php if (!(strcmp(5, date("j")))) {echo "selected=\"selected\"";} ?>>5</option>
          <option value="6" <?php if (!(strcmp(6, date("j")))) {echo "selected=\"selected\"";} ?>>6</option>
          <option value="7" <?php if (!(strcmp(7, date("j")))) {echo "selected=\"selected\"";} ?>>7</option>
          <option value="8" <?php if (!(strcmp(8, date("j")))) {echo "selected=\"selected\"";} ?>>8</option>
          <option value="9" <?php if (!(strcmp(9, date("j")))) {echo "selected=\"selected\"";} ?>>9</option>
          <option value="10" <?php if (!(strcmp(10, date("j")))) {echo "selected=\"selected\"";} ?>>10</option>
          <option value="11" <?php if (!(strcmp(11, date("j")))) {echo "selected=\"selected\"";} ?>>11</option>
          <option value="12" <?php if (!(strcmp(12, date("j")))) {echo "selected=\"selected\"";} ?>>12</option>
<option value="13" <?php if (!(strcmp(13, date("j")))) {echo "selected=\"selected\"";} ?>>13</option>
          <option value="14" <?php if (!(strcmp(14, date("j")))) {echo "selected=\"selected\"";} ?>>14</option>
          <option value="15" <?php if (!(strcmp(15, date("j")))) {echo "selected=\"selected\"";} ?>>15</option>
          <option value="16" <?php if (!(strcmp(16, date("j")))) {echo "selected=\"selected\"";} ?>>16</option>
          <option value="17" <?php if (!(strcmp(17, date("j")))) {echo "selected=\"selected\"";} ?>>17</option>
          <option value="18" <?php if (!(strcmp(18, date("j")))) {echo "selected=\"selected\"";} ?>>18</option>
          <option value="19" <?php if (!(strcmp(19, date("j")))) {echo "selected=\"selected\"";} ?>>19</option>
          <option value="20" <?php if (!(strcmp(20, date("j")))) {echo "selected=\"selected\"";} ?>>20</option>
          <option value="21" <?php if (!(strcmp(21, date("j")))) {echo "selected=\"selected\"";} ?>>21</option>
<option value="22" <?php if (!(strcmp(22, date("j")))) {echo "selected=\"selected\"";} ?>>22</option>
          <option value="23" <?php if (!(strcmp(23, date("j")))) {echo "selected=\"selected\"";} ?>>23</option>
          <option value="24" <?php if (!(strcmp(24, date("j")))) {echo "selected=\"selected\"";} ?>>24</option>
          <option value="25" <?php if (!(strcmp(25, date("j")))) {echo "selected=\"selected\"";} ?>>25</option>
          <option value="26" <?php if (!(strcmp(26, date("j")))) {echo "selected=\"selected\"";} ?>>26</option>
          <option value="27" <?php if (!(strcmp(27, date("j")))) {echo "selected=\"selected\"";} ?>>27</option>
          <option value="28" <?php if (!(strcmp(28, date("j")))) {echo "selected=\"selected\"";} ?>>28</option>
          <option value="29" <?php if (!(strcmp(29, date("j")))) {echo "selected=\"selected\"";} ?>>29</option>
          <option value="30" <?php if (!(strcmp(30, date("j")))) {echo "selected=\"selected\"";} ?>>30</option>
<option value="31" <?php if (!(strcmp(31, date("j")))) {echo "selected=\"selected\"";} ?>>31</option>
        </select>
      </label>
      /
      <label>
      <select name="mes" id="mes">
        <option value="1" <?php if (!(strcmp(1, date("n")))) {echo "selected=\"selected\"";} ?>>Enero</option>
        <option value="2" <?php if (!(strcmp(2, date("n")))) {echo "selected=\"selected\"";} ?>>Febrero</option>
        <option value="3" <?php if (!(strcmp(3, date("n")))) {echo "selected=\"selected\"";} ?>>Marzo</option>
        <option value="4" <?php if (!(strcmp(4, date("n")))) {echo "selected=\"selected\"";} ?>>Abril</option>
        <option value="5" <?php if (!(strcmp(5, date("n")))) {echo "selected=\"selected\"";} ?>>Mayo</option>
        <option value="6" <?php if (!(strcmp(6, date("n")))) {echo "selected=\"selected\"";} ?>>Junio</option>
        <option value="7" <?php if (!(strcmp(7, date("n")))) {echo "selected=\"selected\"";} ?>>Julio</option>
        <option value="8" <?php if (!(strcmp(8, date("n")))) {echo "selected=\"selected\"";} ?>>Agosto</option>
        <option value="9" <?php if (!(strcmp(9, date("n")))) {echo "selected=\"selected\"";} ?>>Septimbre</option>
        <option value="10" <?php if (!(strcmp(10, date("n")))) {echo "selected=\"selected\"";} ?>>Octubre</option>
        <option value="11" <?php if (!(strcmp(11, date("n")))) {echo "selected=\"selected\"";} ?>>Noviembre</option>
        <option value="12" <?php if (!(strcmp(12, date("n")))) {echo "selected=\"selected\"";} ?>>Diciembre</option>
      </select>
      </label>
      /
      <label>
      <select name="ano" id="ano">
        <option value="2007" <?php if (!(strcmp(2007, date("Y")))) {echo "selected=\"selected\"";} ?>> 2007 </option>
        <option value="2008" <?php if (!(strcmp(2008, date("Y")))) {echo "selected=\"selected\"";} ?>> 2008 </option>
        <option value="2009" <?php if (!(strcmp(2009, date("Y")))) {echo "selected=\"selected\"";} ?>> 2009 </option>
        <option value="2010" <?php if (!(strcmp(2010, date("Y")))) {echo "selected=\"selected\"";} ?>> 2010 </option>
        <option value="2011" <?php if (!(strcmp(2011, date("Y")))) {echo "selected=\"selected\"";} ?>> 2011 </option>
        <option value="2012" <?php if (!(strcmp(2012, date("Y")))) {echo "selected=\"selected\"";} ?>> 2012 </option>
        <option value="2013" <?php if (!(strcmp(2013, date("Y")))) {echo "selected=\"selected\"";} ?>> 2013 </option>
        <option value="2014" <?php if (!(strcmp(2014, date("Y")))) {echo "selected=\"selected\"";} ?>> 2014 </option>
        <option value="2015" <?php if (!(strcmp(2015, date("Y")))) {echo "selected=\"selected\"";} ?>> 2015 </option>
        <option value="2016" <?php if (!(strcmp(2016, date("Y")))) {echo "selected=\"selected\"";} ?>> 2016 </option>
        <option value="2017" <?php if (!(strcmp(2017, date("Y")))) {echo "selected=\"selected\"";} ?>> 2017 </option>
      </select>
      </label></td>
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
    <td><input type="submit" name="hacer" value="Cancelar Factura" /></td>
    <td></td>
    <td colspan="2" valign="top"><input type="submit"  name="hacer" value="Aceptar" /></td>
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
mysql_free_result($rsFacturas);
?>
