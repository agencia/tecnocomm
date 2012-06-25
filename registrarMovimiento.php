<?php require_once('Connections/tecnocomm.php'); ?>
<?php
session_start();
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  if($_POST['tipopago'] == 0){
	  $aplica=-1;
	  if($_POST['tipo']==0){$aplica=0;}
	$insertSQL = sprintf("INSERT INTO banco (fecha, concepto, referencia, importe, tipo,tipopago,fechacobro,idip,aplica) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['concepto'], "text"),
                       GetSQLValueString($_POST['referencia'], "text"),
                       GetSQLValueString($_POST['importe'], "double"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['tipopago'], "int"),
					   GetSQLValueString($_POST['fechacobro'], "date"),
					   GetSQLValueString($_POST['idip'], "int"),
					   GetSQLValueString($aplica, "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
	  
	  }
else{  
  $insertSQL = sprintf("INSERT INTO banco (fecha, concepto, referencia, importe, tipo,tipopago, idip) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['concepto'], "text"),
                       GetSQLValueString($_POST['referencia'], "text"),
                       GetSQLValueString($_POST['importe'], "double"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['tipopago'], "int"),
					   GetSQLValueString($_POST['idip'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
}
if($_POST['tipo']==0){

   require_once('lib/eventos.php');
	$evt = new evento(37,$_SESSION['MM_Userid'],"Deposito realizado con el cheke :".$_POST['cheke']);
	$evt->registrar();
}

if($_POST['tipo']==1){

   require_once('lib/eventos.php');
	$evt = new evento(38,$_SESSION['MM_Userid'],"Retiro realizado con el cheke :".$_POST['cheke']);
	$evt->registrar();
}

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Registrar Movimientos en Banco</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css"  rel="stylesheet" type="text/css"/>
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jqueryui.js"></script>
<script language="javascript" src="js/calendario.js"></script>
</head>

<body><form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
<table width="451" border="0" align="center" class="wrapper">
  <!--DWLayoutTable-->
  <tr class="titulos">
    <td height="22" colspan="2" align="center">REGISTRAR MOVIMIENTO </td>
    <td width="47"></td>
    <td width="114"></td>
  </tr>
  <tr>
    <td width="98" height="22">&nbsp;</td>
    <td width="209">&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="67" align="right" valign="top">CONCEPTO</td>
    <td colspan="3" valign="top">
      <label>
        <textarea name="concepto" cols="45" rows="4"></textarea>
      </label>    </td>
    </tr>
  <tr>
    <td height="22" align="right">FECHA:</td>
    <td colspan="3" valign="top"><label>
      <input name="fecha" type="text" id="fecha" value="<?php echo date('Y-m-d')?>" size="15" />
    </label></td>
    </tr>

  <tr>
    <td height="22" align="right">TIPO PAGO:</td>
    <td colspan="3" valign="top">  <select name="tipopago" id="tipopago">

        <option value="0">Cheque</option>
        <option value="1">Transferencia</option>
        <option value="2">Efectivo</option>
        <option value="4">Tarjeta</option>
        <option value="3">Otro</option>
        
      </select></td>
    </tr>
  <tr>
    <td height="22" align="right">REFERENCIA:</td>
    <td colspan="3" valign="top"><input name="referencia" type="text" id="referencia" /></td>
    </tr>
  <tr>
    <td height="22" align="right">IMPORTE:</td>
    <td colspan="3" valign="top"><input name="importe" type="text" id="importe" size="15" /></td>
  </tr>
  
  <tr>
    <td height="22" align="right">IP:</td>
    <td colspan="3" valign="top"><input name="idip" type="text" id="idip" size="15" /></td>
  </tr>
  <tr>
    <td height="22" align="right">FECHA DE COBRO:(aplica solo a cheques)</td>
    <td colspan="3" valign="top"><input name="fechacobro" type="text" id="fechacobro"  class="fecha" /></td>
    </tr>
   
  <tr>
    <td height="23">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td valign="top"><label>
      <input type="submit" name="Submit" value="REGISTRAR" />
    </label></td>
    </tr>
</table>
<input name="tipo" type="hidden" value="<? echo $_GET['tipo'];?>"/>
<input type="hidden" name="MM_insert" value="form1">
</form>
</body>
</html>
