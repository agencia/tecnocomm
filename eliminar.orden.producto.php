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

if ((isset($_POST['id'])) && ($_POST['id'] != "")) {
  $deleteSQL = sprintf("DELETE FROM ordenservicio_detalle WHERE idordenservicio_detalle=%s",
                       GetSQLValueString($_POST['id'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($deleteSQL, $tecnocomm) or die(mysql_error());
  
  
  $updateSQL = sprintf("UPDATE  ordenservicio SET idusuario = %s WHERE idordenservicio=%s", GetSQLValueString($_SESSION['MM_Userid'], "int"), GetSQLValueString($_POST['idordenservicio'], "int"));
                      
  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
  

  $deleteGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}


$colname_Rs = "-1";
if (isset($_GET['idordenservicio_detalle'])) {
  $colname_Rs = $_GET['idordenservicio_detalle'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Rs = sprintf("SELECT * FROM ordenservicio_detalle WHERE idordenservicio_detalle = %s", GetSQLValueString($colname_Rs, "int"));
$Rs = mysql_query($query_Rs, $tecnocomm) or die(mysql_error());
$row_Rs = mysql_fetch_assoc($Rs);
$totalRows_Rs = mysql_num_rows($Rs);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Eliminar Articulo OS</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
.Estilo2 {color: #FFFFFF}
-->
</style>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
<table width="350" border="0" align="center" class="wrapper">
  <tr class="titulos">
    <td colspan="3" align="center"><span class="Estilo2">ELIMINAR PRODUCTO</span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>ESTAS SEGURO QUE DESEAS ELIMINAR EL PRODUCTO <span class="Estilo1"><?php echo $row_Rs['descripcion']; ?></span>?</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="center">
      <label>
        <input type="submit" name="button" id="button" value="Aceptar" />
        </label>    </td>
    <td>&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="id" value="<?php echo $_GET['idordenservicio_detalle'];?>"/>
<input type="hidden" name="idordenservicio" value="<?php echo $row_Rs['idordenservicio'];?>"/>
 </form>
</body>
</html>

<?php
mysql_free_result($Rs);
?>
