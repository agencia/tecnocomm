<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Documento sin título</title>
<script src="js/jquery.js" language="javascript"> </script>
<script language="javascript">

$(document).ready(function(){
					$(".ocultar").click(function(){
										
											id = $(this).attr('id');
											
											if($("#detalle"+id).css('display') == "none"){
												$("#detalle"+id).css("display","block");
											}else{
											$("#detalle"+id).css("display","none");
											}
											
											
											})		   
});

</script>

<style>

.ocultar tbody{
	display:none;
}

</style>
</head>
<body>
<table>
<thead>
<tr><td>Partida</td><td>Marca</td><td>Cantidad Instalada</td><td>Avance</td></tr>
</thead>
<tbody>
<tr>
<td colspan="4">
<table class="ocultar" id="table1">
<thead><tr><td>partida 1</td><td>marca 1</td><td>cantidad 1</td><td>avance1</td></tr></thead>
<tbody id="detalletable1"><tr><td colspan="4">detalle</td></tr></tbody></table>
</td></tr>
<tr>
<td colspan="4">
<table class="ocultar" id="table2">
<thead><tr><td>partida 1</td><td>marca 1</td><td>cantidad 1</td><td>avance1</td></tr></thead>
<tbody id="detalletable2"><tr><td colspan="4">detalle</td></tr></tbody></table>
</td></tr>
<tr>
<td colspan="4">
<table class="ocultar" id="table3">
<thead><tr><td>partida 1</td><td>marca 1</td><td>cantidad 1</td><td>avance1</td></tr></thead>
<tbody id="detalletable3"><tr><td colspan="4">detalle</td></tr></tbody></table>
</td></tr>
<tr>
<td colspan="4">
<table class="ocultar" id="table4">
<thead><tr><td>partida 1</td><td>marca 1</td><td>cantidad 1</td><td>avance1</td></tr></thead>
<tbody id="detalletable4"><tr><td colspan="4">detalle</td></tr></tbody></table>
</td></tr>

</tbody>
</table>


</body>
</html>