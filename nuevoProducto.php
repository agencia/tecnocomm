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


  $insertSQL = sprintf("INSERT INTO articulo (nombre, codigo, marca, medida, moneda, precio, instalacion, empaque, clave, tipo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['codigo'], "text"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['medida'], "text"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['precio'], "double"),
					   GetSQLValueString($_POST['instalacion'], "double"),
                       GetSQLValueString($_POST['empaque'], "text"),			
					   GetSQLValueString($_POST['clave'], "text"),
					   GetSQLValueString($_POST['preco'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());


   require_once('lib/eventos.php');
	$evt = new evento(39,$_SESSION['MM_Userid'],"Articulo dado de alta con la descrpcion :".$_POST['nombre']);
	$evt->registrar();

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSig = "SELECT max(idarticulo) as ultimo FROM articulo";
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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center" class="wrapper">
    <tr valign="baseline" class="titulos">
      <td colspan="2" align="center" nowrap="nowrap"><span class="Estilo1">AGREGAR ARTICULO</span></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Clave:</td>
      <td><input type="text" name="clave" value="CO<?php echo $cad;?>"  readonly="true" size="20" /></td>
    </tr>
    <tr valign="baseline">
      <td align="right" valign="top" nowrap="nowrap">Descripcion:</td>
      <td><textarea name="nombre"  cols="80" rows="12"></textarea></td>
    </tr>
    
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Codigo:</td>
      <td><input type="text" name="codigo" value="" size="20" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Marca:</td>
      <td><input type="text" name="marca" value="" size="20" /></td>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Medida:</td>
      <td><input type="text" name="medida" size="10" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Moneda:</td>
      <td><select name="moneda">
        <option value="0" <?php if (!(strcmp(0, ""))) {echo "SELECTED";} ?>>Pesos</option>
        <option value="1" <?php if (!(strcmp(1, ""))) {echo "SELECTED";} ?>>Dolares</option>
      </select>      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right"><label>
        <select name="preco" id="preco">
          <option value="0">Precio</option>
          <option value="1">Costo</option>
        </select>
      </label>
      :</td>
      <td><input type="text" name="precio" value="" size="10"  style="text-align:right"/></td>
    </tr>
        
        <tr valign="baseline">
      <td nowrap="nowrap" align="right">Costo Instalacion:</td>
      <td><input type="text" name="instalacion" value="<?php echo htmlentities($row_rsProducto['instalacion'], ENT_COMPAT, 'UTF-8'); ?>" size="10" style="text-align:right"/></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Empaque:</td>
      <td><input type="text" name="empaque" value="" size="10" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td align="right"><input type="submit" value="Aceptar" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1" />
</form>
<p>&nbsp;</p>
</body>
</html>
