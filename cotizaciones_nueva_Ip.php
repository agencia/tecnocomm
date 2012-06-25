<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Crear Cotizacion</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js"></script>
<script src="js/valid.js"></script>
</head>

<body>
<h1>Crear Cotizacion</h1>
<p></p>

<div id="myform">

<form action="<?php echo $editFormAction; ?>" name="nuevoBanco" method="POST">
<div>
<h3>Ip Existente</h3>
<label>Seleccione Ip:
  <input type="text" name="clave" value=""  readonly="true" />
</label>
</div>
<div>
<h3>No Hay Ip</h3>
<a href="nuevoIp.php?mod=cotizacion">Crear Ip</a>
</div>

<div class="botones">
<input type="submit" value="Aceptar" />
</div>
<input type="hidden" name="MM_insert" value="nuevoBanco" />
</form>

</div>

</body>
</html>