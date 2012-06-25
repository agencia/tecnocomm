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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "almacen")) {
  $updateSQL = sprintf("UPDATE detalleorden SET almacen=%s WHERE identificador=%s",
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['identificador'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}




$colname_rsOrden = "-1";
if (isset($_GET['identificador'])) {
  $colname_rsOrden = $_GET['identificador'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrden = sprintf("SELECT * FROM detalleorden WHERE identificador = %s", GetSQLValueString($colname_rsOrden, "int"));
$rsOrden = mysql_query($query_rsOrden, $tecnocomm) or die(mysql_error());
$row_rsOrden = mysql_fetch_assoc($rsOrden);
$totalRows_rsOrden = mysql_num_rows($rsOrden);
?>
<link href="style.css" rel="stylesheet" type="text/css" />
<form action="<?php echo $editFormAction; ?>" name="almacen" method="POST">
<table width="290" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr class="titleTabla">
    <td height="22" colspan="5" valign="top" >Disponible En Almacen</td>
  </tr>
  <tr>
    <td width="78" height="5"></td>
    <td width="70"></td>
    <td width="37"></td>
    <td width="92"></td>
    <td width="13"></td>
  </tr>
  <tr>
    <td height="23" valign="top">Cantidad</td>
    <td align="right" valign="top"><label>
      <input type="text" name="cantidad" id="cantidad" size="5" align="right" value="<?php echo $row_rsOrden['almacen']; ?>">
    </label></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="6"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="26"></td>
    <td></td>
    <td></td>
    <td valign="top"><input type="submit" value="Aceptar"></td>
    <td></td>
  </tr>
  <tr>
    <td height="14"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  </table>
 
  <input type="hidden" name="identificador" value="<?php echo $_GET['identificador'];?>">
  <input type="hidden" name="MM_update" value="almacen" />
</form>
<?php
mysql_free_result($rsOrden);
?>
