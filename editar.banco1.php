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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "editarbanco")) {
  $updateSQL = sprintf("UPDATE banco SET fecha=%s, referencia=%s, importe=%s, concepto=%s, tipopago=%s, fechacobro=%s, idip=%s WHERE id=%s",
                       GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['referencia'], "text"),
                       GetSQLValueString($_POST['importe'], "double"),
                       GetSQLValueString($_POST['concepto'], "text"),
                       GetSQLValueString($_POST['tipopago'], "int"),
                       GetSQLValueString($_POST['fechacobro'], "date"),
                       GetSQLValueString($_POST['idip'], "int"),
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

$colname_rsCuenta = "-1";
if (isset($_GET['id'])) {
  $colname_rsCuenta = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCuenta = sprintf("SELECT * FROM banco WHERE id = %s", GetSQLValueString($colname_rsCuenta, "int"));
$rsCuenta = mysql_query($query_rsCuenta, $tecnocomm) or die(mysql_error());
$row_rsCuenta = mysql_fetch_assoc($rsCuenta);
$totalRows_rsCuenta = mysql_num_rows($rsCuenta);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Editar Cuenta</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
<link href="css/redmond/jquery.css"  rel="stylesheet" type="text/css"/>
<script language="javascript" src="js/jqueryui.js"></script>
<script language="javascript" src="js/calendario.js"></script>
</head>

<body>
<div id="myform">
<form action="<?php echo $editFormAction; ?>" name="editarbanco" method="POST">
<h1>Editar Cuenta</h1>
<div>
<h3>Generales</h3>
<label>Concepto:
<textarea name="concepto"><?php echo $row_rsCuenta['concepto']; ?></textarea>
</label>
<label>Fecha:
      <input name="fecha" type="text" id="fecha" value="<?php echo $row_rsCuenta['fecha']; ?>" size="15" />
</label>
<label>Tipo de Pago:
	<select name="tipopago" id="tipopago">
	  <option value="0" <?php if (!(strcmp(0, $row_rsCuenta['tipopago']))) {echo "selected=\"selected\"";} ?>>Cheque</option>
	  <option value="1" <?php if (!(strcmp(1, $row_rsCuenta['tipopago']))) {echo "selected=\"selected\"";} ?>>Transferencia</option>
	  <option value="2" <?php if (!(strcmp(2, $row_rsCuenta['tipopago']))) {echo "selected=\"selected\"";} ?>>Efectivo</option>
	  <option value="4" <?php if (!(strcmp(4, $row_rsCuenta['tipopago']))) {echo "selected=\"selected\"";} ?>>Tarjeta</option>
	  <option value="3" <?php if (!(strcmp(3, $row_rsCuenta['tipopago']))) {echo "selected=\"selected\"";} ?>>Otro</option>
        
      </select>
</label>
<label>Referencia:<input name="referencia" type="text" id="referencia" value="<?php echo $row_rsCuenta['referencia']; ?>" /></label>
<label>Importe:<input name="importe" type="text" id="importe" value="<?php echo $row_rsCuenta['importe']; ?>" size="15" /></label>
<label>IP:<input name="idip" type="text" id="idip" value="<?php echo $row_rsCuenta['idip']; ?>" size="15" /></label>
<label>Fecha Cobro:<input name="fechacobro" type="text"  class="fecha" id="fechacobro" value="<?php echo $row_rsCuenta['fechacobro']; ?>" /></label>
<label></label>
</div>


<div class="botones">
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="id" value="<?php echo $_GET['id'];?>"/>
<input type="hidden" name="MM_update" value="editarbanco" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($rsCuenta);
?>
