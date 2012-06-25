
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



$colname_rsPartida = "-1";
if (isset($_GET['identificador'])) {
  $colname_rsPartida = $_GET['identificador'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartida = sprintf("SELECT * FROM detalleproductoorden WHERE identificador = %s", GetSQLValueString($colname_rsPartida, "int"));
$rsPartida = mysql_query($query_rsPartida, $tecnocomm) or die(mysql_error());
$row_rsPartida = mysql_fetch_assoc($rsPartida);
$totalRows_rsPartida = mysql_num_rows($rsPartida);


?>

<form name="actualizarCantidad" method="POST">
<table width="290" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td height="22" colspan="5" valign="top">Actualizar Cantidad</td>
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
      <input type="text" name="cantidad" id="cantidad" size="5" align="right" value="<?php echo $row_rsPartida['cantidadsurtida']; ?>">
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
  <input type="hidden" name="identificador" value="<?php  echo $row_rsPartida['identificador'];  ?>">
  <input type="hidden" name="MM_update" value="actualizarCantidad">
  </form>
 
<?php mysql_free_result($rsPartida); ?>