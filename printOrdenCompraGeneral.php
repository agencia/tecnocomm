<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>

<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<table width="700" border="0" cellpadding="0" cellspacing="0" class="resaltarTabla" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="20" colspan="5" valign="top" class="realte">Vista de Impresion de Orden de Compra:</td>
  </tr>
  <tr>
    <td width="118" height="14"></td>
    <td width="111"></td>
    <td width="160"></td>
    <td width="187"></td>
    <td width="122"></td>
  </tr>
  
  
  
  <tr>
    <td height="20" valign="top"><a href="enviarOrdenXmail.php?idordencompra=<?php echo $_GET['idordencompra']?>">Enviar Por Mail</a></td>
    <td valign="top"><a href="PrintOrdenCompraPDF.php?idordencompra=<?php echo $_GET['idordencompra']?>">Ver  PDF</a></td>
    <td valign="top"><a href="descargarOrdenPDF.php?idordencompra=<?php echo $_GET['idordencompra']?>">Descargar  PDF</a></td>
    <td valign="top"><a href="ordenCompraModificarDatos.php?idordencompra=<?php echo $_GET['idordencompra']?>">Datos De Orden de Compra</a></td>

  </tr>
</table>
</body>
</html>
