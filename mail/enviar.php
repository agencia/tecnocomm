<?php require_once('../Connections/papsystem.php'); ?>
<?php require_once('mail.php');
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


mysql_select_db($database_papsystem, $papsystem);
$query_rsEnviar = "SELECT me.idenvio,mc.correo AS toaddress,mcd.subject,mcd.contenido AS body,mc.nombre FROM mail_enviar me, mail_contenido mcd,mail_correos mc,mail_envios mev WHERE mc.idcorreo = me.idcorreo AND me.identificadorenvio = mev.id AND mev.idmensaje = mcd.idcontenido AND me.estado=0";
$rsEnviar = mysql_query($query_rsEnviar, $papsystem) or die(mysql_error());
$row_rsEnviar = mysql_fetch_assoc($rsEnviar);
$totalRows_rsEnviar = mysql_num_rows($rsEnviar);


if($totalRows_rsEnviar > 0){

//generar body
		
		$body = "<br> <img src=\"http://papresponder.papcenter.com.mx/rastreo/".$row_rsEnviar['idenvio'].".jpg\">";
		
		
		//enviar Correo
		 
		 $mail = new Correo();
		 $mail->to = $row_rsEnviar['toaddress'];
		 $mail->subject = $row_rsEnviar['subject'];
		 $mail->body = $row_rsEnviar['body'].$body;
		 $mail->from = "leury@papcenter.com.mx";
		 $result = $mail->enviar();
		
		if($result == 1){
			$estado = 1;
			echo "1".date("Y/m/d H:i:s")."|Correo Enviado A:| ".$row_rsEnviar['toaddress'];
		}else{
			$estado = 3;
			echo "3".date("Y/m/d H:i:s")."Error Al Enviar Correo A: ".$row_rsEnviar['toaddress'];
		}
		mysql_select_db($database_papsystem, $papsystem);
		$query_rsEnviar = sprintf("UPDATE mail_enviar SET estado = %s WHERE idenvio = %s",$estado,$row_rsEnviar['idenvio']);
		$rsEnviar = mysql_query($query_rsEnviar, $papsystem) or die(mysql_error());

}else{
		sleep(600);
}
?>