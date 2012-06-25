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

$ide_RsCoti = "-1";
if (isset($_GET['id'])) {
  $ide_RsCoti = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCoti = sprintf("select * from subcotizacion where idsubcotizacion=%s", GetSQLValueString($ide_RsCoti, "int"));
$RsCoti = mysql_query($query_RsCoti, $tecnocomm) or die(mysql_error());
$row_RsCoti = mysql_fetch_assoc($RsCoti);
$totalRows_RsCoti = mysql_num_rows($RsCoti);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsEmpleados = "SELECT * FROM empleado where idempleado not in(select idempleado from subcotizacionpersonal ) ORDER BY nombre ASC";
$RsEmpleados = mysql_query($query_RsEmpleados, $tecnocomm) or die(mysql_error());
$row_RsEmpleados = mysql_fetch_assoc($RsEmpleados);
$totalRows_RsEmpleados = mysql_num_rows($RsEmpleados);



$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) 
{
	$valor = join(',',$_POST['empleado']);
	
	$pc = split(',',$valor);
	
	$count = count($pc);
	
  foreach ($pc as $id)
  {
  $updateSQL = sprintf("INSERT INTO subcotizacionpersonal (idsubcotizacion, idempleado, lider) VALUES(%s, %s, 0)",
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
<h1>Proyecto:<?php echo $row_RsCoti['nombre']; ?>(<?php echo $row_RsCoti['identificador2']; ?>)</h1>


<div id="myform">

<form id="form1" name="form1" method="post" action="<?php echo $editFormAction; ?>">
<div>
<h3>Seleccionar Personal </h3>


  <?php if ($totalRows_RsEmpleados > 0) { // Show if recordset not empty ?>
    <?php do { ?>
      <label>
        <input type="checkbox" name="empleado[]" id="empleado[]" value="<?php echo $row_RsEmpleados['idempleado']; ?>" /><?php echo $row_RsEmpleados['nombre']; ?>        </label>
      <?php } while ($row_RsEmpleados = mysql_fetch_assoc($RsEmpleados)); ?>
    <?php } // Show if recordset not empty ?> 
	<?php if ($totalRows_RsEmpleados == 0) { // Show if recordset empty ?>
    <label> NO HAY PERSONAL POR AGREGAR </label>
    <?php } // Show if recordset empty ?>
  
  <div>
  <input type="submit" name="Submit" value="Aceptar" />
</div>
 
</div>
<input type="hidden" name="id" value="<?php echo $_GET['id'];?>" />
<input type="hidden" name="MM_update" value="form1" />

</form>
</div>
  
  
</body>
</html>
<?php
mysql_free_result($RsCoti);

mysql_free_result($RsEmpleados);
?>
