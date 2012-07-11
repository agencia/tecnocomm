<?php
require_once('Connections/tecnocomm.php'); ?>
<?php
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "guardar")) {
  $updateSQL = sprintf("UPDATE subcotizacion SET notas=%s WHERE idsubcotizacion=%s",
                       GetSQLValueString($_POST['notas'], "text"),
                       GetSQLValueString($_POST['idsubcotizacion'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

  $updateGoTo = "close.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  if ($_POST["cerrar"] == 'si')
  	header(sprintf("Location: %s", $updateGoTo));
}
?>
<?php require_once('utils.php');?>
<?php require_once('numtoletras.php');?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

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
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
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

$bus='';
if((isset($_GET['buscar']))and($_GET['buscar']!='')){

	$bus=" and (descri like '%".$_GET['buscar']."%' or marca1 like '%".$_GET['buscar']."%' or articulo.codigo like '%".$_GET['buscar']."%') ";

}

$ide_rsCotizacion = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $ide_rsCotizacion = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT *,(select nombre from contactoclientes where idcontacto=subcotizacion.contacto) as conta,contacto as con,(select telefono from contactoclientes where idcontacto=subcotizacion.contacto) as tele, (select correo from contactoclientes where idcontacto=subcotizacion.contacto) as mail FROM subcotizacion,cotizacion WHERE idsubcotizacion=%s and subcotizacion.idcotizacion=cotizacion.idcotizacion", GetSQLValueString($ide_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);

$ide_rsPartidas = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $ide_rsPartidas = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT *, suba.moneda  AS monedacotizacion, articulo.moneda as monart, articulo.tipo as tip FROM subcotizacion sub,subcotizacionarticulo suba, articulo WHERE sub.idsubcotizacion=suba.idsubcotizacion and suba.idarticulo=articulo.idarticulo and suba.idsubcotizacion=%s %s ORDER BY idsubcotizacionarticulo ASC", GetSQLValueString($ide_rsPartidas, "int"),$bus);
$rsPartidas = mysql_query($query_rsPartidas, $tecnocomm) or die(mysql_error());
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);
$totalRows_rsPartidas = mysql_num_rows($rsPartidas);

$query_rsPartidas2 = sprintf("SELECT 
    suba.descri, 
    suba.precio_cotizacion, 
    suba.cantidad,
    suba.utilidad,
    suba.mo as mano_de_obra,
    suba.moneda  AS monedacotizacion, articulo.moneda as monart, articulo.tipo as tip FROM subcotizacion sub,subcotizacionarticulo suba, articulo WHERE sub.idsubcotizacion=suba.idsubcotizacion and suba.idarticulo=articulo.idarticulo and suba.idsubcotizacion=%s %s ORDER BY idsubcotizacionarticulo ASC", GetSQLValueString($ide_rsPartidas, "int"),$bus);

//asignamos el num de partida
$k=1;
do{
	$partidas[$row_rsPartidas['idsubcotizacionarticulo']]=$k;
	$k++;
}while($row_rsPartidas = mysql_fetch_assoc($rsPartidas));
@mysql_data_seek($rsPartidas, 0);
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);

//vectores auxiliares
$tip=array(0=>"PL",1=>"C");

$signo = array(0=>"$",1=>"US$");
$suministro=array(0=>"Suministro e Instalacion",1=>"Suministro Global",2=>"Solo Suministro",3=>"Solo instalacion");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title>Presupuestos</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css" rel="stylesheet" type="text/css">
<script src="js/jquery.js" language="javascript"> </script>
<script language="javascript"  src="js/funciones.js"></script>
<script src="js/jqueryui.js" language="javascript"></script>
<script type="text/javascript">
$(function(){
	
	$("#tabs").tabs();
	
	});


</script>
</head>

<body>
<h1>Presupuesto <span class="sip"><?php echo $row_rsCotizacion['identificador2']?></span></h1>
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Datos Cotizacion</a></li>
		<li><a href="#tabs-2">Datos IP</a></li>
		
	</ul>
	<div id="tabs-1">
		<table width="80%" id="encip" cellpadding="3" cellspacing="0"  class="encabezado">
  <tr>
    <td align="right" bgcolor="#CCCCCC">Identificador:</td>
    <td><span class="sip"><?php echo $row_rsCotizacion['identificador2']?></span></td>
    <td align="right" bgcolor="#CCCCCC">Garantia:</td>
    <td><?php echo $row_rsCotizacion['garantia']?></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#CCCCCC">Cotizacion No.:</td>
    <td><?php echo $row_rsCotizacion['identificador']?></td>
    <td align="right" bgcolor="#CCCCCC">Utilidad Global:</td>
    <td><span class="sip"><?php echo $row_rsCotizacion['utilidad_global']?></span></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#CCCCCC">Tipo de cambio:</td>
    <td><span class="sip"><?php echo $row_rsCotizacion['tipo_cambio']?></span></td>
    <td align="right" bgcolor="#CCCCCC">Contacto:</td>
    <td><?php echo $row_rsCotizacion['conta']?><a href="SelectContacto.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>&amp;idcliente=<?php echo $row_rsCotizacion['idcliente'];?>" onclick="NewWindow(this.href,'Buscar articulo','400','400','yes');return false"><img src="images/Edit.png" alt="editar" width="24" height="24" border="0" align="middle" title="Cambiar Contacto" /></a><a href="contactos.php?idcliente=<?php echo $row_rsCotizacion['idcliente']; ?>" onclick="NewWindow(this.href,'Contactos','850','500','no');return false;"><img src="images/Clientes.png" alt="Clientes" width="24" height="24" border="0" align="middle" title="Administrar Contactos" /></a></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#CCCCCC">Forma de Pago:</td>
    <td><?php echo $row_rsCotizacion['formapago']?></td>
    <td align="right" bgcolor="#CCCCCC">E-Mail:</td>
    <td><?php echo $row_rsCotizacion['mail']?></td>
  </tr>
  <tr>
    <td  align="right" bgcolor="#CCCCCC">Vigencia</td>
    <td ><?php echo $row_rsCotizacion['vigencia']?></td>
    <td align="right" bgcolor="#CCCCCC" >Telefono::</td>
    <td ><?php echo $row_rsCotizacion['tele']?></td>
  </tr>
  <tr>
    <td  align="right" bgcolor="#CCCCCC">Tiempo Entrega:</td>
    <td ><?php echo $row_rsCotizacion['tipoentrega']?></td>
    <td align="right" bgcolor="#CCCCCC" >Opciones:</td>
    <td >
        <a href="cotizaciones_nueva_modDatos.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>" onclick="NewWindow(this.href,'Buscar articulo','500','500','yes');return false"><img src="images/Edit.png" width="24" height="24" border="0" align="middle" title="Editar Datos de Cotizacion" /></a>
        <a href="exportarExcel.php?query=<?php echo $query_rsPartidas2; ?>" onclick="NewWindow(this.href,'Ver Partida Extra',600,800,'yes'); return false;"><img src="images/Agregar.png" alt="" width="24" height="24" border="0" align="middle" title="Exportar Datos a Excel" /></a><a href="printCotizacion.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>" onclick="NewWindow(this.href,'IMPRIMIR COTIZACION','1100','980','yes');return false;"><img src="images/Imprimir2.png" alt="Imprimir" width="24" height="24" border="0" align="middle"  title="Imprimir Cotizacion"/></a><?php if ($row_rsCotizacion['estado']<6){?><a href="cotizacionEnviada.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>" onclick="NewWindow(this.href,'ENVIAR Cotizacion','450','200','yes');return false"><img src="images/state2.png" alt="Enviar" width="24" height="24" border="0" align="middle" title="Enviar Cotizacion" /></a><?php } else{?><a href="conciliacionEnviada.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>" onclick="NewWindow(this.href,'ENVIAR Conciliacion','450','200','yes');return false"><img src="images/state2.png" alt="Enviar" width="24" height="24" border="0" align="middle" title="Enviar Conciliacion" /></a><?php } ?><a href="importar.levantamiento.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>&idip=<?php echo $_GET['idip'];?>" onclick="NewWindow(this.href,'Buscar articulo','500','500','yes');return false"><img src="images/Facturacion.png" width="24" height="24" border="0" align="absmiddle" / title="Importar Datos de Levantamiento"></a></td>
  </tr>
  <tr>
    <td  align="right" bgcolor="#CCCCCC">Descuento:</td>
    <td valign="top" ><span class="sip"><?php echo $row_rsCotizacion['descuento']?></span></td>
    <td align="right" valign="top" bgcolor="#CCCCCC">Fecha:</td>
    <td valign="top" ><?php echo formatDate($row_rsCotizacion['fecha']);?></td>
  </tr>
  <tr>
    <td  align="right" bgcolor="#CCCCCC">Tipo de Suministro:</td>
    <td valign="top" ><?php echo $suministro[$row_rsCotizacion['tipo']];?></td>
    <td align="right" valign="top" bgcolor="#CCCCCC">IP:</td>
    <td valign="top" ><span class="sip"><?php echo $_GET['idip'];?></span></td>
  </tr>
  <tr>
    <td  align="right" bgcolor="#CCCCCC">Identificador</td>
    <td colspan="3" valign="top" ><?php echo $row_rsCotizacion['nombre'];?></td>
    </tr>
</table>
</div>
    
    <div id="tabs-2">
    <?php include("ip.encabezado.php");?>
	</div>
</div>


<?php
@mysql_free_result($rsEncabezado);
?>

<div id="submenu">
<ul>
<li><a href="cotizaciones_nueva_buscar_articulo.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>&tipo=<?php echo $row_rsCotizacion['tipo']; $_SESSION['subcoti']=-1?>&cambio=<?php echo $row_rsCotizacion['tipo_cambio'];?>" onclick="NewWindow(this.href,'Buscar articulo','1150','800','yes');return false"><button type="button"><img src="images/Agregar.png" width="24" height="24" border="0" align="absmiddle" />Agregar Partida</button></a></li>
<li><button type="button" onclick="document.guardar.cerrar.value = 'si'; document.guardar.submit();"><img src="images/Bancos.png" width="24" height="24" border="0" align="absmiddle" />Guardar y Cerrar</button></li>
</ul>
</div>
<div>
<form action="" method="get" name="buscar"><label>Buscar: </label><input name="buscar" type="text" value="<?php echo $_GET['buscar']?>"/>
<input name="guardar" type="submit" value="Buscar" />
<input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion'];?>"/>
</form></div>
<div class="distabla">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<thead>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>No.</td>
    <td width="40%">Descripcion</td>
    <td>Marca</td>
    <td>Codigo</td>
    <td>Precio</td>
    <td>Cant. Cot.</td>
    <?php if ($row_rsCotizacion['estado']>5){?><td>Cant. Inst.</td><?php } ?>
    <td>Importe</td>
    <?php if ($row_rsCotizacion['estado']>5){?><td>Subtotal Inst.</td><?php } ?>
    <td>Opciones</td>
    <td>Info</td>
    </tr>
   </thead>
   <tbody>
   	 <?php 
	 	   $sub = 0;
	 	   $subinst = 0;
		   $man = 0;
		   $maninst = 0;
		   
	 ?>
     <?php do { ?>
     <?php if ($totalRows_rsPartidas > 0) { // Show if recordset not empty ?>
     <?php 
	 //calculos!!!!
	 $pre =divisa($row_rsPartidas['precio_cotizacion'],$row_rsPartidas['moneda'],$row_rsCotizacion['moneda'],$row_rsPartidas['tipo_cambio']);
	 $manoobra = divisa($row_rsPartidas['mo'],$row_rsPartidas['moneda'],$row_rsCotizacion['moneda'],$row_rsPartidas['tipo_cambio']);

	if($tipo == 0){
	 if($row_rsCotizacion['tipo']  == 0)
		$p = round(($pre * $row_rsPartidas['utilidad']) + $manoobra,2);
	elseif($row_rsCotizacion['tipo']  == 3){	
		$p = round($manoobra,2) ;
		}
	else{	
		$p = round(($pre * $row_rsPartidas['utilidad']),2) ;
		}
		
		
	$man = $man + ($manoobra*$row_rsPartidas['cantidad']);
	$maninst = $maninst + ($manoobra*$row_rsPartidas['reall']);
	 
	 
	$sub = $sub + $row_rsPartidas['cantidad'] * $p; 
	$subinst = $subinst + $row_rsPartidas['reall'] * $p;
	
	
	}else
	{
	$man = $man + ($manoobra*$row_rsPartidas['cantidad']);
	$maninst = $maninst + ($manoobra*$row_rsPartidas['reall']);
	
	$sub = $sub + $row_rsPartidas['cantidad'] * $p; 
	$subinst = $subinst + $row_rsPartidas['reall'] * $p;
	}
	
	 ?>
       <tr>
         
           <td><?php echo $partidas[$row_rsPartidas['idsubcotizacionarticulo']];?></td>
           <td><?php echo $row_rsPartidas['descri']; ?></td>
           <td><?php echo $row_rsPartidas['marca1']; ?></td>
           <td><?php echo $row_rsPartidas['codigo']; ?></td>
           <td><?php echo ($row_rsCotizacion['tipo']==3) ? "$ 0.00" : format_money($p); ?></td>
           <td align="center"><?php echo $row_rsPartidas['cantidad']; ?></td>
           <?php if ($row_rsCotizacion['estado']>5){?><td align="center"><?php echo $row_rsPartidas['reall']; ?></td><?php } ?>
           <td><?php echo ($row_rsCotizacion['tipo']==3) ? "$ 0.00" : format_money($p*$row_rsPartidas['cantidad']);?></td>
           <?php if ($row_rsCotizacion['estado']>5){?><td><?php echo format_money($p*$row_rsPartidas['reall']);?></td><?php } ?>
           <td><a href="#" name="<?php echo $row_rsPartidas['idsubcotizacionarticulo'];?>" id="<?php echo $row_rsPartidas['idsubcotizacionarticulo'];?>"></a><a href="cotizaciones_nueva_modificar_articulo.php?idsubcotizacion=<?php echo $row_rsPartidas['idsubcotizacionarticulo'];?>&tipo=<?php echo $row_rsCotizacion['tipo']; if($row_rsCotizacion['estado']>5){echo "&conc=1";} ?>"  onclick="NewWindow(this.href,'Modificar articulo','550','300','yes');return false" ><img src="images/Edit.png" alt="cambiar" width="24" height="24" border="0" title="MODIFICAR ARTICULO EN ESTA COTIZACION" /></a><a href="cotizaciones_nueva_eliminar_articulo.php?idsubcotizacion=<?php echo $row_rsPartidas['idsubcotizacionarticulo'];?>&amp;tipo=<?php echo $row_rsCotizacion['tipo'];?>" onclick="NewWindow(this.href,'Eliminar articulo','550','300','yes');return false"><img src="images/eliminar.gif" alt="eliminar" width="19" height="19" border="0" title="ELIMINAR ARTICULO DE ESTA COTIZACION" /></a></td>
           <td><?php echo $signo[$row_rsPartidas['monart']]; ?>&nbsp;&nbsp;<?php echo $tip[$row_rsPartidas['tip']]; ?><?php if ($row_rsCotizacion['estado']>5){if($row_rsPartidas['modd']==0){?><img src="images/bred.png" title="Sin Modificar" /><?php }  if($row_rsPartidas['modd']==1){?><img src="images/bgreen.png" title="Modificado" /><? } } ?></td>
       </tr>
	  <?php } // Show if recordset not empty ?>
  	 <?php } while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas)); ?>
