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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "registrar")) {
  $insertSQL = sprintf("INSERT INTO salidamaterial (idsalida, idarticulo, cantidad, responsable, fecha, hora) VALUES (%s, %s, %s, %s, now(), now())",
                       GetSQLValueString($_POST['idsalida'], "int"),
                       GetSQLValueString($_POST['idarticulo'], "int"),
                       GetSQLValueString($_POST['textfield'], "double"),
                       GetSQLValueString($_POST['responsable'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_RsPartida = "-1";
if (isset($_GET['id'])) {
  $colname_RsPartida = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsPartida = sprintf("SELECT * FROM subcotizacionarticulo WHERE idsubcotizacionarticulo = %s", GetSQLValueString($colname_RsPartida, "int"));
$RsPartida = mysql_query($query_RsPartida, $tecnocomm) or die(mysql_error());
$row_RsPartida = mysql_fetch_assoc($RsPartida);
$totalRows_RsPartida = mysql_num_rows($RsPartida);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsResp = "SELECT * FROM usuarios ORDER BY nombrereal ASC";
$RsResp = mysql_query($query_RsResp, $tecnocomm) or die(mysql_error());
$row_RsResp = mysql_fetch_assoc($RsResp);
$totalRows_RsResp = mysql_num_rows($RsResp);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>AGREGAR ARTICULO A COTIZACION</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript">

function confirmar(elementName,elementValueOld,elementValueNew){

if(!confirm("Usted A Elegido Hacer El Siguiente Cambio, \nConfirme Por Favor, \n Valor Orginial: "+elementValueOld+" \nValor Nuevo: "+elementValueNew)){
	
	document.getElementById(elementName).value =  elementValueOld;

}else{
	document.getElementById(elementName).value =  elementValueNew;

}


}
</script>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>

<body class="wrapper">
<form name="registrar" method="POST" action="<?php echo $editFormAction; ?>" >
<table width="500" border="0" align="center" >
  <tr>
    <td width="15">&nbsp;</td>
    <td colspan="2" align="center" background="images/titulo.gif" class="titulos">DATOS DE PARTIDA</td>
    <td width="17">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>Partida:</td>
    <td><?php echo $row_RsPartida['descri']; ?></td>
    <td>&nbsp;</td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td width="70">Cantidad:</td>
    <td width="380"><label>
      <input type="text" name="textfield" id="textfield" />
    </label></td>
    <td>&nbsp;</td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right"><label>
      <input type="submit" name="button" id="button" value="Aceptar" />
    </label></td>
    <td>&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="idarticulo" value="<?php echo $row_RsPartida['idsubcotizacionarticulo']; ?>"/>
<input type="hidden" name="idsalida" value="<?php echo $_GET['idsalida']; ?>"/>
<input type="hidden" name="MM_insert" value="registrar" />
</form>
</body>
</html>
<?php
mysql_free_result($RsPartida);

mysql_free_result($RsResp);

?>
