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

$maxRows_RsUsr = 20;
$pageNum_RsUsr = 0;
if (isset($_GET['pageNum_RsUsr'])) {
  $pageNum_RsUsr = $_GET['pageNum_RsUsr'];
}
$startRow_RsUsr = $pageNum_RsUsr * $maxRows_RsUsr;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUsr = "SELECT *,(select nombre FROM nombres_accesos WHERE nombres_accesos.id=usuarios.responsabilidad) as nivel  from usuarios ORDER BY id";
$query_limit_RsUsr = sprintf("%s LIMIT %d, %d", $query_RsUsr, $startRow_RsUsr, $maxRows_RsUsr);
$RsUsr = mysql_query($query_limit_RsUsr, $tecnocomm) or die(mysql_error());
$row_RsUsr = mysql_fetch_assoc($RsUsr);

if (isset($_GET['totalRows_RsUsr'])) {
  $totalRows_RsUsr = $_GET['totalRows_RsUsr'];
} else {
  $all_RsUsr = mysql_query($query_RsUsr);
  $totalRows_RsUsr = mysql_num_rows($all_RsUsr);
}
$totalPages_RsUsr = ceil($totalRows_RsUsr/$maxRows_RsUsr)-1;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUsrAc = "select * from usuarios where activar=1";
$RsUsrAc = mysql_query($query_RsUsrAc, $tecnocomm) or die(mysql_error());
$row_RsUsrAc = mysql_fetch_assoc($RsUsrAc);
$totalRows_RsUsrAc = mysql_num_rows($RsUsrAc);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsNivel = "select nombres_accesos.nombre,count(*) as cant  from nombres_accesos, usuarios where nombres_accesos.id=usuarios.responsabilidad group by nombres_accesos.nombre";
$RsNivel = mysql_query($query_RsNivel, $tecnocomm) or die(mysql_error());
$row_RsNivel = mysql_fetch_assoc($RsNivel);
$totalRows_RsNivel = mysql_num_rows($RsNivel);

$queryString_RsUsr = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_RsUsr") == false && 
        stristr($param, "totalRows_RsUsr") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_RsUsr = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_RsUsr = sprintf("&totalRows_RsUsr=%d%s", $totalRows_RsUsr, $queryString_RsUsr);

$estado=array(0=>"Desactivo",1=>"Activo");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script src="js/funciones.js" type="text/javascript" language="javascript"></script>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
</head>

<body>
<div class="wrapper">
<table width="90%" border="0" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td colspan="7" align="center" class="titulos">ADMINISTRACION DE USUARIOS </td>
    </tr>
  <tr>
    <td height="22" colspan="2">&nbsp;</td>
    <td width="109">&nbsp;</td>
    <td colspan="3">&nbsp;</td>
    <td width="52">&nbsp;</td>
    </tr>
  <tr>
    <td colspan="2">Cantidad de Usuarios: <span class="Estilo1"><?php echo $totalRows_RsUsr ?></span> <br />
