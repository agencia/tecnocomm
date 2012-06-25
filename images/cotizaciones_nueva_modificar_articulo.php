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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "registrar")) {
  $updateSQL = sprintf("UPDATE subcotizacionarticulo SET descri=%s, precio_cotizacion=%s, cantidad=%s, utilidad=%s, mo=%s, reall=%s, modd=1,tipo_cambio=%s WHERE idsubcotizacionarticulo=%s",
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['precio'], "double"),
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['utilidad'], "double"),
                       GetSQLValueString($_POST['mo'], "double"),
					   GetSQLValueString($_POST['real'], "double"),
					   GetSQLValueString($_POST['cambio'], "double"),
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());



  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
 session_start();

$sub_RsArticulo = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $sub_RsArticulo = $_GET['idsubcotizacion'];
}
$ide_RsArticulo = "-1";
if (isset($_GET['idarticulo'])) {
  $ide_RsArticulo = $_GET['idarticulo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulo = sprintf("SELECT *,subcotizacionarticulo.moneda AS monedacotizacion FROM articulo,subcotizacionarticulo WHERE articulo.idarticulo=subcotizacionarticulo.idarticulo and  subcotizacionarticulo.idsubcotizacionarticulo=%s", GetSQLValueString($sub_RsArticulo, "int"));
$RsArticulo = mysql_query($query_RsArticulo, $tecnocomm) or die(mysql_error());
$row_RsArticulo = mysql_fetch_assoc($RsArticulo);
$totalRows_RsArticulo = mysql_num_rows($RsArticulo);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript">

function confirmar(elementName,elementValueOld,elementValueNew){

if(!confirm("Usted A Elegido Hacer El Siguiente Cambio, \nConfirme Por Favor, \n Valor Orginial: "+elementValueOld+"\n Valor Nuevo: "+elementValueNew)){
	
	document.getElementById(elementName).value =  elementValueOld;

}else{
	document.getElementById(elementName).value =  elementValueNew;

}


}
</script>

</head>

<body class="wrapper">
<?php $signo = array(0=>"$",1=>"US$");?>
<form action="<?php echo $editFormAction; ?>" name="registrar" method="POST" >
<table width="500" border="0" align="center" >
  <tr>
    <td width="27">&nbsp;</td>
    <td colspan="2" align="center" background="images/titulo.gif" class="titulos">DATOS ARTICULO</td>
    <td width="30">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="212">&nbsp;</td>
    <td width="213">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">DESCRIPCION:</td>
    <td><label>
      <textarea name="nombre" cols="45" rows="3"  class="form" id="nombre" onchange="confirmar('nombre','<?php echo $row_RsArticulo['descri']; ?>',this.form.nombre.value);"><?php echo htmlentities($row_RsArticulo['descri']); ?></textarea>
    </label></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">PRECIO:</td>
    <td><input name="precio" type="text" class="form" id="precio" value="<?php echo money_format('%i',$row_RsArticulo['precio_cotizacion']); ?>"  onchange="confirmar('precio','<?php echo money_format('%i',$row_RsArticulo['precio_cotizacion']); ?>',this.form.precio.value);"/>   <?php echo $signo[$row_RsArticulo['monedacotizacion']]; ?>   </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">MANO DE OBRA: </td>
    <td><input name="mo" type="text" class="form" id="mo" value="<?php echo $row_RsArticulo['mo']; ?>"/>
    <?php echo $signo[$row_RsArticulo['monedacotizacion']]; ?> 
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">CANTIDAD:</td>
    <td><input name="cantidad" type="text" class="form" id="cantidad" value="<?php echo $row_RsArticulo['cantidad']; ?>" />      </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">FACTOR UTILIDAD:</td>
    <td align="left"><input name="utilidad" type="text" class="form" id="utilidad" value="<?php echo $row_RsArticulo['utilidad']; ?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <?php if((isset($_GET['conc'])) and ($_GET['conc'])!=-1){?>
  <tr>
    <td>&nbsp;</td>
    <td align="right">TIPO DE CAMBIO:</td>
    <td align="left"><input name="cambio" type="text" class="form" id="cambio" value="<?php echo $row_RsArticulo['tipo_cambio']; ?>" onchange="confirmar('cambio','<?php echo money_format('%i',$row_RsArticulo['tipo_cambio']); ?>',this.form.cambio.value);" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right">REAL INSTALADO:</td>
    <td align="left"><input name="real" type="text" class="form" id="real" value="<?php echo $row_RsArticulo['reall']; ?>" /></td>
    <td>&nbsp;</td>
  </tr>
  <?php }?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
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
<input type="hidden" name="idarticulo" value="<?php echo $row_RsArticulo['idarticulo']; ?>"/>
<input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion']; ?>"/>
<input type="hidden" name="moold" value="<?php echo $row_RsArticulo['mo']; ?>"/>
<input type="hidden" name="MM_update" value="registrar">
</form>
</body>
</html>
<?php
mysql_free_result($RsArticulo);
?>