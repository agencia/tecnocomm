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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevoAvance")) {
  $insertSQL = sprintf("INSERT INTO partidaextra (idarticulo, cantidad_a, comentario, idsubcotizacion, fecha) VALUES (%s, %s, %s, %s, now())",
                       GetSQLValueString($_POST['idarticulo'], "int"),
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['comentario'], "text"),
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_RsArt = "-1";
if (isset($_GET['idart'])) {
  $colname_RsArt = $_GET['idart'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArt = sprintf("SELECT * FROM articulo WHERE idarticulo = %s", GetSQLValueString($colname_RsArt, "int"));
$RsArt = mysql_query($query_RsArt, $tecnocomm) or die(mysql_error());
$row_RsArt = mysql_fetch_assoc($RsArt);
$totalRows_RsArt = mysql_num_rows($RsArt);
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
<h1>Partida Extra</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoAvance" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Partida: <?php echo $row_RsArt['nombre']; ?></label>

<label>Marca:<?php echo $row_RsArt['marca']; ?></label>
<label>Medida:<?php echo $row_RsArt['medida']; ?></label>

<label>Fecha:
 <?php echo date("d-m-Y");?>
</label>



<label>Candidad a Instalar:
<input type="text" name="cantidad" id="cantidad" class="requerido" value="" />
</label>



</div>
<div>
<h3>Comentarios</h3>
<label>Comentario Justificativo de Partida Extra:
  <textarea name="comentario" cols="25" rows="" class="requerido"></textarea>
</label>
</div>

<div>
<label>
<input type="submit" value="Aceptar" />
</label>
</div>
<input type="hidden" name="idarticulo" value="<?php echo $_GET['idart']?>"/>
<input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion']?>"/>
<input type="hidden" name="MM_insert" value="nuevoAvance" />
</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsArt);
?>