<?php require_once('Connections/tecnocomm.php'); ?>
<?php
$colname_RsEmpleado = "-1";
if (isset($_GET['id'])) {
  $colname_RsEmpleado = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsEmpleado = sprintf("SELECT * FROM empleado WHERE idempleado = %s", $colname_RsEmpleado);
$RsEmpleado = mysql_query($query_RsEmpleado, $tecnocomm) or die(mysql_error());
$row_RsEmpleado = mysql_fetch_assoc($RsEmpleado);
$totalRows_RsEmpleado = mysql_num_rows($RsEmpleado);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsHerramienta = "SELECT * FROM herramienta ORDER BY descripcion ASC";
$RsHerramienta = mysql_query($query_RsHerramienta, $tecnocomm) or die(mysql_error());
$row_RsHerramienta = mysql_fetch_assoc($RsHerramienta);
$totalRows_RsHerramienta = mysql_num_rows($RsHerramienta);

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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) 
{
	$valor = join(',',$_POST['herramienta']);
	
	$pc = split(',',$valor);
	
	$count = count($pc);
	
  foreach ($pc as $id)
  {
  $updateSQL = sprintf("INSERT INTO personalherramienta (idempleado, idherramienta, cantidad) VALUES(%s, %s, 1)",
                       GetSQLValueString($_POST['id'], "int"),
                       GetSQLValueString($id, "int"));
 
  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
}
  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Asignar Herramienta a :<?php echo $row_RsEmpleado['nombre']; ?></h1>


<div id="myform">

<form id="form1" name="form1" method="post" action="<?php echo $editFormAction; ?>">
<div>
<h3>Seleccionar Herramienta </h3>


  
    <?php do { ?>
      <label>
      <input type="checkbox" name="herramienta[]" id="herramienta[]" value="<?php echo $row_RsHerramienta['id']; ?>" />
        <?php echo $row_RsHerramienta['descripcion']; ?></label>
      <?php } while ($row_RsHerramienta = mysql_fetch_assoc($RsHerramienta)); ?>
</div>
<input type="hidden" name="id" value="<?php echo $_GET['id'];?>" />
<input type="hidden" name="MM_update" value="form1" />
<div>
  <label>
  <input type="submit" name="Submit" value="Aceptar" />
  </label>
</div>
</form>
</div>
  
  
</body>
</html>
<?php
mysql_free_result($RsEmpleado);

mysql_free_result($RsHerramienta);
?>