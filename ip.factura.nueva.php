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
<?php  require_once('utils.php');?>
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

if((isset($_POST['oservicio']) && $_POST['oservicio']!= -1) || (isset($_POST['cotizacion']) && $_POST['cotizacion'] != -1)){
	$crearfactura = true;
}else{
	$crearfactura = false;
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "myform") && $crearfactura) {
	
	//comprobar que la factura no este repetida
	
	$query = sprintf("SELECT idfactura FROM factura WHERE numfactura = %s",
					GetSQLValueString($_POST['numfactura'],"int"));
 	mysql_select_db($database_tecnocomm, $tecnocomm);
  	
	$rsQuery = mysql_query($query, $tecnocomm) or die(mysql_error());
	
	if(mysql_num_rows($rsQuery) > 0){
		$factuaExiste = true;
	}else{
	
	
  $insertSQL = sprintf("INSERT INTO factura (idcliente, numfactura, fecha, moneda, idip, anticipo, cotizacion, oservicio, tipocambio) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idcliente'], "int"),
                       GetSQLValueString($_POST['numfactura'], "int"),
                       GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['idip'], "int"),
					   GetSQLValueString($_POST['anticipo'], "int"),
					   GetSQLValueString($_POST['cotizacion'],"int"),
					   GetSQLValueString($_POST['oservicio'],"int"),
					   GetSQLValueString($_POST['tipocambio'],"double"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

$idfactura = mysql_insert_id();

  $insertGoTo = "facturando.php?idfactura=".$idfactura;
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
	}//fin de else
}

$colname_rsFactIp = "-1";
if (isset($_GET['idip'])) {
  $colname_rsFactIp = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFactIp = sprintf("SELECT f.*,SUM(df.cantidad * df.punitario) AS total FROM factura f LEFT JOIN detallefactura df ON f.idfactura = df.idfactura WHERE idip = %s GROUP BY df.idfactura", GetSQLValueString($colname_rsFactIp, "int"));
$rsFactIp = mysql_query($query_rsFactIp, $tecnocomm) or die(mysql_error());
$row_rsFactIp = mysql_fetch_assoc($rsFactIp);
$totalRows_rsFactIp = mysql_num_rows($rsFactIp);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsNumFactura = "SELECT (MAX(numfactura)+1)  AS numerofactura FROM factura";
$rsNumFactura = mysql_query($query_rsNumFactura, $tecnocomm) or die(mysql_error());
$row_rsNumFactura = mysql_fetch_assoc($rsNumFactura);
$totalRows_rsNumFactura = mysql_num_rows($rsNumFactura);

$colname_rsCotizacion = "-1";
if (isset($_GET['idip'])) {
  $colname_rsCotizacion = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT sb.idcotizacion, sb.idsubcotizacion, sb.identificador2 FROM subcotizacion sb JOIN cotizacion c  ON c.idip = %s AND c.idcotizacion = sb.idcotizacion WHERE sb.estado = 3 OR sb.estado = 8", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);

$colname_rsOrdenes = "-1";
if (isset($_GET['idip'])) {
  $colname_rsOrdenes = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenes = sprintf("SELECT * FROM ordenservicio WHERE idip = %s", GetSQLValueString($colname_rsOrdenes, "int"));
$rsOrdenes = mysql_query($query_rsOrdenes, $tecnocomm) or die(mysql_error());
$row_rsOrdenes = mysql_fetch_assoc($rsOrdenes);
$totalRows_rsOrdenes = mysql_num_rows($rsOrdenes);

$colname_rsCliente = "-1";
if (isset($_GET['idip'])) {
  $colname_rsCliente = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCliente = sprintf("SELECT c.* FROM cliente c, ip WHERE ip.idip = %s AND ip.idcliente = c.idcliente", GetSQLValueString($colname_rsCliente, "int"));
$rsCliente = mysql_query($query_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);
$totalRows_rsCliente = mysql_num_rows($rsCliente);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jqueryui.js"></script>
<script language="javascript" src="js/ip.factura.nueva.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<title>Nueva Factura</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css"  rel="stylesheet" type="text/css"/>
</head>

<body>
<h1>Nueva Factura</h1>
<?php if($factuaExiste == true){?>
<div class="error">El numero de factura ya existe, indique otro numero</div>
<?php } ?>
<?php include("ip.encabezado.php");?>

<?php if(isset($_POST["MM_insert"]) && $crearfactura == false){ ?>
<div class="error">
No Se Puede Crear La Factura Debe Seleccionar Una Cotizacion u Orden De Servicio.
</div>
<?php } ?>
<form method="POST" action="<?php echo $editFormAction; ?>" name="myform" id="myform">
<div>
<h3>Facturas Creadas Para Esta Ip:</h3>
<?php if ($totalRows_rsFactIp == 0) { // Show if recordset empty ?>
  No hay facturas creadas
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsFactIp > 0) { // Show if recordset not empty ?>
  <div id="distabla">
    <table width="80%" cellspacing="0">
      <thead>
        <tr>
          <td>Numero</td>
          <td>Fecha</td>
          <td>Monto</td>
          </tr>
      </thead>
      <tbody>
        <?php do { ?>
          <tr>
            <td><?php echo $row_rsFactIp['numfactura']; ?></td>
            <td align="left"><?php echo $row_rsFactIp['fecha']; ?></td>
            <td align="right"><?php $monto = $row_rsFactIp['total'] + ($row_rsFactIp['total']*$row_rsFactIp['iva']/100);  echo format_money($monto); $total = $total + $monto;?></td>
          </tr>
          <?php } while ($row_rsFactIp = mysql_fetch_assoc($rsFactIp)); ?>
      </tbody>
      <tfoot>
        <tr>
          <td></td>
          <td align="right">Total:</td>
          <td align="right"><?php  echo format_money($total);?></td>
          </tr>
      </tfoot>
    </table>
  </div>
  <?php } // Show if recordset not empty ?>
</div>
<div>
<h3>Concepto De La Factura:</h3>
<?php if($totalRows_rsCotizacion > 0){?>
<label>Cotizacion: </label>
<select name="cotizacion" id="cotizacion">
<option value="">Ninguna</option>
  <?php
do {  
?>
  <option value="<?php echo $row_rsCotizacion['idcotizacion']?>"><?php echo $row_rsCotizacion['identificador2']?></option>
  <?php
} while ($row_rsCotizacion = mysql_fetch_assoc($rsCotizacion));
  $rows = mysql_num_rows($rsCotizacion);
  if($rows > 0) {
      mysql_data_seek($rsCotizacion, 0);
	  $row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
  }
?>
</select>
<?php } ?>

<?php if($totalRows_rsOrdenes > 0){?>
<label>Orden Servicio: </label>
<select name="oservicio" id="oservicio">
<option value="">Ninguna</option>
  <?php
do {  
?>
  <option value="<?php echo $row_rsOrdenes['idordenservicio']?>"><?php echo $row_rsOrdenes['identificador']?></option>
  <?php
} while ($row_rsOrdenes = mysql_fetch_assoc($rsOrdenes));
  $rows = mysql_num_rows($rsOrdenes);
  if($rows > 0) {
      mysql_data_seek($rsOrdenes, 0);
	  $row_rsOrdenes = mysql_fetch_assoc($rsOrdenes);
  }
?>
</select>
<?php } ?>

<label>
Tipo Cambio:
<input type="text" name="tipocambio"/>
</label>
<label>RFC: <a href="cambiar.rfc.php?idcliente=<?php echo $row_rsCliente['idcliente']; ?>" class="popupm" w="600" h="510"><img src="images/reeditar.png" border="0" height="16" /></a> <input type="text" name="rfct" readonly="readonly" value="<?php echo $row_rsCliente['rfc']; ?>" /></label>
<input type="hidden" name="idcliente" value="<?php echo $row_rsCliente['idcliente']; ?>" />
</div>
<div>
<h3>Datos De Nueva Factura</h3>
<label>
Num.Factura:
<input type="text" name="numfactura"  value="<?php echo $row_rsNumFactura['numerofactura']; ?>"/>
</label>
<label>
Fecha:
<input type="text" name="fecha"  value="<?php echo date("Y/m/d");?>" class="fecha"/>
</label>
<label>Moneda:
<select name="moneda" id="mno"><option value="0">Pesos</option><option value="1">Dolares</option></select>
</label>
<label>
Anticipo:
<select name="anticipo" id="anticipo">
          <option value="1" <?php if($totalRows_rsFactIp == 0){echo "selected=\"selected\"";}?>>Anticipo 1</option>
          <option value="2" <?php if($totalRows_rsFactIp == 1){echo "selected=\"selected\"";}?>>Anticipo 2</option>
          <option value="3" <?php if($totalRows_rsFactIp == 2){echo "selected=\"selected\"";}?>>Anticipo 3</option>
          <option value="4" <?php if($totalRows_rsFactIp == 3){echo "selected=\"selected\"";}?>>Anticipo 4</option>
          <option value="10" <?php if($totalRows_rsFactIp == 4){echo "selected=\"selected\"";}?>>Finiquito</option>
      </select>
  </label>
</div>
<div class="botones">
<button name="aceptar" type="submit" class="button"><span>Crear</span></button>
</div>
<input type="hidden" name="idip" value="<?php echo $_GET['idip']?>" />
<input type="hidden" name="MM_insert" value="myform" />
</form>

</body>
</html>
<?php
mysql_free_result($rsFactIp);

mysql_free_result($rsCotizacion);

mysql_free_result($rsOrdenes);
?>
