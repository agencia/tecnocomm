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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO proveedor (idproveedor, nombrecomercial, razonsocial, domicilio, cp, ciudad, estado, telefono, rfc, ctabancaria, clabe, banco, contacto, email, faccionamiento, abreviacion, clave) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idproveedor'], "int"),
                       GetSQLValueString($_POST['nombrecomercial'], "text"),
                       GetSQLValueString($_POST['razonsocial'], "text"),
                       GetSQLValueString($_POST['domicilio'], "text"),
                       GetSQLValueString($_POST['cp'], "text"),
                       GetSQLValueString($_POST['ciudad'], "text"),
                       GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['ctabancaria'], "text"),
                       GetSQLValueString($_POST['clabe'], "text"),
                       GetSQLValueString($_POST['banco'], "text"),
                       GetSQLValueString($_POST['contacto'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
					   GetSQLValueString($_POST['fraccionamiento'], "text"),
					    GetSQLValueString($_POST['abreviacion'], "text"),
						GetSQLValueString($_POST['clave'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());



   require_once('lib/eventos.php');
	$evt = new evento(33,$_SESSION['MM_Userid'],"Proveedor creado con el nombre comercial de  :".$_POST['nombrecomercial']);
	$evt->registrar();

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSig = "SELECT max(idproveedor) as ultimo FROM proveedor";
$RsSig = mysql_query($query_RsSig, $tecnocomm) or die(mysql_error());
$row_RsSig = mysql_fetch_assoc($RsSig);
$totalRows_RsSig = mysql_num_rows($RsSig);
$num=$row_RsSig['ultimo']+1;

$lon=strlen($num);

if($lon==1){$cad="000".$num;}
if($lon==2){$cad="00".$num;}
if($lon==3){$cad="0".$num;}
if($lon==4){$cad=$num;}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Proveedor</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="form1" id="form1">
  <table width="680" align="center" class="wrapper">
    <!--DWLayoutTable-->
    <tr valign="baseline">
      <td height="20" colspan="5" align="center" nowrap="nowrap" class="titulos">NUEVO PROVEEDOR</td>
    </tr>
    <tr valign="baseline">
      <td height="20" colspan="2" align="center" nowrap="nowrap"><!--DWLayoutEmptyCell-->&nbsp;</td>
      <td colspan="3" align="center" valign="top" nowrap="nowrap"><!--DWLayoutEmptyCell-->&nbsp;</td>
    </tr>
    <tr valign="baseline" class="titulos">
      <td height="20" colspan="2" align="center" nowrap="nowrap"><span class="Estilo1">Datos Generales</span></td>
      <td colspan="3" align="center" valign="top" nowrap="nowrap"><span class="Estilo1">Datos de Contacto</span></td>
    </tr>
    <tr valign="baseline">
      <td height="22" align="right" nowrap="nowrap">CLAVE:</td>
      <td valign="top"><input type="text" name="clave" id="clave" value="PR<?php echo $cad;?>"  readonly="true" /></td>
      <td align="right" valign="top" nowrap="nowrap"><!--DWLayoutEmptyCell-->&nbsp;</td>
      <td colspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td width="140" height="22" align="right" nowrap="nowrap">NOMBRE COMERCIAL:</td>
      <td width="191" valign="top"><label>
        <input type="text" name="nombrecomercial" id="nombrecomercial" />
      </label></td>
      <td width="105" align="right" valign="top" nowrap="nowrap">CONTACTO:</td>
      <td colspan="2" valign="top"><label>
        <input type="text" name="contacto" id="contacto" />
      </label></td>
    </tr>
    <tr valign="baseline">
      <td height="22" align="right" nowrap="nowrap">RAZON SOCIAL:</td>
      <td valign="top"><label>
        <input type="text" name="razonsocial" id="razonsocial" />
      </label></td>
      <td align="right" valign="top" nowrap="nowrap">TELEFONO:</td>
      <td colspan="2" valign="top"><label>
        <input type="text" name="telefono" id="telefono" />
      </label></td>
    </tr>
     <tr valign="baseline">
      <td height="22" align="right" nowrap="nowrap">RFC:</td>
      <td valign="top"><label>
        <input type="text" name="rfc" id="rfc" />
      </label></td>
      <td align="right" valign="top" nowrap="nowrap">E-MAIL:</td>
      <td colspan="2" valign="top"><label>
        <input type="text" name="email" id="email" />
      </label></td>
    </tr>
    <tr valign="baseline">
      <td height="22" align="right" nowrap="nowrap">DOMICILIO:</td>
      <td valign="top"><label>
        <input type="text" name="domicilio" id="domicilio" />
      </label></td>
      <td colspan="3" align="center" valign="top" class="realte">Datos Financieros</td>
    </tr>
    <tr valign="baseline">
      <td height="22" align="right" nowrap="nowrap">FRACCIONAMIENTO:</td>
      <td valign="top"><label>
        <input type="text" name="fraccionamiento" id="fraccionamiento" />
      </label></td>
      <td align="right" valign="top" nowrap="nowrap">CTA. BANCARIA:</td>
      <td colspan="2" valign="top"><label>
        <input type="text" name="ctabancaria" id="ctabancaria" />
      </label></td>
    </tr>
    <tr valign="baseline">
      <td height="22" align="right" nowrap="nowrap">CP:</td>
      <td valign="top"><label>
        <input type="text" name="cp" id="cp" />
      </label></td>
      <td align="right" valign="top" nowrap="nowrap">CLABE:</td>
      <td colspan="2" valign="top"><label>
        <input type="text" name="clabe" id="clabe" />
      </label></td>
    </tr>
    <tr valign="baseline">
      <td height="22" align="right" nowrap="nowrap">CIUDAD:</td>
      <td valign="top"><label>
        <input type="text" name="ciudad" id="ciudad" />
      </label></td>
      <td align="right" valign="top" nowrap="nowrap">BANCO:</td>
      <td colspan="2" valign="top"><label>
        <input type="text" name="banco" id="banco" />
      </label></td>
    </tr>
    <tr valign="baseline">
      <td height="22" align="right" nowrap="nowrap">ESTADO:</td>
      <td valign="top"><label>
        <input type="text" name="estado" id="estado" />
      </label></td>
      <td></td>
      <td width="126"></td>
      <td width="96"></td>
    </tr>
    <tr>
      <td height="33" align="right" valign="top">ABREVIACION:</td>
      <td valign="top"><input type="text" name="abreviacion" id="abreviacion" /></td>
      <td></td>
      <td>&nbsp;</td>
      <td valign="top">
          <input type="submit" name="button" id="button" value="ACEPTAR" />  </td>
    </tr>
  </table>
  
  <input type="hidden" name="idproveedor" value="<?php echo $row_rsProveedor['idproveedor']; ?>" />
  <input type="hidden" name="MM_insert" value="form1" />
</form>
<p>&nbsp;</p>
</body>
</html>