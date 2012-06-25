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

$ide_rsCotizacion = "-1";
if (isset($idsubcotizacion)) {
  $ide_rsCotizacion = $idsubcotizacion;
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT *,(select nombre from contactoclientes where idcontacto=subcotizacion.contacto) as conta,contacto as con,(select telefono from contactoclientes where idcontacto=subcotizacion.contacto) as tele, (select correo from contactoclientes where idcontacto=subcotizacion.contacto) as mail FROM subcotizacion,cotizacion WHERE idsubcotizacion=%s and subcotizacion.idcotizacion=cotizacion.idcotizacion", GetSQLValueString($ide_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);


$ide_rsPartidas = "-1";
if (isset($idsubcotizacion)) {
  $ide_rsPartidas = $idsubcotizacion;
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT *, suba.moneda  AS monedacotizacion, articulo.moneda as monart, articulo.tipo as tip FROM subcotizacion sub,subcotizacionarticulo suba, articulo WHERE sub.idsubcotizacion=suba.idsubcotizacion and suba.idarticulo=articulo.idarticulo and suba.idsubcotizacion=%s %s ORDER BY idsubcotizacionarticulo ASC", GetSQLValueString($ide_rsPartidas, "int"),$bus);
$rsPartidas = mysql_query($query_rsPartidas, $tecnocomm) or die(mysql_error());
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);
$totalRows_rsPartidas = mysql_num_rows($rsPartidas);

 require_once('utils.php');
 require_once('numtoletras.php');
 
 	 	   $sub = 0;
	 	   $subinst = 0;
		   $man = 0;
		   $maninst = 0;

do {
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
	} while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas));
	
	  $ivareal = ($subinst*$row_rsCotizacion['iva'])/100;
	 
?><?php echo format_money($subinst + $ivareal);?>