<?php require_once('Connections/tecnocomm.php'); ?>
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


$_POST['fechavencimiento'] = $_POST['ano']."-".$_POST['mes']."-".$_POST['dia'];
$_POST['fecha'] = $_POST['anofecha']."-".$_POST['mesfecha']."-".$_POST['diafecha'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    
    //MM_insert2
    if($_POST["MM_insert2"] == "idordencompra"){
        mysql_select_db($database_tecnocomm, $tecnocomm);
        $query_rsOrden2 = sprintf("SELECT * FROM ordencompra WHERE idordencompra = %s ", $_POST['idordencompra']);
        $rsOrden2 = mysql_query($query_rsOrden2, $tecnocomm) or die(mysql_error());
        $row_rsOrden2 = mysql_fetch_assoc($rsOrden2);
        $totalRows_rsOrden2 = mysql_num_rows($rsOrden2);
        $idproveedor = $row_rsOrden2["idproveedor"];
    } else
        $idproveedor = $_POST['idproveedor'];
  $insertSQL = sprintf("INSERT INTO cuentasporpagar (idproveedor, monto, tipo, nofactura, fecha, fechavencimiento, estado, moneda, idordencompra) VALUES (%s, %s, %s, %s,%s,%s, 0,%s, %s)",
             
                       GetSQLValueString($idproveedor, "int"),
                       GetSQLValueString($_POST['monto'], "double"),
                       GetSQLValueString($_POST['tipo'], "int"),
					   GetSQLValueString($_POST['nofactura'], "text"),
					     GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['fechavencimiento'], "date"),
					    GetSQLValueString($_POST['moneda'], "date"),
					    GetSQLValueString($_POST['idordencompra'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedor = "SELECT * FROM proveedor ORDER BY nombrecomercial ASC";
$rsProveedor = mysql_query($query_rsProveedor, $tecnocomm) or die(mysql_error());
$row_rsProveedor = mysql_fetch_assoc($rsProveedor);
$totalRows_rsProveedor = mysql_num_rows($rsProveedor);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrden = "SELECT * FROM ordencompra ORDER BY idordencompra DESC";
$rsOrden = mysql_query($query_rsOrden, $tecnocomm) or die(mysql_error());
$row_rsOrden = mysql_fetch_assoc($rsOrden);
$totalRows_rsOrden = mysql_num_rows($rsOrden);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript">
function change(name,size,lab){
s = document.getElementById(name);
obj = document.createElement('input')
obj.type = 'text'
obj.id = name;
obj.name = name;
obj.size = size;
document.getElementById(lab).replaceChild(obj,s)
}

function activar(obj,val) {
    //dis = obj.selectedIndex==0 ? false : true;
    dis = (val) ? "visible": "hidden";
    document.getElementById(obj).style.visibility = dis;
    document.getElementById("MM_insert2").value = obj;
    
} 
</script>

</head>

    <body onload="activar('idproveedor',true);activar('idordencompra',false);">
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center" class="wrapper">

    <tr valign="baseline">
        <td colspan="2" nowrap="nowrap">
            <label onclick="activar('idproveedor',true);activar('idordencompra',false);"><input type="radio" checked="checked" name="tipo" id="cxp_a" />Abierta</label>
            <label onclick="activar('idproveedor',false);activar('idordencompra',true);"><input type="radio" name="tipo" id="cxp_o" />Orden de compra</label></td>
    </tr>
    <tr valign="baseline" id="idproveedor">
      <td nowrap="nowrap" align="right">Proveedor:</td>
      <td><select name="idproveedor">
              <option value="0">Elija un proveedor</option>
          <?php
do {  
?><option value="<?php echo $row_rsProveedor['idproveedor']?>"><?php echo $row_rsProveedor['nombrecomercial']?></option>
          <?php
} while ($row_rsProveedor = mysql_fetch_assoc($rsProveedor));
  $rows = mysql_num_rows($rsProveedor);
  if($rows > 0) {
      mysql_data_seek($rsProveedor, 0);
	  $row_rsProveedor = mysql_fetch_assoc($rsProveedor);
  }
?>
      </select>
      </td>
    </tr>
    <tr valign="baseline" id="idordencompra">
      <td nowrap="nowrap" align="right">Orden de Compra:</td>
      <td><select name="idordencompra">
              <option value="0">Elija Orden de Compra</option>
          <?php
do {  
?><option value="<?php echo $row_rsOrden['idordencompra']?>"><?php echo $row_rsOrden['identificador']?></option>
          <?php
} while ($row_rsOrden = mysql_fetch_assoc($rsOrden));
?>
      </select>
      </td>
    </tr>
    <tr> </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Tipo:</td>
      <td><select name="tipo"><option value="0">Factura</option><option value="1">Nota de Credito</option></select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">No:</td>
      <td><input type="text" name="nofactura" value="" size="10" style="text-align:right;" /></td>
    </tr>
    
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Monto:</td>
      <td><input type="text" name="monto" value="" size="10" style="text-align:right;" /></td>
    </tr> 
     <tr valign="baseline">
      <td nowrap="nowrap" align="right">Moneda:</td>
      <td><select name="moneda"><option value="0">PESOS</option><option value="1">DOLARES</option></select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Fecha:</td>
      <td>    <select name="diafecha" id="diafecha">
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
      <select name="mesfecha" id="mesfecha">
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
      <select name="anofecha" id="anofecha">
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
      </label><input type="button" value="Cal" onclick="displayCalendarSelectBox(document.forms[0].ano,document.forms[0].mes,document.forms[0].dia,false,false,this)"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Fecha Vencimiento:</td>
      <td>    <select name="dia" id="dia">
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
        <option value="2017" <?php if (!(strcmp(2018, date("Y")))) {echo "selected=\"selected\"";} ?>> 2018 </option>
        <option value="2017" <?php if (!(strcmp(2019, date("Y")))) {echo "selected=\"selected\"";} ?>> 2019 </option>
      </select>
      </label><input type="button" value="Cal" onclick="displayCalendarSelectBox(document.forms[0].ano,document.forms[0].mes,document.forms[0].dia,false,false,this)"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Guardar" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1" />
  <input type="hidden" name="MM_insert2" id="MM_insert2" value="idproveedor" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rsProveedor);
?>
