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

$colname_rsNivel = "-1";
if (isset($_GET['id'])) {
  $colname_rsNivel = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsNivel = sprintf("SELECT * FROM nombres_accesos WHERE id = %s", GetSQLValueString($colname_rsNivel, "int"));
$rsNivel = mysql_query($query_rsNivel, $tecnocomm) or die(mysql_error());
$row_rsNivel = mysql_fetch_assoc($rsNivel);
$totalRows_rsNivel = mysql_num_rows($rsNivel);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLinks = "SELECT * FROM link ORDER BY nombre";
$rsLinks = mysql_query($query_rsLinks, $tecnocomm) or die(mysql_error());
$row_rsLinks = mysql_fetch_assoc($rsLinks);
$totalRows_rsLinks = mysql_num_rows($rsLinks);

$colname_rsAsignados = "-1";
if (isset($_GET['id'])) {
  $colname_rsAsignados = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAsignados = sprintf("SELECT l.id, l.nombre, a.nivel FROM link l LEFT JOIN autorizacion a ON l.id = a.idlink AND a.nivel = %s ORDER BY l.nombre ASC", GetSQLValueString($colname_rsAsignados, "int"));
$rsAsignados = mysql_query($query_rsAsignados, $tecnocomm) or die(mysql_error());
$row_rsAsignados = mysql_fetch_assoc($rsAsignados);
$totalRows_rsAsignados = mysql_num_rows($rsAsignados);

if(isset($_POST['guardar']) && $_POST['guardar'] == 'true'){
	
	//Limpiar Permisos.
	$query = sprintf('DELETE FROM autorizacion WHERE nivel = %s',GetSQLValueString($_POST['nivel'],"int"));
	mysql_select_db($database_tecnocomm,$tecnocomm);
	mysql_query($query,$tecnocomm);
									
									
	//guardar permisos nuevos
	foreach((array) $_POST['permiso'] as $id => $value){
			if($value == '1'){
				$query = sprintf('INSERT INTO autorizacion(idlink, nivel) VALUES(%s, %s)',
								  GetSQLValueString($id,"int"),
								  GetSQLValueString($_POST['nivel'],"int"));
				mysql_query($query,$tecnocomm) or die(mysql_error());
			}
	}
	
	header('Location: close.php');
	
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Permisos De Nivel</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Permisos De Nivel</h1>
<h3>Nivel: <?php echo $row_rsNivel['nombre']; ?></h3>
<div>
<form method="post" id="myform" style="width:90%;">
<div style="width:100%">
<ul>
<?php do{?>
<li style="float:left; margin:10px; padding:10px; width:300px">
<label for="<?php echo $row_rsAsignados['id']; ?>"><?php echo $row_rsAsignados['nombre']; ?></label>
<input type="checkbox" <?php if($row_rsAsignados['nivel'] == $_GET['id']) echo 'checked="checked"'; ?>  name="permiso[<?php echo $row_rsAsignados['id']?>]" value="1" id="<?php echo $row_rsAsignados['id']; ?>"/>
</li>
<?php }while($row_rsAsignados = mysql_fetch_assoc($rsAsignados));?>
</ul>
</div>
<input type="submit" value="Guardar" />
<input type="hidden" name="guardar" value="true" />
<input type="hidden" name="nivel" value="<?php echo $_GET['id']; ?>" />
</form>
</div>




</body>
</html>
<?php
mysql_free_result($rsNivel);

mysql_free_result($rsLinks);

mysql_free_result($rsAsignados);
?>
