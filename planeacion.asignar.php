<?php require_once('Connections/tecnocomm.php');
session_start();

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addEvento")) {

	if(isset($_POST['fecha'])  && $_POST['fecha'] != ""){

		$err_insert = false;
		if ($_POST['tipoelemento'] == 0) {
		$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, idip, 
												   idcotizacion, idtareaanterior)
											 VALUES(%s, %s, NOW(), 0, %s, %s, %s)",
											 GetSQLValueString($_POST['idjunta'],"int"),
											 GetSQLValueString($_POST['fecha'],"date"),
											 GetSQLValueString($_POST['idip'],"int"),
											 GetSQLValueString($_POST['valorreferencia'],"int"),
											 GetSQLValueString($_POST['idtarea'],"int"));
		} elseif ($_POST['tipoelemento'] == 1) {
		$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, idip, 
												   idlevantamiento, idtareaanterior)
											 VALUES(%s, %s, NOW(), 0, %s, %s, %s)",
											 GetSQLValueString($_POST['idjunta'],"int"),
											 GetSQLValueString($_POST['fecha'],"date"),
											 GetSQLValueString($_POST['idip'],"int"),
											 GetSQLValueString($_POST['valorreferencia'],"int"),
											 GetSQLValueString($_POST['idtarea'],"int"));
		} elseif ($_POST['tipoelemento'] == 2) {
					$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, idip, 
												   idordenservicio, idtareaanterior)
											 VALUES(%s, %s, NOW(), 0, %s, %s, %s)",
											 GetSQLValueString($_POST['idjunta'],"int"),
											 GetSQLValueString($_POST['fecha'],"date"),
											 GetSQLValueString($_POST['idip'],"int"),
											 GetSQLValueString($_POST['valorreferencia'],"int"),
											 GetSQLValueString($_POST['idtarea'],"int"));
		} elseif ($_POST['tipoelemento'] == 4) {
			if ($_POST['ip'] > 0) {
				$ip = $_POST['ip'];
			} else {
				$ip = "NULL";
			}
		$query_insert = sprintf("INSERT INTO tarea(idip, idjunta, fecharealizar, fechaasigno, estado, idtareaanterior, administrativo)
											 VALUES(%s, %s, %s, NOW(), 0, %s, %s)",
											 $ip,
											 GetSQLValueString($_POST['idjunta'],"int"),
											 GetSQLValueString($_POST['fecha'],"date"),
											 GetSQLValueString($_POST['idtarea'],"int"),
											 GetSQLValueString($_POST['comentario'],"text"));
		} elseif ($_POST['tipoelemento'] == 5) {
				$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, idip, 
												   idfactura, idtareaanterior)
											 VALUES(%s, %s, NOW(), 0, %s, %s, %s)",
											 GetSQLValueString($_POST['idjunta'],"int"),
											 GetSQLValueString($_POST['fecha'],"date"),
											 GetSQLValueString($_POST['idip'],"int"),
											 GetSQLValueString($_POST['valorreferencia'],"int"),
											 GetSQLValueString($_POST['idtarea'],"int"));
		} elseif ($_POST['tipoelemento'] == 6) {
		$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, idip, 
												   idcuentaporpagar, idtareaanterior)
											 VALUES(%s, %s, NOW(), 0, %s, %s, %s",
											 GetSQLValueString($_POST['idjunta'],"int"),
											 GetSQLValueString($_POST['fecha'],"date"),
											 GetSQLValueString($_POST['idip'],"int"),
											 GetSQLValueString($_POST['valorreferencia'],"int"),
											 GetSQLValueString($_POST['idtarea'],"int"));
		}
		mysql_select_db($database_tecnocomm,$tecnocomm);
		$rs_insert = mysql_query($query_insert,$tecnocomm) or ($err_insert = true);
		$idtarea = mysql_insert_id();
		$query = sprintf("INSERT INTO tarea_comentarios (idTarea, idUsuario, comentario, fecha) VALUES (%s, %s, '%s', NOW())", $idtarea, $_SESSION['MM_Userid'], $_POST['comentario']);
		mysql_query($query,$tecnocomm) or ($err_insert = true);
		if(!$err_insert){
			//ASIGNAR USUARIOS A TAREA
			if(is_array($_POST['usuarios']))
			  foreach($_POST['usuarios'] as $usuario){
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_usuario(idtarea, idusuario) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					}
			
			if(is_array($_POST['subcontratistas']))
			  foreach($_POST['subcontratistas'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_subcontratista(idtarea, idsubcontratista) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);

					}
		}
	}
	header("Location: close.php");
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuarios = "SELECT * FROM usuarios WHERE activar = 1 ORDER BY username ASC";
$rsUsuarios = mysql_query($query_rsUsuarios, $tecnocomm) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsSubcontratistas = "SELECT * FROM subcontratistas ORDER BY abreviacion ASC";
$rsSubcontratistas = mysql_query($query_rsSubcontratistas, $tecnocomm) or die(mysql_error());
$row_rsSubcontratistas = mysql_fetch_assoc($rsSubcontratistas);
$totalRows_rsSubcontratistas = mysql_num_rows($rsSubcontratistas);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Asignar Tarea</title>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jqueryui.js"></script>
<script language="javascript" type="text/javascript" src="js/planeacion.junta2.js"></script>
<script language="javascript" type="text/ecmascript" src="js/funciones.js"></script>
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css" />
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="wrapper">
<h1>Asignar</h1>
<form action="<?php echo $editFormAction; ?>" name="addEvento" id="addEvento" method="POST">
<table width="100%">
<tr>
<td valign="top" style="font-size:14px;">
<h3>Empleados</h3>
<ul style="list-style:none;">
  <?php do { ?>
    <li><label><input type="checkbox"  name="usuarios[]" value="<?php echo $row_rsUsuarios['id']; ?>"/><?php echo $row_rsUsuarios['username']; ?></label></li>
    <?php } while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios)); ?>
</ul>
<?php if ($totalRows_rsSubcontratistas > 0) { ?>
<h3>Subcontratistas</h3>
<ul style="list-style:none;">
	<?php do { ?>
	  <li>
	    <label><input type="checkbox" name="subcontratistas[]" value="<?php echo $row_rsSubcontratistas['id']; ?>" /><?php echo $row_rsSubcontratistas['abreviacion']; ?></label>
	    </li>
	  <?php } while ($row_rsSubcontratistas = mysql_fetch_assoc($rsSubcontratistas)); ?>
</ul>
<?php } ?><?php if ($_GET['tipoelemento'] == 4) { ?>
<label>IP <input type="text" name="ip" size="20" /></label>
<?php } ?>
</td>
<td valign="top"  style="font-size:14px;"><textarea style="width:600px;height:400px" name="comentario" id="comentarioadd"></textarea></td>
<td valign="top"  style="font-size:14px;"><div id="calendario">
</div><br /><input type="submit" value="Asignar" />
</td>
</tr>
</table>
<input type="hidden" name="fecha" value="<?php echo date("Y/m/d");?>" id="fechadest" class="fechadest"/>
<input type="hidden" name="idjunta" value="<?php echo $_GET['idjunta'];?>" >
<input type="hidden" name="valorreferencia" id="valorreferencia" value="<?php echo $_GET['valorreferencia'];?>" />
<input type="hidden" name="idip" id="idip" value="<?php echo $_GET['idip'];?>" />
<input type="hidden" name="tipoelemento" id="tipoelemento" value="<?php echo $_GET['tipoelemento'];?>" />
<input type="hidden" name="MM_insert" value="addEvento" />
</form>

</div>
</body>
</html>
<?php
mysql_free_result($rsUsuarios);
?>
