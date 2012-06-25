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


$colname_rsJunta = "-1";
if (isset($_GET['idjunta'])) {
  $colname_rsJunta = $_GET['idjunta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsJunta = sprintf("SELECT * FROM junta WHERE junta.idjunta = %s", GetSQLValueString($colname_rsJunta, "int"));
$rsJunta = mysql_query($query_rsJunta, $tecnocomm) or die(mysql_error());
$row_rsJunta = mysql_fetch_assoc($rsJunta);
$totalRows_rsJunta = mysql_num_rows($rsJunta);

$colname_rsTareas = "-1";
if (isset($row_rsJunta['fecha'])) {
  $colname_rsTareas = $row_rsJunta['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTareas = sprintf("SELECT t.*, tu.idusuario as user FROM tarea t LEFT JOIN tarea_usuario tu ON t.idtarea = tu.idtarea WHERE (t.estado = 0 AND t.fecharealizar <= %s) OR (t.fecharealizo = %s) OR (t.fecharealizar >= %s AND t.fecharealizo <= %s)",GetSQLValueString($colname_rsTareas,'date'),GetSQLValueString($colname_rsTareas,'date'),GetSQLValueString($colname_rsTareas,'date'),GetSQLValueString($colname_rsTareas,'date'));
$rsTareas = mysql_query($query_rsTareas, $tecnocomm) or die(mysql_error());
$row_rsTareas = mysql_fetch_assoc($rsTareas);
$totalRows_rsTareas = mysql_num_rows($rsTareas);

//echo $query_rsTareas;
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUusarios = "SELECT * FROM usuarios ORDER BY username ASC";
$rsUusarios = mysql_query($query_rsUusarios, $tecnocomm) or die(mysql_error());
$row_rsUusarios = mysql_fetch_assoc($rsUusarios);
$totalRows_rsUusarios = mysql_num_rows($rsUusarios);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAdministrativos = sprintf("SELECT * FROM tarea  t WHERE administrativo is not NULL  AND t.estado < 2 OR (t.fecharealizar = %s AND t.estado > 0) OR (t.fecharealizar >= %s AND t.fecharealizo <= %s)",GetSQLValueString($row_rsJunta['fecha'],'date'),GetSQLValueString($row_rsJunta['fecha'],'date'),GetSQLValueString($row_rsJunta['fecha'],'date'),GetSQLValueString($row_rsJunta['fecha'],'date'));
$rsAdministrativos = mysql_query($query_rsAdministrativos, $tecnocomm) or die(mysql_error());
$row_rsAdministrativos = mysql_fetch_assoc($rsAdministrativos);
$totalRows_rsAdministrativos = mysql_num_rows($rsAdministrativos);




$colname_rsFacturas = "-1";
if (isset($row_rsJunta['fecha'])) {
  $colname_rsFacturas = $row_rsJunta['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = sprintf("SELECT t.*, f.numfactura, c.abreviacion FROM tarea t JOIN factura f ON t.idfactura = f.idfactura  JOIN cliente c ON f.idcliente = c.idcliente WHERE t.estado < 2 OR (t.fecharealizar = %s AND t.estado > 0) OR (t.fecharealizar >= %s AND t.fecharealizo <= %s)", GetSQLValueString($colname_rsFacturas, "date"),GetSQLValueString($colname_rsFacturas,'date'),GetSQLValueString($colname_rsFacturas,'date'),GetSQLValueString($colname_rsFacturas,'date'));
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());

$totalRows_rsFacturas = mysql_num_rows($rsFacturas);

$colname_rsLevantamientos = "-1";
if (isset($row_rsJunta['fecha'])) {
  $colname_rsLevantamientos = $row_rsJunta['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevantamientos = sprintf("SELECT t.*, l.consecutivo, l.descripcion, l.idlevantamientoip FROM tarea t JOIN levantamientoip l ON t.idlevantamiento = l.idlevantamientoip WHERE t.estado < 2 OR (t.fecharealizar = %s AND t.estado > 0) OR (t.fecharealizar >= %s AND t.fecharealizo <= %s)", GetSQLValueString($colname_rsLevantamientos, "date"),GetSQLValueString($colname_rsLevantamientos,'date'),GetSQLValueString($colname_rsLevantamientos,'date'),GetSQLValueString($colname_rsLevantamientos,'date'),GetSQLValueString($colname_rsLevantamientos,'date'),GetSQLValueString($colname_rsLevantamientos,'date'));
$rsLevantamientos = mysql_query($query_rsLevantamientos, $tecnocomm) or die(mysql_error());
$totalRows_rsLevantamientos = mysql_num_rows($rsLevantamientos);

$colname_rsOrdenes = "-1";
if (isset($row_rsJunta['fecha'])) {
  $colname_rsOrdenes = $row_rsJunta['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenes = sprintf("SELECT t.*, o.descripcionreporte, o.identificador FROM tarea t JOIN ordenservicio o ON t.idordenservicio = o.idordenservicio WHERE t.estado < 2  OR (t.fecharealizar = %s AND t.estado > 0) OR (t.fecharealizar >= %s AND t.fecharealizo <= %s)", GetSQLValueString($colname_rsOrdenes, "date"),GetSQLValueString($colname_rsOrdenes,'date'),GetSQLValueString($colname_rsOrdenes,'date'));
$rsOrdenes = mysql_query($query_rsOrdenes, $tecnocomm) or die(mysql_error());
$totalRows_rsOrdenes = mysql_num_rows($rsOrdenes);


$colname_rsCotizaciones = "-1";
if (isset($row_rsJunta['fecha'])) {
  $colname_rsCotizaciones = $row_rsJunta['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = sprintf("SELECT t.*, sb.identificador2, sb.nombre FROM tarea t JOIN subcotizacion sb ON t.idcotizacion = sb.idsubcotizacion WHERE t.estado < 2  OR (t.fecharealizar = %s AND t.estado > 0) OR (t.fecharealizar >= %s AND t.fecharealizo <= %s)", GetSQLValueString($colname_rsCotizaciones, "date"),GetSQLValueString($colname_rsCotizaciones,'date'),GetSQLValueString($colname_rsCotizaciones,'date'));
$rsCotizaciones = mysql_query($query_rsCotizaciones, $tecnocomm) or die(mysql_error());
$totalRows_rsCotizaciones = mysql_num_rows($rsCotizaciones);


$colname_rsCuentas = "-1";
if (isset($row_rsJunta['fecha'])) {
  $colname_rsCuentas = $row_rsJunta['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCuentas = sprintf("SELECT t.*, ct.nofactura, ct.idcuenta, ct.monto, ct.nameprov  FROM tarea t JOIN (select *,(select nombrecomercial from proveedor where idproveedor= cuentasporpagar.idproveedor) as nameprov from cuentasporpagar  ) ct ON t.idcuentaporpagar	 = ct.idcuenta WHERE fecharealizar = date(%s) OR (t.fecharealizar = %s AND t.estado > 0) OR (t.fecharealizar >= %s AND t.fecharealizo <= %s)", GetSQLValueString($colname_rsCuentas, "date"), GetSQLValueString($colname_rsCuentas, "date"), GetSQLValueString($colname_rsCuentas, "date"), GetSQLValueString($colname_rsCuentas, "date"));
$rsCuentas = mysql_query($query_rsCuentas, $tecnocomm) or die(mysql_error());
$totalRows_rsCuentas = mysql_num_rows($rsCuentas);

$colname_rsAsistentes = "-1";
if (isset($_GET['idjunta'])) {
  $colname_rsAsistentes = $_GET['idjunta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAsistentes = sprintf("SELECT * FROM junta_asistente ju LEFT JOIN usuarios u ON u.id = ju.idusuario WHERE idjunta = %s", GetSQLValueString($colname_rsAsistentes, "int"));
$rsAsistentes = mysql_query($query_rsAsistentes, $tecnocomm) or die(mysql_error());
$row_rsAsistentes = mysql_fetch_assoc($rsAsistentes);
$totalRows_rsAsistentes = mysql_num_rows($rsAsistentes);


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

$txtAsisten = "";

 do{
	$txtAsisten .= $row_rsAsistentes['username']." | ";
}while($row_rsAsistentes = mysql_fetch_assoc($rsAsistentes));

$edotar = array(0=> "Pendiente", 1 => 'Realizado', 2=>'Verificado', 3=>'Reasignada');
 require_once('utils.php');?>
<?php 
require('classPdfTable.php');

$pdf=new pdf_Table('L','mm','A4');

$pdf->AddPage();

$pdf->SetMargins(15, 15 ,15, 15);

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',15);
$pdf->Cell(175,5,'Reporte de Asignaciones',0,0,'L');
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->Cell(25,5,'Fecha',1,0,'R');
$pdf->Cell(65,5,formatDate(date('Y-m-d')),1,0,'L');
$pdf->Cell(25,5,'Hora Impresion',1,0,'R');
$pdf->Cell(65,5,date('H:i:s'),1,0,'L');
$pdf->Ln();

$pdf->Cell(25,5,'Hora Inicio',1,0,'R');
$pdf->Cell(65,5,$row_rsJunta['horainicio'],1,0,'L');
$pdf->Cell(25,5,'Hora Finalizacion',1,0,'R');
$pdf->Cell(65,5,$row_rsJunta['horafin'],1,0,'L');
$pdf->Ln();
$pdf->Cell(25,5,'Asistentes',1,0,'R');
$pdf->Cell(155,5,$txtAsisten,1,0,'L');
////////////////////////////cotizaciones
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Cotizaciones',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(10,50,158,25,20));
$pdf->Row(array('IP','Consecutivo','Descripcion','Responsable','Estado'));
//echo "HOLA";
//print_r($usuarios);
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($cotizaciones[$kusuario])){
		
	foreach($cotizaciones[$kusuario] as $cotizacion){

		$pdf->Row(array($coti[$cotizacion['idcotizacion']]['idip'],$coti[$cotizacion['idcotizacion']]['identificador2'],$coti[$cotizacion['idcotizacion']]['nombre'],$usuario['username'],$edotar[$cotizacion['estado']]));

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
$pdf->SetWidths(array(10,50,158,25,20));
$pdf->Row(array('IP','Consecutivo','Descripcion','Responsable','Estado'));
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($levantamientos[$kusuario])){
		
	foreach($levantamientos[$kusuario] as $levantamiento){

		$pdf->Row(array($lev[$levantamiento['idlevantamiento']]['idip'],$lev[$levantamiento['idlevantamiento']]['consecutivo'],$lev[$levantamiento['idlevantamiento']]['descripcion'],$usuario['username'],$edotar[$levantamiento['estado']]));

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
$pdf->SetWidths(array(10,50,158,25,20));
$pdf->Row(array('IP','Consecutivo','Descripcion','Responsable','Estado'));
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($ordenservicio[$kusuario])){
		
	foreach($ordenservicio[$kusuario] as $orden){

		$pdf->Row(array($ord[$orden['idordenservicio']]['idip'],$ord[$orden['idordenservicio']]['identificador'],$ord[$orden['idordenservicio']]['descripcionreporte'],$usuario['username'],$edotar[$orden['estado']]));

	}
}
}

////////////////////////////admin

$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(175,5,'Administrativos',0,0,'L');

$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->SetWidths(array(218,25,20,1,1));
$pdf->Row(array('Descripcion', 'Responsable', 'Estado'));
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($administrativos[$kusuario])){
		
	foreach($administrativos[$kusuario] as $administrativo){

		$pdf->Row(array($administrativo['administrativo'],$usuario['username'],$edotar[$administrativo['estado']]));

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
$pdf->SetWidths(array(10,50,158,25,20));
$pdf->Row(array('IP','Numero','Cliente','Responsable','Estado'));
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($facturas[$kusuario])){
		
	foreach($facturas[$kusuario] as $factura){

		$pdf->Row(array($factura['idip'],$facs[$factura['idfactura']]['numfactura'],$facs[$factura['idfactura']]['abreviacion'],$usuario['username'],$edotar[$factura['estado']]));

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
$pdf->SetWidths(array(30,60,70,25,20));
$pdf->Row(array('Numero','Proveedor','Monto','Responsable','Estado'));
foreach($usuarios as $kusuario => $usuario){
//echo $kusuario."<br>";
if(is_array($cuentas[$kusuario])){
		
	foreach($cuentas[$kusuario] as $cuenta){

		$pdf->Row(array($cxp[$cuenta['idcuentaporpagar']]['nofactura'],$cxp[$cuenta['idcuentaporpagar']]['nameprov'],$cxp[$cuenta['idcuentaporpagar']]['monto'],$usuario['username'],$edotar[$cuenta['estado']]));

	}
}
}


 $pdf->Output('Planeacion.pdf','I');
?>
<?php
mysql_free_result($rsTareas);

mysql_free_result($rsJunta);
?>