Usuarios Activos: <span class="Estilo1"><?php echo $totalRows_RsUsrAc ?></span> </td>
    <td align="center"><a href="usuario_agregar.php" onclick="NewWindow(this.href,'nuevo usuario','500','500','yes');return false"><img src="images/AddUser.png" alt="Agregar" width="24" height="24" border="0" /> Nuevo Usuario </a></td>
    <td colspan="4" valign="top">
	<?php if ($totalRows_RsNivel > 0) { // Show if recordset not empty ?>
      <?php 
	do {
	echo $row_RsNivel['nombre'].":<span class='Estilo1'>".$row_RsNivel['cant']."</span><br>";
	}while($row_RsNivel = mysql_fetch_assoc($RsNivel));
	?>
      <?php } // Show if recordset not empty ?>	</td>
    </tr>
  <tr>
    <td height="46" colspan="2">&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="2">&nbsp;</td>
    <td width="141">&nbsp;</td>
    <td align="center" valign="top"><a href="<?php printf("%s?pageNum_RsUsr=%d%s", $currentPage, 0, $queryString_RsUsr); ?>">
      <?php if ($pageNum_RsUsr > 0) { // Show if not first page ?>
        <img src="images/First.gif" alt="Primera" width="24" height="24" border="0" />
        <?php } // Show if not first page ?>
    </a><a href="<?php printf("%s?pageNum_RsUsr=%d%s", $currentPage, max(0, $pageNum_RsUsr - 1), $queryString_RsUsr); ?>">
    <?php if ($pageNum_RsUsr > 0) { // Show if not first page ?>
      <img src="images/Back.gif" alt="Atras" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
    </a><a href="<?php printf("%s?pageNum_RsUsr=%d%s", $currentPage, min($totalPages_RsUsr, $pageNum_RsUsr + 1), $queryString_RsUsr); ?>">
    <?php if ($pageNum_RsUsr < $totalPages_RsUsr) { // Show if not last page ?>
      <img src="images/Forward.gif" alt="Siguiente" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
    </a><a href="<?php printf("%s?pageNum_RsUsr=%d%s", $currentPage, $totalPages_RsUsr, $queryString_RsUsr); ?>">
    <?php if ($pageNum_RsUsr < $totalPages_RsUsr) { // Show if not last page ?>
      <img src="images/Last.gif" alt="Ultima" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
    </a></td>
  </tr>
  
  <?php if ($totalRows_RsUsr > 0) { // Show if recordset not empty ?>
    <tr class="titleTabla">
      <td width="84" align="center">ID</td>
      <td width="133" align="center">NOMBRE</td>
      <td align="center">USUARIO</td>
      <td width="117" align="center">NIVEL ACCESO </td>
      <td width="76" align="center">ESTADO</td>
      <td colspan="2" align="center">OPCIONES</td>
      </tr>
    <?php } // Show if recordset not empty ?>
  <?php do { ?>
    <tr>
      <td align="left"><?php echo $row_RsUsr['id']; ?></td>
      <td align="left"><?php echo $row_RsUsr['nombrereal']; ?></td>
      <td align="left"><?php echo $row_RsUsr['username']; ?></td>
      <td align="left"><?php echo $row_RsUsr['nivel']; ?></td>
      <td align="left"><?php echo $estado[$row_RsUsr['activar']]; ?></td>
    <td colspan="2" align="left">
<?php if ($totalRows_RsUsr > 0) { // Show if recordset not empty ?>
            <a href="usuarios_eliminar.php?id=<?php echo $row_RsUsr['id']; ?>" onclick="NewWindow(this.href,'eliminar usuario','450','215','yes');return false"><img src="images/Delete User.png" alt="Eliminar" width="24" height="24" border="0" align="middle" title="ELIMINAR USUARIO" /></a>
        <?php } // Show if recordset not empty ?>
          &nbsp;&nbsp;&nbsp;&nbsp;<?php if ($totalRows_RsUsr > 0) { // Show if recordset not empty ?><a href="usuario_modificar.php?id=<?php echo $row_RsUsr['id']; ?>" onclick="NewWindow(this.href,'modificar usuario','500','500','yes');return false"><img src="images/Contact.png" alt="Modificar" width="24" height="24" border="0" align="middle" title="MODIFICAR DATOS DE USUARIO" /></a>
      <?php } // Show if recordset not empty ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php if ($totalRows_RsUsr > 0) { // Show if recordset not empty ?><a href="printCard1.php?idusuario=<?php echo $row_RsUsr['id']; ?>" onclick="NewWindow(this.href,'imprimir credencial 1','600','400','yes');return false"><img src="images/Imprimir2.png" width="24" height="24" border="0" align="middle" title="IMPRIMIR CREDENCIAL FRENTE" /></a><?php } // Show if recordset not empty ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php if ($totalRows_RsUsr > 0) { // Show if recordset not empty ?><a href="printCard2.php?idusuario=<?php echo $row_RsUsr['id']; ?>" onclick="NewWindow(this.href,'imprimir credencial2','600','400','yes');return false"><img src="images/Imprimir1.png" width="24" height="24" border="0" align="middle" title="IMPRIMIR CREDENCIAL POSTERIOR" /></a><?php } // Show if recordset not empty ?>
      <a href="nuevaCuentaCorreo.php?idusuario=<?php echo $row_RsUsr['id']; ?>" onclick="NewWindow(this.href,'imprimir credencial2','400','300','yes');return false"><img src="images/state2.png" width="24" height="24" align="middle" /></a></td>
    </tr>
    <?php } while ($row_RsUsr = mysql_fetch_assoc($RsUsr)); ?>
  <tr>
    <td height="22" align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td colspan="2" align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    </tr>
  <?php if ($totalRows_RsUsr == 0) { // Show if recordset empty ?>
    <tr>
      <td height="22" align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">No hay Usuarios </td>
      <td align="center">&nbsp;</td>
      <td colspan="2" align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      </tr>
    <tr>
      <td height="38"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td valign="top"><a href="<?php printf("%s?pageNum_RsUsr=%d%s", $currentPage, 0, $queryString_RsUsr); ?>">
        <?php if ($pageNum_RsUsr > 0) { // Show if not first page ?>
        <img src="images/First.gif" alt="Primera" width="24" height="24" border="0" />
        <?php } // Show if not first page ?>
      </a><a href="<?php printf("%s?pageNum_RsUsr=%d%s", $currentPage, max(0, $pageNum_RsUsr - 1), $queryString_RsUsr); ?>">
      <?php if ($pageNum_RsUsr > 0) { // Show if not first page ?>
      <img src="images/Back.gif" alt="Atras" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
      </a><a href="<?php printf("%s?pageNum_RsUsr=%d%s", $currentPage, min($totalPages_RsUsr, $pageNum_RsUsr + 1), $queryString_RsUsr); ?>">
      <?php if ($pageNum_RsUsr < $totalPages_RsUsr) { // Show if not last page ?>
      <img src="images/Forward.gif" alt="Siguiente" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
      </a><a href="<?php printf("%s?pageNum_RsUsr=%d%s", $currentPage, $totalPages_RsUsr, $queryString_RsUsr); ?>">
      <?php if ($pageNum_RsUsr < $totalPages_RsUsr) { // Show if not last page ?>
      <img src="images/Last.gif" alt="Ultima" width="24" height="24" border="0" />
      <?php } // Show if not last page ?>
      </a></td>
    </tr>
    <tr>
      <td height="38"></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td>&nbsp;</td>
      </tr>
    <?php } // Show if recordset empty ?>
</table>
</div>
</body>
</html>
<?php
mysql_free_result($RsUsr);

mysql_free_result($RsUsrAc);

mysql_free_result($RsNivel);
?>
