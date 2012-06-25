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
$query_rsTareas = "SELECT t.*, tu.idusuario as user FROM tarea t LEFT JOIN tarea_usuario tu ON t.idtarea = tu.idtarea WHERE t.estado = 0 ";
$rsTareas = mysql_query($query_rsTareas, $tecnocomm) or die(mysql_error());
$row_rsTareas = mysql_fetch_assoc($rsTareas);
$totalRows_rsTareas = mysql_num_rows($rsTareas);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUusarios = "SELECT * FROM usuarios ORDER BY username ASC";
$rsUusarios = mysql_query($query_rsUusarios, $tecnocomm) or die(mysql_error());
$row_rsUusarios = mysql_fetch_assoc($rsUusarios);
$totalRows_rsUusarios = mysql_num_rows($rsUusarios);




mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT t.*, f.numfactura, c.abreviacion FROM tarea t JOIN factura f ON t.idfactura = f.idfactura  JOIN cliente c ON f.idcliente = c.idcliente WHERE t.estado = 0";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());

$totalRows_rsFacturas = mysql_num_rows($rsFacturas);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevantamientos = "SELECT t.*, l.consecutivo, l.descripcion FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip WHERE t.estado = 0";
$rsLevantamientos = mysql_query($query_rsLevantamientos, $tecnocomm) or die(mysql_error());
$totalRows_rsLevantamientos = mysql_num_rows($rsLevantamientos);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenes = "SELECT t.*, o.descripcion, o.identificador FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio WHERE t.estado = 0";
$rsOrdenes = mysql_query($query_rsOrdenes, $tecnocomm) or die(mysql_error());
$totalRows_rsOrdenes = mysql_num_rows($rsOrdenes);


$colname_rsCotizaciones = "-1";
if (isset($row_rsJunta['fecharealizar'])) {
  $colname_rsCotizaciones = $row_rsJunta['fecharealizar'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = "SELECT t.*, sb.identificador2, sb.nombre FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion WHERE t.estado = 0";
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);


$colname_rsCuentas = "-1";
if (isset($row_rsJunta['fecharealizar'])) {
  $colname_rsCuentas = $row_rsJunta['fecharealizar'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCuentas = "SELECT t.*, ct.nofactura, ct.idcuenta, ct.monto, ct.nameprov  FROM tarea t JOIN (select *,(select nombrecomercial from proveedor where idproveedor= cuentasporpagar.idproveedor) as nameprov from cuentasporpagar  ) ct ON t.idcuentaporpagar	 = ct.idcuenta WHERE t.estado = 0";
$rsCuentas = mysql_query($query_rsCuentas, $tecnocomm) or die(mysql_error());
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAdministrativos = "SELECT * FROM tarea WHERE administrativo is not NULL  AND estado = 0";
$rsAdministrativos = mysql_query($query_rsAdministrativos, $tecnocomm) or die(mysql_error());
$row_rsAdministrativos = mysql_fetch_assoc($rsAdministrativos);
$totalRows_rsAdministrativos = mysql_num_rows($rsAdministrativos);


$coti = toArray($rsCotizaciones,'idcotizacion');
$lev = toArray($rsLevantamientos,'idlevantamientoip');
$ord = toArray($rsOrdenes,'idordenservicio');
$facs = toArray($rsFacturas,'idfactura');
$cxp = toArray($rsCuentas,'idcuenta');



do{
	
	$usuarios[$row_rsUusarios['id']] = $row_rsUusarios;
	
}while($row_rsUusarios = mysql_fetch_assoc($rsUusarios));


do{
	
	if($row_rsTareas['idcotizacion'] != ''){
		$cotizaciones[$row_rsTareas['user']][] = $row_rsTareas;
		
	}
	
	if($row_rsTareas['idlevantamiento'] != ''){
		$levantamientos[$row_rsTareas['user']][] = $row_rsTareas;
	}
	
	if($row_rsTareas['idordenservicio'] != ''){
		$ordenservicio[$row_rsTareas['user']][] = $row_rsTareas;
	}
		
	if($row_rsTareas['idfactura'] != ''){
		$facturas[$row_rsTareas['user']][] = $row_rsTareas;
	}
	
	if($row_rsTareas['idcuentaporpagar'] != ''){
		$cuentas[$row_rsTareas['user']][] = $row_rsTareas;
	}
	
	if($row_rsTareas['administrativo'] != ''){
		$administrativos[$row_rsTareas['user']][] = $row_rsTareas;
	}
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));


function toArray($rs,$campo)
{
	$array = array();
	
	while($row = mysql_fetch_assoc($rs)){		
		$array[$row[$campo]] = $row;
		
	}
	
	return $array;
}




 require_once('utils.php');?>
<?php 
require('classPdfTable.php');

$pdf=new pdf_Table('P','mm','A4');

$pdf->AddPage();

$pdf->SetMargins(15, 15 ,15, 15);

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',15);
$pdf->Cell(175,5,'Reporte de Asignaciones Pendientes',0,0,'L');
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
$pdf->SetWidths(array(10,40,110,25));
$pdf->Row(array('IP','Consecutivo','Descripcion','Responsable'));
//echo "HOLA";
//print_r($usuarios);
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($cotizaciones[$kusuario])){
		
	foreach($cotizaciones[$kusuario] as $cotizacion){

		$pdf->Row(array($coti[$cotizacion['idcotizacion']]['idip'],$coti[$cotizacion['idcotizacion']]['identificador2'],$coti[$cotizacion['idcotizacion']]['nombre'],$usuario['username']));

	}
}
}

////////////////////////////levantamientos

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Levantamientos',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(10,40,110,25));
$pdf->Row(array('IP','Consecutivo','Descripcion','Responsable'));
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($levantamientos[$kusuario])){
		
	foreach($levantamientos[$kusuario] as $levantamiento){

		$pdf->Row(array($lev[$levantamiento['idlevantamientoip']]['idip'],$lev[$levantamiento['idlevantamientoip']]['consecutivo'],$lev[$levantamiento['idlevantamientoip']]['descripcion'],$usuario['username']));

	}
}
}


////////////////////////////ordenes

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Orden de Servicio',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(10,40,110,25));
$pdf->Row(array('IP','Consecutivo','Descripcion','Responsable'));
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($ordenservicio[$kusuario])){
		
	foreach($ordenservicio[$kusuario] as $orden){

		$pdf->Row(array($ord[$orden['idordenservicio']]['idip'],$ord[$orden['idordenservicio']]['identificador'],$ord[$orden['idordenservicio']]['descripcion'],$usuario['username']));

	}
}
}

////////////////////////////admin

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Administrativos u Operadores',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(160,25));
$pdf->Row(array('Descripcion','Responsable'));
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($administrativos[$kusuario])){
		
	foreach($administrativos[$kusuario] as $administrativo){

		$pdf->Row(array($administrativo['administrativo'],$usuario['username']));

	}
}
}

////////////////////////////facturas

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Facturas',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(10,40,110,25));
$pdf->Row(array('IP','Numero','Cliente','Responsable'));
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($facturas[$kusuario])){
		
	foreach($facturas[$kusuario] as $factura){

		$pdf->Row(array($factura['idip'],$facs[$factura['idfactura']]['numfactura'],$facs[$factura['idfactura']]['abreviacion'],$usuario['username']));

	}
}
}

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


 $pdf->Output("ReporteAsignacionesPendientes".formatDate(date('Y-m-d')).'.pdf','I');
?>
<?php
mysql_free_result($rsTareas);

mysql_free_result($rsJunta);
?>
