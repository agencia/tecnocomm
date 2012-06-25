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

if($_POST['confirmar'] == "true"){
	
//verificar codigo de seguridad
mysql_select_db($database_tecnocomm,$tecnocomm);
$sql = sprintf("SELECT *  FROM usuarios WHERE id=%s AND password=%s AND activar=1",
			   		GetSQLValueString($_POST['recibe'],"int"),
					GetSQLValueString($_POST['codigo'],"text"));

$rsValid = mysql_query($sql,$tecnocomm) or die(mysql_error());

if(mysql_num_rows($rsValid) == 1){
	//codigo aceptado
	
	//dar como cerrada la entrega y cambiar de numero
	mysql_select_db($database_tecnocomm,$tecnocomm);
	$updateSQL = sprintf("UPDATE ip SET numero=%s WHERE idip = %s",
						 			GetSQLValueString($_POST['numero'] + 1,"text"),
									GetSQLValueString($_POST['idip'],"text"));
	
	$rsUpdate = mysql_query($updateSQL,$tecnocomm) or die(mysql_error());
		
	header("Location: close.php");
}else{
	$msj = "El Codigo no corresponde con el usuario";
	}


}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPersonal = "SELECT * FROM usuarios ORDER BY username ASC";
$rsPersonal = mysql_query($query_rsPersonal, $tecnocomm) or die(mysql_error());
$row_rsPersonal = mysql_fetch_assoc($rsPersonal);
$totalRows_rsPersonal = mysql_num_rows($rsPersonal);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Recibir Mercancia</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Confirmar Material Recibido</h1>
<?php include("ip.encabezado.php");?>


<?php if($_GET['num'] == $row_rsEncabezado['numero']){ 
$permitir = true;
}
?>

<?php if($_GET['num'] == 2 && $row_rsEncabezado['numero'] == 0){ 
$permitir = true;
}
?>

<?php if($_GET['num'] == 1 && $row_rsEncabezado['numero'] == 0){ 
$permitir = true;
}
?>
<?php if($permitir == false){?>

<p> El numero de solicitud o entrega esta cerrado </p>

<?php } ?>

<?php if($permitir == true){?>

<p><?php echo $msj;?></p>
<p>Esta accion dara como cerrada la entrega y no se podan reaizar modificaciones. </p>
<form name="confirmar" method="post" id="myform">
<div>
<h3>
Quien Recibe:
</h3>
<label>
<select name="recibe">
  <?php
do {  
?>
  <option value="<?php echo $row_rsPersonal['id']?>"><?php echo $row_rsPersonal['username']?></option>
  <?php
} while ($row_rsPersonal = mysql_fetch_assoc($rsPersonal));
  $rows = mysql_num_rows($rsPersonal);
  if($rows > 0) {
      mysql_data_seek($rsPersonal, 0);
	  $row_rsPersonal = mysql_fetch_assoc($rsPersonal);
  }
?>
</select>
</label>
<label>
Codigo Seguridad:
<input type="password" name="codigo"  />
</label>
</div>
<div class="botones">
<button type="submit"><span>Aceptar</span></button>
</div>
<input type="hidden" name="numero" value="<?php echo $_GET['num'];?>" />
<input type="hidden" name="idip" value="<?php echo $_GET['idip']?>" />
<input type="hidden" name="confirmar" value="true" />
</form>
</body>
</html>
<?php } ?>
<?php
mysql_free_result($rsPersonal);
?>
