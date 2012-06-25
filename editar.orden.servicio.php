<?php require_once('Connections/tecnocomm.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "systemFail.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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

include("utils.php");
$colname_rsOrden = "-1";
if (isset($_GET['idordenservicio'])) {
  $colname_rsOrden = $_GET['idordenservicio'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrden = sprintf("SELECT * FROM ordenservicio WHERE idordenservicio = %s", GetSQLValueString($colname_rsOrden, "int"));
$rsOrden = mysql_query($query_rsOrden, $tecnocomm) or die(mysql_error());
$row_rsOrden = mysql_fetch_assoc($rsOrden);
$totalRows_rsOrden = mysql_num_rows($rsOrden);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "nuevaOrden")) {
  $updateSQL = sprintf("UPDATE ordenservicio SET trabajorealizado=%s, descripcionreporte=%s, descripcion=%s, observaciones=%s, moneda=%s, tipo_cambio=%s, utilidad=%s, iva=%s, fechaatencion=%s, nopersonas=%s, totalhoras=%s, cargo=%s, pendiente=%s, idusuario=%s WHERE idordenservicio=%s",
                       GetSQLValueString($_POST['trabajorealizado'], "text"),
                       GetSQLValueString($_POST['descripcionreporte'], "text"),
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['tipo_cambio'], "double"),
                       GetSQLValueString($_POST['utilidad'], "double"),
                       GetSQLValueString($_POST['iva'], "double"),
                       GetSQLValueString($_POST['fechaatencion'], "date"),
                       GetSQLValueString($_POST['nopersonas'], "int"),
                       GetSQLValueString($_POST['totalhoras'], "double"),
                       GetSQLValueString($_POST['cargo'], "int"),
                       GetSQLValueString($_POST['pendiente'], "int"),
                       GetSQLValueString($_SESSION['MM_Userid'], "int"),
                       GetSQLValueString($_POST['idordenservicio'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

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
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Nueva Orden de Servicio</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Editar Orden de Servicio</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevaOrden" method="POST">
<div>
<h3>Datos Reporte</h3>
<label>IP:<?php echo $row_rsOrden['idip']; ?></label>
<label>Identificador:<?php echo $row_rsOrden['identificador']; ?></label>
<label>Descripcion Reporte:
 <textarea name="descripcionreporte"><?php echo $row_rsOrden['descripcionreporte']; ?></textarea>
</label>
<?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?>
<label>Moneda:
  <select name="moneda">
    <option value="0" <?php if (!(strcmp(0, $row_rsOrden['moneda']))) {echo "selected=\"selected\"";} ?>>Pesos</option>
    <option value="1" <?php if (!(strcmp(1, $row_rsOrden['moneda']))) {echo "selected=\"selected\"";} ?>>Dolares</option>
  </select></label>
  <label>Tipo Cambio:
  <input name="tipo_cambio" type="text" value="<?php echo $row_rsOrden['tipo_cambio']; ?>"  />
</label>
<label>IVA:
  <input name="iva" type="text" value="<?php echo $row_rsOrden['iva']; ?>"  />
</label>
<label>Utilidad:
  <input name="utilidad" type="text" value="<?php echo $row_rsOrden['utilidad']; ?>"  />
</label>
<?php }?>
<label>Observaciones:
  <textarea name="observaciones"><?php echo $row_rsOrden['observaciones']; ?></textarea>
</label>
</div>

<div>
<h3>Datos Trabajo Realizado</h3>
<label>Trabajo Realizado:
  <textarea name="trabajorealizado"><?php echo $row_rsOrden['trabajorealizado']; ?></textarea>
</label>
<label>Descripcion:
  <textarea name="descripcion"><?php echo $row_rsOrden['descripcion']; ?></textarea>
</label>
<label>Fecha Atencion:
  <input name="fechaatencion" type="text" value="<?php echo $row_rsOrden['fechaatencion']; ?>"  />
</label>
<label>Numero de Personas:
  <input name="nopersonas" type="text" value="<?php echo $row_rsOrden['nopersonas']; ?>"  />
</label>
<label>Total Horas:
  <input name="totalhoras" type="text" value="<?php echo $row_rsOrden['totalhoras']; ?>"  />
</label>
<label>Cargo:
 <select name="cargo">
   <option value="2" <?php if (!(strcmp(1, $row_rsOrden['cargo']))) {echo "selected=\"selected\"";} ?>>Sin definir</option>
   <option value="1" <?php if (!(strcmp(1, $row_rsOrden['cargo']))) {echo "selected=\"selected\"";} ?>>Si</option>
   <option value="0" <?php if (!(strcmp(0, $row_rsOrden['cargo']))) {echo "selected=\"selected\"";} ?>>No</option>
 </select>
</label>
<label>Trabajo Pendiente::
  <select name="pendiente">
   <option value="2" <?php if (!(strcmp(1, $row_rsOrden['cargo']))) {echo "selected=\"selected\"";} ?>>Sin definir</option>
    <option value="0" <?php if (!(strcmp(0, $row_rsOrden['pendiente']))) {echo "selected=\"selected\"";} ?>>No</option>
    <option value="1" <?php if (!(strcmp(1, $row_rsOrden['pendiente']))) {echo "selected=\"selected\"";} ?>>Si</option>
  </select>
</label>
</div>
<div class="button">
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="idordenservicio" value="<?php echo $_GET['idordenservicio']?>"/>
<input type="hidden" name="MM_update" value="nuevaOrden" />
</form>

</div>

</body>
</html>
<?php
mysql_free_result($rsOrden);

?>