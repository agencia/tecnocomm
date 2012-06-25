<?php require_once('Connections/tecnocomm.php'); ?>
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

$ide_RsCoti = "-1";
if (isset($_GET['id'])) {
  $ide_RsCoti = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCoti = sprintf("select * from subcotizacion where idsubcotizacion=%s", GetSQLValueString($ide_RsCoti, "int"));
$RsCoti = mysql_query($query_RsCoti, $tecnocomm) or die(mysql_error());
$row_RsCoti = mysql_fetch_assoc($RsCoti);
$totalRows_RsCoti = mysql_num_rows($RsCoti);

$ide_RsLider = "-1";
if (isset($_GET['id'])) {
  $ide_RsLider = $_GET['id'];
}
$ide_RsLider = "-1";
if (isset($_GET['id'])) {
  $ide_RsLider = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsLider = sprintf("SELECT *,(Select nombrereal FROM usuarios WHERE id=subcotizacionlider.idusuario) as nomemp from  subcotizacionlider where idsubcotizacion=%s", $ide_RsLider);
$RsLider = mysql_query($query_RsLider, $tecnocomm) or die(mysql_error());
$row_RsLider = mysql_fetch_assoc($RsLider);
$totalRows_RsLider = mysql_num_rows($RsLider);

$ide_RsEmpleados = "-1";
if (isset($_GET['id'])) {
  $ide_RsEmpleados = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsEmpleados = sprintf("SELECT *,(Select nombre FROM empleado WHERE idempleado=subcotizacionpersonal.idempleado) as nomemp from  subcotizacionpersonal where idsubcotizacion=%s and lider=0", GetSQLValueString($ide_RsEmpleados, "int"));
$RsEmpleados = mysql_query($query_RsEmpleados, $tecnocomm) or die(mysql_error());
$row_RsEmpleados = mysql_fetch_assoc($RsEmpleados);
$totalRows_RsEmpleados = mysql_num_rows($RsEmpleados);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Banco</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
<script language="javascript"  src="js/funciones.js"></script>
</head>

<body>
<h1>Proyecto:<?php echo $row_RsCoti['nombre']; ?>(<?php echo $row_RsCoti['identificador2']; ?>)</h1>
<p></p>

<div id="myform">


<div>
<h3>Datos Generales</h3>
<label>
Fecha:<?php echo $row_RsCoti['fecha']; ?>
</label>

<label>
Forma de Pago:<?php echo $row_RsCoti['formapago']; ?>
</label>

<label>
Vigencia:<?php echo $row_RsCoti['vigencia']; ?>
</label>

<label>
Tiempo de Entrega:<?php echo $row_RsCoti['tipoentrega']; ?>
</label>
</div>
<div>
<h3>Personal Asigando</h3>
<label><a href="agregarLider.php?id=<?php echo $_GET['id']; ?>" onclick="NewWindow(this.href,'Agregar Lider',600,800,'yes'); return false;">
<img src="images/AddUser.png" width="24" height="24" border="0" align="middle" title="Asignar Lider de Proyecto" /></a>Lider de Proyecto:<?php echo $row_RsLider['nomemp'];?>
</label>

<label>
<a href="agregarEmpleado.php?id=<?php echo $_GET['id']; ?>" onclick="NewWindow(this.href,'Agregar Empleado',600,800,'yes'); return false;">
<img src="images/AddUser.png" alt="" width="24" height="24" border="0" align="middle" title="Asignar Personal" /></a>Personal Asignado: </label>

<?php do { ?><label>
   <?php if ($totalRows_RsEmpleados > 0) { // Show if recordset not empty ?>
    <a href="agregarHerramienta.php?id=<?php echo $row_RsEmpleados['idempleado']; ?>" onclick="NewWindow(this.href,'Agregar Herramienta',600,800,'yes'); return false;">
      <img src="images/Configuracion.png" width="24" height="24" border="0" align="middle" title="Asignar Herramienta" /></a>
      <?php } // Show if recordset not empty ?>
	   <?php echo $row_RsEmpleados['nomemp'];?> </label>
   		Herramienta Asignada:<br />
 		 <?php 
		$ide_RsHerramienta = "-1";
if (isset($row_RsEmpleados['idempleado'])) {
  $ide_RsHerramienta = (get_magic_quotes_gpc()) ? $row_RsEmpleados['idempleado'] : addslashes($row_RsEmpleados['idempleado']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsHerramienta = sprintf("select *,(select descripcion from herramienta where id=idherramienta)as nom  from personalherramienta where idempleado=%s", $ide_RsHerramienta);
$RsHerramienta = mysql_query($query_RsHerramienta, $tecnocomm) or die(mysql_error());
$row_RsHerramienta = mysql_fetch_assoc($RsHerramienta);
$totalRows_RsHerramienta = mysql_num_rows($RsHerramienta);
$i=0;
if ($totalRows_RsHerramienta > 0) { // Show if recordset not empty
			do{
				$i++;
				echo $i.".-".$row_RsHerramienta['nom']."<br>";	
		
			}while($row_RsHerramienta = mysql_fetch_assoc($RsHerramienta));
		 } // Show if recordset not empty 
		 else{
		 	echo "NO HAY HERRAMIENTA ASIGNADA PARA ESTA PERSONA";
			}
		 ?>
  <?php } while ($row_RsEmpleados = mysql_fetch_assoc($RsEmpleados)); ?>
</div>




</div>

</body>
</html>
<?php
mysql_free_result($RsCoti);

mysql_free_result($RsLider);

mysql_free_result($RsEmpleados);

mysql_free_result($RsHerramienta);
?>