<tr>
  <?php if ($totalRows_rsPartidas == 0) { // Show if recordset empty ?>
    <td colspan="11" align="center">NO HAY MATERIALES O ARTICULOS AGREGADOS</td>
    <?php } // Show if recordset empty ?>
</tr>
 </tbody>
    <?php if((($row_rsCotizacion['tipo']==1) || $row_rsCotizacion['tipo']==3) and $totalRows_rsPartidas > 0){?>
  <tr>
    <td colspan="8" align="right"><a href="modificarMO.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>&cant= <?php echo money_format('%i',$man*$row_rsCotizacion['monto']); ?>" onclick="NewWindow(this.href,'Buscar articulo','400','250','yes');return false"><?php echo $row_rsCotizacion['descrimano']?></a></td>
    <td align="left">+
      <?php $inst = $man*$row_rsCotizacion['monto']; echo format_money($inst);?></td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php if ($row_rsCotizacion['estado']>5){?>
  <tr>
    <td colspan="8" align="right"><a href="modificarMOreal.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>&cant= <?php echo money_format('%i',$maninst*$row_rsCotizacion['montoreal']); ?>&real=1" onclick="NewWindow(this.href,'Buscar articulo','400','250','yes');return false"><?php echo $row_rsCotizacion['descrimano']?> Real:</a></td>
    <td align="left">+
      <?php $inst2 = $maninst*$row_rsCotizacion['montoreal']; echo format_money($inst2);?></td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
     <?php } //if estado?>
     <?php } //if?>
    <?php ///sumamos la mano de obra
  $sub = ($row_rsCotizacion['tipo']==3) ? $inst: $sub+$inst;
  $subinst=$subinst+$inst2;
  
  ?>
  <tr>
    <td colspan="8" align="right">&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
    <?php if ($row_rsCotizacion['estado']>5){?>
  <tr>
    <td colspan="5" align="right">&nbsp;</td>
    <td align="left" bgcolor="#CCCCCC">&nbsp;</td>
    <td align="left" bgcolor="#CCCCCC"><b>Cotizado</b></td>
    <td align="right" colspan="2" bgcolor="#CCCCCC"><b>Conciliado</b></td>
    <td align="left">&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
    <?php } ?>
  <tr>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="center" bgcolor="#CCCCCC"><strong>SUBTOTAL:</strong></td>
    <td align="center"><?php echo $signo[$row_rsCotizacion['moneda']]; ?><?php echo format_money($sub);?></td>
    <td colspan="2" align="right"><?php if ($row_rsCotizacion['estado']>5){?>
        <?php echo $signo[$row_rsCotizacion['moneda']]; ?><?php echo format_money($subinst);?>
        <?php } ?></td>
    <td >&nbsp;</td>
    <td >&nbsp;</td>
    </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="center" bgcolor="#CCCCCC"><strong>DESCUENTO:</strong></td>
    <td align="center"><?php echo $signo[$row_rsCotizacion['moneda']]; ?>-
      <?php  $descuento = ($row_rsCotizacion['descuento']/100)*$sub; echo format_money($descuento);$sub = $sub - $descuento;?></td>
    <td colspan="2" align="right"><?php if ($row_rsCotizacion['estado']>5)
        {?>
            <?php echo $signo[$row_rsCotizacion['moneda']]; ?>-
      <?php  $descuentoreal = ($row_rsCotizacion['descuentoreal']/100)*$subinst; echo format_money($descuentoreal);$subinst = $subinst - $descuentoreal;?>
            <?php } ?></td>
    <td align="center">&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="center" bgcolor="#CCCCCC"><strong>SUBTOTAL</strong></td>
    <td align="center"><?php echo $signo[$row_rsCotizacion['moneda']]; ?><?php echo format_money($sub);?></td>
    <td colspan="2" align="right"><?php if ($row_rsCotizacion['estado']>5){?>
<?php echo $signo[$row_rsCotizacion['moneda']]; ?><?php echo format_money($subinst);?>
<?php } ?></td>
    <td align="center">&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="center" bgcolor="#CCCCCC"><strong>I.V.A:</strong></td>
    <td align="center"><?php echo $signo[$row_rsCotizacion['moneda']]; ?>      <?php  $iva = ($sub*$row_rsCotizacion['iva'])/100;echo format_money($iva);?></td>
    <td colspan="2" align="right"><?php if ($row_rsCotizacion['estado']>5){?>
<?php echo $signo[$row_rsCotizacion['moneda']]; ?>      <?php  $ivareal = ($subinst*$row_rsCotizacion['iva'])/100;echo format_money($ivareal);?>
<?php } ?></td>
    <td align="center">&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="center" bgcolor="#CCCCCC"><strong>TOTAL:</strong></td>
      <td align="center"><?php echo $signo[$row_rsCotizacion['moneda']]; ?><?php echo format_money($sub + $iva);?></td>
      <td colspan="2" align="right"><?php if ($row_rsCotizacion['estado']>5){?>
<?php echo $signo[$row_rsCotizacion['moneda']]; ?><?php echo format_money($subinst + $ivareal);?>
<?php } ?></td>
      <td align="center">&nbsp;</td><td align="left">&nbsp;</td><td>&nbsp;</td></tr>
    <?php if ($row_rsCotizacion['estado']>5){?>
<!--    <tr>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center" bgcolor="#CCCCCC"><strong>SUBTOTAL REAL:</strong></td>
    <td align="center"><?php echo $signo[$row_rsCotizacion['moneda']]; ?><?php echo format_money($subinst);?></td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="left">&nbsp;</td>
      <td align="center" bgcolor="#CCCCCC"><strong>DESCUENTO REAL:</strong></td>
      <td align="center"><?php echo $signo[$row_rsCotizacion['moneda']]; ?>-
      <?php  $descuentoreal = ($row_rsCotizacion['descuentoreal']/100)*$subinst; echo format_money($descuentoreal);$subinst = $subinst - $descuentoreal;?></td>
      <td align="center">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="right">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center">&nbsp;</td>
      <td align="center" bgcolor="#CCCCCC"><strong>SUBTOTAL REAL:</strong></td>
      <td align="center"><?php echo $signo[$row_rsCotizacion['moneda']]; ?><?php echo format_money($subinst);?></td>
      <td align="left">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="left">&nbsp;</td>
    <td align="center" bgcolor="#CCCCCC"><strong>I.V.A. REAL:</strong></td>
    <td align="center"><?php echo $signo[$row_rsCotizacion['moneda']]; ?>      <?php  $ivareal = ($subinst*$row_rsCotizacion['iva'])/100;echo format_money($ivareal);?></td>
    <td align="center">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center" bgcolor="#CCCCCC"><strong>TOTAL REAL:</strong></td>
    <td align="center"><?php echo $signo[$row_rsCotizacion['moneda']]; ?><?php echo format_money($subinst + $ivareal);?></td>
    <td align="left">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  
 -->
    <?php } //if no 0?>
   
</table>
</div>

CANTIDAD CON LETRA:<? echo num2letras(money_format('%!n',$sub + $iva),false,true,$row_rsCotizacion['moneda']); ?><br />
<?php if ($row_rsCotizacion['estado']>5){?>CANTIDAD CON LETRA REAL:<? echo num2letras(money_format('%!n',$subinst + $ivareal),false,true,$row_rsCotizacion['moneda']); ?><?php } ?>
<br />
<form action="<?php echo $editFormAction; ?>" method="POST" name="guardar"><label>Notas:</label>
<br /><textarea name="notas" cols="80" rows="8"><?php echo $row_rsCotizacion['notas'];?></textarea>
<input type="hidden" name="idsubcotizacion" value="<?php echo $_GET['idsubcotizacion'];?>"/>
<input type="hidden" name="MM_update" value="guardar" />
<input type="hidden" name="cerrar" value="no" />
<input type="submit" value="Guardar notas" />
</form>

</body>
</html>
<?php
mysql_free_result($rsCotizacion);

mysql_free_result($rsPartidas);
?>
