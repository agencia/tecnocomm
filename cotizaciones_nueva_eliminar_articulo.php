<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('Connections/tecnocomm.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

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

if ((isset($_POST['idsubcotizacion'])) && ($_POST['idsubcotizacion'] != "")) {
  $deleteSQL = sprintf("DELETE FROM subcotizacionarticulo WHERE idsubcotizacionarticulo=%s ",
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));
//					   echo $deleteSQL;


  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($deleteSQL, $tecnocomm) or die(mysql_error());

  $deleteGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

session_start();

$sub_RsArticulo = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $sub_RsArticulo = (get_magic_quotes_gpc()) ? $_GET['idsubcotizacion'] : addslashes($_GET['idsubcotizacion']);
}
$ide_RsArticulo = "-1";
if (isset($_GET['idarticulo'])) {
  $ide_RsArticulo = (get_magic_quotes_gpc()) ? $_GET['idarticulo'] : addslashes($_GET['idarticulo']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulo = sprintf("SELECT * FROM articulo,subcotizacionarticulo WHERE  subcotizacionarticulo.idsubcotizacionarticulo=%s", $sub_RsArticulo);
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
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>

<body class="wrapper">
<form name="registrar" method="POST" >
<table width="500" border="0" align="center" >
  <tr>
    <td width="16">&nbsp;</td>
    <td colspan="3" align="center" background="images/titulo.gif" class="titulos">ELIMINAR ARTICULO</td>
    <td width="18">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="5">Estas seguro que deseas eliminar el articulo o servicio: <span class="Estilo1"><?php echo $row_RsArticulo['descri']; ?></span>.Con el precio:<span class="Estilo1"> <?php echo $row_RsArticulo['precio_cotizacion'] ; ?></span> y cantidad:<span class="Estilo1"> <?php echo $row_RsArticulo['cantidad'] ; ?></span> ? </td>
    </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td width="5">&nbsp;</td>
    <td width="313" align="right"><label>
      <input type="submit" name="Submit" value="Cancelar" onclick="window.close();" />
    </label></td>
    <td width="126" align="right"><input type="submit" name="button" id="button" value="Aceptar" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="idarticulo" value="<?php echo $row_RsArticulo['idarticulo']; ?>"/>
<input type="hidden" name="mo" value="<?php echo $row_RsArticulo['mo']; ?>"/>
<input type="hidden" name="tipo" value="<?php echo $_GET['tipo']; ?>"/>
<input type="hidden" name="idsub" value="<?php echo $row_RsArticulo['idsubcotizacion']; ?>"/>
<input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion']; ?>"/>
</form>
</body>
</html>
<?php
mysql_free_result($RsArticulo);
?>