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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "editarip")) {
  $updateSQL = sprintf("UPDATE ip SET idcontacto=%s, descripcion=%s WHERE idip=%s",
                       GetSQLValueString($_POST['idcontacto'], "int"),
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['idip'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsIp = "-1";
if (isset($_GET['idip'])) {
  $colname_rsIp = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsIp = sprintf("SELECT * FROM ip WHERE idip = %s", GetSQLValueString($colname_rsIp, "int"));
$rsIp = mysql_query($query_rsIp, $tecnocomm) or die(mysql_error());
$row_rsIp = mysql_fetch_assoc($rsIp);
$totalRows_rsIp = mysql_num_rows($rsIp);

$colname_Recordset1 = "-1";
if (isset($row_rsIp['idcliente'])) {
  $colname_Recordset1 = $row_rsIp['idcliente'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Recordset1 = sprintf("SELECT * FROM contactoclientes WHERE idcliente = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $tecnocomm) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Editar IP</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
</head>

<body>
<div id="myform">
<form action="<?php echo $editFormAction; ?>" name="editarip" method="POST">
<h1>Editar IP</h1>
<div>
<h3>Generales</h3>
<label>Descripcion
<textarea name="descripcion"><?php echo $row_rsIp['descripcion']; ?></textarea>
</label>
<label>Persona de Contacto:
  <select name="idcontacto">
    <?php
do {  
?>
    <option value="<?php echo $row_Recordset1['idcontacto']?>"<?php if (!(strcmp($row_Recordset1['idcontacto'], $row_rsIp['idcontacto']))) {echo "selected=\"selected\"";} ?>><?php echo $row_Recordset1['nombre']?></option>
    <?php
} while ($row_Recordset1 = mysql_fetch_assoc($Recordset1));
  $rows = mysql_num_rows($Recordset1);
  if($rows > 0) {
      mysql_data_seek($Recordset1, 0);
	  $row_Recordset1 = mysql_fetch_assoc($Recordset1);
  }
?>
  </select></label>
</div>
<div>
<h3>Nuevo Contacto</h3>
<a href="nuevoContacto.php?idcliente=<?php echo $row_rsIp['idcliente'];?>" class="popup">Crear Nuevo Contacto</a>
</div>

<div class="botones">
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="idip" value="<?php echo $_GET['idip'];?>"/>
<input type="hidden" name="MM_update" value="editarip" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($rsIp);

mysql_free_result($Recordset1);
?>
