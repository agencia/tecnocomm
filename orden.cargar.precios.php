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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
	 $updateSQL3 = sprintf("UPDATE ordenservicio SET  moneda=%s, tipo_cambio=%s, utilidad=%s, iva=%s WHERE idordenservicio=%s",
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['tipo_cambio'], "double"),
                       GetSQLValueString($_POST['utilidad'], "double"),
                       GetSQLValueString($_POST['iva'], "double"),
                       GetSQLValueString($_POST['idordenservicio'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result13 = mysql_query($updateSQL3, $tecnocomm) or die(mysql_error());
	
	
	
	
  $updateSQL = sprintf(" UPDATE ordenservicio o,ordenservicio_detalle ord,articulo a SET ord.precio = a.precio,ord.moneda = a.moneda,ord.utilidad = o.utilidad,ord.mano_obra = a.instalacion WHERE o.idordenservicio =%s AND o.idordenservicio = ord.idordenservicio AND ord.idarticulo = a.idarticulo ",GetSQLValueString($_POST['idordenservicio'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
  
  
  mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenDetalle = sprintf("SELECT * FROM ordenservicio_detalle WHERE idordenservicio = %s", GetSQLValueString($_POST['idordenservicio'], "int"));
$rsOrdenDetalle = mysql_query($query_rsOrdenDetalle, $tecnocomm) or die(mysql_error());
$row_rsOrdenDetalle = mysql_fetch_assoc($rsOrdenDetalle);
$totalRows_rsOrdenDetalle = mysql_num_rows($rsOrdenDetalle);
  
  $suma=0;
  do{
	  $suma+=$row_rsOrdenDetalle['mano_obra'];
	  }while($row_rsOrdenDetalle = mysql_fetch_assoc($rsOrdenDetalle));
  
  
   $updateSQL = sprintf(" UPDATE ordenservicio o SET manoobra = %s WHERE o.idordenservicio =%s ",GetSQLValueString($suma, "double"),GetSQLValueString($_POST['idordenservicio'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsOrdenDetalle = "-1";
if (isset($_GET['idordenservicio'])) {
  $colname_rsOrdenDetalle = $_GET['idordenservicio'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenDetalle = sprintf("SELECT * FROM ordenservicio_detalle WHERE idordenservicio = %s", GetSQLValueString($colname_rsOrdenDetalle, "int"));
$rsOrdenDetalle = mysql_query($query_rsOrdenDetalle, $tecnocomm) or die(mysql_error());
$row_rsOrdenDetalle = mysql_fetch_assoc($rsOrdenDetalle);
$totalRows_rsOrdenDetalle = mysql_num_rows($rsOrdenDetalle);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Cargar Precios </title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Cargar Precios</h1>
<div id="myform">
<form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
<div>
<h3>Informacion</h3>
<label>Total de datos a cargar:<?php echo $totalRows_rsOrdenDetalle ?></label>
</div>
<div>
<h3>Valores</h3>
<label>Moneda:
  <select name="moneda">
    <option value="0" >Pesos</option>
    <option value="1" >Dolares</option>
  </select></label>
  <label>Tipo Cambio:
  <input name="tipo_cambio" type="text"  />
</label>
<label>IVA:
  <input name="iva" type="text" value="16"  />
</label>
<label>Utilidad:
  <input name="utilidad" type="text" value="1"  />
</label>

</div>
<div class="button">
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="idordenservicio" value="<?php echo $_GET['idordenservicio']?>"/>
<input type="hidden" name="MM_update" value="form1" />
</form>
</div>
</body>
</html>
<?php
@mysql_free_result($rsOrdenDetalle);

@mysql_free_result($rsOrden);
?>
