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

$colname_rsEncabezado = "-1";
if (isset($_GET['idip'])) {
  $colname_rsEncabezado = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsEncabezado = sprintf("SELECT i.idip, i.fecha, i.hora,i.descripcion,i.titulo, c.nombre AS nombrecliente, c.direccion, c.ciudad,c.abreviacion, co.nombre AS nombrecontacto, co.correo, co.telefono, co.telefono2,c.idcliente FROM ip i LEFT JOIN cliente c ON i.idcliente = c.idcliente LEFT JOIN contactoclientes co ON i.idcontacto = co.idcontacto WHERE i.idip = %s", GetSQLValueString($colname_rsEncabezado, "int"));
$rsEncabezado = mysql_query($query_rsEncabezado, $tecnocomm) or die(mysql_error());
$row_rsEncabezado = mysql_fetch_assoc($rsEncabezado);
$totalRows_rsEncabezado = mysql_num_rows($rsEncabezado);

$colname_rsAtendido = "-1";
if (isset($_GET['idip'])) {
  $colname_rsAtendido = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAtendido = sprintf("SELECT u.nombrereal,u.username FROM ip i LEFT JOIN usuarios u ON i.idatendio = u.id WHERE idip = %s", GetSQLValueString($colname_rsAtendido, "int"));
$rsAtendido = mysql_query($query_rsAtendido, $tecnocomm) or die(mysql_error());
$row_rsAtendido = mysql_fetch_assoc($rsAtendido);
$totalRows_rsAtendido = mysql_num_rows($rsAtendido);

$colname_rsResponsable = "-1";
if (isset($_GET['idip'])) {
  $colname_rsResponsable = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsResponsable = sprintf("SELECT pp.*,u.nombrereal,u.username FROM ip i LEFT JOIN proyecto_personal pp ON i.idip = pp.idip LEFT JOIN usuarios u ON u.id = pp.idusuario WHERE i.idip = %s AND pp.estado = 1", GetSQLValueString($colname_rsResponsable, "int"));
$rsResponsable = mysql_query($query_rsResponsable, $tecnocomm) or die(mysql_error());
$row_rsResponsable = mysql_fetch_assoc($rsResponsable);
$totalRows_rsResponsable = mysql_num_rows($rsResponsable);
 

$colname_rsLevDetalle = "-1";
if (isset($_GET['idlevantamiento'])) {
  $colname_rsLevDetalle = $_GET['idlevantamiento'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevDetalle = sprintf("SELECT l.*,a.nombre FROM levantamientoipdetalle l LEFT JOIN articulo a ON l.idarticulo = a.idarticulo WHERE idlevantamientoip = %s", GetSQLValueString($colname_rsLevDetalle, "int"));
$rsLevDetalle = mysql_query($query_rsLevDetalle, $tecnocomm) or die(mysql_error());
$row_rsLevDetalle = mysql_fetch_assoc($rsLevDetalle);
$totalRows_rsLevDetalle = mysql_num_rows($rsLevDetalle);

$colname_rsLevantamiento = "-1";
if (isset($_GET['idlevantamiento'])) {
  $colname_rsLevantamiento = $_GET['idlevantamiento'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsLevantamiento = sprintf("SELECT * FROM levantamientoip WHERE idlevantamientoip = %s", GetSQLValueString($colname_rsLevantamiento, "int"));
$rsLevantamiento = mysql_query($query_rsLevantamiento, $tecnocomm) or die(mysql_error());
$row_rsLevantamiento = mysql_fetch_assoc($rsLevantamiento);
$totalRows_rsLevantamiento = mysql_num_rows($rsLevantamiento);
?>
<?php
require('fpdf.php');
require('utils.php');
define('FPDF_FONTPATH','font/');


class LPDF extends FPDF
{
var $B;
var $I;
var $U;
var $HREF;
var $linea = 61;
var $signo = '$';

	function PDF($orientation='P',$unit='mm',$format='A4'){
		//Llama al constructor de la clase padre
		$this->FPDF($orientation,$unit,$format);
		$this->setMargins(0,0,0);
		//Iniciación de variables
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';
	}
	
	function setGenero($nombre){
		$this->SetFont('Arial','',5);
		$this->setXY(190,54.5);
		$this->MultiCell(50,5.2,"Genero: " . $nombre,0,'R');
	}
	function setNotas($notas){
		$this->SetFont('Arial','',8);
		$this->setXY(8,142);
		$this->MultiCell(275,4,$notas,0,'L');
	}

function setEncabezado($nombre='',$direccion='',$contacto='',$telefono='',$ciudad='',$email='',$cotizacion='',$fecha='',$ip='',$descripcion=''){
			$this->SetFont('Arial','',8);
			$this->setXY(22,39);
			$this->Write(5,$nombre);
			
			$this->setXY(220,39);
			$this->Write(5,$ip);
			
			$this->setXY(22,43);
			$this->Write(5,$direccion);
			
			$this->setXY(22,47);
			$this->Write(5,$contacto);
		
			$this->setXY(22,51);
			$this->Write(5,$telefono);
			
			$this->setXY(140,47);
			$this->Write(5,$ciudad);
			
			$this->setXY(140,51);
			$this->Write(5,$email);
			
			$this->setFont("Arial","IB","5");
			$this->setXY(245,37);
			$this->Write(5,"Levantamiento:");
			
			$this->SetFont('Arial','',8);
			$this->setXY(245,42);
			$this->Write(5,$cotizacion);
			
			$this->setXY(255,51);
			$this->Write(5,$fecha);
			
			$this->setXY(22,54.5);
			$this->Write(5,$descripcion);
	}
	
	


	
	function addFondo1(){
		$this->Image('tec/levantamiento1.jpg',5,5,287,190);
	}
	
	function addFondo2(){
		$this->Image('tec/levantamiento2.jpg',5,5,190,280);
	}
	function addFondo3(){
		$this->Image('tec/levantamiento3.jpg',5,5,190,280);
	}
	
	
}

	$descripcion = ($row_rsLevDetalle['descripcion'] == '')?$row_rsLevantamiento['descripcion']:$row_rsEncabezado['descripcion'];
	
	$pdf = new LPDF();
	$pdf->AddPage("L");
	$pdf->addFondo1();
	$pdf->setEncabezado($row_rsEncabezado['nombrecliente'],$row_rsEncabezado['direccion'],$row_rsEncabezado['nombrecontacto'],$row_rsEncabezado['telefono'],$row_rsEncabezado['ciudad'],$row_rsEncabezado['correo'],$row_rsLevantamiento['consecutivo'],formatDate($row_rsEncabezado['fecha']),$row_rsEncabezado['idip'],$descripcion);
	$pdf->setGenero($row_rsAtendido['nombrereal']);
	$pdf->setNotas($row_rsLevantamiento['notas']);
	$pdf->AddPage("P");
	$pdf->addFondo2();
	$pdf->AddPage("P");
	$pdf->addFondo3();
	$pdf->Output('levantamiento'.'.pdf','I');

?>
<?php
mysql_free_result($rsEncabezado);

mysql_free_result($rsAtendido);

mysql_free_result($rsResponsable);

mysql_free_result($rsLevantamiento);
?>
