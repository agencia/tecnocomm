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

$maxRows_rsProveedor = 10;
$pageNum_rsProveedor = 0;
if (isset($_GET['pageNum_rsProveedor'])) {
  $pageNum_rsProveedor = $_GET['pageNum_rsProveedor'];
}
$startRow_rsProveedor = $pageNum_rsProveedor * $maxRows_rsProveedor;

$colname_rsProveedor = "-1";
if (isset($_GET['idproveedor'])) {
  $colname_rsProveedor = $_GET['idproveedor'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedor = sprintf("SELECT * FROM proveedor WHERE idproveedor = %s", GetSQLValueString($colname_rsProveedor, "int"));
$query_limit_rsProveedor = sprintf("%s LIMIT %d, %d", $query_rsProveedor, $startRow_rsProveedor, $maxRows_rsProveedor);
$rsProveedor = mysql_query($query_limit_rsProveedor, $tecnocomm) or die(mysql_error());
$row_rsProveedor = mysql_fetch_assoc($rsProveedor);

if (isset($_GET['totalRows_rsProveedor'])) {
  $totalRows_rsProveedor = $_GET['totalRows_rsProveedor'];
} else {
  $all_rsProveedor = mysql_query($query_rsProveedor);
  $totalRows_rsProveedor = mysql_num_rows($all_rsProveedor);
}
$totalPages_rsProveedor = ceil($totalRows_rsProveedor/$maxRows_rsProveedor)-1;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
</style>
</head>

<body>
<form method="post" name="form1" id="form1">
  <table width="680" align="center" class="wrapper">
    <!--DWLayoutTable-->
    <tr valign="baseline">
      <td height="20" colspan="4" align="center" nowrap="nowrap" class="titulos">DETALLE PROVEEDOR</td>
    </tr>
    <tr valign="baseline">
      <td height="20" colspan="2" align="center" nowrap="nowrap"><!--DWLayoutEmptyCell-->&nbsp;</td>
      <td colspan="2" align="center" valign="top" nowrap="nowrap"><!--DWLayoutEmptyCell-->&nbsp;</td>
    </tr>
    <tr valign="baseline" class="titulos">
      <td height="20" colspan="2" align="center" nowrap="nowrap"><span class="Estilo1">DATOS GENERALES</span></td>
      <td colspan="2" align="center" valign="top" nowrap="nowrap"><span class="Estilo1">DATOS DE CONTACTO</span></td>
    </tr>
    <tr valign="baseline">
      <td width="116" height="20" align="right" nowrap="nowrap">NOMBRE COMERCIAL:</td>
      <td width="204" valign="top"><?php echo $row_rsProveedor['nombrecomercial']; ?></td>
      <td width="105" align="right" valign="top" nowrap="nowrap">CONTACTO:</td>
      <td width="227" valign="top"><?php echo $row_rsProveedor['contacto']; ?></td>
    </tr>
    <tr valign="baseline">
      <td height="20" align="right" nowrap="nowrap">RAZON SOCIAL:</td>
      <td valign="top"><?php echo $row_rsProveedor['razonsocial']; ?></td>
      <td align="right" valign="top" nowrap="nowrap">TELEFONO:</td>
      <td valign="top"><?php echo $row_rsProveedor['telefono']; ?></td>
    </tr>
     <tr valign="baseline">
      <td height="20" align="right" nowrap="nowrap">RFC:</td>
      <td valign="top"><?php echo $row_rsProveedor['rfc']; ?></td>
      <td align="right" valign="top" nowrap="nowrap">E-MAIL:</td>
      <td valign="top"><?php echo $row_rsProveedor['email']; ?></td>
    </tr>
     <tr valign="baseline">
       <td height="20" align="right" nowrap="nowrap"><!--DWLayoutEmptyCell-->&nbsp;</td>
       <td valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
       <td colspan="2" align="center" valign="top" class="realte">DATOS FINANCIEROS</td>
     </tr>
    <tr valign="baseline">
      <td height="20" align="right" nowrap="nowrap">DOMICILIO:</td>
      <td valign="top"><?php echo $row_rsProveedor['domicilio']; ?></td>
      <td colspan="2" align="center" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td height="20" align="right" nowrap="nowrap">FRACCIONAMIENTO:</td>
      <td valign="top"><?php echo $row_rsProveedor['faccionamiento']; ?></td>
      <td align="right" valign="top" nowrap="nowrap">CTA. BANCARIA:</td>
      <td valign="top"><?php echo $row_rsProveedor['ctabancaria']; ?></td>
    </tr>
    <tr valign="baseline">
      <td height="20" align="right" nowrap="nowrap">CP:</td>
      <td valign="top"><?php echo $row_rsProveedor['cp']; ?></td>
      <td align="right" valign="top" nowrap="nowrap">CLABE BANCARIA:</td>
      <td valign="top"><?php echo $row_rsProveedor['clabe']; ?></td>
    </tr>
    <tr valign="baseline">
      <td height="20" align="right" nowrap="nowrap">CIUDAD:</td>
      <td valign="top"><?php echo $row_rsProveedor['ciudad']; ?></td>
      <td align="right" valign="top" nowrap="nowrap">BANCO:</td>
      <td valign="top"><?php echo $row_rsProveedor['banco']; ?></td>
    </tr>
    <tr valign="baseline">
      <td height="20" align="right" nowrap="nowrap">ESTADO:</td>
      <td valign="top"><?php echo $row_rsProveedor['estado']; ?></td>
      <td></td>
      <td></td>
    </tr>
  </table>
  
  <input type="hidden" name="idproveedor" value="<?php echo $row_rsProveedor['idproveedor']; ?>" />
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rsProveedor);
?>
