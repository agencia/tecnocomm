<?php require_once('../Connections/tecnocomm.php'); ?>
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
  $updateSQL = sprintf("UPDATE mn_actividades SET actividad=%s, estado=%s, ultimo=NOW() WHERE idActividad=%s",
                       GetSQLValueString($_POST['actividad'], "text"),
                       GetSQLValueString($_POST['estado'], "int"),
                       GetSQLValueString($_POST['idActividad'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "../close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_Actividad = "-1";
if (isset($_GET['idactividad'])) {
  $colname_Actividad = $_GET['idactividad'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Actividad = sprintf("SELECT * FROM mn_actividades WHERE idActividad = %s", GetSQLValueString($colname_Actividad, "int"));
$Actividad = mysql_query($query_Actividad, $tecnocomm) or die(mysql_error());
$row_Actividad = mysql_fetch_assoc($Actividad);
$totalRows_Actividad = mysql_num_rows($Actividad);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Estado = "SELECT * FROM mn_estados";
$Estado = mysql_query($query_Estado, $tecnocomm) or die(mysql_error());
$row_Estado = mysql_fetch_assoc($Estado);
$totalRows_Estado = mysql_num_rows($Estado);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<link href="../style.css" rel="stylesheet" type="text/css" />
<script>
	window.resizeTo(550, 360);
</script>
</head>

<body><form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST"><br />

<table width="500" border="0" align="center" cellpadding="1" cellspacing="1" class="resaltarTabla">
  <tr>
    <td align="center" class="titulos">Nueva actividad</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
      <textarea name="actividad" cols="60" rows="5"><?php echo $row_Actividad['actividad']; ?></textarea>
      </td>
  </tr>
  <tr>
    <td>Estado: <select name="estado">
      <?php do { ?>
        <option <?php echo ($row_Actividad['estado']==$row_Estado['idEstado']) ? 'selected="selected"': ""; ?> value="<?php echo $row_Estado['idEstado']; ?>"><?php echo $row_Estado['estado']; ?></option>
        <?php } while ($row_Estado = mysql_fetch_assoc($Estado)); ?></select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right"><input type="submit" value="Modificar" /></td>
  </tr>
</table>
<input type="hidden" name="idActividad" value="<?php echo $_GET['idactividad']; ?>" />
<input type="hidden" name="MM_update" value="form1" />
</form>
</body>
</html>
<?php
mysql_free_result($Actividad);

mysql_free_result($Estado);
?>
