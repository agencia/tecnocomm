<?php require_once('Connections/tecnocomm.php'); ?>
<?php
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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "modAviso")) {
  $updateSQL = sprintf("UPDATE avisos SET liberado=%s WHERE id=%s or padre=%s",
                       GetSQLValueString($_POST['liberado'], "int"),
                       GetSQLValueString($_POST['id'], "int"),
					   GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_RsMensaje = "-1";
if (isset($_GET['id'])) {
  $colname_RsMensaje = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsMensaje = sprintf("SELECT *,(select nombrereal from usuarios where id=de) as dee, (select nombrereal from usuarios where id=para) as par FROM avisos WHERE id = %s", $colname_RsMensaje);
$RsMensaje = mysql_query($query_RsMensaje, $tecnocomm) or die(mysql_error());
$row_RsMensaje = mysql_fetch_assoc($RsMensaje);
$totalRows_RsMensaje = mysql_num_rows($RsMensaje);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Banco</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Modificar Aviso </h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="modAviso" method="POST">
<div>
<h3>Datos de Aviso </h3>
<label>De:<?php echo $row_RsMensaje['dee']; ?></label>

<label>Para:<?php echo $row_RsMensaje['par']; ?></label>

<label>Fecha:<?php echo $row_RsMensaje['fecha']; ?></label>

<label>Fecha Realizado:<?php echo $row_RsMensaje['fecharealizado']; ?></label>


<label>Liberado?:
<select name="liberado">
  <option value="0" <?php if (!(strcmp(0, $row_RsMensaje['liberado']))) {echo "selected=\"selected\"";} ?>>No</option>
  <option value="1" <?php if (!(strcmp(1, $row_RsMensaje['liberado']))) {echo "selected=\"selected\"";} ?>>Si</option>
</select>
</label>

</div>
<div>
<h3>Aviso </h3>

<label>Mensaje:<br />
<?php echo $row_RsMensaje['mensaje']; ?>
</label>
<label>Respuesta:<br />
<?php echo $row_RsMensaje['respuesta']; ?>
</label>
</div>

<div>
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>"/>
<input type="hidden" name="MM_update" value="modAviso">

</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsMensaje);
?>