<?php require_once('Connections/tecnocomm.php'); ?>
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





mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = "SELECT s.*, cl.nombre as nombrecliente, c.idip FROM subcotizacion s LEFT JOIN cotizacion c ON c.idcotizacion = s.idcotizacion JOIN cliente cl ON cl.idcliente = c.idcliente WHERE s.estado = 3";
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);



mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTareas = "SELECT t.*, u.username FROM tarea t JOIN tarea_usuario tu ON t.idtarea = tu.idtarea JOIN usuarios u ON u.id = tu.idusuario WHERE t.estado = 0";
$rsTareas = mysql_query($query_rsTareas, $tecnocomm) or die(mysql_error());
$row_rsTareas = mysql_fetch_assoc($rsTareas);
$totalRows_rsTareas = mysql_num_rows($rsTareas);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevantamientos = "SELECT l.*,c.nombre as nombrecliente FROM levantamientoip l JOIN ip ON l.idip = ip.idip JOIN cliente c ON c.idcliente = ip.idcliente WHERE l.estado < 2 ";
$rsLevantamientos = mysql_query($query_rsLevantamientos, $tecnocomm) or die(mysql_error());
$row_rsLevantamientos = mysql_fetch_assoc($rsLevantamientos);
$totalRows_rsLevantamientos = mysql_num_rows($rsLevantamientos);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenes = "SELECT o.*, c.nombre as nombrecliente FROM ordenservicio o JOIN ip ON ip.idip = o.idip JOIN cliente c ON c.idcliente = ip.idcliente WHERE o.estado < 4";
$rsOrdenes = mysql_query($query_rsOrdenes, $tecnocomm) or die(mysql_error());
$row_rsOrdenes = mysql_fetch_assoc($rsOrdenes);
$totalRows_rsOrdenes = mysql_num_rows($rsOrdenes);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT f.*, c.nombre as nombrecliente FROM factura f JOIN ip ON ip.idip = f.idip JOIN cliente c ON c.idcliente = f.idcliente WHERE f.estado < 5";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);




//Cotizaciones
do{
	$cotizaciones[$row_rsTareas['idcotizacion']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));

mysql_data_seek($rsTareas,0);

do{
	$levantamientos[$row_rsTareas['idlevantamiento']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));

mysql_data_seek($rsTareas,0);

do{
	$ordenes[$row_rsTareas['idordenservicio']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));


mysql_data_seek($rsTareas,0);
do{
	$facturas[$row_rsTareas['idfactura']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));

mysql_data_seek($rsTareas,0);
do{
	$cuentasporpagar[$row_rsTareas['idcuentaporpagar']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));

mysql_data_seek($rsTareas,0);
do{
	$administrativo[$row_rsTareas['idcotizacion']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));


 require_once('utils.php');?>
<?php 
require('classPdfTable.php');

$pdf=new pdf_Table('L','mm','A4');

$pdf->AddPage();

$pdf->SetMargins(15, 15 ,15, 15);

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',15);
$pdf->Cell(175,5,'Reporte de Status General',0,0,'L');
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->Cell(25,5,'Fecha',1,0,'R');
$pdf->Cell(65,5,formatDate(date('Y-m-d')),1,0,'L');
$pdf->Cell(25,5,'Hora Impresion',1,0,'R');
$pdf->Cell(65,5,date('H:i:s'),1,0,'L');
$pdf->Ln();


//$pdf->Ln();
//$pdf->Cell(25,5,'Asistentes',1,0,'R');
//$pdf->Cell(155,5,$txtAsisten,1,0,'L');
////////////////////////////cotizaciones
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Cotizaciones',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(7,30,60,120,40));
$pdf->Row(array('IP', 'Cotizacion', 'Cliente', 'Descripcion', 'Responsable'));

do {

	$var='';
	if(is_array($cotizaciones[$row_rsCotizaciones['idsubcotizacion']]) ):
	 	$var = join(', ',$cotizaciones[$row_rsCotizaciones['idsubcotizacion']]);
	 endif;


		$pdf->Row(array($row_rsCotizaciones['idip'],$row_rsCotizaciones['identificador2'],$row_rsCotizaciones['nombrecliente'],$row_rsCotizaciones['nombre'], $var));

} while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones)); 

////////////////////////////levantamientos

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Levantamientos',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(7,30,60,120,40));
$pdf->Row(array('IP','Levantamiento','Cliente', 'Descripcion','Responsable'));


do {

$var='';
	if(is_array($levantamientos[$row_rsLevantamientos['idlevantamientoip']]) ):
	 	$var = join(', ',$levantamientos[$row_rsLevantamientos['idlevantamientoip']]);
	 endif;


		$pdf->Row(array($row_rsLevantamientos['idip'],$row_rsLevantamientos['consecutivo'], $row_rsLevantamientos['nombrecliente'], $row_rsLevantamientos['descripcion'],$var));


} while ($row_rsLevantamientos = mysql_fetch_assoc($rsLevantamientos));

////////////////////////////ordenes

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Orden de Servicio',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(7,30,60,120,40));
$pdf->Row(array('IP','Orden Servicio', 'Cliente','Descripcion','Responsable'));
 
do {

$var='';
	if(is_array($ordenes[$row_rsOrdenes['idordenservicio']]) ):
	 	$var = join(', ',$ordenes[$row_rsOrdenes['idordenservicio']]);
	 endif;



		$pdf->Row(array($row_rsOrdenes['idip'],$row_rsOrdenes['identificador'],$row_rsOrdenes['nombrecliente'],$row_rsOrdenes['descripcion'],$var));


} while ($row_rsOrdenes = mysql_fetch_assoc($rsOrdenes));

////////////////////////////facturas

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Facturas',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(7,30,60,120,40));
$pdf->Row(array('IP','Factura','Cliente','Concepto','Responsable'));
 

 do {
 
 $var='';
	if(is_array($facturas[$row_rsFacturas['idfactura']]) ):
	 	$var = join(', ',$facturas[$row_rsFacturas['idfactura']]);
	 endif;
 
 
		$pdf->Row(array($row_rsFacturas['idip'],$row_rsFacturas['numfactura'],$row_rsFacturas['nombrecliente'],$row_rsFacturas['referencia1']." ".$row_rsFacturas['referencia2']." ".$row_rsFacturas['referencia3'],$var));

 } while ($row_rsFacturas = mysql_fetch_assoc($rsFacturas));


/*
////////////////////////////cuentas

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Cuentas por Pagar',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(30,60,70,25));
$pdf->Row(array('Numero','Proveedor','Monto','Responsable'));
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($cuentas[$kusuario])){
		
	foreach($cuentas[$kusuario] as $cuenta){

		$pdf->Row(array($cxp[$cuenta['idcuentaporpagar']]['nofactura'],$cxp[$cuenta['idcuentaporpagar']]['nameprov'],$cxp[$cuenta['idcuentaporpagar']]['monto'],$usuario['username']));

	}
}
}
*/


 $pdf->Output('StatusGeneral.pdf','I');
?>
<?php
mysql_free_result($rsTareas);

mysql_free_result($rsJunta);
?>
