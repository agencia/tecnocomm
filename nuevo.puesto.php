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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevopuesto")) {
  $insertSQL = sprintf("INSERT INTO puesto (clave, nombre, descripcion, funciones, sueldo) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['clave'], "text"),
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['funciones'], "text"),
                       GetSQLValueString($_POST['sueldo'], "double"));

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
$query_RsSig = "SELECT max(idpuesto) as ultimo FROM puesto";
$RsSig = mysql_query($query_RsSig, $tecnocomm) or die(mysql_error());
$row_RsSig = mysql_fetch_assoc($RsSig);
$totalRows_RsSig = mysql_num_rows($RsSig);
$num=$row_RsSig['ultimo']+1;

$lon=strlen($num);

if($lon==1){$cad="000".$num;}
if($lon==2){$cad="00".$num;}
if($lon==3){$cad="0".$num;}
if($lon==4){$cad=$num;}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Nuevo Puesto</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Nuevo Puesto</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevopuesto" method="POST">
<div>
<h3>Datos del Puesto</h3>
<label>Clave:
<input type="text" name="clave" value="PU<?php echo $cad;?>"  readonly="true" />
</label>
<label>Nombre:
<input type="text" name="nombre" class="requerido" />
</label>

<label>Descripcion
<textarea name="descripcion" cols="" rows=""></textarea>
</label>

<label>Funciones:
<textarea name="funciones" cols="" rows=""></textarea>
</label>

<label>Sueldo:
<input type="text" name="sueldo" />
</label>

</div>


<div>
<input type="submit" value="Aceptar" />
</div>

<input type="hidden" name="MM_insert" value="nuevopuesto" />
</form>

</div>
</body>
</html>
