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



if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
if($_POST['mont']==0){$mo=1;}else{$mo=$_POST['mont'];}
@$newmon=($_POST['monto']*$_POST['mont'])/$_POST['mo'];
//echo $newmon;
if(isset($_POST['re']) && $_POST['re']==1){
$updateSQL = sprintf("UPDATE subcotizacion SET descrimano=%s, montoreal=%s, cantidadreal=%s, unidad=%s, codigo=%s, marca=%s WHERE idsubcotizacion=%s",

                       GetSQLValueString($_POST['descrimano'], "text"),

                       GetSQLValueString($newmon, "double"),
					   
					   GetSQLValueString($_POST['cantidad'], "double"),

                       GetSQLValueString($_POST['unidad'], "text"),

                       GetSQLValueString($_POST['codigo'], "text"),

                       GetSQLValueString($_POST['marca'], "text"),

                       GetSQLValueString($_POST['idsubcotizacion'], "int"));



}
else{

$updateSQL = sprintf("UPDATE subcotizacion SET descrimano=%s, monto=%s, cantidad=%s, unidad=%s, codigo=%s, marca=%s WHERE idsubcotizacion=%s",

                       GetSQLValueString($_POST['descrimano'], "text"),

                       GetSQLValueString($newmon, "double"),
					   
					   GetSQLValueString($_POST['cantidad'], "double"),

                       GetSQLValueString($_POST['unidad'], "text"),

                       GetSQLValueString($_POST['codigo'], "text"),

                       GetSQLValueString($_POST['marca'], "text"),

                       GetSQLValueString($_POST['idsubcotizacion'], "int"));



}

  
//echo $updateSQL;


  mysql_select_db($database_tecnocomm, $tecnocomm);

  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());



 $updateGoTo = "close.php";

  if (isset($_SERVER['QUERY_STRING'])) {

    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";

    $updateGoTo .= $_SERVER['QUERY_STRING'];

  }

  header(sprintf("Location: %s", $updateGoTo));

}



$ide_RsMO = "-1";

if (isset($_GET['idsubcotizacion'])) {

  $ide_RsMO = $_GET['idsubcotizacion'];

}

mysql_select_db($database_tecnocomm, $tecnocomm);

$query_RsMO = sprintf("select * from subcotizacion where idsubcotizacion=%s", GetSQLValueString($ide_RsMO, "int"));

$RsMO = mysql_query($query_RsMO, $tecnocomm) or die(mysql_error());

$row_RsMO = mysql_fetch_assoc($RsMO);

$totalRows_RsMO = mysql_num_rows($RsMO);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Documento sin t&iacute;tulo</title>

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

</head>



<body>

<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">

  <table align="center" class="wrapper">

    <tr valign="baseline">

      <td colspan="2" align="center" nowrap="nowrap" class="titulos">DATOS MANO DE OBRA</td>

    </tr>

    <tr valign="baseline">

      <td nowrap="nowrap" align="right">&nbsp;</td>

      <td>&nbsp;</td>

    </tr>

    <tr valign="baseline">

      <td nowrap="nowrap" align="right">DESCRIPCION MANO DE OBRA:</td>

      <td><input type="text" name="descrimano" value="<?php echo htmlentities($row_RsMO['descrimano'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>

    </tr>

    <tr valign="baseline">

      <td nowrap="nowrap" align="right">FIJAR PRECIO DE MANO DE OBRA : </td>

      <td><input name="monto" type="text" id="monto" value="<?php echo htmlentities($_GET['cant'], ENT_COMPAT, 'utf-8'); ?>" size="32" onchange="confirmar('monto','<?php echo money_format('%i',$_GET['cant']); ?>',this.form.monto.value);"/></td>

    </tr>

    <tr valign="baseline">

      <td nowrap="nowrap" align="right">CANTIDAD:</td>
		<?php if(isset($_GET['real']) && $_GET['real']==1){$cant=$row_RsMO['cantidadreal'];}else{$cant=$row_RsMO['cantidad'];}?>
      <td><input name="cantidad" type="text" id="cantidad" value="<?php echo htmlentities($cant, ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>

    </tr>

    <tr valign="baseline">

      <td nowrap="nowrap" align="right">UNIDAD:</td>

      <td><input name="unidad" type="text" id="unidad" value="<?php echo htmlentities($row_RsMO['unidad'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>

    </tr>

    <tr valign="baseline">

      <td nowrap="nowrap" align="right">MARCA:</td>

      <td><input name="marca" type="text" id="marca" value="<?php echo htmlentities($row_RsMO['marca'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>

    </tr>

    <tr valign="baseline">

      <td nowrap="nowrap" align="right">CODIGO:</td>

      <td><input name="codigo" type="text" id="codigo" value="<?php echo htmlentities($row_RsMO['codigo'], ENT_COMPAT, 'utf-8'); ?>" size="32" /></td>

    </tr>

    <tr valign="baseline">

      <td nowrap="nowrap" align="right">&nbsp;</td>

      <td><input type="submit" value="ACEPTAR" /></td>

    </tr>

  </table>

  <input type="hidden" name="idsubcotizacion" value="<?php echo htmlentities($row_RsMO['idsubcotizacion'], ENT_COMPAT, 'utf-8'); ?>" />

  <input type="hidden" name="MM_update" value="form1" />

  <input type="hidden" name="idsubcotizacion" value="<?php echo $row_RsMO['idsubcotizacion']; ?>" />

   <input type="hidden" name="mont" value="<?php if(isset($_GET['real']) and $_GET['real']==1){echo $row_RsMO['montoreal'];}else{echo $row_RsMO['monto']; }?>" />

   <input type="hidden" name="mo" value="<?php echo $_GET['cant']; ?>" />
   
   <input type="hidden" name="re" value="<?php echo $_GET['real']; ?>" />

</form>

<p>&nbsp;</p>

</body>

</html>

<?php

mysql_free_result($RsMO);

?>

