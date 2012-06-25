<?php require_once('Connections/tecnocomm.php'); ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "guardar")) {


  $insertSQL = sprintf("INSERT INTO ordencompra (idcotizacion,formapago,moneda,vigencia,tiempoentrega,garantia,notas,consecutivo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s,%s)",
                       GetSQLValueString($_POST['radio'], "int"),
					   GetSQLValueString($_POST['formadepago'], "text"),
					   GetSQLValueString($_POST['moneda'], "int"),
					   GetSQLValueString($_POST['vigencia'], "text"),
					   GetSQLValueString($_POST['tiempoentrega'], "text"),
					   GetSQLValueString($_POST['garantia'], "text"),
					   GetSQLValueString($_POST['notas'], "text"),
					   GetSQLValueString($_POST['concecutivo'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

	
$idinsertado = mysql_insert_id();


//creamos detalle e  compra
	
	$query = sprintf("INSERT INTO detalleorden(idordencompra,idpartida,cantidad)  SELECT %s, s.idsubcotizacionarticulo, s.cantidad FROM subcotizacionarticulo s WHERE s.idsubcotizacion= %s",$idinsertado,GetSQLValueString($_POST['radio'], "int"));

mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($query, $tecnocomm) or die(mysql_error());



   require_once('lib/eventos.php');
	$evt = new evento(25,$_SESSION['MM_Userid'],"Orden de Compra Creada ");
	$evt->registrar();

//  $insertGoTo = "detalleOrden.php?idorden=".$idinsertado;
   $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$query = "SELECT s.idsubcotizacion AS id,s.identificador2 AS nomcotizacion,cl.nombre AS nombrecliente FROM subcotizacion s,cotizacion c,cliente cl WHERE s.idcotizacion = c.idcotizacion AND c.idcliente = cl.idcliente";

if(isset($_GET['buscar'])){
	
	if($_GET['field'] == 0){
		//buscar por cliente
		$query = sprintf("SELECT s.idsubcotizacion AS id,s.identificador2 AS nomcotizacion,cl.nombre AS nombrecliente FROM subcotizacion s,cotizacion c,cliente cl WHERE s.idcotizacion = c.idcotizacion AND c.idcliente = cl.idcliente AND cl.nombre like \"%%%s%%\"",$_GET['buscar']);
	}
	
	if($_GET['field']==1){
		//buscar por cotizacion
			$query = sprintf("SELECT s.idsubcotizacion AS id,s.identificador2 AS nomcotizacion,cl.nombre AS nombrecliente FROM subcotizacion s,cotizacion c,cliente cl WHERE s.idcotizacion = c.idcotizacion AND c.idcliente = cl.idcliente AND s.identificador2 like \"%%%s%%\"",$_GET['buscar']);
	}


}else{


}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrden = "SELECT * FROM ordencompra";
$rsOrden = mysql_query($query_rsOrden, $tecnocomm) or die(mysql_error());
$row_rsOrden = mysql_fetch_assoc($rsOrden);
$totalRows_rsOrden = mysql_num_rows($rsOrden);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConcecutivo = "SELECT MAX(consecutivo) AS conc FROM ordencompra";
$rsConcecutivo = mysql_query($query_rsConcecutivo, $tecnocomm) or die(mysql_error());
$row_rsConcecutivo = mysql_fetch_assoc($rsConcecutivo);
$totalRows_rsConcecutivo = mysql_num_rows($rsConcecutivo);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = $query;
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/html">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nueva Orden Compra</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="js/funciones.js"></script>
<script type=text/javascript>
function change(name,size,lab){
s = document.getElementById(name);
obj = document.createElement('input')
obj.type = 'text'
obj.id = name;
obj.name = name;
obj.size = size;
document.getElementById(lab).replaceChild(obj,s)
}
</script>
</head>

<body>
<table width="650" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="28" colspan="5" valign="top" class="titulos">Nueva Orden de Compra</td>
    <td width="55"></td>
  </tr>
  <tr>
    <td width="19" height="20">&nbsp;</td>
    <td width="38">&nbsp;</td>
    <td width="460">&nbsp;</td>
    <td width="51">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td height="67">&nbsp;</td>
    <td>&nbsp;</td>
    <td valign="top">
    <form name="buscar" method="get">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
       <tr>
    <td height="20" colspan="3" valign="top">Seleccione Una Cotizacion:</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
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
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="21" colspan="2" valign="top">Buscar:</td>
    <td colspan="2" valign="top"><label>
      <input name="buscar" type="text" id="buscar" value="<?php  echo $_GET['buscar']; ?>" />
    </label></td>
    <td valign="top"><label>
      <select name="field" id="field">
        <option value="0" <?php if (!(strcmp(0, $_GET['field']))) {echo "selected=\"selected\"";} ?>>Cliente</option>
        <option value="1" <?php if (!(strcmp(1, $_GET['field']))) {echo "selected=\"selected\"";} ?>>Cotizacion</option>
      </select>
    </label></td>
    <td valign="top"><label>
      <input type="submit" name="button" id="button" value="Filtrar" />
    </label></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
    </table></form></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
  
 
  <tr>
    <td height="404">&nbsp;</td>
    <td colspan="3" valign="top">
      <form action="<?php echo $editFormAction; ?>" name="guardar" method="POST">
        <table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <tr>
            <td width="13" height="199"></td>
            <td width="4"></td>
            <td colspan="7" valign="top"><div style="overflow:scroll; height:199px"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="resaltarTabla">
              <!--DWLayoutTable-->
              <tr class="titleTabla">
                <td width="18" height="20" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
                <td width="218" valign="top">Cotizacion</td>
                <td width="235" valign="top">Cliente</td>
                <td width="80">&nbsp;</td>
                <td width="11">&nbsp;</td>
              </tr>
              <tr>
                <td height="4"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <?php do { ?>
                <tr>
                  <td height="20" valign="top"></td>
                  <td valign="top"><label>
                    <input type="radio" name="radio" id="idcotizacion" value="<?php echo $row_rsCotizaciones['id']; ?>" />
                    <?php echo $row_rsCotizaciones['nomcotizacion']; ?>  </label></td>
                  <td valign="top"><?php echo $row_rsCotizaciones['nombrecliente']; ?></td>
                  <td valign="top"><a href="printCotizacion.php?idsubcotizacion=<?php echo $row_rsCotizaciones['id']; ?>" onclick="NewWindow(this.href,'Ver Cotizacion',1000,600,'yes'); return false;">Ver</a></td>
                  <td>&nbsp;</td>
                </tr>
                <?php } while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones)); ?>
              
            </table></div></td>
            <td width="45"></td>
            <td width="8"></td>
          </tr>
          <tr>
            <td height="23"></td>
            <td></td>
            <td width="122">&nbsp;</td>
            <td width="15">&nbsp;</td>
            <td width="275">&nbsp;</td>
            <td width="29">&nbsp;</td>
            <td width="4">&nbsp;</td>
            <td width="4">&nbsp;</td>
            <td width="31">&nbsp;</td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td height="21"></td>
            <td></td>
            <td colspan="3" valign="top" class="realte">Condiciones Comerciales</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
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
          </tr>
          <tr>
            <td height="20"></td>
            <td></td>
            <td align="right" valign="top">FORMA DE PAGO</td>
            <td>&nbsp;</td>
            <td colspan="4" valign="top"><label>
              <select name="forma" id="forma" class="form">
                <option value="CONTADO">CONTADO</option>
                <option value="50 % ANTICIPO y 50% CONTRAENTREGA">50 % ANTICIPO y 50% CONTRAENTREGA</option>
                <option value="50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE">50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE</option>
                <option value="30 DIAS">30 DIAS</option>
                <option onclick="change('forma',40,'fo');">Otro...</option>
              </select>
            </label></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          
          <tr>
            <td height="23"></td>
            <td></td>
            <td align="right" valign="top">MONEDA</td>
            <td>&nbsp;</td>
            <td colspan="3" valign="top"><label>
              <select name="moneda" class="form" id="moneda">
                <option value="0">PESOS</option>
                <option value="1">DOLARES</option>
              </select>
            </label></td>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td height="24"></td>
            <td></td>
            <td align="right" valign="top">VIGENCIA</td>
            <td>&nbsp;</td>
            <td colspan="2" valign="top"><label>
              <select name="vigencia" id="vigencia" class="form">
                <option value="-1">Seleccionar</option>
                <option value="PRECIOS SUJETOS A CAMBIO SIN PREVIO AVISO">PRECIOS SUJETOS A CAMBIO SIN PREVIO AVISO</option>
                <option value="30 DIAS">30 DIAS</option>
                <option onclick="change('vigencia',40,'vig');">Otro...</option>
              </select>
            </label></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td height="40">&nbsp;</td>
            <td colspan="2" align="right" valign="top">TIEMPO DE ENTREGA</td>
            <td>&nbsp;</td>
            <td colspan="3" valign="top"><label>
              <input type="text" name="tiempoentrega" id="tiempoentrega" />
            </label></td>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td height="25">&nbsp;</td>
            <td colspan="2" align="right" valign="top">GARANTIA:</td>
            <td>&nbsp;</td>
            <td colspan="3" valign="top"><label>
              <select name="garantia" id="garantia" class="form">
                <option value="-1">SELECCIONAR</option>
                <option value="1 AÑO MATERIAL Y MANO DE OBRA">1 AÑO MATERIAL Y MANO DE OBRA</option>
                <option value="25 AÑOS PANDUIT">25 AÑOS PANDUIT</option>
                <option onclick="change('garantia',40,'gar');">Otro...</option>
              </select>
            </label></td>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          
          <tr>
            <td height="56"></td>
            <td colspan="2" align="right" valign="top">NOTAS:</td>
            <td></td>
            <td colspan="3" valign="top"><label>
              <textarea name="notas" id="notas" cols="35" rows="3"></textarea>
            </label></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          
          <tr>
            <td height="5"></td>
            <td colspan="2" rowspan="2" align="right" valign="top">CONSECUTIVO</td>
            <td></td>
            <td colspan="2" rowspan="2" valign="top"><label>
              <input name="identificador" type="text" id="identificador" value="<?php echo $row_rsConcecutivo['conc']; ?>" />
            </label></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td height="21"></td>
            <td></td>
            <td colspan="4" valign="top"><label>
            <input type="submit" name="button2" id="button2" value="Aceptar" />
            </label></td>
          <td>&nbsp;</td>
          </tr>
        </table>
      <input type="hidden" name="MM_insert" value="guardar" />
      </form></td>
  <td>&nbsp;</td>
    <td></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsOrden);

mysql_free_result($rsConcecutivo);

mysql_free_result($rsCotizaciones);
?>
