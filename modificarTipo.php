<?php $tipo=array(0=>"SUMINISTRO E INSTALACION",1=>"INSTALACION GLOBAL",2=>"SOLO SUMINISTRO");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="500" border="0" align="center" class="wrapper">
  <tr>
    <td colspan="2" align="center" class="titulos">CAMBIAR TIPO DE COTIZACION </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center">TIPO ORIGEN: <?php echo $tipo[$_GET['tipo']]; ?></td>
  </tr>
  <tr>
    <td colspan="2" align="center">TIPO DESTINO: </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
</table>
</body>
</html>
