<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
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

if ((isset($_POST["guardar"])) && ($_POST["guardar"] == "true")) {
  $insertSQL = sprintf("DELETE FROM junta_asistente WHERE idjunta = %s",
                       GetSQLValueString($_POST['idjunta'], "date"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

	$idjunta = $_POST['idjunta'];
	
	  //insertar destinatarios
  if(is_array($_POST['usuarios']))
  foreach($_POST['usuarios'] as $usuario => $on){
	  
	  $insertSQL = sprintf("INSERT INTO junta_asistente (idjunta, idusuario) VALUES (%s, %s)",
                       GetSQLValueString($idjunta, "int"),
                       GetSQLValueString($usuario, "int"));

	  mysql_select_db($database_tecnocomm, $tecnocomm);
  	$Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
  
  }

	

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuarios = "SELECT * FROM usuarios WHERE activar = 1 ORDER BY username ASC";
$rsUsuarios = mysql_query($query_rsUsuarios, $tecnocomm) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsVerificar = sprintf("SELECT * FROM junta j JOIN junta_asistente ju ON j.idjunta = ju.idjunta WHERE j.idjunta = %s",
							 GetSQLValueString($_GET['idjunta'],'int'));
$rsVerificar = mysql_query($query_rsVerificar, $tecnocomm) or die(mysql_error());
$row_rsVerificar = mysql_fetch_assoc($rsVerificar);
$totalRows_rsVerificar = mysql_num_rows($rsVerificar);

do{
	
	$usuarios[$row_rsVerificar['idusuario']] = '1';

}while($row_rsVerificar = mysql_fetch_assoc($rsVerificar));



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Asistentes</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Asistentes</h1>

<form action="<?php echo $editFormAction; ?>" name="nuevaReunion" method="POST" id="myform">
<div>
<h3>Asistentes</h3>
<fieldset>
<ul id="usersAl">
<?php
do {  
?>
<li>
<label>
<input type="checkbox" name="usuarios[<?php echo $row_rsUsuarios['id']?>]" class="cu"  <?php if($usuarios[$row_rsUsuarios['id']] == '1') echo 'checked="checked"';?>/>
<?php echo $row_rsUsuarios['username']?>
</label>
</li>
<?php } while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios));?>
</ul>
</fieldset>
</div>
<label>
<input type="hidden" name="idjunta" value="<?php echo $_GET['idjunta'];?>" />
<input type="hidden" name="guardar" value="true" />
<button type="submit" value="Aceptar">Aceptar</button>
</label>
</form>
</body>
</html>
<?php
mysql_free_result($rsUsuarios);

mysql_free_result($rsVerificar);
?>
