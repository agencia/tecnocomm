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


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Recordset1 = "SELECT * FROM conceptosfactura";
$Recordset1 = mysql_query($query_Recordset1, $tecnocomm) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Especificar Concepto</title>
<script language="JavaScript" type="text/javascript">
function change(name,size,lab){
s = document.getElementById(name);
obj = document.createElement('input')
obj.type = 'text'
obj.id = name;
obj.name = name;
obj.size = size;
document.getElementById(lab).replaceChild(obj,s)
}
</script>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form method="POST" action="<?php echo $editFormAction; ?>" name="guardarConcepto">
<table width="384" border="0" cellpadding="0" cellspacing="0" class="wrapper">
  <!--DWLayoutTable-->
  <tr>
    <td height="24" colspan="4" valign="top">Agregar Concepto:</td>
    <td width="5"></td>
  </tr>
  <tr>
    <td width="94" height="10"></td>
    <td width="204"></td>
    <td width="71"></td>
    <td width="8"></td>
    <td></td>
  </tr>
  
  
  <tr>
    <td height="24" align="right" valign="top">Concepto:</td>
    <td colspan="2" valign="top">
      <label id="con">
      <select name="concepto" id="concepto">
        <?php
do {  
?>
        <option value="<?php echo $row_Recordset1['concepto'];?>"><?php echo $row_Recordset1['concepto'];?></option>
        <?php
} while ($row_Recordset1 = mysql_fetch_assoc($Recordset1));
  $rows = mysql_num_rows($Recordset1);
  if($rows > 0) {
      mysql_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysql_fetch_assoc($Recordset1);
  }te
?>
<option value="-1" onclick="change('concepto',45,'con');">Nuevo....</option>
      </select>
      </label>    </td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td height="20" align="right" valign="top">Cantidad:</td>
    <td colspan="2" valign="top"><label>
      <input type="text" name="cantidad" id="cantidad" />
    </label></td>
    <td></td>
    <td></td>
    </tr>
  <tr>
    <td height="22" align="right" valign="top">Unidad:</td>
    <td colspan="2" valign="top"><label>
      <input type="text" name="unidad" id="unidad" />
    </label></td>
    <td></td>
    <td></td>
    </tr>
  <tr>
    <td height="24" align="right" valign="top">Precio Unitario:</td>
    <td colspan="2" valign="top"><label>
      <input type="text" name="precio" id="precio" />
    </label></td>
    <td></td>
    <td></td>
    </tr>
  <tr>
    <td height="20">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td height="24"></td>
    <td></td>
    <td valign="top"><label>
      <input type="submit" name="Aceptar" id="Aceptar" value="Aceptar" />
    </label></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="9"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="factura" value="<?php echo $_GET['idfactura']; ?>" />
<input type="hidden" name="MM_insert" value="guardarConcepto" />
</form>
</body>
</html>
<?php
mysql_free_result($Recordset1);

mysql_free_result($rsConcepto);
?>
