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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "agregarConcepto")) {
  $insertSQL = sprintf("INSERT INTO detallefactura (idfactura, concepto, punitario, cantidad, unidad) VALUES (%s, UPPER(%s), %s, %s, %s)",
                       GetSQLValueString($_POST['factura'], "int"),
                       GetSQLValueString($_POST['concepto'], "text"),
                       GetSQLValueString($_POST['textfield'], "double"),
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['unidad'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Recordset1 = "SELECT * FROM conceptosfactura";
$Recordset1 = mysql_query($query_Recordset1, $tecnocomm) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

$maxRows_RsArticulos = 10;
$pageNum_RsArticulos = 0;
if (isset($_GET['pageNum_RsArticulos'])) {
  $pageNum_RsArticulos = $_GET['pageNum_RsArticulos'];
}
$startRow_RsArticulos = $pageNum_RsArticulos * $maxRows_RsArticulos;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos = "SELECT * FROM articulo";
$query_limit_RsArticulos = sprintf("%s LIMIT %d, %d", $query_RsArticulos, $startRow_RsArticulos, $maxRows_RsArticulos);
$RsArticulos = mysql_query($query_limit_RsArticulos, $tecnocomm) or die(mysql_error());
$row_RsArticulos = mysql_fetch_assoc($RsArticulos);

if (isset($_GET['totalRows_RsArticulos'])) {
  $totalRows_RsArticulos = $_GET['totalRows_RsArticulos'];
} else {
  $all_RsArticulos = mysql_query($query_RsArticulos);
  $totalRows_RsArticulos = mysql_num_rows($all_RsArticulos);
}
$totalPages_RsArticulos = ceil($totalRows_RsArticulos/$maxRows_RsArticulos)-1;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript">
function change(name,cols,lab){
s = document.getElementById(name);
obj = document.createElement('textarea')
//obj.type = 'textarea'
obj.id = name;
obj.name = name;
obj.cols = cols;
obj.rows = 5;
document.getElementById(lab).replaceChild(obj,s)
}
</script>
</head>

<body>

<table width="700" border="0" cellpadding="0" cellspacing="0" class="wrapper">
  <!--DWLayoutTable-->
  <tr>
    <td height="28" colspan="3" valign="top" class="titulos">Agregar Concepto A Factura:</td>
  </tr>
  <tr>
    <td width="27" height="8"></td>
    <td width="629"></td>
    <td width="42"></td>
  </tr>
  <tr>
    <td height="123"></td>
    <td valign="top"><form action="<?php echo $editFormAction; ?>" name="agregarConcepto" method="POST"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="resaltarTabla">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="27"></td>
        <td height="23" colspan="7" valign="top">Concepto Personalizado:</td>
        <td width="37"></td>
      </tr>
      <tr>
        <td></td>
        <td width="65" height="2"></td>
        <td width="313"></td>
        <td width="8"></td>
        <td width="59"></td>
        <td width="10"></td>
        <td width="146"></td>
        <td width="26"></td>
        <td></td>
      </tr>
      
      <tr>
        <td></td>
        <td height="1"></td>
        <td></td>
        <td></td>
        <td rowspan="2" align="right" valign="top">Cantidad:</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
      </tr>
      
      <tr>
        <td></td>
        <td height="22"></td>
          <td rowspan="5" valign="top"><label>
          <textarea name="concepto" rows="5" cols="40"></textarea>
            </label></td>
          <td></td>
          <td>&nbsp;</td>
          <td valign="top"><label>
            <input type="text" name="cantidad" id="cantidad" size="10" value="1"/>
          </label></td>
          <td></td>
          <td></td>
      </tr>
      <tr>
        <td></td>
        <td height="5"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      
      
      <tr>
        <td></td>
        <td height="22"></td>
          <td></td>
          <td align="right" valign="top">Unidad:</td>
          <td>&nbsp;</td>
          <td valign="top"><label>
            <input type="text" name="unidad" id="unidad" size="10" />
          </label></td>
          <td></td>
          <td></td>
      </tr>
      <tr>
        <td></td>
        <td height="2"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
      
      <tr>
        <td></td>
        <td height="22"></td>
        <td></td>
        <td align="right" valign="top">Precio:</td>
          <td>&nbsp;</td>
          <td valign="top"><label>
            <input type="text" name="textfield" id="textfield" size="10"  value="1"/>
          </label></td>
          <td></td>
          <td></td>
      </tr>
      
      <tr>
        <td></td>
        <td height="22"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right"><input type="submit" value="Aceptar" /></td>
        <td></td>
        <td></td>
      </tr>
      
      
      
      
      
      
      
      
      
      
      
    </table><input type="hidden" name="factura" value="<?php echo $_GET['idfactura']; ?>" />
    <input type="hidden" name="MM_insert" value="agregarConcepto" />
    </form></td>
    <td></td>
  </tr>
  <tr>
    <td height="11"></td>
    <td></td>
    <td></td>
  </tr>
</table>

</body>
</html>
<?php
mysql_free_result($Recordset1);

mysql_free_result($RsArticulos);
?>
