<?php require_once('Connections/tecnocomm.php');
session_start();
$sql = sprintf("SELECT estado FROM conversacion_destinatario WHERE idconversacion = %s AND destinatario = %s", $_GET['id'], $_SESSION['MM_Userid']);
$rsConversaciones = mysql_query($sql, $tecnocomm) or die(mysql_error() . " SQL: " . $sql);
$row = mysql_fetch_array($rsConversaciones);
if ($row['estado'] == 0) {
	$sql= sprintf("UPDATE conversacion_destinatario SET estado = 1 WHERE idconversacion = %s AND destinatario = %s", $_GET['id'], $_SESSION['MM_Userid']);
	
} else if($_GET['edo'] == 1)
	$sql= sprintf("UPDATE conversacion_destinatario SET estado = 2 WHERE idconversacion = %s AND destinatario = %s", $_GET['id'], $_SESSION['MM_Userid']);
	mysql_select_db($database_tecnocomm, $tecnocomm);
	mysql_query($sql, $tecnocomm) or die(mysql_error() . " <br> SQL: " . $sql);
	header("Location: close.php");


?>