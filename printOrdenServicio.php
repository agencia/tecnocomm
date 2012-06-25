<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
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

$colname_rsOrden = "-1";
if (isset($_GET['idordenservicio'])) {
  $colname_rsOrden = $_GET['idordenservicio'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrden = sprintf("SELECT o.*, u.nombrereal as genero, DATE_FORMAT(fecha, '%%d-%%m-%%Y') as fecha, DATE_FORMAT(hora, '%%H:%%i') as hora FROM ordenservicio o, usuarios u WHERE o.idordenservicio = %s AND u.id = o.idusuario", GetSQLValueString($colname_rsOrden, "int"));
$rsOrden = mysql_query($query_rsOrden, $tecnocomm) or die(mysql_error());
$row_rsOrden = mysql_fetch_assoc($rsOrden);
$totalRows_rsOrden = mysql_num_rows($rsOrden);

$ide_rsIp = "-1";
if (isset($row_rsOrden['idip'])) {
  $ide_rsIp = $row_rsOrden['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsIp = sprintf("SELECT i.idip, i.fecha, DATE_FORMAT(i.hora, '%%H:%%i') as hora,i.descripcion,i.titulo, c.nombre AS nombrecliente, c.direccion, c.ciudad, co.nombre AS nombrecontacto, co.correo, co.telefono, co.telefono2,c.idcliente FROM ip i LEFT JOIN cliente c ON i.idcliente = c.idcliente LEFT JOIN contactoclientes co ON i.idcontacto = co.idcontacto WHERE i.idip = %s", GetSQLValueString($ide_rsIp, "int"));
$rsIp = mysql_query($query_rsIp, $tecnocomm) or die(mysql_error());
$row_rsIp = mysql_fetch_assoc($rsIp);
$totalRows_rsIp = mysql_num_rows($rsIp);

$colname_rsOrdenDetalle = "-1";
if (isset($_GET['idordenservicio'])) {
  $colname_rsOrdenDetalle = $_GET['idordenservicio'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenDetalle = sprintf("SELECT * FROM ordenservicio_detalle WHERE idordenservicio = %s ORDER BY idordenservicio_detalle ASC", GetSQLValueString($colname_rsOrdenDetalle, "int"));
$rsOrdenDetalle = mysql_query($query_rsOrdenDetalle, $tecnocomm) or die(mysql_error());
$row_rsOrdenDetalle = mysql_fetch_assoc($rsOrdenDetalle);
$totalRows_rsOrdenDetalle = mysql_num_rows($rsOrdenDetalle);
?>
<?php require_once('pdfCreatorOrden.php');?>
<?php 
$pdf = new PDF();
	$pdf->signo=$signo[$row_rsCotizacion['moneda']];
	$pdf->AddPage();
	$pdf->createFondo();  
	$pdf->setIP($row_rsIp['idip']);
	$pdf->setEncabezado($row_rsIp['nombrecliente'],$row_rsOrden['identificador'],$row_rsIp['direccion'], $row_rsIp['telefono'],$row_rsIp['nombrecontacto'],$row_rsOrden['fecha'] . ' ' . $row_rsOrden['hora']);
	
	$pdf->descripcion($row_rsOrden['descripcionreporte']);
	$pdf->trabajoRealizado($row_rsOrden['trabajorealizado']);
	$pdf->setDatos($row_rsOrden['nopersonas'],$row_rsOrden['totalhoras'],$row_rsOrden['cargo'],$row_rsOrden['pendiente']);
	
	$subtotal = 0;
	if($totalRows_rsOrdenDetalle > 0)
	do{
		
	$pre =divisa($row_rsOrdenDetalle['precio'],$row_rsOrdenDetalle['moneda'],$row_rsOrden['moneda'],$row_rsOrden['tipo_cambio']);
	$manoobra = divisa($row_rsOrdenDetalle['mano_obra'],$row_rsOrdenDetalle['moneda'],$row_rsOrden['moneda'],$row_rsOrden['tipo_cambio']);
	$p = round((($pre * $row_rsOrdenDetalle['utilidad'])),2);
	$importe=$p*$row_rsOrdenDetalle['cantidad'];			  
				  
	

	$pdf->addPartida($row_rsOrdenDetalle['cantidad'],$row_rsOrdenDetalle['codigo'],$row_rsOrdenDetalle['marca'],trim($row_rsOrdenDetalle['descripcion']),format_money($p),format_money($importe));

	
		
	$subtotal += $importe;
	
	}while($row_rsOrdenDetalle = mysql_fetch_assoc($rsOrdenDetalle));
	
	$tot=$subtotal + $row_rsOrden['manoobra'];
        if (format_money(($tot*1.16)) > 0)
            $pdf->setTotales(format_money($subtotal),format_money($row_rsOrden['manoobra']),format_money($tot), format_money($tot*.16),format_money(($tot*1.16)));
        else
            $pdf->setTotales("","","","","","");
	
	$pdf->setDescripcion($row_rsOrden['descripcion']);
	$pdf->setObservaciones($row_rsOrden['observaciones']);
	$pdf->setGenero($row_rsOrden['genero']);
	$pdf->Output('prueba.pdf','I');





mysql_free_result($rsOrden);

mysql_free_result($rsIp);
?>