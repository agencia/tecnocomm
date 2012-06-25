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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE usuarios SET nombrereal=%s, direccion=%s, telefono=%s, celular=%s, email=%s, contacto=%s, username=%s, password=%s, responsabilidad=%s, activar=%s, puesto=%s, tipo_sangre=%s, imss=%s, encaso=%s  WHERE id=%s",
                       GetSQLValueString($_POST['nombre'], "text"),
                       GetSQLValueString($_POST['direccion'], "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['celular'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['contacto'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['responsabilidad'], "int"),
                       $_POST['estado'],
					   GetSQLValueString($_POST['puesto'], "text"),
					   GetSQLValueString($_POST['tiposangre'], "text"),
					   GetSQLValueString($_POST['imss'], "text"),
					   GetSQLValueString($_POST['avisar'], "text"),
                       GetSQLValueString($_POST['idusuario'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
  
  require_once('lib/eventos.php');
	$evt = new evento(21,$_SESSION['MM_Userid'],"Usuario modificado:".$_POST['username']);
	$evt->registrar();

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsNivel = "select * from nombres_accesos order by nombre";
$RsNivel = mysql_query($query_RsNivel, $tecnocomm) or die(mysql_error());
$row_RsNivel = mysql_fetch_assoc($RsNivel);
$totalRows_RsNivel = mysql_num_rows($RsNivel);

$ide_RsUsr = "-1";
if (isset($_GET['id'])) {
  $ide_RsUsr = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUsr = sprintf("select * from usuarios where id=%s", $ide_RsUsr);
$RsUsr = mysql_query($query_RsUsr, $tecnocomm) or die(mysql_error());
$row_RsUsr = mysql_fetch_assoc($RsUsr);
$totalRows_RsUsr = mysql_num_rows($RsUsr);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsEmpleado = "SELECT * FROM empleado ORDER BY nombre ASC";
$rsEmpleado = mysql_query($query_rsEmpleado, $tecnocomm) or die(mysql_error());
$row_rsEmpleado = mysql_fetch_assoc($rsEmpleado);
$totalRows_rsEmpleado = mysql_num_rows($rsEmpleado);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="js/funciones.js"></script>
</head>

<body><form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">
<table width="450" border="0" align="center" class="wrapper">
  <tr>
    <td colspan="2" align="center" class="titulos">MODIFICAR USUARIO </td>
  </tr>
  <tr>
    <td width="209">&nbsp;</td>
    <td width="231">&nbsp;</td>
  </tr>
  <tr>
    <td align="right">NOMBRE:</td>
    <td>
      <label>
      <Input type="text" name="nombre" value="<?php echo $row_RsUsr['nombre']; ?>">
        </label></td>
  </tr>
    <td align="right">NOMBRE USUARIO: </td>
    <td><input name="username" type="text" id="username" size="15" class="form" value="<?php echo $row_RsUsr['username']; ?>" />      </td>
  </tr>
  <tr>
    <td align="right">CONTRASE&Ntilde;A:</td>
    <td><input name="password" type="text" id="password" size="15" class="form" value="<?php echo $row_RsUsr['password']; ?>" /></td>
  </tr>
  <tr>
    <td align="right">NIVEL DE ACCESO: </td>
    <td><label>
      <select name="responsabilidad" id="responsabilidad" class="form">
        <?php
do {  
?>
        <option value="<?php echo $row_RsNivel['id']?>" <?php if($row_RsNivel['id']==$row_RsUsr['responsabilidad']){echo "selected='selected'";}?> ><?php echo $row_RsNivel['nombre']?></option>
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
      <option value="1" <?php if($row_RsUsr['activar']==1){echo "selected='selected'";}?>>Activado</option>
      <option value="0" <?php if($row_RsUsr['activar']==0){echo "selected='selected'";}?>>Desactivado</option>
    </select></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><a href="modificarFima.php?username=<?php echo $row_RsUsr['username'];?>" onclick="NewWindow(this.href,'Modificar Firma',400,300,'yes');return false;"><img src="firmas/<?php echo $row_RsUsr['username']; ?>.jpg" width="150" height="150"  alt="Click aqui para agregar fimra"/></a></td>
    <td align="center"><a href="modificarFoto.php?username=<?php echo $row_RsUsr['username'];?>" onclick="NewWindow(this.href,'Modificar Firma',400,300,'yes');return false;"><img src="fotos/<?php echo $row_RsUsr['username']; ?>.jpg" alt="Click aqui para agregar  Foto" width="150" height="150" /></a></td>
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
 <input type="hidden" name="idusuario" value="<?php echo $_GET['id'];?>"/>
 <input type="hidden" name="MM_update" value="form1">
</form>
</body>
</html>
<?php
mysql_free_result($RsNivel);

mysql_free_result($RsUsr);

mysql_free_result($rsEmpleado);
?>
