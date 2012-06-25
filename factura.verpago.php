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

$colname_rsAbonos = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsAbonos = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAbonos = sprintf("SELECT * FROM factura_abono WHERE idfactura = %s", GetSQLValueString($colname_rsAbonos, "int"));
$rsAbonos = mysql_query($query_rsAbonos, $tecnocomm) or die(mysql_error());
$row_rsAbonos = mysql_fetch_assoc($rsAbonos);
$totalRows_rsAbonos = mysql_num_rows($rsAbonos);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Pagar Factura</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
</head>
<body>
<form name="pagoFactura" method="pos" id="myform">
<div>
<h3>

<input type="radio" name="tipopago" />Un Solo Pago

</h3>
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
<div>
<h3><input type="radio" name="tipopago" />Pago Parcial</h3>
<a href="factura.addAbono.php?idfactura=<?php echo $_GET['idfactura'];?>" class="popup">Registrar Abono</a> | <a href="factura.addAbono.php?idfactura=<?php echo $_GET['idfactura'];?>">Liquidar Factura</a> 
<h3>Abonos:</h3>
<table width="80%" cellpadding="4">
<thead>
<tr><td>Fecha</td><td>Monto</td><td>Referencia</td></tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsAbonos['fecha']; ?></td> <td><?php echo $row_rsAbonos['monto']; ?></td>
      <td><?php echo $row_rsAbonos['referenciaabono']; ?></td>
    </tr>
    <?php } while ($row_rsAbonos = mysql_fetch_assoc($rsAbonos)); ?>
</tbody>
</table>
</div>
<div>
<h3><input type="radio" /> Cancelar Factura</h3>
</div>
<div class="botones">
<button type="submit">Aceptar</button>
</div>
</form>
</body>
</html>
<?php
mysql_free_result($rsAbonos);
?>
