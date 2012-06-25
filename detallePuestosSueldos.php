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

$colname_RsSueldos = "-1";
if (isset($_GET['idempleado'])) {
  $colname_RsSueldos = $_GET['idempleado'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSueldos = sprintf("SELECT * FROM empleadosueldo WHERE idempleado = %s ORDER BY fecha DESC", GetSQLValueString($colname_RsSueldos, "int"));
$RsSueldos = mysql_query($query_RsSueldos, $tecnocomm) or die(mysql_error());
$row_RsSueldos = mysql_fetch_assoc($RsSueldos);
$totalRows_RsSueldos = mysql_num_rows($RsSueldos);

$colname_RsPuestos = "-1";
if (isset($_GET['idempleado'])) {
  $colname_RsPuestos = $_GET['idempleado'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsPuestos = sprintf("SELECT * FROM empleadopuesto WHERE idempleado = %s ORDER BY fecha DESC", GetSQLValueString($colname_RsPuestos, "int"));
$RsPuestos = mysql_query($query_RsPuestos, $tecnocomm) or die(mysql_error());
$row_RsPuestos = mysql_fetch_assoc($RsPuestos);
$totalRows_RsPuestos = mysql_num_rows($RsPuestos);

$colname_RsEmpleado = "-1";
if (isset($_GET['idempleado'])) {
  $colname_RsEmpleado = $_GET['idempleado'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsEmpleado = sprintf("SELECT nombre FROM empleado WHERE idempleado = %s", GetSQLValueString($colname_RsEmpleado, "int"));
$RsEmpleado = mysql_query($query_RsEmpleado, $tecnocomm) or die(mysql_error());
$row_RsEmpleado = mysql_fetch_assoc($RsEmpleado);
$totalRows_RsEmpleado = mysql_num_rows($RsEmpleado);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Detalle de Puestos y Sueldos</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/valid.js"></script>
<script language="javascript"  src="js/funciones.js"></script>
</head>

<body>
<h1> Detalle de Puestos y Sueldos (<?php echo $row_RsEmpleado['nombre']; ?>)</h1>
<h1> Sueldos</h1>
<div id="distabla">
  
<table width="100%" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <thead>
        
        <tr>
          <td>Fecha</td>
          <td>Monto</td>
          <td>Porcentaje</td>
          <td>Comentario</td>
        </tr>
            </thead>
      <tbody>
       
            
          <?php do { ?>
            <tr>
              <td  valign="top"><?php echo $row_RsSueldos['fecha']; ?></td>
              <td  valign="top"><?php echo $row_RsSueldos['monto']; ?></td>
              <td   valign="top"><?php echo $row_RsSueldos['porcentaje']; ?></td>
              <td   valign="top"><?php echo $row_RsSueldos['comentario']; ?></td>
          </tr>
            <?php } while ($row_RsSueldos = mysql_fetch_assoc($RsSueldos)); ?>
         
      </tbody>      
      <tfoot>
        <tr>
          <td height="30" colspan="4" align="right"></td>
        </tr>
            </tfoot>
      </table>
   
</div>

<h1> Puestos</h1>
<div id="distabla">
  
    <table width="100%" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <thead>
        
        <tr>
          <td>Fecha</td>
          <td>Puesto Anterior</td>
          <td>Puesto Nuevo</td>
          <td>Comentario</td>
        </tr>
      </thead>
      <tbody>
        <?php do { ?>
        <tr>
            <td  valign="top"><?php echo $row_RsPuestos['fecha']; ?></td>
            <td  valign="top"><?php echo $row_RsPuestos['puestoanterior']; ?></td>
            <td   valign="top"><?php echo $row_RsPuestos['puestonuevo']; ?></td>
            <td   valign="top"><?php echo $row_RsPuestos['comentario']; ?></td>
        </tr>
          <?php } while ($row_RsPuestos = mysql_fetch_assoc($RsPuestos)); ?>

            </tbody>      
      <tfoot>
        <tr>
          <td height="30" colspan="4" align="right"></td>
        </tr>
      </tfoot>
  </table>
   
</div>
</body>
</html>
<?php
mysql_free_result($RsSueldos);

mysql_free_result($RsPuestos);

mysql_free_result($RsEmpleado);
?>
