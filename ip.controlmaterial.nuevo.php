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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "nuevocontrol")) {
  $insertSQL = sprintf("INSERT INTO proyecto_material (idip, idcotizacion) VALUES (%s, %s)",
                       GetSQLValueString($_POST['idip'], "int"),
                       GetSQLValueString($_POST['idcotizacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
	
  $idproyectomaterial = mysql_insert_id();


//crear detalle de partidas para control de material
$insertSQL = sprintf("INSERT INTO proyecto_material_partida(idproyecto_material, descripcion, cantidad, idsubcotizacionarticulo, idarticulo, pextra, marca, codigo) SELECT %s, sb.descri, sb.cantidad, sb.idsubcotizacionarticulo, sb.idarticulo, 0, a.marca, a.codigo FROM subcotizacionarticulo sb LEFT JOIN articulo a ON sb.idarticulo = a.idarticulo JOIN subcotizacion s ON s.idcotizacion = %s AND s.estado = 3 AND s.idsubcotizacion = sb.idsubcotizacion",
												  GetSQLValueString($idproyectomaterial,"int"),
												  GetSQLValueString($_POST['idcotizacion'],"int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());



  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rs_Autorizadas = "-1";
if (isset($_GET['idip'])) {
  $colname_rs_Autorizadas = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rs_Autorizadas = sprintf("SELECT sb.idcotizacion, sb.identificador2 FROM subcotizacion sb, cotizacion c WHERE c.idip = %s AND c.idcotizacion = sb.idcotizacion AND sb.estado =  3", GetSQLValueString($colname_rs_Autorizadas, "int"));
$rs_Autorizadas = mysql_query($query_rs_Autorizadas, $tecnocomm) or die(mysql_error());
$row_rs_Autorizadas = mysql_fetch_assoc($rs_Autorizadas);
$totalRows_rs_Autorizadas = mysql_num_rows($rs_Autorizadas);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Documento sin título</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>Nuevo Control De Material Para Proyecto</h1>
<?php include("ip.encabezado.php");?>
<form action="<?php echo $editFormAction; ?>" name="nuevocontrol" method="POST" id="myform">
<div>
<h3>Seleccione Proyecto</h3>
<select name="idcotizacion">
  <?php
do {  
?>
  <option value="<?php echo $row_rs_Autorizadas['idcotizacion']?>"><?php echo $row_rs_Autorizadas['identificador2']?></option>
  <?php
} while ($row_rs_Autorizadas = mysql_fetch_assoc($rs_Autorizadas));
  $rows = mysql_num_rows($rs_Autorizadas);
  if($rows > 0) {
      mysql_data_seek($rs_Autorizadas, 0);
	  $row_rs_Autorizadas = mysql_fetch_assoc($rs_Autorizadas);
  }
?>
</select>
</div>
<div class="botones">
<button type="submit">Crear</button>
</div>
<input type="hidden" name="idip"  value="<?php echo $_GET['idip']?>"/>
<input type="hidden" name="MM_insert" value="nuevocontrol" />
</form>
</body>
</html>
<?php
mysql_free_result($rs_Autorizadas);
?>
