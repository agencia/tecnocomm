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


$colname_rsCoti = "-1";
if (isset($_POST['idsubcotizacion'])) {
  $colname_rsCoti = $_POST['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCoti2 = sprintf("SELECT * FROM subcotizacion,cotizacion WHERE idsubcotizacion = %s  and subcotizacion.idcotizacion=cotizacion.idcotizacion", GetSQLValueString($colname_rsCoti, "int"));
$rsCoti2 = mysql_query($query_rsCoti2, $tecnocomm) or die(mysql_error());
$row_rsCoti2 = mysql_fetch_assoc($rsCoti2);
$totalRows_rsCoti2 = mysql_num_rows($rsCoti2);
//echo $totalRows_rsCoti2;


	
	
  $insertSQL = sprintf("INSERT INTO subcotizacion (idcotizacion, identificador,identificador2, fecha, formapago, moneda, vigencia, tipoentrega, garantia,estado, nombre,contacto,tipo_cambio,notas,usercreo,utilidad_global,tipo,descrimano,monto,descuento, cantidad, unidad, codigo, marca,montoreal,cantidadreal,iva) VALUES (%s, %s, %s, now(), %s, %s, %s, %s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s, %s, %s, %s, %s, %s,cantidad,%s)",$row_rsCoti2['idcotizacion'],
                       GetSQLValueString($row_rsCoti2['identificador'],"int"),
					   GetSQLValueString($_POST['identificador'],"text"),
                       GetSQLValueString($row_rsCoti2['formapago'],"text"),
                       GetSQLValueString($row_rsCoti2['moneda'],"text"),
                       GetSQLValueString($row_rsCoti2['vigencia'],"text"),
                       GetSQLValueString($row_rsCoti2['tipoentrega'],"text"),
                       GetSQLValueString($row_rsCoti2['garantia'],"text"),
					   GetSQLValueString($_POST['estado'],"int"),
					   GetSQLValueString($row_rsCoti2['nombre'],"text"),
					   GetSQLValueString($row_rsCoti2['contacto'],"int"),
					   GetSQLValueString($row_rsCoti2['tipo_cambio'],"double"),
					   GetSQLValueString($row_rsCoti2['notas'],"text"),
					   GetSQLValueString($_SESSION['MM_Userid'],"int"),
					   GetSQLValueString($row_rsCoti2['utilidad_global'],"double"),
					   GetSQLValueString($row_rsCoti2['tipo'],"int"),
					   GetSQLValueString($row_rsCoti2['descrimano'],"text"),
					   GetSQLValueString($row_rsCoti2['monto'],"double"),
					   GetSQLValueString($row_rsCoti2['descuento'],"double"),
					   GetSQLValueString($row_rsCoti2['cantidad'],"int"),
					   GetSQLValueString($row_rsCoti2['unidad'],"text"),
					   GetSQLValueString($row_rsCoti2['codigo'],"text"),
					   GetSQLValueString($row_rsCoti2['marca'],"text"),
					   GetSQLValueString($row_rsCoti2['montoreal'],"double"),
					   GetSQLValueString($row_rsCoti2['iva'],"double"));
mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

$idsub = mysql_insert_id();
 
 mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos = sprintf("
    SELECT 
        *,
        articulo.idarticulo as ida,
        precio_cotizacion as pc,
        suba.moneda AS monedasubcotizacion 
    FROM 
        subcotizacion sub,
        subcotizacionarticulo suba, 
        articulo 
    WHERE 
        sub.idsubcotizacion=suba.idsubcotizacion and 
        suba.idarticulo=articulo.idarticulo and 
        suba.idsubcotizacion=%s 
    ORDER BY 
        suba.idsubcotizacionarticulo ASC", 
    $_POST['idsubcotizacion']);
$RsArticulos = mysql_query($query_RsArticulos, $tecnocomm) or die(mysql_error());
$row_RsArticulos = mysql_fetch_assoc($RsArticulos);
$totalRows_RsArticulos = mysql_num_rows($RsArticulos);

do{
 $insertSQL = sprintf("INSERT INTO subcotizacionarticulo (idsubcotizacion, idarticulo, precio_cotizacion, cantidad, utilidad,descri,mo,moneda,marca1,tipo_cambio,reall) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($idsub,"int"),
                       GetSQLValueString($row_RsArticulos['idarticulo'],"int"),
                       GetSQLValueString($row_RsArticulos['pc'],"double"),
                       GetSQLValueString($row_RsArticulos['cantidad'],"double"),
                       GetSQLValueString($row_RsArticulos['utilidad'],"double"),
					   GetSQLValueString($row_RsArticulos['descri'],"text"),
					   GetSQLValueString($row_RsArticulos['mo'],"double"),
					   GetSQLValueString($row_RsArticulos['monedasubcotizacion'],"int"),
					   GetSQLValueString($row_RsArticulos['marca1'],"text"),
					   GetSQLValueString($row_RsArticulos['tipo_cambio'],"double"),
					   GetSQLValueString($row_RsArticulos['reall'],"double"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error()." error en insertar articulos");

}while($row_RsArticulos = mysql_fetch_assoc($RsArticulos));
 
  $insertGoTo = "cotizacion.detalle.ip.php?idsubcotizacion=".$idsub."&idip=".$row_rsCoti2['idip'];
  if (isset($_SERVER['QUERY_STRING'])) {
    //$insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    //$insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if($_GET['type']==3){
	$var = " and estado>5 ";
	}

$colname_rsCoti = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsCoti = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCoti = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_rsCoti, "int"));
$rsCoti = mysql_query($query_rsCoti, $tecnocomm) or die(mysql_error());
$row_rsCoti = mysql_fetch_assoc($rsCoti);
$totalRows_rsCoti = mysql_num_rows($rsCoti);

$colname_rsCoti2 = "-1";
if (isset($row_rsCoti['idcotizacion'])) {
  $colname_rsCoti2 = $row_rsCoti['idcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCoti2 = sprintf("SELECT * FROM subcotizacion WHERE idcotizacion = %s %s", GetSQLValueString($colname_rsCoti2, "int"),$var);
$rsCoti2 = mysql_query($query_rsCoti2, $tecnocomm) or die(mysql_error());
$row_rsCoti2 = mysql_fetch_assoc($rsCoti2);
$totalRows_rsCoti2 = mysql_num_rows($rsCoti2);

if($_GET['type']==1){
	$cad=$row_rsCoti['identificador2'];
	$c=$row_rsCoti['identificador2'];
	$cad=strrchr($cad,'-');
	if((substr($cad,1,1)=='0')and ($totalRows_rsCoti2==0)){$cad2="-A".$cad;}
	else{
		$n=65+$totalRows_rsCoti2-1;
		$cad2="-".chr($n).$cad;
	}
	
	$l=strlen($row_rsCoti['identificador']);
	$sa="";
	if($l==1){$sa="00";}

	if($l==2){$sa="0";}
	
	$identi="C-".$sa.$row_rsCoti['identificador'].$cad2;
	$estado=1;
}

if($_GET['type']==2){
	$identi=$row_rsCoti['identificador2']."-Conc";
	$estado=6;
	}
	
if($_GET['type']==3){
	$cad=$row_rsCoti['identificador2'];
	if($totalRows_rsCoti2==0){$cad1=$cad."-A";}
	else{ if ($totalRows_rsCoti2>=0){
		$n=65+$totalRows_rsCoti2-1;
		$cad1=$cad."-".chr($n);
	}
	}
	$identi = $cad1;
	$estado=6;
	}	
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
<h1>Subcotizaciones</h1>
Cotizacion Original:<?php echo $row_rsCoti['identificador2']; ?><br />
Subcotizacion por Generar:<span class="realce"><?php echo $identi;?></span><br />
Es correcto?<br /><br />
   
<form action="<?php echo $editFormAction; ?>" method="POST" name="guardar">
<div class="botones">
<button type="submit" class="button"><span>Aceptar</span></button>

<button type="button" class="button" onclick="window.close()"><span>Cancelar</span></button>
  </div>
<input type="hidden" name="identificador" value="<?php echo $identi;?>"/>
<input type="hidden" name="estado" value="<?php echo $estado;?>"/>
<input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion'];?>"/>
<input type="hidden" name="MM_insert" value="guardar" />
</form>
</body>
</html>
<?php
mysql_free_result($rsCoti);

mysql_free_result($rsCoti2);
?>
