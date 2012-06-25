<?php require_once('Connections/tecnocomm.php'); ?>
<?php
session_start();
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

$fecha = $_POST['ano']."-".$_POST['mes']."-".$_POST['dia'];

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevaFactura")) {
  $insertSQL = sprintf("INSERT INTO factura (idcliente, numfactura, moneda, tipo, referencia,fecha) VALUES (%s, %s, %s, %s, %s,%s)",
                       GetSQLValueString($_POST['idcliente'], "int"),
                       GetSQLValueString($_POST['numfactura'], "int"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['referencia1'], "text"),
					   GetSQLValueString($fecha, "date"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  $idfactura = mysql_insert_id();
  
  //Guardar En facturaCotizacion
  if($_POST['tipo']==1){
    $insertSQL = sprintf("INSERT INTO facturacotizacion (idfactura,idcotizacion,numeroanticipo) VALUES (%s, %s, %s)",
                       GetSQLValueString($idfactura, "int"),
                       GetSQLValueString($_POST['referencia'], "int"),
					   GetSQLValueString($_POST['anticipo'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
    require_once('lib/eventos.php');
	$evt = new evento(23,$_SESSION['MM_Userid'],"Factura creada para la cotizacion :".$_POST['referencia']);
	$evt->registrar();

  }

  $insertGoTo = "facturando.php?idfactura=".$idfactura;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsClientes = "SELECT * FROM cliente ORDER BY abreviacion ASC";
$rsClientes = mysql_query($query_rsClientes, $tecnocomm) or die(mysql_error());
$row_rsClientes = mysql_fetch_assoc($rsClientes);
$totalRows_rsClientes = mysql_num_rows($rsClientes);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConcepto = "SELECT * FROM conceptosfactura ORDER BY concepto ASC";
$rsConcepto = mysql_query($query_rsConcepto, $tecnocomm) or die(mysql_error());
$row_rsConcepto = mysql_fetch_assoc($rsConcepto);
$totalRows_rsConcepto = mysql_num_rows($rsConcepto);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = "SELECT * FROM subcotizacion ORDER BY idsubcotizacion DESC";
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsNumFactura = "SELECT (MAX(numfactura)+1)  AS numerofactura FROM factura";
$rsNumFactura = mysql_query($query_rsNumFactura, $tecnocomm) or die(mysql_error());
$row_rsNumFactura = mysql_fetch_assoc($rsNumFactura);
$totalRows_rsNumFactura = mysql_num_rows($rsNumFactura);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsIP = "SELECT * FROM ip ORDER BY idip DESC";
$rsIP = mysql_query($query_rsIP, $tecnocomm) or die(mysql_error());
$row_rsIP = mysql_fetch_assoc($rsIP);
$totalRows_rsIP = mysql_num_rows($rsIP);
?>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function mostrarOcultar(obj) {
if(obj.value==0){
    referencia1.style.display = 'block';
	referencia.style.display	= 'none';
	anticipo.style.display = 'none';
}
if(obj.value==2){
    referencia1.style.display = 'block';
	referencia.style.display	= 'none';
	anticipo.style.display = 'none';
}
if(obj.value==1){
    referencia1.style.display = 'none';
	referencia.style.display	= 'block';
	anticipo.style.display = 'block';
}
}  
</script>

<form action="<?php echo $editFormAction; ?>" method="POST" name="nuevaFactura" id="nuevaFactura">
  <table width="577" align="center" class="wrapper">
    <!--DWLayoutTable-->
    <tr>
    <tr valign="baseline">
      <td width="126" height="46" align="right" valign="top" nowrap="nowrap">NUM. FACTURA:</td>
      <td colspan="2" valign="top"><input type="text" name="numfactura" value="<?php echo $row_rsNumFactura['numerofactura']; ?>" size="10" /></td>
      <td width="56" align="right" valign="top" nowrap="nowrap">FECHA:</td>
      <td colspan="5" valign="top"><label>
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
      <td width="1">&nbsp;</td>
    </tr>
    <tr>
      <td height="26" align="right" valign="bottom" nowrap="nowrap">SELECCION IP:</td>
      <td colspan="4" valign="middle"> <select name="idcliente" class="form" id="idcliente">
      <option value="-1">Selecciona Cliente</option>
      <?php
do {  
?>
      <option value="<?php echo $row_rsIP['idip']?>"><?php echo $row_rsIP['idip']?></option>
      <?php
} while ($row_rsIP = mysql_fetch_assoc($rsIP));
  $rows = mysql_num_rows($rsIP);
  if($rows > 0) {
      mysql_data_seek($rsIP, 0);
	  $row_rsIP = mysql_fetch_assoc($rsIP);
  }
?>
    </select><a href="nuevoIP.php" onclick="NewWindow(this.href,'NUevo Cliente','500','500','yes');return false"><img src="images/light_bulb__plus.png" alt="Agregar Usuario" width="24" height="24" border="0" title="AGREGAR NUEVO CLIENTE" align="absmiddle"/></a>      </td>
      <td width="23" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
      <td width="79" align="right" valign="middle" nowrap="nowrap">MONEDA:</td>
      <td colspan="2" valign="middle"><select name="moneda">
        <option value="0">PESOS</option>
        <option value="1">DOLAR</option>
      </select></td>
    <td></td>
    </tr>
    <tr valign="baseline">
      <td height="24">&nbsp;</td>
      <td width="19">&nbsp;</td>
      <td width="95">&nbsp;</td>
      <td>&nbsp;</td>
      <td width="32">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td width="1">&nbsp;</td>
      <td width="80">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td height="22" colspan="3" align="center" valign="top" nowrap="nowrap">SERVICIO A FACTURAR</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td height="25" colspan="2" valign="top"><select name="tipo" id="tipo" onchange=" mostrarOcultar(tipo);" >
          <option value="0">ORDEN DE SERVICIO</option>
          <option value="1" selected="selected">COTIZACION</option>
          <option value="2">OTRO</option>
      </select></td>
      <td colspan="7" valign="top"><label>
        <select name="referencia" id="referencia"  style="display:block;float:left;" >
          <?php
do {  
?>
          <option value="<?php echo $row_rsCotizaciones['idsubcotizacion']?>"><?php echo $row_rsCotizaciones['identificador2']?></option>
          <?php
} while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones));
  $rows = mysql_num_rows($rsCotizaciones);
  if($rows > 0) {
      mysql_data_seek($rsCotizaciones, 0);
	  $row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
  }
?>
        </select></label>
        &nbsp;&nbsp;
        <select name="anticipo" id="anticipo">
          <option value="1" selected="selected" style="float:left">Anticipo 1</option>
          <option value="2">Anticipo 2</option>
          <option value="3">Anticipo 3</option>
          <option value="4">Anticipo 4</option>
          <option value="5">Finiquito</option>
      </select>
        <input type="text" name="referencia1" id="referencia1"   style="display:none;float:left;"  /></td>
      <td></td>
    </tr>
    <tr valign="baseline">
      <td height="23"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      <td align="right" valign="middle"><label>
        <input type="submit" name="Aceptar" id="Aceptar" value="ACEPTAR" />
      </label></td>
      <td></td>
    </tr>
    <tr valign="baseline">
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
    </tr>
    </table>
  
  <input type="hidden" name="MM_insert" value="nuevaFactura" />
</form>

<?php
mysql_free_result($rsClientes);

mysql_free_result($rsConcepto);

mysql_free_result($rsCotizaciones);

mysql_free_result($rsNumFactura);
?>
