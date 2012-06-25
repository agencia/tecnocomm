<?php require_once('Connections/tecnocomm.php'); 
session_start(); ?>
<?php require_once('utils.php'); ?>
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
//print_r($_POST);

if(isset($_POST['idtarea']) && $_POST['idtarea'] != ''){
									
/*
	$query_tarea = sprintf('SELECT *  FROM tarea WHERE idtarea = %s',
						   GetSQLValueString($_POST['idtarea'],"int"));
	mysql_select_db($database_tecnocomm,$tecnocomm);
	$rs_tarea = mysql_query($query_tarea, $tecnocomm) or die(mysql_error());
	
	$row_tarea = mysql_fetch_assoc($rs_tarea);
	*/
	//echo $row_tarea['comentario']."<br> json: <br>";	
	if ($_POST['estado'] != 0) {
	switch($_POST['estado']){
			case 0:
				$edo = " fecharealizar = '". $_POST['fecha'] . "' ";
				break;
			case 1:
				$edo = " fecharealizo = NOW()";
				break;
			case 2:
				$edo = " fechaveri = NOW()";
				break;
			case 3:
			
				$edo = " fecharealizar = '". $_POST['fecha'] . "', fecharealizo = NULL, fechaveri = NULL";
				break;
		}
	$query_update = sprintf("UPDATE tarea SET %s, estado=%s  WHERE idtarea = %s ", $edo,
							GetSQLValueString($_POST['estado'],"int"),
							GetSQLValueString($_POST['idtarea'],"int"));
	mysql_select_db($database_tecnocomm,$tecnocomm);
	$rs_update = mysql_query($query_update,$tecnocomm) or die(mysql_error() . "<br />Query: " . $query_update);
	}
		$query = sprintf("INSERT INTO tarea_comentarios (idTarea, idUsuario, comentario, fecha) VALUES (%s, %s, '%s', NOW())", $_POST['idtarea'], $_POST['idusuario'], $_POST['comentario']);
		mysql_query($query,$tecnocomm) or ($err_insert = true);
	//echo $query_update;
	
	if (($_POST['estado'] == 3) && (isset($_POST['usuariosm']))) {
		$query = sprintf("DELETE FROM tarea_usuario WHERE idtarea = %s", $_POST['idtarea']);
		mysql_query($query,$tecnocomm) or (die(mysql_error()));
		foreach($_POST['usuariosm'] as $usuario){
			$query_insert = sprintf("INSERT INTO tarea_usuario(idtarea, idusuario) VALUES(%s, %s)",
				GetSQLValueString($_POST['idtarea'],"int"),
				GetSQLValueString($usuario,"int"));
			mysql_select_db($database_tecnocomm,$tecnocomm);
			$rs_insert = mysql_query($query_insert,$tecnocomm);
		}
	}
}

$estadotareas = array(0=>"",1=>"realizado",2=>"finalizado",3=>"reasignada");

?>
<script>
window.opener.refrescaTarea(<?php echo $_POST['idtarea']; ?>, '<?php echo $estadotareas[$_POST['estado']]; ?>');
window.close();
</script>