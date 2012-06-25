<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SubCotizaciones</title>
<link href="<?php echo base_url() ?>../style2.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url() ?>../css/redmond/jquery.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url() ?>../js/jquery.js" language="javascript"> </script>
<script language="javascript"  src="<?php echo base_url() ?>../js/funciones.js"></script>
<script src="<?php echo base_url() ?>../js/jqueryui.js" language="javascript"></script>
<style type="text/css">
<!--
.realce {
	color: #F00;
}
-->
</style>
</head>

<body>
<h1>Subcotizaciones(Importar Externa)</h1>
Cotizacion Original:<span class="realce"><?php echo $origen['identificador2']; ?></span><br />
Cotizacion para importar:<span class="realce"><?php echo $destino['identificador2']; ?></span><br />
Total de partidas:<span class="realce"><?php echo $partidas;?></span><br />
Es correcto?<br /><br />
   
<form action="<?php echo base_url() ?>index.php/cotizaciones/set_duplicado" method="POST" name="guardar">
<!--    <div>
        <label for="exacta">Copia exacta</label><input type="checkbox" name="exacta" id="exacta" />
    </div>-->
<div class="botones">
<button type="submit" class="button"><span>Aceptar</span></button>

<button type="button" class="button" onclick="window.close()"><span>Cancelar</span></button>
  </div>
<input type="hidden" name="idorigen" value="<?php echo $origen['idsubcotizacion'];?>"/>
<input type="hidden" name="iddestino" value="<?php echo $destino['idsubcotizacion'];?>"/>
<input type="hidden" name="idip" value="<?php echo $idip;?>"/>
</form>
<br /><br />
</body>
</html>