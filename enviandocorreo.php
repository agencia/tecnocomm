<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Enviando Mail</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="js/codigo.js"></script>
</head>

<body onload="llamarasincronoPost('enviarcorreo.php','contenido','enviarmail')">
<form name="enviarmail" id="enviarmail">
<input type="hidden" name="idsubcotizacion" value="<?php echo $_POST['idsubcotizacion'];?>" />
<input type="hidden" name="asunto" value="<?php echo $_POST['asunto'];?>" />
<input type="hidden" name="mensaje" value="<?php echo $_POST['mensaje'];?>" accept="text/html" />
<input type="hidden" name="concopia" value="<?php echo $_POST['concopia'];?>" />
<input type="hidden" name="cc" value="<?php echo $_POST['cc'];?>" />
</form>

<div id="contenido">
<table width="462" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td width="42" height="30">&nbsp;</td>
    <td width="420">&nbsp;</td>
  </tr>
  <tr>
    <td height="40" valign="top" class="resaltarTabla"><img src="images/loader.gif" width="31" height="31" /></td>
  <td valign="middle" class="resaltarTabla">Eniando Mail, Esta Operacion Puede Tardar Varios Minutos Espere Por Favor...</td>
  </tr>
  <tr>
    <td height="22">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
</body>
</html>
