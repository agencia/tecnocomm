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

$maxRows_rsCliente = 30;
$pageNum_rsCliente = 0;
if (isset($_GET['pageNum_rsCliente'])) {
  $pageNum_rsCliente = $_GET['pageNum_rsCliente'];
}
$startRow_rsCliente = $pageNum_rsCliente * $maxRows_rsCliente;

$colname_rsCliente = "-1";
if (isset($_GET['idcliente'])) {
  $colname_rsCliente = $_GET['idcliente'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCliente = sprintf("SELECT nombre FROM cliente WHERE idcliente = %s", GetSQLValueString($colname_rsCliente, "int"));
$query_limit_rsCliente = sprintf("%s LIMIT %d, %d", $query_rsCliente, $startRow_rsCliente, $maxRows_rsCliente);
$rsCliente = mysql_query($query_limit_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);

if (isset($_GET['totalRows_rsCliente'])) {
  $totalRows_rsCliente = $_GET['totalRows_rsCliente'];
} else {
  $all_rsCliente = mysql_query($query_rsCliente);
  $totalRows_rsCliente = mysql_num_rows($all_rsCliente);
}
$totalPages_rsCliente = ceil($totalRows_rsCliente/$maxRows_rsCliente)-1;

$colname_rsContactos = "-1";
if (isset($_GET['idcliente'])) {
  $colname_rsContactos = $_GET['idcliente'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsContactos = sprintf("SELECT * FROM contactoclientes WHERE idcliente = %s ORDER BY nombre ASC", GetSQLValueString($colname_rsContactos, "int"));
$rsContactos = mysql_query($query_rsContactos, $tecnocomm) or die(mysql_error());
$row_rsContactos = mysql_fetch_assoc($rsContactos);
$totalRows_rsContactos = mysql_num_rows($rsContactos);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Contactos</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/funciones.js"></script>
</head>

<body>
<table width="803" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="26" colspan="5" valign="top" class="titulos">Contactos</td>
  </tr>
  <tr>
    <td width="6" height="5"></td>
    <td width="71"></td>
    <td width="381"></td>
    <td width="339"></td>
    <td width="6"></td>
  </tr>
  
  
  <tr>
    <td height="23" colspan="2" valign="top">Cliente:</td>
    <td valign="top"><?php echo $row_rsCliente['nombre']; ?></td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="26" colspan="5" align="center" valign="top"><a href="nuevoContacto.php?idcliente=<?php echo $_GET['idcliente'];?>" onclick="NewWindow(this.href,'Nuevo Contacto','500','500','no');return false"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" /></strong>Nuevo Contacto</a></td>
  </tr>
  <tr>
    <td height="8"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="78"></td>
    <td colspan="3" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="101" height="22" valign="top">Opciones</td>
          <td width="195" valign="top">Nombre</td>
          <td width="178" valign="top">Puesto</td>
          <td width="166" valign="top">Telefono</td>
          <td width="151" valign="top">Email</td>
        </tr>
      <tr>
        <td height="13"></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      <?php do { ?>
        <tr>
          <td height="23" valign="top"><a href="editarContacto.php?idcontacto=<?php echo $row_rsContactos['idcontacto']; ?>" onclick="NewWindow(this.href,'Nuevo Contacto','500','500','no');return false"> <img src="images/Edit.png" width="24" height="24" /></a><a href="eliminarContacto.php?idcontacto=<?php echo $row_rsContactos['idcontacto']; ?>" onclick="NewWindow(this.href,'Nuevo Contacto','500','500','no');return false"> <img src="images/eliminar.gif" width="19" height="19" /></a></td>
          <td valign="top"><?php echo $row_rsContactos['nombre']; ?></td>
          <td valign="top"><?php echo $row_rsContactos['puesto']; ?></td>
          <td valign="top"><?php echo $row_rsContactos['telefono']; ?></td>
          <td valign="top"><?php echo $row_rsContactos['correo']; ?></td>
        </tr>
        <?php } while ($row_rsContactos = mysql_fetch_assoc($rsContactos)); ?>
      <tr>
        <td height="20">&nbsp;</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
    </table></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="20"></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
</table>
</body>
</html>
<?php
mysql_free_result($rsCliente);

mysql_free_result($rsContactos);
?>
