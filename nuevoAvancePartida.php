<?php require_once('Connections/tecnocomm.php'); ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevoAvance")) {
  $insertSQL = sprintf("INSERT INTO subcotizacionavance (idarticulo, cantidad, comentario, fecha) VALUES (%s, %s, %s, now())",
                       GetSQLValueString($_POST['idarticulo'], "int"),
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['comentario'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$ide_RsPartida = "-1";
if (isset($_GET['idart'])) {
  $ide_RsPartida = $_GET['idart'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsPartida = sprintf("Select (select descri from subcotizacionarticulo where idsubcotizacionarticulo=sv.idarticulo) as nom, sa.cantidad as canti, sa.marca1, sum(sv.cantidad) as cant from subcotizacionavance sv,subcotizacionarticulo sa where sv.idarticulo=sa.idsubcotizacionarticulo and sv.idarticulo=%s", GetSQLValueString($ide_RsPartida, "int"));
//echo $query_RsPartida;
$RsPartida = mysql_query($query_RsPartida, $tecnocomm) or die(mysql_error());
$row_RsPartida = mysql_fetch_assoc($RsPartida);
$totalRows_RsPartida = mysql_num_rows($RsPartida);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<h1>Avance</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoAvance" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Partida:
<?php echo $row_RsPartida['nom'];?>
</label>

<label>Marca:
<?php echo $row_RsPartida['marca1'];?>
</label>

<label>Fecha:
 <?php echo date("d-m-Y");?>
</label>

<label>Cantidad instalada al momento:
<?php if($row_RsPartida['cant']!=0){echo $row_RsPartida['cant'];}else{echo '0';};?>
</label>

<label>Candidad instalada ahora:
<input type="text" name="cantidad" id="cantidad" class="requerido" value="<?php echo $row_RsPartida['canti'] - $row_RsPartida['cant'];?>" onchange="confirmar('cantidad','<?php echo $row_RsPartida['canti'] - $row_RsPartida['cant']; ?>',this.form.cantidad.value);" />
</label>



</div>
<div>
<h3>Comentarios</h3>
<label>Comentario:
  <textarea name="comentario" cols="25" rows="" class="requerido"></textarea>
</label>
</div>

<div>
<label>
<input type="submit" value="Aceptar" />
</label>
</div>
<input type="hidden" name="idarticulo" value="<?php echo $_GET['idart']?>"/>
<input type="hidden" name="MM_insert" value="nuevoAvance" />
</form>

</div>

</body>
</html>
<?php
mysql_free_result($RsPartida);
?>
