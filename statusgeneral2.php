<script src="js/jquery.qtip-1.0.0-rc3.min.js"></script>
<script language="javascript" type="text/javascript" src="js/statusgenerral.js"></script>
<style>
.realizado td{
	color:#F00;
}
s
.finalizado td{
	color:#00F;
	text-decoration:line-through;
}
.pendiente{
	
}
</style>
<h1>Estado Greneral</h1>
<div id="statusgeneral">
<div>
<h3><a href="#d1">Cotizaciones</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.cotizaciones.php?type=2&fecha=' + fec,this,'.ayercotizaciones')" id="buscoti"/>
</label>
<hr />
<div class="ayercotizaciones">
</div>
</div>

<h3><a href="#d1">Levantamientos</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="q" size="35" onkeyup="buscar('planeacion.levantamientos.php?type=2&fecha=' + fec,this,'.ayerlevantamientos')" id="buscoti"/>
</label>
<hr />
<div class="ayerlevantamientos">
</div>
</div>

<h3><a href="#d1">Ordenes Servicio</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="q" size="35" onkeyup="buscar('planeacion.ordenservicio.php?type=2&fecha=' + fec,this,'.ayerordenservicio')" id="buscoti"/>
</label>
<hr />
<div class="ayerordenservicio">
</div>
</div>

<h3><a href="#d1">Administrativo u Operativo</a></h3>
<div>
    <label>Buscar:<br />
    	<input type="text" name="q" size="35" onkeyup="buscar('planeacion.admin.php?type=2&fecha=' + fec,this, '.ayeradmin')" id="buscoti"/>
    </label>
    <hr />
    <div class='ayeradmin'></div>
</div>

<h3><a href="#">Facturas</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.admin.facturas.php?type=2&fecha=' +fec,this,'.ayerfacturas')" id="buscoti"/>
</label>
<hr />
<div class="ayerfacturas"></div></div>

<h3><a href="#">Cuentas Por Pagar</a></h3>
<div>
<label>Buscar:
<br />
<input type="text" name="buscar" size="35" onkeyup="buscar('planeacion.admin.cxp.php?type=2&fecha=' +fec,this,'.ayercxo')" id="buscoti"/>
</label>
<hr />
<div class="ayercxo"></div>
</div>
</div>
</div></div>
<div id="historial" title="Comentarios">
<div id="contHistorial">
</div>
</div>