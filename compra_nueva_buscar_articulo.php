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

$currentPage = $_SERVER["PHP_SELF"];



$maxRows_RsArticulos = 30;
$pageNum_RsArticulos = 0;
if (isset($_GET['pageNum_RsArticulos'])) {
  $pageNum_RsArticulos = $_GET['pageNum_RsArticulos'];
}
$startRow_RsArticulos = $pageNum_RsArticulos * $maxRows_RsArticulos;


$consulta=0;
$sql="";
if(isset($_GET['buscar']) and ($_GET['buscar']!="")){
	$sql.="  nombre like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR codigo like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR marca like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	
	$consulta++;
}
if ($consulta>0){

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos = "SELECT * FROM articulo where ".$sql." ORDER BY nombre";

$query_limit_RsArticulos = sprintf("%s LIMIT %d, %d", $query_RsArticulos, $startRow_RsArticulos, $maxRows_RsArticulos);

$RsArticulos = mysql_query($query_limit_RsArticulos, $tecnocomm) or die(mysql_error());
$row_RsArticulos = mysql_fetch_assoc($RsArticulos);
$totalRows_RsArticulos = mysql_num_rows($RsArticulos);

}
else{
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos = "SELECT * FROM articulo ORDER BY nombre";
$query_limit_RsArticulos = sprintf("%s LIMIT %d, %d", $query_RsArticulos, $startRow_RsArticulos, $maxRows_RsArticulos);
$RsArticulos = mysql_query($query_limit_RsArticulos, $tecnocomm) or die(mysql_error());
$row_RsArticulos = mysql_fetch_assoc($RsArticulos);
}

if (isset($_GET['totalRows_RsArticulos'])) {
  $totalRows_RsArticulos = $_GET['totalRows_RsArticulos'];
} else {
  $all_RsArticulos = mysql_query($query_RsArticulos);
  $totalRows_RsArticulos = mysql_num_rows($all_RsArticulos);
}
$totalPages_RsArticulos = ceil($totalRows_RsArticulos/$maxRows_RsArticulos)-1;

$queryString_RsArticulos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_RsArticulos") == false && 
        stristr($param, "totalRows_RsArticulos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_RsArticulos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_RsArticulos = sprintf("&totalRows_RsArticulos=%d%s", $totalRows_RsArticulos, $queryString_RsArticulos);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/funciones.js"></script>

<style type="text/css">
<!--
.Estilo1 {
	color: #FFFFFF;
	font-size: 17px;
}
-->
</style>
</head>


<body class="wrapper" onload = "document.forms[0].buscar.focus()">
<form id="form1" name="form1" method="get" action="">
<table width="1036" border="0" align="center">
  <tr class="titulos">
    <td colspan="7" align="center" class="Estilo1" >AGREGAR ARTICULOS A ORDEN DE COMPRA</td>
    </tr>
  <tr>
    <td width="100">&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td colspan="4">&nbsp;</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2">BUSCAR : 
      <input name="buscar" type="text" class="form" id="buscar" value="<?php echo $_GET['buscar'];?>" size="50" />
      <input type="submit" name="button" id="button" value="Buscar" /></td>
    <td colspan="4">&nbsp;</td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="6" align="center"><label></label></td>
    </tr>
  <tr>
    <td align="center"><label>
    <input type="button" name="Submit" value="cerrar" onclick="window.location='close.php'" />
    </label></td>
    <td colspan="2">&nbsp;</td>
    <td colspan="4" align="center"><a href="nuevoProducto.php" onclick="NewWindow(this.href,'modificar articulo','500','500','no');return false"><img src="images/Agregar.png" width="24" height="24" border="0" align="middle" title="AGREGAR NUEVO ARTICULO" />AGREGAR ARTICULO</a></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="6" align="center"><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, 0, $queryString_RsArticulos); ?>">
    <?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
      <img src="images/First.gif" alt="primera" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
