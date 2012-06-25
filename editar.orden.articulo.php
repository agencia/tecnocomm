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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "nuevoProd")) {
  $updateSQL = sprintf("UPDATE ordenservicio_detalle SET descripcion=%s, marca=%s, codigo=%s, precio=%s, moneda=%s, cantidad=%s, utilidad=%s, mano_obra=%s WHERE idordenservicio_detalle=%s",
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['marca'], "text"),
                       GetSQLValueString($_POST['codigo'], "text"),
                       GetSQLValueString($_POST['precio'], "double"),
                       GetSQLValueString($_POST['moneda'], "int"),
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['utilidad'], "double"),
                       GetSQLValueString($_POST['mano_obra'], "double"),
                       GetSQLValueString($_POST['idordenservicio_detalle'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

$updateSQL = sprintf("UPDATE  ordenservicio SET idusuario = %s WHERE idordenservicio=%s", GetSQLValueString($_SESSION['MM_Userid'], "int"), GetSQLValueString($_POST['idordenservicio'], "int"));
                      

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsArticulo = "-1";
if (isset($_GET['idordenservicio_detalle'])) {
  $colname_rsArticulo = $_GET['idordenservicio_detalle'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsArticulo = sprintf("SELECT * FROM ordenservicio_detalle WHERE idordenservicio_detalle = %s", GetSQLValueString($colname_rsArticulo, "int"));
$rsArticulo = mysql_query($query_rsArticulo, $tecnocomm) or die(mysql_error());
$row_rsArticulo = mysql_fetch_assoc($rsArticulo);
$totalRows_rsArticulo = mysql_num_rows($rsArticulo);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Nuevo Banco</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Editar Porducto o Servicio </h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoProd" method="POST">
<div>
<h3>Datos Generales</h3>
<label>Descripcion:
<input name="descripcion" type="text" class="requerido" value="<?php echo $row_rsArticulo['descripcion']; ?>"  />
</label>
<label>Marca:
<input name="marca" type="text" value="<?php echo $row_rsArticulo['marca']; ?>"  />
</label>
<label>Codigo:
<input name="codigo" type="text" value="<?php echo $row_rsArticulo['codigo']; ?>"  />
</label>


</div>
<div>
<h3>Adicionales</h3>

<label>Precio Unitario:
<input name="precio" type="text" value="<?php echo $row_rsArticulo['precio']; ?>" />
<?php if($row_rsArticulo['moneda']==0){ echo "M.N.";}if($row_rsArticulo['moneda']==1){ echo "USD";} ?>&nbsp;&nbsp;<?php if($row_rsArticulo['tipo']==0){ echo "PL";}if($row_rsArticulo['tipo']==1){ echo "CO";} ?>
</label>

<label>Cantidad:
<input name="cantidad" type="text" value="<?php echo $row_rsArticulo['cantidad']; ?>" />
</label>

<label>Utilidad:
<input name="utilidad" type="text" value="<?php echo $row_rsArticulo['utilidad']; ?>" />
</label>

<label>Mano De Obra:
<input name="mano_obra" type="text" value="<?php echo $row_rsArticulo['mano_obra']; ?>" />
</label>




<label>Moneda:
<select name="moneda">
  <option value="0" <?php if (!(strcmp(0, $row_rsArticulo['moneda']))) {echo "selected=\"selected\"";} ?>>Moneda Nacional</option>
  <option value="1" <?php if (!(strcmp(1, $row_rsArticulo['moneda']))) {echo "selected=\"selected\"";} ?>>Dolares</option>
</select>
</label>
</div>

<div class="botones">
<input type="submit" value="Aceptar" />
</div>

<input type="hidden" name="idordenservicio_detalle" value="<?php echo $row_rsArticulo['idordenservicio_detalle']; ?>"/>
<input type="hidden" name="idordenservicio" value="<?php echo $row_rsArticulo['idordenservicio']; ?>"/>
<input type="hidden" name="MM_update" value="nuevoProd" />


</form>

</div>

</body>
</html>
<?php
@mysql_free_result($RsOrden);

@mysql_free_result($rsArticulo);
?>