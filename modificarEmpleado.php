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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "nuevoEmpleado")) {
  $updateSQL = sprintf("UPDATE empleado SET clave=%s, nombre=%s, fechanacimiento=%s, lugarnacimiento=%s, estadocivil=%s, nombrepareja=%s, sexo=%s, domicilio=%s, telefono=%s, celular=%s, correo=%s, refper1=%s, refper2=%s, refcom1=%s, refcom2=%s, ultimosueldo=%s, motivodespido=%s, comentarios=%s, formacontrato=%s, fechatermino=%s, idpuesto=%s, tiposangre=%s, imss=%s WHERE idempleado=%s",
                       GetSQLValueString($_POST['clave'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['fechanac'], "date"),
                       GetSQLValueString($_POST['lugarnac'], "text"),
                       GetSQLValueString($_POST['edocivil'], "int"),
                       GetSQLValueString($_POST['pareja'], "text"),
                       GetSQLValueString($_POST['sexo'], "int"),
                       GetSQLValueString($_POST['domicilio'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['celular'], "text"),
                       GetSQLValueString($_POST['correo'], "text"),
                       GetSQLValueString($_POST['refper1'], "text"),
                       GetSQLValueString($_POST['refper2'], "text"),
                       GetSQLValueString($_POST['refcom1'], "text"),
                       GetSQLValueString($_POST['refcom2'], "text"),
                       GetSQLValueString($_POST['ultsueldo'], "double"),
                       GetSQLValueString($_POST['motivo'], "text"),
                       GetSQLValueString($_POST['comentarios'], "text"),
                       GetSQLValueString($_POST['formacontrato'], "int"),
					   GetSQLValueString($_POST['idpuesto'], "int"),
					   GetSQLValueString($_POST['tiposangre'], "text"),
					   GetSQLValueString($_POST['imss'], "text"),
                       GetSQLValueString($_POST['fechaout'], "date"),
					   
                       GetSQLValueString($_POST['idempleado'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_RsEmpleado = "-1";
if (isset($_GET['idempleado'])) {
  $colname_RsEmpleado = $_GET['idempleado'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsEmpleado = sprintf("SELECT * FROM empleado WHERE idempleado = %s", GetSQLValueString($colname_RsEmpleado, "int"));
$RsEmpleado = mysql_query($query_RsEmpleado, $tecnocomm) or die(mysql_error());
$row_RsEmpleado = mysql_fetch_assoc($RsEmpleado);
$totalRows_RsEmpleado = mysql_num_rows($RsEmpleado);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPuesto = "SELECT * FROM puesto ORDER BY nombre ASC";
$rsPuesto = mysql_query($query_rsPuesto, $tecnocomm) or die(mysql_error());
$row_rsPuesto = mysql_fetch_assoc($rsPuesto);
$totalRows_rsPuesto = mysql_num_rows($rsPuesto);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Editar Empleado</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
<script language="javascript" src="js/funciones.js"></script>
</head>

<body>
<h1>Editar Empleado</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoEmpleado" method="POST">
<div>
<h3>Datos Personales</h3>
<label>Clave:
<input name="clave" type="text" value="<?php echo $row_RsEmpleado['clave']; ?>"  />
</label>
<label>Nombre:
<input name="nombre" type="text" class="requerido" value="<?php echo $row_RsEmpleado['nombre']; ?>" />
</label>
<label>Fecha de Nacimiento:
<input name="fechanac" type="text" class="requerido" value="<?php echo $row_RsEmpleado['fechanacimiento']; ?>" />
</label>
<label>Lugar de Nacimiento:
<input name="lugarnac" type="text" value="<?php echo $row_RsEmpleado['lugarnacimiento']; ?>"  />
</label>
<label>Estado Civil:
<select name="edocivil">
  <option value="0" <?php if (!(strcmp(0, $row_RsEmpleado['estadocivil']))) {echo "selected=\"selected\"";} ?>>Soltero(a)</option>
  <option value="1" <?php if (!(strcmp(1, $row_RsEmpleado['estadocivil']))) {echo "selected=\"selected\"";} ?>>Casado(a)</option>
  <option value="2" <?php if (!(strcmp(2, $row_RsEmpleado['estadocivil']))) {echo "selected=\"selected\"";} ?>>Viudo(a)</option>
  <option value="3" <?php if (!(strcmp(3, $row_RsEmpleado['estadocivil']))) {echo "selected=\"selected\"";} ?>>Union libre</option>
  <option value="4" <?php if (!(strcmp(4, $row_RsEmpleado['estadocivil']))) {echo "selected=\"selected\"";} ?>>Separado(a)</option>
</select>
</label>
<label>Nombre de Pareja:
<input name="pareja" type="text" value="<?php echo $row_RsEmpleado['nombrepareja']; ?>" />
</label>
<label>Sexo:
<select name="sexo">
  <option value="0" <?php if (!(strcmp(0, $row_RsEmpleado['sexo']))) {echo "selected=\"selected\"";} ?>>Masculino</option>
  <option value="1" <?php if (!(strcmp(1, $row_RsEmpleado['sexo']))) {echo "selected=\"selected\"";} ?>>Femenino</option>
</select>
</label>
<label>Domicilio:
<input name="domicilio" type="text" class="requerido" value="<?php echo $row_RsEmpleado['domicilio']; ?>" />
</label>
<label>Telefono:
<input name="telefono" type="text" value="<?php echo $row_RsEmpleado['telefono']; ?>"  />
</label>
<label>Celular:
<input name="celular" type="text" value="<?php echo $row_RsEmpleado['celular']; ?>" />
</label>
<label>Correo:
<input name="correo" type="text" value="<?php echo $row_RsEmpleado['correo']; ?>"/>
</label>
<label>Tipo de Sangre:
<input type="text" name="tiposangre" value="<?php echo $row_RsEmpleado['tiposangre']; ?>"/>
</label>
<label>IMSS:
<input type="text" name="imss" value="<?php echo $row_RsEmpleado['imss']; ?>"/>
</label>

</div>
<div>
<h3>Referencias </h3>
<label>Forma de Contrato:
<select name="formacontrato">
  <option value="0" <?php if (!(strcmp(0, $row_RsEmpleado['formacontrato']))) {echo "selected=\"selected\"";} ?>>Eventual</option>
  <option value="1" <?php if (!(strcmp(1, $row_RsEmpleado['formacontrato']))) {echo "selected=\"selected\"";} ?>>Por Tiempo Determinado</option>
  <option value="2" <?php if (!(strcmp(2, $row_RsEmpleado['formacontrato']))) {echo "selected=\"selected\"";} ?>>Por Tiempo Indeterminado</option>
  <option value="3" <?php if (!(strcmp(3, $row_RsEmpleado['formacontrato']))) {echo "selected=\"selected\"";} ?>>Por Honorarios</option>
  <option value="4" <?php if (!(strcmp(4, $row_RsEmpleado['formacontrato']))) {echo "selected=\"selected\"";} ?>>Por Proyecto</option>
</select>
</label>
<label>Fecha Contratado:
<input name="fechaout" type="text" value="<?php echo $row_RsEmpleado['fechaingreso']; ?>" />
</label>
<label>Fecha Termino:
<input name="fechaout" type="text" value="<?php echo $row_RsEmpleado['fechatermino']; ?>" />
</label>

<label>Puesto:

<select name="puesto" id="puesto">
  <?php
do {  
?>
  <option value="<?php echo $row_rsPuesto['idpuesto']?>"<?php if (!(strcmp($row_rsPuesto['idpuesto'], $row_RsEmpleado['idpuesto']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsPuesto['nombre']?></option>
  <?php
} while ($row_rsPuesto = mysql_fetch_assoc($rsPuesto));
  $rows = mysql_num_rows($rsPuesto);
  if($rows > 0) {
      mysql_data_seek($rsPuesto, 0);
	  $row_rsPuesto = mysql_fetch_assoc($rsPuesto);
  }
?>
</select>
</label>

<label>Referencia Personal 1:
<input name="refper1" type="text" value="<?php echo $row_RsEmpleado['refper1']; ?>" />
</label>
<label>Referencia Personal 2:
<input name="refper2" type="text" value="<?php echo $row_RsEmpleado['refper2']; ?>" />
</label>
<label>Referencia Comercial 1:
<input name="refcom1" type="text" value="<?php echo $row_RsEmpleado['refcom1']; ?>" />
</label>
<label>Referencia Comercial 2:
<input name="refcom2" type="text" value="<?php echo $row_RsEmpleado['refcom2']; ?>" />
</label>
<label>Ultimo Sueldo:
<input name="ultsueldo" type="text" value="<?php echo $row_RsEmpleado['ultimosueldo']; ?>" />
</label>
<label>Motivo de Despido:
<input name="motivo" type="text" value="<?php echo $row_RsEmpleado['motivodespido']; ?>" />
</label>
<label>Comentarios:
<textarea name="comentarios" cols="" rows=""><?php echo $row_RsEmpleado['comentarios']; ?></textarea>
</label>
</div>

<div class="botones">
<button type="submit" class="button"><span>Aceptar</span></button>
  </div>  
<input type="hidden" name="idempleado" value="<?php echo $_GET['idempleado'];?>"/>
<input type="hidden" name="MM_update" value="nuevoEmpleado" />

</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsEmpleado);

mysql_free_result($rsPuesto);
?>