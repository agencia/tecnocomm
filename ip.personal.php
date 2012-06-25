<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php') ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

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
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
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

$colname_rsPersonalIp = "-1";
if (isset($_GET['idip'])) {
  $colname_rsPersonalIp = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPersonalIp = sprintf("SELECT pp.*,u.nombrereal FROM proyecto_personal pp, usuarios u WHERE pp.idip = %s AND u.id = pp.idusuario", GetSQLValueString($colname_rsPersonalIp, "int"));
$rsPersonalIp = mysql_query($query_rsPersonalIp, $tecnocomm) or die(mysql_error());
$row_rsPersonalIp = mysql_fetch_assoc($rsPersonalIp);
$totalRows_rsPersonalIp = mysql_num_rows($rsPersonalIp);

$pestados =  array("Reelevado","Activo");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Personal De Proyecto</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js" language="javascript"> </script>
<script src="js/funciones.js"></script>
<script src="js/onpopup.js"></script>
</head>

<body> 
<h1>Personal De Proeyecto</h1>
<?php include("ip.encabezado.php");?>
<div id="opciones">
<ul><li><a href="ip.personal.add.php?idip=<?php echo $_GET['idip'];?>" class="popup">Asignar Personal</a></li></ul>
</div>
<div id="distabla">
<table width="50%" cellspacing="0" cellpadding="4">
<thead>
<tr>
<td>Rol</td>
<td>Nombre</td>
<td>Fecha Asignado</td>
<td>Fecha Relevado</td>
<td>Estado</td>
<td>Opciones</td></tr>
</thead>
<tbody>
  <?php do { ?>
      <tr>
      <td><?php echo $row_rsPersonalIp['rol']; ?></td>
      <td><?php echo $row_rsPersonalIp['nombrereal']; ?></td>
      <td><?php echo formatDate($row_rsPersonalIp['fechaasignado']); ?></td>
      <td><?php echo formatDate($row_rsPersonalIp['fecharelevado']); ?></td>
      <td><?php echo $pestados[$row_rsPersonalIp['estado']]; ?></td>
      <td><a href="ip.personal.releevar.php?idproyecto_usuarios=<?php echo $row_rsPersonalIp['idproyecto_usuarios']; ?>&idip=<?php echo $_GET['idip']?>" class="popup">Relevar</a></td>
    </tr>
    <?php } while ($row_rsPersonalIp = mysql_fetch_assoc($rsPersonalIp)); ?>
</tbody>
</table>
</div>
</body>
</html>
<?php
mysql_free_result($rsPersonalIp);
?>
