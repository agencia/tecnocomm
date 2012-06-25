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
$bus='';
if(isset($_GET['busqueda'])&&$_GET['busqueda']!=''){
	$bus = " and nombre like \"%".$_GET['busqueda']."%\" OR  marca like \"%".$_GET['busqueda']."%\" OR codigo like \"%".$_GET['busqueda']."%\" OR idarticulo like \"%".$_GET['busqueda']."%\" ORDER BY nombre ASC";
}
$maxRows_RsArticulos = 50;
$pageNum_RsArticulos = 0;
if (isset($_GET['pageNum_RsArticulos'])) {
  $pageNum_RsArticulos = $_GET['pageNum_RsArticulos'];
}
$startRow_RsArticulos = $pageNum_RsArticulos * $maxRows_RsArticulos;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos = "SELECT * FROM articulo where 1 $bus";
$query_limit_RsArticulos = sprintf("%s LIMIT %d, %d", $query_RsArticulos, $startRow_RsArticulos, $maxRows_RsArticulos);
$RsArticulos = mysql_query($query_limit_RsArticulos, $tecnocomm) or die(mysql_error());
$row_RsArticulos = mysql_fetch_assoc($RsArticulos);

if (isset($_GET['totalRows_RsArticulos'])) {
  $totalRows_RsArticulos = $_GET['totalRows_RsArticulos'];
} else {
  $all_RsArticulos = mysql_query($query_RsArticulos);
  $totalRows_RsArticulos = mysql_num_rows($all_RsArticulos);
}
$totalPages_RsArticulos = ceil($totalRows_RsArticulos/$maxRows_RsArticulos)-1;
?>
    <div id="distabla">
		<table width="100%" cellpadding="2" cellspacing="0">
        <thead>
        <tr>
          <td colspan="2">&nbsp;
Registros <?php echo ($startRow_RsArticulos + 1) ?> a <?php echo min($startRow_RsArticulos + $maxRows_RsArticulos, $totalRows_RsArticulos) ?> de <?php echo $totalRows_RsArticulos ?> </td>
          <td>&nbsp;</td>
          <td colspan="3" align="right">&nbsp;
            <table border="0">
              <tr>
                <td><?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, 0, $queryString_RsArticulos); ?>"><img src="images/First.gif" border="0" /></a>
                <?php } // Show if not first page ?>                </td>
                <td><?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, max(0, $pageNum_RsArticulos - 1), $queryString_RsArticulos); ?>"><img src="images/Back.gif" border="0" /></a>
                <?php } // Show if not first page ?>                </td>
                <td><?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, min($totalPages_RsArticulos, $pageNum_RsArticulos + 1), $queryString_RsArticulos); ?>"><img src="images/Forward.gif" border="0" /></a>
                <?php } // Show if not last page ?>                </td>
                <td><?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, $totalPages_RsArticulos, $queryString_RsArticulos); ?>"><img src="images/Last.gif" border="0" /></a>
                <?php } // Show if not last page ?>                </td>
              </tr>
            </table></td>
          </tr>
        <tr>
          <td width="10%">Clave</td>
        	<td width="50%">Descripcion</td>
            <td width="10%">Marca</td>
            <td width="10%">Codigo</td>
            <?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?><td width="10%">Precio</td><? } ?>
            <td width="10%">Opciones</td>
         </tr>
         </thead>
        <tbody>
          <?php do { ?>
          <tr>
            <td><?php echo $row_RsArticulos['clave']; ?></td>
            <td><?php echo $row_RsArticulos['nombre']; ?></td>
            <td><?php echo $row_RsArticulos['marca']; ?></td>
            <td><?php echo $row_RsArticulos['codigo']; ?></td>
            <?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?><td align="right"><?php echo $row_RsArticulos['precio']; ?></td><? }?>
            <td><?php if(permiso($_SESSION['MM_UserGroup'],27)){ ?><a href="modificarProducto.php?idarticulo=<?php echo $row_RsArticulos['idarticulo'];?>" onclick="NewWindow(this.href,'modificar articulo','500','500','no');return false"> <img src="images/Edit.png" alt="modificar" width="24" height="24" border="0" title="MODIFICAR DATOS DEL ARTICULO"/></a><?php } ?><a href="nuevo.orden.agregar.php?idarticulo=<?php echo $row_RsArticulos['idarticulo'];?>&idordenservicio=<?php echo $_GET['idordenservicio'];?>" onclick="NewWindow(this.href,'Agregar articulo','600','500','no');return false"> <img src="images/Checkmark.png" alt="agregar" border="0" title="AGREGAR ARTICULO A ORDEN DE SERVICIO" /></a></td>
          </tr>
          <?php } while ($row_RsArticulos = mysql_fetch_assoc($RsArticulos)); ?>
        </tbody>
        <tfoot>
        <tr>
          <td colspan="2">&nbsp;
Registros <?php echo ($startRow_RsArticulos + 1) ?> a <?php echo min($startRow_RsArticulos + $maxRows_RsArticulos, $totalRows_RsArticulos) ?> de <?php echo $totalRows_RsArticulos ?> </td>
          <td>&nbsp;</td>
          <td colspan="3" align="right">&nbsp;
            <table border="0">
              <tr>
                <td><?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, 0, $queryString_RsArticulos); ?>"><img src="images/First.gif" border="0" /></a>
                <?php } // Show if not first page ?>                </td>
                <td><?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
                  <a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, max(0, $pageNum_RsArticulos - 1), $queryString_RsArticulos); ?>"><img src="images/Back.gif" border="0" /></a>
                <?php } // Show if not first page ?>                </td>
                <td><?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?>
                  <a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, min($totalPages_RsArticulos, $pageNum_RsArticulos + 1), $queryString_RsArticulos); ?>"><img src="images/Forward.gif" border="0" /></a>
                <?php } // Show if not last page ?>                </td>
                <td><?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?>
                  <a href="images/Last.gif"><img src="images/Last.gif" border="0" /></a>
                <?php } // Show if not last page ?></td>
              </tr>
            </table></td>
          </tr>
        </tfoot>
        </table>
</div>
    <?php
mysql_free_result($RsArticulos);
?>
