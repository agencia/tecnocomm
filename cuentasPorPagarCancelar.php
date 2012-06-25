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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE cuentasporpagar SET justificacion=%s, estado=2 WHERE idcuenta=%s",
                       GetSQLValueString($_POST['justificacion'], "text"),
                       GetSQLValueString($_POST['idcuenta'], "int"));
  echo $updateSQL;

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css"  rel="stylesheet" type="text/css"/>
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jqueryui.js"></script>
<script language="javascript" src="js/calendario.js"></script>
<title>Cancelar Factura</title>
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="form1" id="form1">
  <table align="center" class="wrapper">

    <tr valign="baseline">
      <td align="right" valign="top" nowrap="nowrap">Justificacion:</td>
      <td><label>
        <textarea name="justificacion" id="textarea" cols="45" rows="5"></textarea>
      </label></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Guardar" /></td>
    </tr>
  </table>
  
<input type="hidden" name="idcuenta" value="<?php echo  $_GET['idcuenta'];?>"/>
<input type="hidden" name="MM_update" value="form1" />
</form>
</body>
</html>