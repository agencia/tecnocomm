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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "frmpersonal")) {
  $insertSQL = sprintf("INSERT INTO proyecto_personal (idusuario, idip, rol,fechaasignado, estado) VALUES (%s, %s, %s, NOW(), 1)",
                       GetSQLValueString($_POST['idusuario'], "int"),
                       GetSQLValueString($_POST['idip'], "int"),
                       GetSQLValueString($_POST['rol'], "int"));

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
$query_rsPersonal = "SELECT * FROM usuarios ORDER BY nombrereal ASC";
$rsPersonal = mysql_query($query_rsPersonal, $tecnocomm) or die(mysql_error());
$row_rsPersonal = mysql_fetch_assoc($rsPersonal);
$totalRows_rsPersonal = mysql_num_rows($rsPersonal);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Asignar Personal</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Asignar Personal</h1>
<div id="myform">
<form action="<?php echo $editFormAction; ?>" name="frmpersonal" method="POST">
<div>
<label>
Usuario: <select name="idusuario">
  <?php
do {  
?>
  <option value="<?php echo $row_rsPersonal['id']?>"><?php echo $row_rsPersonal['nombrereal']?></option>
  <?php
} while ($row_rsPersonal = mysql_fetch_assoc($rsPersonal));
  $rows = mysql_num_rows($rsPersonal);
  if($rows > 0) {
      mysql_data_seek($rsPersonal, 0);
	  $row_rsPersonal = mysql_fetch_assoc($rsPersonal);
  }
?>
</select>
</label>
<label>
Rol:
<select name="rol">
<option value="0">Levantamiento</option>
<option value="1">O.Sevicio</option>
<option value="2">Cotizacion</option>
<option value="3">Lider De Proyecto</option>
</select>
</label>
<label>
<button type="submit">Aceptar</button>
</label>
</div>
<input type="hidden" name="idip" value="<?php echo $_GET['idip'];?>" />
<input type="hidden" name="MM_insert" value="frmpersonal" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($rsPersonal);
?>
