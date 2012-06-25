<?php require_once('Connections/tecnocomm.php'); ?>
<?php
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "modSubcontratista")) {
  $insertSQL = sprintf("INSERT INTO subcontratistas (nombre, abreviacion, calle, colonia, ciudad, estado, tel1, tel2, cel1, cel2, correo1, correo2, fecha_inicio, clave) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['Nombre'], "text"),
                       GetSQLValueString($_POST['abreviacion'], "text"),
                       GetSQLValueString($_POST['calle'], "text"),
                       GetSQLValueString($_POST['colonia'], "text"),
                       GetSQLValueString($_POST['ciudad'], "text"),
                       GetSQLValueString($_POST['estado'], "text"),
                       GetSQLValueString($_POST['tel1'], "text"),
                       GetSQLValueString($_POST['tel2'], "text"),
                       GetSQLValueString($_POST['cel1'], "text"),
                       GetSQLValueString($_POST['cel2'], "text"),
                       GetSQLValueString($_POST['mail1'], "text"),
                       GetSQLValueString($_POST['mail2'], "text"),
                       GetSQLValueString($_POST['fechainicio'], "date"),
					   GetSQLValueString($_POST['clave'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSig = "SELECT max(id) as ultimo FROM subcontratistas";
$RsSig = mysql_query($query_RsSig, $tecnocomm) or die(mysql_error());
$row_RsSig = mysql_fetch_assoc($RsSig);
$totalRows_RsSig = mysql_num_rows($RsSig);
$num=$row_RsSig['ultimo']+1;

$lon=strlen($num);

if($lon==1){$cad="000".$num;}
if($lon==2){$cad="00".$num;}
if($lon==3){$cad="0".$num;}
if($lon==4){$cad=$num;}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Modificar Subcontratista</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>


<body>
<h1>Agregar SubContratista</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="modSubcontratista" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Clave:
  <input name="clave" type="text"  value="SU<?php echo $cad;?>"  readonly="true" />
</label>
<label>Nombre:
  <input name="Nombre" type="text"  />
</label>

<label>Abreviacion:
  <input name="abreviacion" type="text"  />
</label>

<label>Calle y Numero:
  <input name="calle" type="text"  />
</label>

<label>Colonia:
  <input name="colonia" type="text"  />
</label>

<label>Ciudad:
  <input name="ciudad" type="text"  />
</label>

<label>Estado:
  <input name="estado" type="text"  />
</label>

<label>Telefono 1:
  <input name="tel1" type="text"  />
</label>

<label>Telefono 2:
  <input name="tel2" type="text"  />
</label>

<label>Celular 1:
  <input name="cel1" type="text"  />
</label>


<label>Celular 2:
  <input name="cel2" type="text"  />
</label>

<label>Correo 1:
  <input name="mail1" type="text"  />
</label>

<label>Correo 2:
  <input name="mail2" type="text"  />
</label>

<label>Fecha Compra:
  <input name="fechainicio" type="text" class="fecha"  />
</label>
<input type="submit" value="Aceptar" />
</div>





<input type="hidden" name="MM_insert" value="modSubcontratista">
</form>

</div>

</body>
</html>