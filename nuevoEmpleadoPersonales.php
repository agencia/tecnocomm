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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevoEmpleado")) {
  $insertSQL = sprintf("INSERT INTO empleado (clave, nombre, fechanacimiento, lugarnacimiento, estadocivil, nombrepareja, sexo, domicilio, telefono, celular, correo, refper1, refper2, refcom1, refcom2, ultimosueldo, motivodespido, comentarios, formacontrato, fechaingreso, fechatermino, suledo, idpuesto, tiposangre, imss) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
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
                       GetSQLValueString($_POST['fechain'], "date"),
                       GetSQLValueString($_POST['fechaout'], "date"),
                       GetSQLValueString($_POST['ultsueldo'], "double"),
                       GetSQLValueString($_POST['puesto'], "int"),
					   GetSQLValueString($_POST['tiposangre'], "text"),
					   GetSQLValueString($_POST['imss'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  $idempleado=mysql_insert_id();
  


  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSigEmp = "SELECT max(idempleado) as ultimo FROM empleado";
$RsSigEmp = mysql_query($query_RsSigEmp, $tecnocomm) or die(mysql_error());
$row_RsSigEmp = mysql_fetch_assoc($RsSigEmp);
$totalRows_RsSigEmp = mysql_num_rows($RsSigEmp);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPuesto = "SELECT * FROM puesto ORDER BY nombre ASC";
$rsPuesto = mysql_query($query_rsPuesto, $tecnocomm) or die(mysql_error());
$row_rsPuesto = mysql_fetch_assoc($rsPuesto);
$totalRows_rsPuesto = mysql_num_rows($rsPuesto);
$num=$row_RsSigEmp['ultimo']+1;

$lon=strlen($num);

if($lon==1){$cad="00".$num;}
if($lon==2){$cad="0".$num;}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Empleado</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
<script src="js/calendario.js"></script>
</head>

<body>
<h1>Nuevo Empleado</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoEmpleado" method="POST">
<div>
<h3>Datos Personales</h3>
<label>Clave:
<input type="text" name="clave" value="EM0<?php echo $cad;?>"  readonly="true" />
</label>
<label>Nombre:
<input type="text" name="nombre" class="requerido" />
</label>
<label>Fecha de Nacimiento:
<input type="text" name="fechanac" class="requerido" />
</label>
<label>Lugar de Nacimiento:
<input type="text" name="lugarnac"  />
</label>
<label>Estado Civil:
<select name="edocivil">
  <option value="0">Soltero(a)</option>
  <option value="1">Casado(a)</option>
  <option value="2">Viudo(a)</option>
  <option value="3">Union libre</option>
  <option value="4">Separado(a)</option>
</select>
</label>
<label>Nombre de Pareja:
<input type="text" name="pareja" />
</label>
<label>Sexo:
<select name="sexo">
  <option value="0">Masculino</option>
  <option value="1">Femenino</option>
</select>
</label>
<label>Domicilio:
<input type="text" name="domicilio" class="requerido" />
</label>
<label>Telefono:
<input type="text" name="telefono"  />
</label>
<label>Celular:
<input type="text" name="celular" />
</label>
<label>Correo:
<input type="text" name="correo"/>
</label>
<label>Tipo de Sangre:
<input type="text" name="tiposangre"/>
</label>
<label>IMSS:
<input type="text" name="imss"/>
</label>



</div>
<div>
<h3>Referencias </h3>
<label>Forma de Contrato:
<select name="formacontrato">
  <option value="0">Eventual</option>
  <option value="1">Por Tiempo Determinado</option>
  <option value="2">Por Tiempo Indeterminado</option>
  <option value="3">Por Honorarios</option>
  <option value="4">Por Proyecto</option>
</select>
</label>
<label>Fecha de Contratacion:
<input type="text" name="fechain" class="fecha" />
</label>
<label>Fecha Termino:
<input type="text" name="fechaout" class="fecha" />
</label>

<label>Puesto:
<select name="puesto">
  <?php
do {  
?>
  <option value="<?php echo $row_rsPuesto['idpuesto']?>"><?php echo $row_rsPuesto['nombre']?></option>
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
<input type="text" name="refper1" />
</label>
<label>Referencia Personal 2:
<input type="text" name="refper2" />
</label>
<label>Referencia Comercial 1:
<input type="text" name="refcom1" />
</label>
<label>Referencia Comercial 2:
<input type="text" name="refcom2" />
</label>
<label>Ultimo Sueldo:
<input type="text" name="ultsueldo" />
</label>
<label>Motivo de Despido:
<input type="text" name="motivo" />
</label>
<label>Comentarios:
<textarea name="comentarios" cols="" rows=""></textarea>
</label>
</div>

<div class="botones">
<button type="submit" class="button"><span>Aceptar</span></button>
  </div>  

<input type="hidden" name="MM_insert" value="nuevoEmpleado" />
</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsSigEmp);

mysql_free_result($rsPuesto);
?>