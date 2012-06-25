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

$ide_RsSub = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $ide_RsSub = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSub = sprintf("SELECT *,(select nombre from contactoclientes where idcontacto=subcotizacion.contacto) as conta,contacto as con,(select telefono from contactoclientes where idcontacto=subcotizacion.contacto) as tele, (select correo from contactoclientes where idcontacto=subcotizacion.contacto) as mail FROM subcotizacion WHERE idsubcotizacion=%s", GetSQLValueString($ide_RsSub, "int"));
$RsSub = mysql_query($query_RsSub, $tecnocomm) or die(mysql_error()." error en suncotizacion");
$row_RsSub = mysql_fetch_assoc($RsSub);
$totalRows_RsSub = mysql_num_rows($RsSub);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCliente = "select * from cliente cl,subcotizacion sb,cotizacion c where  sb.idcotizacion = c.idcotizacion AND cl.idcliente=c.idcliente AND sb.idsubcotizacion=".GetSQLValueString($ide_RsSub, "int");
$RsCliente = mysql_query($query_RsCliente, $tecnocomm) or die(mysql_error()." error en cliente");
$row_RsCliente = mysql_fetch_assoc($RsCliente);
$totalRows_RsCliente = mysql_num_rows($RsCliente);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="850" border="0" align="center" class="wrapper">
  <!--DWLayoutTable-->
  <tr >
    <td height="28" align="center" class="titulos">DATOS GENERALES DE COTIZACION</td>
  </tr>
  <tr>
    <td height="311" align="left"><fieldset>
      <legend>DATOS COTIZACION </legend>
	    <table width="835" border="0">
	      <!--DWLayoutTable-->
          <tr>
            <td height="22" colspan="3" valign="top">IDENTIFICADOR:<span class="Estilo1"><?php echo $row_RsSub['nombre'];?></span></td>
            <td colspan="5" valign="top"><p>COTIZACION No.:<span class="Estilo1"><?php echo $row_RsSub['identificador2'];?></span></p></td>
          </tr>
          <tr>
            <td height="22" colspan="4" valign="top">
FORMA DE PAGO:<span class="Estilo1"><?php echo $row_RsSub['formapago'];?></span></td>
            <td width="69">&nbsp;</td>
            <td width="19">&nbsp;</td>
            <td width="115">&nbsp;</td>
            <td width="58">&nbsp;</td>
          </tr>
          <tr>
            <td height="22" colspan="4" valign="top">VIGENCIA:<span class="Estilo1"><?php echo $row_RsSub['vigencia'];?></span></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>   <tr>
            <td height="22" colspan="4" valign="top">TIEMPO ENTREGA:<span class="Estilo1"><?php echo $row_RsSub['tipoentrega'];?></span></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td width="380" height="22" valign="top">GARANTIA:<span class="Estilo1"><?php echo $row_RsSub['garantia'];?></span></td>
            <td width="53">&nbsp;</td>
            <td width="20">&nbsp;</td>
            <td width="87">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="22" colspan="2" valign="top">UTILIDAD GLOBAL:<span class="Estilo1"><?php echo $row_RsSub['utilidad_global'];?>(Este factor puede cambiar dependiendo del articulo)</span></td>
            <td>&nbsp;</td>
            <td colspan="2" valign="top">TIPO DE CAMBIO:<span class="Estilo1"><?php echo $row_RsSub['tipo_cambio'];?></span></td>
            <td>&nbsp;</td>
            <td valign="top">DESCUENTO:<span class="Estilo1"><?php echo $row_RsSub['descuento'];  ?></span><span class="Estilo1">%</span></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td height="7"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </table>
	    
        </fieldset>
       
<fieldset><legend>DATOS CLIENTE</legend>
        <table width="800" border="0">
          <tr>
            <td width="441">NOMBRE CLIENTE:<span class="content"> <?php echo $row_RsCliente['nombre']; ?></span></td>
            <td width="315">RFC:<?php echo $row_RsCliente['rfc']; ?></td>
            <td width="30">&nbsp;</td>
          </tr>
          <tr>
            <td>DIRECCION:<?php echo $row_RsCliente['direccion']; ?></td>
            <td>TELEFONO:<?php echo $row_RsSub['tele']; ?></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>CONTACTO: <span class="Estilo1"><?php if($row_RsSub['con']!=0){echo $row_RsSub['conta'];}else{echo "CONTACTO NO ASIGNADO";}?></span> </td>
            <td>EMAIL:<?php echo $row_RsSub['mail']; ?></td>
            <td>&nbsp;</td>
          </tr>
        </table>
        </fieldset>  </td>
  </tr>
  
  <tr>
    <td width="879" height="64">&nbsp;</td>
  </tr>
  <tr>
    <td height="1"></td>
  </tr>
        </table>

</body>
</html>
