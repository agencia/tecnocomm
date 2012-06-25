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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "frmAutorizar")) {
	
	//verificar codigo de seguridad
mysql_select_db($database_tecnocomm,$tecnocomm);
$sql = sprintf("SELECT *  FROM usuarios WHERE id=%s AND password=%s AND activar=1",
			   		GetSQLValueString($_POST['idusuario'],"int"),
					GetSQLValueString($_POST['pass'],"text"));

$rsValid = mysql_query($sql,$tecnocomm) or die(mysql_error());

if(mysql_num_rows($rsValid) == 1){
	
  $updateSQL = sprintf("UPDATE proyecto_material_movimiento SET autorizo=%s WHERE idproyecto_material_movimiento=%s",
                       GetSQLValueString($_POST['idusuario'], "int"),
                       GetSQLValueString($_POST['idproyecto_material_movimiento'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
  
}//fin de if is valid
else{
	$msj = "Contrase&Ntilde;a No coincide con la del empleado";
}
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsEmpleados = "SELECT * FROM usuarios ORDER BY username ASC";
$rsEmpleados = mysql_query($query_rsEmpleados, $tecnocomm) or die(mysql_error());
$row_rsEmpleados = mysql_fetch_assoc($rsEmpleados);
$totalRows_rsEmpleados = mysql_num_rows($rsEmpleados);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Autorizar</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Autorizar</h1>
<?php include('ip.encabezado.php');?>
<?php if(isset($msj)){?>
<p><?php echo $msj;?></p>
<?php } ?>
<form action="<?php echo $editFormAction; ?>" name="frmAutorizar" method="POST" id="myform">
<div>
<h3>Empleado</h3>
<select name="idusuario">
  <?php
do {  
?>
  <option value="<?php echo $row_rsEmpleados['id']?>"<?php if (!(strcmp($row_rsEmpleados['id'], $_GET['idusuario']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsEmpleados['username']?></option>
  <?php
} while ($row_rsEmpleados = mysql_fetch_assoc($rsEmpleados));
  $rows = mysql_num_rows($rsEmpleados);
  if($rows > 0) {
      mysql_data_seek($rsEmpleados, 0);
	  $row_rsEmpleados = mysql_fetch_assoc($rsEmpleados);
  }
?>
</select>
<label>Contrase&ntilde;a</label>
<input type="password" name="pass" value="" />
</div>
<input type="hidden" name="idproyecto_material_movimiento"  value="<?php echo $_GET['idproyecto_material_movimiento']?>"/>
<input type="hidden" name="MM_update" value="frmAutorizar" />
<div class="botones">
<button class="button"><span>Aceptar</span></button>
</div>
</form>
</body>
</html>
<?php
mysql_free_result($rsEmpleados);
?>
