<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Salida Herramienta</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script src="js/jquery.js" language="javascript"> </script>
<script language="javascript"  src="js/funciones.js"></script>
</head>

<body>
<h1>Asiganacion de Herramienta</h1>
<?php include("ip.encabezado.php");?>
<p>Modulo de asignacion de herramienta</p>
<div id="opciones"><ul><li><a href="herramienta.add.php?idip=<?php echo $_GET['idip'];?>">Agregar Herramienta</a></li></ul></div>
<div id="distabla">
<table width="100%" cellpadding="2" cellspacing="0">
<thead>
<tr><td>Clave Herramienta</td><td>Descripcion</td><td>Existencia</td><td>En Uso</td><td>Opciones</td></tr>
</thead>
</table>
</div>
</body>
</html>