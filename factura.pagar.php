<?php require_once('Connections/tecnocomm.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

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
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "pagoFactura")) {
 
 switch($_POST['modopago']){
	case 1: $estado = 1;
	break;
	
	case 2: $estado = 4;
	break;
		
	case 3: $estado = 2;
	break;
	

}
 
 
 $insertSQL = sprintf("UPDATE factura SET referenciabnca = %s, banco = %s, fechapago = %s, tipopago = %s, modopago = %s , estado = %s WHERE idfactura = %s",                   
                       GetSQLValueString($_POST['referenciabnca'], "text"),
                       GetSQLValueString($_POST['banco'], "text"),
                       GetSQLValueString($_POST['fechapago'], "date"),
                       GetSQLValueString($_POST['tipopago'], "int"),
					   GetSQLValueString($_POST['modopago'],"int"),
					   GetSQLValueString($estado,"int"),
					   GetSQLValueString($_POST['idfactura'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rsAbonos = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsAbonos = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAbonos = sprintf("SELECT *, DATE_FORMAT(fecha, '%%d-%%m-%%Y') as fecha_es FROM factura_abono WHERE idfactura = %s", GetSQLValueString($colname_rsAbonos, "int"));
$rsAbonos = mysql_query($query_rsAbonos, $tecnocomm) or die(mysql_error());
$row_rsAbonos = mysql_fetch_assoc($rsAbonos);
$totalRows_rsAbonos = mysql_num_rows($rsAbonos);

$colname_rsFactura = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsFactura = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFactura = sprintf("SELECT * FROM factura WHERE idfactura = %s", GetSQLValueString($colname_rsFactura, "int"));
$rsFactura = mysql_query($query_rsFactura, $tecnocomm) or die(mysql_error());
$row_rsFactura = mysql_fetch_assoc($rsFactura);
$totalRows_rsFactura = mysql_num_rows($rsFactura);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Pagar Factura</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jqueryui.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
<script language="javascript" >
$(function(){
	
	$('.fecha').datepicker({ dateFormat: "yy-mm-dd" } );
		   
});
</script>
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css" />
</head>
<body>
<form action="<?php echo $editFormAction; ?>" name="pagoFactura" method="POST" id="myform">
<?php  if($row_rsFactura['modopago'] == 0){ ?>
<div>
<h3>
<input type="radio" name="modopago" value="1"/>Un Solo Pago
</h3>
<label>
Tipo Pago:
    <select name="tipopago" id="tipopago">
        <option value="0">Cheque</option>
        <option value="1">Transferencia</option>
        <option value="2">Efectivo</option>
        <option value="3">Otro</option>
      </select>

</label>
<label>
Referenica:
<input type="text" name="referenciabnca" id="referenciabnca" />
</label>
<label>
Banco:
<input type="text" name="banco" id="banco" />
</label>
<label>
Fecha De Pago:
<input type="text" name="fechapago" class="fecha"/>
</label>
<input type="hidden" name="idfactura" value="<?php echo $_GET['idfactura']?>" />
</div>
<?php } ?>
<?php  if($row_rsFactura['modopago'] == 0 || $row_rsFactura['modopago'] == 2 ){ ?>
<div>
<h3><input type="radio" name="modopago" value="2" />Pago Parcial</h3>
<a href="factura.addAbono.php?idfactura=<?php echo $_GET['idfactura'];?>" class="popup">Registrar Abono</a> | <a href="factura.addAbono.php?idfactura=<?php echo $_GET['idfactura'];?>">Liquidar Factura</a> 
<h3>Abonos:</h3>
<table cellpadding="4">
<thead>
    <tr><td>Fecha</td><td>Monto</td><td>Referencia</td></tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsAbonos['fecha_es']; ?></td>
      <td><?php echo $row_rsAbonos['monto']; ?></td>
      <td><?php echo $row_rsAbonos['referenciapago']; ?></td>
    </tr>
    <?php } while ($row_rsAbonos = mysql_fetch_assoc($rsAbonos)); ?>
</tbody>
</table>
</div>
<?php } ?>
<?php  if($row_rsFactura['modopago'] == 0 ){ ?>
<div>
<h3><input type="radio" name="modopago" value="3"/> Cancelar Factura</h3>
</div>
<?php } ?>
<div class="botones">
<button type="submit">Aceptar</button>
</div>
<input type="hidden" name="MM_insert" value="pagoFactura" />
</form>
</body>
</html>
<?php
mysql_free_result($rsAbonos);

mysql_free_result($rsFactura);
?>
