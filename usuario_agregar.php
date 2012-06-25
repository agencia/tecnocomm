<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('Connections/tecnocomm.php'); ?>
<?php
session_start();
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

if (is_uploaded_file($_FILES['firma']['tmp_name'])) {
		if($_FILES['firma']['size'] <= 500000) {
			   if($_FILES['firma']['type']=="image/jpeg" ){
					if(!move_uploaded_file  ($_FILES['firma']['tmp_name'] , "firmas/".$_POST['username'].".jpg")){
						$error = "no se ha podido mover";
					}
				
			   }else{$error = "fomato no valido";}
		}	else{$error = "excede el tamaño";}
	}

if (is_uploaded_file($_FILES['foto']['tmp_name'])) {
		if($_FILES['foto']['size'] <= 500000) {
			   if($_FILES['foto']['type']=="image/jpeg" ){
					if(!move_uploaded_file  ($_FILES['foto']['tmp_name'] , "fotos/".$_POST['username'].".jpg")){
						$error = "no se ha podido mover";
					}
				
			   }else{$error = "fomato no valido";}
		}	else{$error = "excede el tamaño";}
	}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1") && !isset($error)) {
  $insertSQL = sprintf("INSERT INTO usuarios (nombrereal, username, password, responsabilidad, activar ) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['responsabilidad'], "int"),
                       GetSQLValueString(isset($_POST['estado']) ? "true" : "", "defined","1","0"));
					   

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

	require_once('lib/eventos.php');
	$evt = new evento(19,$_SESSION['MM_Userid'],"Usuario registrado:".$_POST['username']);
	$evt->registrar();

$insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
   $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
 header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsNivel = "select * from nombres_accesos order by nombre";
$RsNivel = mysql_query($query_RsNivel, $tecnocomm) or die(mysql_error());
$row_RsNivel = mysql_fetch_assoc($RsNivel);
$totalRows_RsNivel = mysql_num_rows($RsNivel);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsNombre = "SELECT * FROM empleado ORDER BY nombre ASC";
$rsNombre = mysql_query($query_rsNombre, $tecnocomm) or die(mysql_error());
$row_rsNombre = mysql_fetch_assoc($rsNombre);
$totalRows_rsNombre = mysql_num_rows($rsNombre);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nuevo Usuario</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php echo $error;?>
<form id="form1" name="form1" method="post" action="<?php echo $editFormAction; ?>" enctype="multipart/form-data">
<table width="450" border="0" align="center" class="wrapper">
  <tr>
    <td colspan="2" align="center" class="titulos">AGREGAR USUARIO </td>
  </tr>
  <tr>
    <td width="209">&nbsp;</td>
    <td width="231">&nbsp;</td>
  </tr>
  <tr>
    <td align="right">NOMBRE:</td>
    <td>
      <Input type="text" name="nombre"></td>
  </tr>
  
  <tr>
    <td align="right">NOMBRE USUARIO: </td>
    <td><input name="username" type="text" id="username" size="15" class="form" /></td>
  </tr>
  <tr>
    <td align="right">CONTRASE&Ntilde;A:</td>
    <td><input name="password" type="text" id="password" size="15" class="form" /></td>
  </tr>
  <tr>
    <td align="right">NIVEL DE ACCESO: </td>
    <td><label>
      <select name="responsabilidad" id="responsabilidad" class="form">
        <?php
do {  
?>
        <option value="<?php echo $row_RsNivel['id']?>"><?php echo $row_RsNivel['nombre']?></option>
        <?php
} while ($row_RsNivel = mysql_fetch_assoc($RsNivel));
  $rows = mysql_num_rows($RsNivel);
  if($rows > 0) {
      mysql_data_seek($RsNivel, 0);
	  $row_RsNivel = mysql_fetch_assoc($RsNivel);
  }
?>
      </select>
    </label></td>
  </tr>
  <tr>
    <td align="right">ESTADO:</td>
    <td><select name="estado" id="estado" class="form">
      <option value="1">Activado</option>
      <option value="0">Desactivado</option>
    </select></td>
  </tr>
  <tr>
    <td align="right">FIRMA:</td>
    <td><input name="firma" type="file" id="firma" size="35" class="form" /></td>
  </tr>
  <tr>
    <td align="right">FOTOGRAFIA:</td>
    <td><input name="foto" type="file" id="foto" size="35" class="form" />
	<input type="hidden" name="MAX_FILE_SIZE" value="1000000"></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td><label>
      <input type="submit" name="Submit" value="Aceptar" />
    </label></td>
  </tr>
</table>
 <input type="hidden" name="MM_insert" value="form1">
</form>
</body>
</html>
<?php
mysql_free_result($RsNivel);

mysql_free_result($rsNombre);
?>