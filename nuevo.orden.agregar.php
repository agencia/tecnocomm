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

$MM_restrictGoTo = "index1.php";
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

include("utils.php");

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevoProd")) {
  $insertSQL = sprintf("INSERT INTO ordenservicio_detalle (idordenservicio, idarticulo, descripcion, marca, codigo, precio, moneda, cantidad, utilidad, mano_obra) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idordenservicio'], "int"),
                       GetSQLValueString($_POST['idarticulo'], "int"),
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['codigo'], "text"),
                       GetSQLValueString($_POST['precio'], "double"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['utilidad'], "double"),
                       GetSQLValueString($_POST['mano_obra'], "double"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());


//sumamos a la mano de obra
$updateSQL = sprintf("UPDATE  ordenservicio SET manoobra=manoobra+%s, idusuario = %s WHERE idordenservicio=%s",GetSQLValueString($_POST['mano_obra'], "double"), GetSQLValueString($_SESSION['MM_Userid'], "int"), GetSQLValueString($_POST['idordenservicio'], "int"));
                      

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());


  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_RsOrden = "-1";
if (isset($_GET['idordenservicio'])) {
  $colname_RsOrden = $_GET['idordenservicio'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsOrden = sprintf("SELECT * FROM ordenservicio WHERE idordenservicio = %s", GetSQLValueString($colname_RsOrden, "int"));
$RsOrden = mysql_query($query_RsOrden, $tecnocomm) or die(mysql_error());
$row_RsOrden = mysql_fetch_assoc($RsOrden);
$totalRows_RsOrden = mysql_num_rows($RsOrden);

$colname_rsArticulo = "-1";
if (isset($_GET['idarticulo'])) {
  $colname_rsArticulo = $_GET['idarticulo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsArticulo = sprintf("SELECT * FROM articulo WHERE idarticulo = %s", GetSQLValueString($colname_rsArticulo, "int"));
$rsArticulo = mysql_query($query_rsArticulo, $tecnocomm) or die(mysql_error());
$row_rsArticulo = mysql_fetch_assoc($rsArticulo);
$totalRows_rsArticulo = mysql_num_rows($rsArticulo);


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
<h1>Agregar Porducto o Servicio </h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoProd" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Descripcion:
<input name="descripcion" type="text" class="requerido" value="<?php echo $row_rsArticulo['nombre']; ?>"  />
</label>
<label>Marca:
<input name="marca" type="text" value="<?php echo $row_rsArticulo['marca']; ?>"  />
</label>
<label>Codigo:
  <input name="codigo" type="text" value="<?php echo $row_rsArticulo['codigo']; ?>"  />
</label>
<label>Cantidad:
<input name="cantidad" type="text" value="1" />
</label>

</div>
<?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?>
<div>
<h3>Adicionales</h3>

<label>Precio Unitario:
<input type="text" name="precio" value="<?php echo $row_rsArticulo['precio']; ?>" /><?php if($row_rsArticulo['moneda']==0){ echo "M.N.";}if($row_rsArticulo['moneda']==1){ echo "USD";} ?>&nbsp;&nbsp;<?php if($row_rsArticulo['tipo']==0){ echo "PL";}if($row_rsArticulo['tipo']==1){ echo "CO";} ?>
</label>



<label>Utilidad:
<input name="utilidad" type="text" value="<?php echo $row_RsOrden['utilidad']; ?>" />
</label>

<label>Mano de Obra:
<input name="mano_obra" type="text" value="<?php echo $row_rsArticulo['instalacion']; ?>" />
</label>

</div>
<? }?>
<div class="botones">
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="moneda" value="<?php echo $row_rsArticulo['moneda']; ?>"/>
<input type="hidden" name="idarticulo" value="<?php echo $_GET['idarticulo']; ?>"/>
<input type="hidden" name="idordenservicio" value="<?php echo $_GET['idordenservicio']; ?>"/>
<input type="hidden" name="MM_insert" value="nuevoProd" />


</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsOrden);

mysql_free_result($rsArticulo);
?>