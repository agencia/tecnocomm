<?php require_once('Connections/tecnocomm.php'); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

if(($_POST['tipo']==1) || ($_POST['tipo']==3)){
  $updateSQL = sprintf("UPDATE subcotizacion SET tipo=%s,descrimano='SERVICIO DE INSTALACION',monto=1,cantidad=1,unidad='SERV',codigo='TECNOCOMM',marca='TECNOCOMM' WHERE idsubcotizacion=%s",
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));
}
else{
		  $updateSQL = sprintf("UPDATE subcotizacion SET tipo=%s,descrimano='' WHERE idsubcotizacion=%s",
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));


}
  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
 /////////////////////cambio de sum e inst a nada 
//  if (($_POST['oldtype']==0) || ($_POST['oldtype']==2) || ($_POST['oldtype']==3)) {
  
  $colname_rsArt = "-1";
if (isset($_POST['idsubcotizacion'])) {
  $colname_rsArt = $_POST['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsArt = sprintf("SELECT idsubcotizacionarticulo,descri, idarticulo FROM subcotizacionarticulo WHERE idsubcotizacion = %s", GetSQLValueString($colname_rsArt, "int"));
$rsArt = mysql_query($query_rsArt, $tecnocomm) or die(mysql_error());
$row_rsArt = mysql_fetch_assoc($rsArt);
$totalRows_rsArt = mysql_num_rows($rsArt);
if($totalRows_rsArt != 0) {
  do{
//  $ndes = ($_POST['oldtype']==0) ? 14 : 8;
//  $ndes = ($_POST['oldtype']==2) ? 7 : $ndes;
//  $ndes = ($_POST['oldtype']==3) ? 8 : $ndes;
//  $des=substr($row_rsArt['descri'],$ndes);
  //echo $des."<br>";
    mysql_select_db($database_tecnocomm, $tecnocomm);
    $query_rsNueArt = sprintf("SELECT nombre FROM articulo WHERE idarticulo = %s", $row_rsArt['idarticulo']);
    $rsNueArt = mysql_query($query_rsNueArt, $tecnocomm) or die(mysql_error());
    $row_rsNueArt = mysql_fetch_assoc($rsNueArt);
    $totalRows_rsNueArt = mysql_num_rows($rsNueArt); 
 

$des=$row_rsNueArt['nombre'];
if($_POST['tipo']==0){
	$des="SUM E INST ".htmlentities($des);
}elseif($_POST['tipo']==2){
    $des="SUM DE ".htmlentities($des);
}elseif($_POST['tipo']==3){
    $des="INST DE ".htmlentities($des);
}
      
  $updateSQL = sprintf("UPDATE subcotizacionarticulo SET descri=%s WHERE idsubcotizacionarticulo=%s",
                       GetSQLValueString($des, "text"),
                       GetSQLValueString($row_rsArt['idsubcotizacionarticulo'], "int"));
//echo $updateSQL."<br>";
  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
  
 } while($row_rsArt = mysql_fetch_assoc($rsArt));
}
  //////////////////////////////////////////////////
   

 /////////////////////cambio de nada  a SUm e Ins
//  if (($_POST['tipo']==0) || ($_POST['tipo']==2) || ($_POST['tipo']==3)) {
//  
//  $colname_rsArt = "-1";
//if (isset($_POST['idsubcotizacion'])) {
//  $colname_rsArt = $_POST['idsubcotizacion'];
//}
//mysql_select_db($database_tecnocomm, $tecnocomm);
//$query_rsArt = sprintf("SELECT idsubcotizacionarticulo,descri FROM subcotizacionarticulo WHERE idsubcotizacion = %s", GetSQLValueString($colname_rsArt, "int"));
//$rsArt = mysql_query($query_rsArt, $tecnocomm) or die(mysql_error());
//$row_rsArt = mysql_fetch_assoc($rsArt);
//$totalRows_rsArt = mysql_num_rows($rsArt);
//  do{
//  	$des = ($_POST['tipo']==0) ? "SUM E INST DE " :  "";
//  	$des = ($_POST['tipo']==2) ? "SUM DE " :  $des;
//  	$des = ($_POST['tipo']==3) ? "INST DE " :  $des;
//    $des.=$row_rsArt['descri'];
//    //echo $des."<br>";
//  $updateSQL = sprintf("UPDATE subcotizacionarticulo SET descri=%s WHERE idsubcotizacionarticulo=%s",
//  										GetSQLValueString($des, "text"),
//                                        GetSQLValueString($row_rsArt['idsubcotizacionarticulo'], "int"));
////echo  $updateSQL."<br>";
//  mysql_select_db($database_tecnocomm, $tecnocomm);
//  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
//  
// } while($row_rsArt = mysql_fetch_assoc($rsArt));
//}
  //////////////////////////////////////////////////  

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}



$colname_RsSub = "-1";
if (isset($_GET['idsub'])) {
  $colname_RsSub = $_GET['idsub'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSub = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_RsSub, "int"));
$RsSub = mysql_query($query_RsSub, $tecnocomm) or die(mysql_error());
$row_RsSub = mysql_fetch_assoc($RsSub);
$totalRows_RsSub = mysql_num_rows($RsSub);


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form action="<?php echo $editFormAction; ?>" method="POST" name="form1" id="form1">
  <table align="center" class="wrapper">
    <tr valign="baseline">
      <td colspan="2" align="center" nowrap="nowrap" class="titulos">CAMBIAR TIPO DE COTIZACION</td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Tipo:</td>
      <td><select name="tipo">
        <option value="0" <?php if (!(strcmp(0, htmlentities($row_RsSub['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SUMINISTRO E INSTALACION</option>
        <option value="1" <?php if (!(strcmp(1, htmlentities($row_RsSub['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SERVICIO DE INSTALACION</option>
        <option value="2" <?php if (!(strcmp(2, htmlentities($row_RsSub['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SOLO SUMINISTRO</option>
        <option value="3" <?php if (!(strcmp(3, htmlentities($row_RsSub['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SOLO INSTALACION</option>
      </select>      </td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr valign="baseline">
      <td colspan="2" align="center" nowrap="nowrap"><input type="submit" value="CAMBIAR TIPO" /></td>
    </tr>
  </table>
  <input type="hidden" name="oldtype" value="<?php echo $row_RsSub['tipo']; ?>" />
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="idsubcotizacion" value="<?php echo $row_RsSub['idsubcotizacion']; ?>" />
  
  
</form>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($RsSub);

mysql_free_result($rsArt);
?>
