<?php
require_once('Connections/tecnocomm.php');
 require_once('utils.php');
 require_once('numtoletras.php');
  
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

  $ide_rsCotizacion = $idsubcotizacion;
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT *,(select nombre from contactoclientes where idcontacto=subcotizacion.contacto) as conta,contacto as con,(select telefono from contactoclientes where idcontacto=subcotizacion.contacto) as tele, (select correo from contactoclientes where idcontacto=subcotizacion.contacto) as mail FROM subcotizacion,cotizacion WHERE idsubcotizacion=%s and subcotizacion.idcotizacion=cotizacion.idcotizacion", GetSQLValueString($idsubcotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);

$ide_rsPartidas = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $ide_rsPartidas = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT *, suba.moneda  AS monedacotizacion, articulo.moneda as monart, articulo.tipo as tip FROM subcotizacion sub,subcotizacionarticulo suba, articulo WHERE sub.idsubcotizacion=suba.idsubcotizacion and suba.idarticulo=articulo.idarticulo and suba.idsubcotizacion=%s %s ORDER BY idsubcotizacionarticulo ASC", GetSQLValueString($idsubcotizacion, "int"),$bus);
$rsPartidas = mysql_query($query_rsPartidas, $tecnocomm) or die(mysql_error());
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);
$totalRows_rsPartidas = mysql_num_rows($rsPartidas);

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

	 	   $sub = 0;
	 	   $subinst = 0;
		   $man = 0;
		   $maninst = 0;
	 do { 
	  if ($totalRows_rsPartidas > 0) { // Show if recordset not empty
	  
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
	  } // Show if recordset not empty 
	   } while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas));
	   	if((($row_rsCotizacion['tipo']==1) || $row_rsCotizacion['tipo']==3) and $totalRows_rsPartidas > 0){
		 $inst = $man*$row_rsCotizacion['monto'];
   if ($row_rsCotizacion['estado']>5){
      $inst2 = $maninst*$row_rsCotizacion['montoreal']; 
	   } //if estado
	    } //if
		 ///sumamos la mano de obra
  $sub = ($row_rsCotizacion['tipo']==3) ? $inst: $sub+$inst;
  $subinst=$subinst+$inst2;
  
  $descuento = ($row_rsCotizacion['descuento']/100)*$sub; $sub = $sub - $descuento;?><?php  $iva = ($sub*$row_rsCotizacion['iva'])/100;
  if ($row_rsCotizacion['estado']>5){  
  $descuentoreal = ($row_rsCotizacion['descuentoreal']/100)*$subinst; $subinst = $subinst - $descuentoreal;
    $ivareal = ($subinst*$row_rsCotizacion['iva'])/100;
	 
	 echo format_money($subinst + $ivareal);
	  } else {
		echo format_money($sub + $iva);
		}
		
mysql_free_result($rsCotizacion);

mysql_free_result($rsPartidas);
?>
