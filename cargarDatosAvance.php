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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "nuevoAvance")) {

$ide_RsArt = "-1";
if (isset($_POST['idsubcotizacion'])) {
  $ide_RsArt = $_POST['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArt = sprintf("select *, (select sum(cantidad) as suma  from subcotizacionavance where idarticulo=idsubcotizacionarticulo)as rea from subcotizacionarticulo where idsubcotizacion=%s", GetSQLValueString($ide_RsArt, "int"));
$RsArt = mysql_query($query_RsArt, $tecnocomm) or die(mysql_error());
$row_RsArt = mysql_fetch_assoc($RsArt);
$totalRows_RsArt = mysql_num_rows($RsArt);

//echo $query_RsArt;
do{
  $updateSQL = sprintf("UPDATE subcotizacionarticulo SET reall=%s, modd=1 WHERE idsubcotizacion=%s and idarticulo=%s and idsubcotizacionarticulo in (select idarticulo  from subcotizacionavance)",
                       GetSQLValueString($row_RsArt['rea'], "double"),
                       GetSQLValueString($_POST['idsub'], "int"),
					   GetSQLValueString($row_RsArt['idarticulo'],"int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
}while($row_RsArt = mysql_fetch_assoc($RsArt));


  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$ide_RsRepor = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $ide_RsRepor = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsRepor = sprintf("select * from subcotizacionarticulo sb,subcotizacionavance sa where sb.idsubcotizacionarticulo=sa.idarticulo  and sb.idsubcotizacion=%s group by sa.idarticulo", GetSQLValueString($ide_RsRepor, "int"));
$RsRepor = mysql_query($query_RsRepor, $tecnocomm) or die(mysql_error());
$row_RsRepor = mysql_fetch_assoc($RsRepor);
$totalRows_RsRepor = mysql_num_rows($RsRepor);

$ide2_RsCoti = "-1";
if (isset($_GET['idsub'])) {
  $ide2_RsCoti = $_GET['idsub'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCoti = sprintf("Select * from subcotizacionarticulo where idsubcotizacion=%s", GetSQLValueString($ide2_RsCoti, "int"));
$RsCoti = mysql_query($query_RsCoti, $tecnocomm) or die(mysql_error());
$row_RsCoti = mysql_fetch_assoc($RsCoti);
$totalRows_RsCoti = mysql_num_rows($RsCoti);

$colname_RsExtra = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_RsExtra = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsExtra = sprintf("SELECT * FROM partidaextra WHERE idsubcotizacion = %s", GetSQLValueString($colname_RsExtra, "int"));
$RsExtra = mysql_query($query_RsExtra, $tecnocomm) or die(mysql_error());
$row_RsExtra = mysql_fetch_assoc($RsExtra);
$totalRows_RsExtra = mysql_num_rows($RsExtra);

$colname_RsOriginal = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_RsOriginal = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsOriginal = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_RsOriginal, "int"));
$RsOriginal = mysql_query($query_RsOriginal, $tecnocomm) or die(mysql_error());
$row_RsOriginal = mysql_fetch_assoc($RsOriginal);
$totalRows_RsOriginal = mysql_num_rows($RsOriginal);

$colname_RsGenerada = "-1";
if (isset($_GET['idsub'])) {
  $colname_RsGenerada = $_GET['idsub'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsGenerada = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_RsGenerada, "int"));
$RsGenerada = mysql_query($query_RsGenerada, $tecnocomm) or die(mysql_error());
$row_RsGenerada = mysql_fetch_assoc($RsGenerada);
$totalRows_RsGenerada = mysql_num_rows($RsGenerada);



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Avance</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
<script language="javascript">

function confirmar(elementName,elementValueOld,elementValueNew){

if(elementValueNew>elementValueOld){

if(!confirm("Usted A Elegido agregar mayor cantidad a la propuesta debe justificar ese material!!!, \nConfirme Por Favor, \n Valor Orginial: "+elementValueOld+" \nValor Nuevo: "+elementValueNew)){
	
	document.getElementById(elementName).value =  elementValueOld;

}else{
	document.getElementById(elementName).value =  elementValueNew;

}
}


}
</script>
</head>

<body>
<h1>Cargar Datos de Reporte de Avance</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoAvance" method="POST">
<div>
<h3>Datos de Partidas</h3>
<label>Total de Partidas Reportadas:<?php echo $totalRows_RsRepor ?> </label>

<label>Total de Partidas Cotizadas:<?php echo $totalRows_RsCoti ?> </label>
<label>Total de Partidas Extra por Agregar:<?php echo $totalRows_RsExtra ?> </label>



</div>
<div>
<h3>Datos Adicionales</h3>
<label>Proyecto Original: <?php echo $row_RsOriginal['identificador2']; ?></label>

<label>Proyecto Conciliado: <?php echo $row_RsGenerada['identificador2']; ?></label>


<label>ESTAS SEGURO QUE DESEAS CARGAR LOS DATOS A ESTA CONCILIACION DE LO REPORTADO EN EL AVANCE DEL PROYECTO?</label>
</div>

<div>
<label>
<input type="submit" value="Aceptar" />
</label>
<label>
<input type="button" value="Cancelar" onclick="window.close();" />
</label>
</div>
<input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion']?>"/>
<input type="hidden" name="idsub" value="<?php echo $_GET['idsub']?>"/>

<input type="hidden" name="xx" value="2"/>
<input type="hidden" name="MM_update" value="nuevoAvance" />
</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsRepor);

mysql_free_result($RsCoti);

mysql_free_result($RsExtra);

mysql_free_result($RsOriginal);

mysql_free_result($RsGenerada);
?>

