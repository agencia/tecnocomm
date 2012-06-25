<?php require_once('Connections/tecnocomm.php'); ?>
<?php
session_start();
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


$sql = "SELECT * FROM articulo";

if(isset($_GET['buscar'])){
$sql = "SELECT * FROM articulo WHERE nombre like ".GetSQLValueString("%".$_GET['buscar']."%","text")." OR marca like ".GetSQLValueString("%".$_GET['buscar']."%","text")." OR codigo like ".GetSQLValueString("%".$_GET['buscar']."%","text");
}


$currentPage = $_SERVER["PHP_SELF"];

$maxRows_RsArticulos = 30;
$pageNum_RsArticulos = 0;
if (isset($_GET['pageNum_RsArticulos'])) {
  $pageNum_RsArticulos = $_GET['pageNum_RsArticulos'];
}
$startRow_RsArticulos = $pageNum_RsArticulos * $maxRows_RsArticulos;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos = $sql;
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
<title>Productos y Servicios</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script type=text/javascript>
var win= null;
function NewWindow(mypage,myname,w,h,scroll){
  var winl = (screen.width-w)/2;
  var wint = (screen.height-h)/2;
  var settings  ='height='+h+',';
      settings +='width='+w+',';
      settings +='top='+wint+',';
      settings +='left='+winl+',';
      settings +='scrollbars='+scroll+',';
      settings +='resizable=yes';
  win=window.open(mypage,myname,settings);
  if(parseInt(navigator.appVersion) >= 4){win.window.focus();}
}
//-->

function mover(posy,posx)
{
  var winl = (screen.width-posy)/2;
  var wint = (screen.height-posx)/2;
  
if (parseInt(navigator.appVersion)>3)
  top.resizeTo(posy,posx);
  top.moveTo(winl,wint);
}
//mover('1035','400');

</script>

<style type="text/css">
<!--
.Estilo1 {
	color: #FFFFFF;
	font-size: 17px;
}
-->
</style>
</head>


<body class="wrapper">
<table  border="0" cellpadding="0" cellspacing="0" class="resaltarTabla">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="20" height="22">&nbsp;</td>
          <td colspan="3" valign="top">Productos</td>
          <td width="18">&nbsp;</td>
        </tr>
      <tr>
        <td height="8"></td>
          <td width="271"></td>
          <td width="281"></td>
          <td width="42"></td>
          <td></td>
        </tr>
      <tr>
        <td height="23"></td>
          <td colspan="2" valign="top"><form name="buscar" method="get">Buscar: 
            <input name="buscar" type="text" id="buscar"  size="40" value="<?php echo $_GET['buscar'];?>"/>
            <label></label>
            <input type="submit" name="buscar2" id="buscar2" value="Buscar" />
          <input type="hidden" name="idordencompra" value="<?php echo $_GET['idordencompra'];?>" />
          
          </form></td>
          <td></td>
          <td></td>
        </tr>
      <tr>
        <td height="20"></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td></td>
          <td></td>
        </tr>
      <tr>
        <td height="44"></td>
          <td>&nbsp;</td>
          <td colspan="2" align="right" valign="top"><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, 0, $queryString_RsArticulos); ?>">
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
          <td></td>
        </tr>
      
      
      <tr>
        <td height="22"></td>
          <td colspan="3" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <!--DWLayoutTable-->
              <tr class="titleTabla">
                <td width="277" height="22" valign="top">Descripcion</td>
                <td width="83" valign="top">Marca</td>
                <td width="85" valign="top">Codigo</td>
                <td width="70" valign="top">Precio</td>
                <td width="99" valign="top">Opciones</td>
              </tr>
          </table></td>
          <td></td>
        </tr>
      <tr>
        <td height="3"></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      <tr>
        <td height="24"></td>
          <td colspan="3" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
              <!--DWLayoutTable-->
              <?php do { ?>
                <tr>
                  <td width="278" height="20" valign="top"><?php echo $row_RsArticulos['nombre']; ?></td>
                  <td width="82" valign="top"><?php echo $row_RsArticulos['marca']; ?></td>
                  <td width="85" valign="top"><?php echo $row_RsArticulos['codigo']; ?></td>
                  <td width="70" valign="top"><?php echo $row_RsArticulos['precio']; ?></td>
                  <td width="99" valign="top"><a href="asignarDetalleOrdenCatalago.php?idarticulo=<?php echo $row_RsArticulos['idarticulo']; ?>&idordencompra=<?php echo $_GET['idordencompra'];?>"><img src="images/Checkmark.png" width="24" height="24" /></a></td>
                </tr>
                <?php } while ($row_RsArticulos = mysql_fetch_assoc($RsArticulos)); ?>
          </table></td>
          <td></td>
        </tr>
      <tr>
        <td height="25"></td>
          <td></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td></td>
        </tr>      
    </table>
</body>
</html>
<?php
mysql_free_result($RsArticulos);
?>
