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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "modificarBanco")) {
  $updateSQL = sprintf("UPDATE bancos SET institucion=%s, tipodecuenta=%s, sucursal=%s, numerodecuenta=%s, clabe=%s, fechaapertura=%s, plastico=%s, token=%s, chequera=%s, internet=%s, funcionario=%s, domiciliosucursal=%s, telefonosucursal1=%s, telefonosucursal2=%s, telefonocelular=%s, correofuncionario1=%s, correofuncionario2=%s, telefono800=%s WHERE idbanco=%s",
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
                       GetSQLValueString($_POST['idbanco'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsBanco = "-1";
if (isset($_GET['idbanco'])) {
  $colname_rsBanco = $_GET['idbanco'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsBanco = sprintf("SELECT * FROM bancos WHERE idbanco = %s", GetSQLValueString($colname_rsBanco, "int"));
$rsBanco = mysql_query($query_rsBanco, $tecnocomm) or die(mysql_error());
$row_rsBanco = mysql_fetch_assoc($rsBanco);
$totalRows_rsBanco = mysql_num_rows($rsBanco);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Modificar Banco</title>
<link href="style2.css" rel="stylesheet" type="text/css" />

<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Modificar Banco</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="modificarBanco" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Institucion:
<input name="institucion" type="text" value="<?php echo $row_rsBanco['institucion']; ?>" class="requerido"/>
</label>

<label>Tipo de Cuenta:
<input name="tipodecuenta" type="text" value="<?php echo $row_rsBanco['tipodecuenta']; ?>" class="requerido"/>
</label>

<label>Sucursal:
<input name="sucursal" type="text" value="<?php echo $row_rsBanco['sucursal']; ?>" />
</label>

<label>Numero de Cuenta:
<input name="numerocuenta" type="text" value="<?php echo $row_rsBanco['numerodecuenta']; ?>" />
</label>

<label>Clabe:
<input name="clabe" type="text" value="<?php echo $row_rsBanco['clabe']; ?>" />
</label>

<label>Fecha Apertura:
<input name="fechaapertura" type="text" class="fecha" value="<?php echo $row_rsBanco['fechaapertura']; ?>" />
</label>

<label>Plastico:
<input name="plastico" type="text" value="<?php echo $row_rsBanco['plastico']; ?>" />
</label>

<label>Token:
<input name="token" type="text" value="<?php echo $row_rsBanco['token']; ?>" />
</label>

<label>Chequera:
<input name="chequera" type="text" value="<?php echo $row_rsBanco['chequera']; ?>" />
</label>


<label>Operaciones por Internet:
<input name="internet" type="text" value="<?php echo $row_rsBanco['internet']; ?>" />
</label>
</div>
<div>
<h3>Datos de Contacto</h3>

<label>Funcionario:
<input name="funcionario" type="text" value="<?php echo $row_rsBanco['funcionario']; ?>" />
</label>



<label>Domicilio Sucursal:
<input name="domiciliosucursal" type="text" value="<?php echo $row_rsBanco['domiciliosucursal']; ?>" />
</label>



<label>Telefono 1:
<input name="telefonosucursal1" type="text" value="<?php echo $row_rsBanco['telefonosucursal1']; ?>" />
</label>



<label>Telefono 2:
<input name="telefonosucursal2" type="text" value="<?php echo $row_rsBanco['telefonosucursal2']; ?>" />
</label>


<label>Celular:
<input name="telefonocelular" type="text" value="<?php echo $row_rsBanco['telefonocelular']; ?>" />
</label>

<label>Correo:
<input name="correofuncionario1" type="text" value="<?php echo $row_rsBanco['correofuncionario1']; ?>" />
</label>

<label>Correo Alternativo:
<input name="correofuncionario2" type="text" value="<?php echo $row_rsBanco['correofuncionario2']; ?>" />
</label>
<label>Numero 01 800:
<input name="telefono800" type="text" value="<?php echo $row_rsBanco['telefono800']; ?>" />
</label>
</div>

<div>
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="idbanco" value="<?php echo $row_rsBanco['idbanco']; ?>" />
<input type="hidden" name="MM_update" value="modificarBanco" />
</form>

</div>

</body>
</html>
<?php
mysql_free_result($rsBanco);
?>