</a><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, max(0, $pageNum_RsArticulos - 1), $queryString_RsArticulos); ?>">
<?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
  <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
  <?php } // Show if not first page ?>
</a><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, min($totalPages_RsArticulos, $pageNum_RsArticulos + 1), $queryString_RsArticulos); ?>">
<?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?>
  <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
  <?php } // Show if not last page ?>
</a><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, $totalPages_RsArticulos, $queryString_RsArticulos); ?>">
<?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?>
  <img src="images/Last.gif" alt="ultima" width="24" height="24" border="0" />
  <?php } // Show if not last page ?>
</a></td>
    </tr>
  <?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
    <tr class="titleTabla">
      <td colspan="3" align="center">DESCRIPCION</td>
      <td width="103" align="center">MARCA</td>
      <td width="106" align="center">CODIGO</td>
      <td width="106" align="center">PRECIO(DE LISTA)</td>
      <td width="107" align="center">OPCIONES</td>
      </tr>
    <?php } // Show if recordset not empty ?>

  <tr>
    <td colspan="7">&nbsp;</td>
    </tr>
  <?php do { ?>
    <tr onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
      <td colspan="3"><?php echo $row_RsArticulos['nombre']; ?></td>
      <td align="center"><?php echo $row_RsArticulos['marca']; ?></td>
      <td align="center"><?php echo $row_RsArticulos['codigo']; ?></td>
      <td align="center"><?php echo $row_RsArticulos['precio']; ?>&nbsp;&nbsp;</td>
      <td align="center"><?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
       <a href="modificarProducto.php?idarticulo=<?php echo $row_RsArticulos['idarticulo'];?>" onclick="NewWindow(this.href,'modificar articulo','500','500','no');return false"> <img src="images/Edit.png" alt="modificar" width="24" height="24" border="0" title="MODIFICAR DATOS DEL ARTICULO"/></a>
        <?php } // Show if recordset not empty ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php if ($totalRows_RsArticulos > 0) { // Show if recordset not empty ?>
<a href="asignarDetalleOrdenCatalago.php?idarticulo=<?php echo $row_RsArticulos['idarticulo'];?>&idordencompra=<?php echo $_GET["idordencompra"];?>" onclick="NewWindow(this.href,'Agregar articulo','600','500','no');return false"> <img src="images/Checkmark.png" alt="agregar" border="0" title="AGREGAR ARTICULO A ORDEN DE COMPRA" /></a>
<?php } // Show if recordset not empty ?></td>
    </tr>
    <?php } while ($row_RsArticulos = mysql_fetch_assoc($RsArticulos)); ?>
    <?php if ($totalRows_RsArticulos == 0) { // Show if recordset empty ?>
        <tr>
          <td colspan="7" align="center">NO SE ENCONTRARON REGISTROS</td>
        </tr> <?php } // Show if recordset empty ?>
        <tr>
          <td colspan="7" align="center">&nbsp;</td>
        </tr>
      <tr>
        <td colspan="7" align="center"><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, 0, $queryString_RsArticulos); ?>">
          <?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
          <img src="images/First.gif" alt="primera" width="24" height="24" border="0" />
          <?php } // Show if not first page ?>
        </a><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, max(0, $pageNum_RsArticulos - 1), $queryString_RsArticulos); ?>">
        <?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
        <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
        <?php } // Show if not first page ?>
        </a><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, min($totalPages_RsArticulos, $pageNum_RsArticulos + 1), $queryString_RsArticulos); ?>">
        <?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?>
        <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
        <?php } // Show if not last page ?>
        </a><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, $totalPages_RsArticulos, $queryString_RsArticulos); ?>">
        <?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?>
        <img src="images/Last.gif" alt="ultima" width="24" height="24" border="0" />
        <?php } // Show if not last page ?>
        </a></td>
      </tr>
</table>
<input type="hidden" name="idordencompra" value="<?php echo $_GET["idordencompra"];?>"/>
</form>
</body>
</html>
<?php
mysql_free_result($RsArticulos);
?>
