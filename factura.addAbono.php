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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "regAbono")) {
  $insertSQL = sprintf("INSERT INTO factura_abono (idfactura, monto, fecha, referenciapago) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['idfactura'], "int"),
                       GetSQLValueString($_POST['monto'], "double"),
                       GetSQLValueString($_POST['fechapago'], "date"),
                       GetSQLValueString($_POST['referenciapago'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  //var_dump($insertSQL);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Registrar Abono</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/jqueryui.js"></script>
<script>
	$(function() {
		$( "#fechapago" ).datepicker({
                    altField: "#fechapagoO",
                    altFormat: "yy-mm-dd",
                    dateFormat: "dd/mm/yy"
                });
	});
	</script>
</head>
<body>
<form action="<?php echo $editFormAction; ?>" name="regAbono" method="POST" id="myform">
<div>
<h3>Registrar Abono</h3>
<label>
Monto:
<input type="text" name="monto" id="monto" />
</label>
<label>
Referencia:
<input type="text" name="referenciapago" id="referenciapago" />
</label>
<label>
Fecha De Pago:
<input type="text" class="fecha" id="fechapago" value="<?php echo date("d/m/Y"); ?>" />
<input type="hidden" name="fechapago" class="fecha" id="fechapagoO" value="<?php echo date("m-d-Y"); ?>" />
</label>
</div>
<div class="botones">
<button type="submit">Aceptar</button>
</div>
<input type="hidden" name="idfactura" value="<?php echo $_GET['idfactura'];?>"/>
<input type="hidden" name="MM_insert" value="regAbono" />
</form>
</body>
</html>