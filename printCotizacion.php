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

$colname_rsCotizacion = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsCotizacion = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Imprimir Cotizacion</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/funciones.js"></script>
</head>

<body>
<table width="700" border="0" cellpadding="0" cellspacing="0" class="resaltarTabla" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="20" colspan="6" valign="top" class="realte">Vista de Impresion de Cotizacion:</td>
  </tr>
  <tr>
    <td width="118" height="14"></td>
    <td width="111"></td>
    <td width="160"></td>
    <td width="187"></td>
    <td width="122"></td>
    <td width="122"></td>
  </tr>
  
  
  
  <tr>
    <td height="20" valign="top"><a href="enviarXmail.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion']?>">Enviar Por Mail</a></td>
    <td valign="top">
    <?php if($row_rsCotizacion['estado'] >= 6) {?>
       <a href="printCotizacionPDFv2.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion']?>" onclick="NewWindow(this.href,'Imprimir Cotizacion <?php echo $_GET['idsubcotizacion']?>','800','800','yes'); return false;">Ver  PDF</a>
    <?php }else{ ?>
    <a href="printCotizacionPDF.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion']?>" onclick="NewWindow(this.href,'Imprimir Cotizacion <?php echo $_GET['idsubcotizacion']?>','800','800','yes'); return false;">Ver  PDF</a>
    <? }?>
    </td>
    <td valign="top"> <?php if($row_rsCotizacion['estado'] >= 6) {?>
       <a href="descargarCotizacionPDFv2.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion']?>" onclick="NewWindow(this.href,'Imprimir Cotizacion','800','800','yes'); return false;">Descargar  PDF</a>
    <?php }else{ ?>
    <a href="descargarCotizacionPDF.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion']?>" onclick="NewWindow(this.href,'Imprimir Cotizacion','800','800','yes'); return false;">Descargar  PDF</a>
    <? }?></td>
    <td valign="top"><a href="datosPresupuesto.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion']?>" onclick="NewWindow(this.href,'Imprimir Cotizacion','800','800','yes'); return false;">Datos De Cotizacion</a></td>
    <td valign="top"><a href="printCotizacionCostos.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion']?>" onclick="NewWindow(this.href,'Imprimir Cotizacion','800','800','yes'); return false;">Costos</a></td>
    <td valign="top"><a href="printCotizacionSinPrecios.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion']?>" onclick="NewWindow(this.href,'Imprimir Cotizacion','800','800','yes'); return false;">Sin Precios</a></td>
  </tr>
</table>
</body>
</html>
<?php
mysql_free_result($rsCotizacion);
?>
