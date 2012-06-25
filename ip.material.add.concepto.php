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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "materialExtra")) {
  $insertSQL = sprintf("INSERT INTO proyecto_material_partida (idproyecto_material, descripcion, cantidad, idarticulo, pextra, marca, codigo) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['idproyecto_material'], "int"),
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['idarticulo'], "int"),
                       GetSQLValueString($_POST['pextra'], "int"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['codigo'], "text"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());

  $insertGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rs_Partida = "-1";
if (isset($_GET['idarticulo'])) {
  $colname_rs_Partida = $_GET['idarticulo'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rs_Partida = sprintf("SELECT * FROM articulo WHERE idarticulo = %s", GetSQLValueString($colname_rs_Partida, "int"));
$rs_Partida = mysql_query($query_rs_Partida, $tecnocomm) or die(mysql_error());
$row_rs_Partida = mysql_fetch_assoc($rs_Partida);
$totalRows_rs_Partida = mysql_num_rows($rs_Partida);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Agergar Partida</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Agregar Material Extra</h1>
<p>El material que agregue extra debera llevar justificacion.</p>
<form method="POST" action="<?php echo $editFormAction; ?>" name="materialExtra" id="myform">
<div>
<h3><?php echo $row_rs_Partida['nombre']; ?></h3>
<div class="botones">
<button type="submit"><span>Aceptar</span></button>
</div>
<input type="hidden" name="idarticulo" value="<?php echo $row_rs_Partida['idarticulo']; ?>" />
<input type="hidden" name="idproyecto_material"  value="<?php echo $_GET['idproyecto_material'];?>"/>
<input type="hidden" name="marca" value="<?php echo $row_rs_Partida['marca']; ?>"/>
<input type="hidden" name="codigo" value="<?php echo $row_rs_Partida['codigo']; ?>" />
<input type="hidden" name="cantidad" value="0" />
<input type="hidden" name="pextra" value="1" />
<textarea name="descripcion" style="display:none"><?php echo $row_rs_Partida['nombre']; ?></textarea>
<input type="hidden" name="MM_insert" value="materialExtra" />
</form>
</body>
</html>
<?php
mysql_free_result($rs_Partida);
?>
