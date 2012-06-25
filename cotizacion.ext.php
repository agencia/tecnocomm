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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "guardar")) {

$colname_rsCoti2 = "-1";
if (isset($_POST['idsubcotizacion2'])) {
  $colname_rsCoti2 = $_POST['idsubcotizacion2'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCoti2 = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s ", GetSQLValueString($colname_rsCoti2, "int"));
$rsCoti2 = mysql_query($query_rsCoti2, $tecnocomm) or die(mysql_error());
$row_rsCoti2 = mysql_fetch_assoc($rsCoti2);
$totalRows_rsCoti2 = mysql_num_rows($rsCoti2);

 mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos = sprintf("SELECT *,articulo.idarticulo as ida, precio_cotizacion as pc,articulo.moneda as mon FROM subcotizacion sub,subcotizacionarticulo suba, articulo WHERE sub.idsubcotizacion=suba.idsubcotizacion and suba.idarticulo=articulo.idarticulo and suba.idsubcotizacion=%s ORDER BY suba.idsubcotizacionarticulo ASC", $_POST['idsubcotizacion1']);
$RsArticulos = mysql_query($query_RsArticulos, $tecnocomm) or die(mysql_error());
$row_RsArticulos = mysql_fetch_assoc($RsArticulos);
$totalRows_RsArticulos = mysql_num_rows($RsArticulos);

do{
///validaciones
 
    $colname_rsNueArt = "-1";
    if (isset($row_RsArticulos['idarticulo'])) {
      $colname_rsNueArt = (get_magic_quotes_gpc()) ? $row_RsArticulos['idarticulo'] : addslashes($row_RsArticulos['idarticulo']);
    }
    mysql_select_db($database_tecnocomm, $tecnocomm);
    $query_rsNueArt = sprintf("SELECT nombre, precio FROM articulo WHERE idarticulo = %s", $colname_rsNueArt);
    $rsNueArt = mysql_query($query_rsNueArt, $tecnocomm) or die(mysql_error());
    $row_rsNueArt = mysql_fetch_assoc($rsNueArt);
    $totalRows_rsNueArt = mysql_num_rows($rsNueArt); 
 

$des=$row_rsNueArt['nombre'];
if($row_rsCoti2['tipo']==0){
	$des="SUM E INST ".htmlentities($des);
}elseif($row_rsCoti2['tipo']==2){
    $des="SUM DE ".htmlentities($des);
}elseif($row_rsCoti2['tipo']==3){
    $des="INST DE ".htmlentities($des);
}

        $pre=0;
if($_POST["precio"] == 0) {
    if($row_rsNueArt['precio']!=NULL){
        $pre=$row_rsNueArt['precio'];
        $tipo_cambio = $row_rsCoti2['tipo_cambio'];
        $reall = null;
        $utilidad = $row_rsCoti2['utilidad_global'];
    }
} else {
    $pre = $row_RsArticulos['precio_cotizacion'];
    $tipo_cambio = $row_RsArticulos['tipo_cambio'];
    $reall = $row_RsArticulos['reall'];
    $utilidad = $row_RsArticulos['utilidad'];
}
	
	
 $insertSQL = sprintf("INSERT INTO 
     subcotizacionarticulo (
        idsubcotizacion, 
        idarticulo, 
        precio_cotizacion, 
        cantidad, 
        utilidad,descri,mo,moneda,marca1,tipo_cambio, reall) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idsubcotizacion2'],"int"),
                       GetSQLValueString($row_RsArticulos['ida'],"int"),
                       GetSQLValueString($pre,"double"),
                       GetSQLValueString($row_RsArticulos['cantidad'],"double"),
                       GetSQLValueString($utilidad,"double"),
					   GetSQLValueString($des,"text"),
					   GetSQLValueString($row_RsArticulos['instalacion'],"double"),
					   GetSQLValueString($row_RsArticulos['mon'],"int"),
					   GetSQLValueString($row_RsArticulos['marca'],"text"),
					   GetSQLValueString($tipo_cambio,"double"),
					   GetSQLValueString($reall,"int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error()." error en insertar articulos");

}while($row_RsArticulos = mysql_fetch_assoc($RsArticulos));
 
  $insertGoTo = "cotizacion.detalle.ip.php?idsubcotizacion=".$_POST['idsubcotizacion2']."&idip=".$_POST['idip'];
  if (isset($_SERVER['QUERY_STRING'])) {
    //$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    //$insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



$colname_rsCoti = "-1";
if (isset($_GET['idsubcotizacion2'])) {
  $colname_rsCoti = $_GET['idsubcotizacion2'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCoti = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_rsCoti, "int"));
$rsCoti = mysql_query($query_rsCoti, $tecnocomm) or die(mysql_error());
$row_rsCoti = mysql_fetch_assoc($rsCoti);
$totalRows_rsCoti = mysql_num_rows($rsCoti);

$colname_rsCoti2 = "-1";
if (isset($_GET['idsubcotizacion1'])) {
  $colname_rsCoti2 = $_GET['idsubcotizacion1'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCoti2 = sprintf("SELECT * FROM subcotizacion,cotizacion WHERE cotizacion.idcotizacion=subcotizacion.idcotizacion and idsubcotizacion = %s ", GetSQLValueString($colname_rsCoti2, "int"));
$rsCoti2 = mysql_query($query_rsCoti2, $tecnocomm) or die(mysql_error());
$row_rsCoti2 = mysql_fetch_assoc($rsCoti2);
$totalRows_rsCoti2 = mysql_num_rows($rsCoti2);


 mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos2 = sprintf("SELECT *,articulo.idarticulo as ida, precio_cotizacion as pc,suba.moneda AS monedasubcotizacion FROM subcotizacion sub,subcotizacionarticulo suba, articulo WHERE sub.idsubcotizacion=suba.idsubcotizacion and suba.idarticulo=articulo.idarticulo and suba.idsubcotizacion=%s ORDER BY suba.idsubcotizacionarticulo ASC", $_GET['idsubcotizacion2']);
$RsArticulos2 = mysql_query($query_RsArticulos2, $tecnocomm) or die(mysql_error());
$row_RsArticulos2 = mysql_fetch_assoc($RsArticulos2);
$totalRows_RsArticulos2 = mysql_num_rows($RsArticulos2);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SubCotizaciones</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css">
<script src="js/jquery.js" language="javascript"> </script>
<script language="javascript"  src="js/funciones.js"></script>
<script src="js/jqueryui.js" language="javascript"></script>
<style type="text/css">
<!--
.realce {
	color: #F00;
}
-->
</style>
</head>

<body>
<h1>Subcotizaciones(Importar Externa)</h1>
Cotizacion Original:<span class="realce"><?php echo $row_rsCoti['identificador2']; ?></span><br />
Cotizacion para importar:<span class="realce"><?php echo $row_rsCoti2['identificador2']; ?></span><br />
Total de partidas:<span class="realce"><?php echo $totalRows_RsArticulos2;?></span><br />
Es correcto?<br /><br />
   
<form action="<?php echo $editFormAction; ?>" method="POST" name="guardar">
    <div>
        <p>
            Precio:
            <div><label for="pl">De lista</label> <input type="radio" id="pl" name="precio" value="0" /></div>
            <div><label for="or">Igual a Original</label> <input type="radio" id="or" name="precio" value="1" /></div>
        </p>
    </div>
<div class="botones">
<button type="submit" class="button"><span>Aceptar</span></button>

<button type="button" class="button" onclick="window.close()"><span>Cancelar</span></button>
  </div>
<input type="hidden" name="idsubcotizacion2" value="<?php echo $_GET['idsubcotizacion1'];?>"/>
<input type="hidden" name="idsubcotizacion1" value="<?php echo $_GET['idsubcotizacion2'];?>"/>
<input type="hidden" name="idip" value="<?php echo $row_rsCoti2['idip'];?>"/>
<input type="hidden" name="MM_insert" value="guardar" />
</form>
<br /><br />
</body>
</html>
<?php
mysql_free_result($rsCoti);

mysql_free_result($rsCoti2);
?>
