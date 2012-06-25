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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevoBanco")) {
  $insertSQL = sprintf("INSERT INTO bancos (institucion, tipodecuenta, sucursal, numerodecuenta, clabe, fechaapertura, plastico, token, chequera, internet, funcionario, domiciliosucursal, telefonosucursal1, telefonosucursal2, telefonocelular, correofuncionario1, correofuncionario2, telefono800, clave) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['institucion'], "text"),
                       GetSQLValueString($_POST['tipodecuenta'], "text"),
                       GetSQLValueString($_POST['sucursal'], "text"),
                       GetSQLValueString($_POST['numerocuenta'], "text"),
                       GetSQLValueString($_POST['clabe'], "text"),
                       GetSQLValueString($_POST['fechaapertura'], "date"),
                       GetSQLValueString($_POST['plastico'], "int"),
                       GetSQLValueString($_POST['token'], "int"),
                       GetSQLValueString($_POST['chequera'], "int"),
                       GetSQLValueString($_POST['internet'], "int"),
                       GetSQLValueString($_POST['funcionario'], "text"),
                       GetSQLValueString($_POST['domiciliosucursal'], "text"),
                       GetSQLValueString($_POST['telefonosucursal1'], "text"),
                       GetSQLValueString($_POST['telefonosucursal2'], "text"),
                       GetSQLValueString($_POST['telefonocelular'], "text"),
                       GetSQLValueString($_POST['correofuncionario1'], "text"),
                       GetSQLValueString($_POST['correofuncionario2'], "text"),
                       GetSQLValueString($_POST['telefono800'], "text"),
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
$query_RsSig = "SELECT max(idbanco) as ultimo FROM bancos";
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
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Banco</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Nuevo Banco</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoBanco" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Clave:
<input type="text" name="clave" value="BA<?php echo $cad;?>"  readonly="true" />
</label>
<label>Institucion:
<input type="text" name="institucion" class="requerido" />
</label>

<label>Tipo de Cuenta:
<input type="text" name="tipodecuenta" class="requerido" />
</label>

<label>Sucursal:
<input type="text" name="sucursal" />
</label>

<label>Numero de Cuenta:
<input type="text" name="numerocuenta" />
</label>

<label>Clabe:
<input type="text" name="clabe" />
</label>

<label>Fecha Apertura:
<input type="text" name="fechaapertura" class="fecha" />
</label>

<label>Plastico:
<input type="text" name="plastico" />
</label>

<label>Token:
<input type="text" name="token" />
</label>

<label>Chequera:
<input type="text" name="chequera" />
</label>


<label>Operaciones por Internet:
<input type="text" name="internet" />
</label>
</div>
<div>
<h3>Datos de Contacto</h3>

<label>Funcionario:
<input type="text" name="funcionario" />
</label>



<label>Domicilio Sucursal:
<input type="text" name="domiciliosucursal" />
</label>



<label>Telefono 1:
<input type="text" name="telefonosucursal1" />
</label>



<label>Telefono 2:
<input type="text" name="telefonosucursal2" />
</label>


<label>Celular:
<input type="text" name="telefonocelular" />
</label>

<label>Correo:
<input type="text" name="correofuncionario1" />
</label>

<label>Correo Alternativo:
<input type="text" name="correofuncionario2" />
</label>
<label>Numero 01 800:
<input type="text" name="telefono800" />
</label>
</div>

<div>
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="MM_insert" value="nuevoBanco" />
</form>

</div>

</body>
</html>