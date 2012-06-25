<?php
require_once "lib/Swift.php";
//require_once "lib/Swift/Connection/SMTP.php";
require_once "lib/Swift/Connection/NativeMail.php";

		$server  = "smtp.gmail.com";
		$pwd     = "mimetasermejorcadadia";   
		$username = "leury@leysoft.com.mx";

	
		$swift =& new Swift(new Swift_Connection_NativeMail());
   // $smtp->setUsername($username);
	//$smtp->setpassword($pwd);
	
	//$swift =& new Swift($smtp);
 
$message =& new Swift_Message("Envio de Cotizacion");
$message->attach(new Swift_Message_Part("Cotizacion Adjunta"));
$message->attach(new Swift_Message_Attachment(
  file_get_contents("http://tecnocomm.leysoft.com.mx/printCotizacionPDF.php?idsubcotizacion=138"), "cotizacion.pdf", "application/pdf"));
 
if($swift->send($message, "leury@leysoft.com.mx", "leury@leysoft.com.mx")){
	echo "Mensaje Enviado Exitosamente";
	}

?>