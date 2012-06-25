<?php
require('fpdf.php');
define('FPDF_FONTPATH','font/');




class PDF extends FPDF
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

	function setIP($ip=''){
			$this->SetFont('Arial','',8);
			$this->setXY(180,5);
			$this->Write(5,"IP:".$ip);
	}


	function setEncabezado($nombre='',$direccion='',$contacto='',$telefono='',$ciudad='',$email='',$cotizacion='',$fecha=''){
			$this->SetFont('Arial','',8);
			$this->setXY(25,31);
			$this->Write(5,$nombre);
			
			$this->setXY(25,34.7);
			$this->Write(5,$direccion);
			
			$this->setXY(25,38.6);
			$this->Write(5,$contacto);
			
			$this->setXY(25,42.4);
			$this->Write(5,$telefono);
			
			$this->setXY(95,38.6);
			$this->Write(5,$ciudad);
			
			$this->setXY(95,42.5);
			$this->Write(5,$email);
			
			$this->setXY(168,32);
			$this->Write(5,$cotizacion);
			
			$this->setXY(163,41);
			$this->Write(5,$fecha);
	}
	
	function setContenido($partida='',$codigo='',$marca='',$descripcion='',$cantidad='',$unidad='',$precio='',$importe=''){
			$this->SetFont('Arial','','6.5');
			$this->setXY(10,$this->linea);
			$this->Cell(10,3.8,$partida,0,1,'C');
			
			$this->setXY(20,$this->linea);
			$this->Cell(18,3.8,$codigo,0,1,'C');
			
			$this->setXY(38,$this->linea);
			$this->Cell(19,3.8,$marca,0,1,'C');
			
			$this->setXY(58,$this->linea);
			$this->Cell(79,3.8,$descripcion,0,1,'L');
			
			$this->setXY(137.5,$this->linea);
			$this->Cell(12,3.8,$cantidad,0,1,'C');
			
			$this->setXY(149.5,$this->linea);
			$this->Cell(12,3.8,$unidad,0,1,'C');
			
			
			if($precio){
			$this->setXY(161,$this->linea);
			$this->Cell(17,3.8,$this->signo,0,1,'L');
			}
			$this->setXY(162.5,$this->linea);
			$this->Cell(17,3.8,$precio,0,1,'R');
			
			if($importe){
			$this->setXY(179,$this->linea);
			$this->Cell(22,3.8,$this->signo,0,1,'L');
			}
			$this->setXY(179,$this->linea);
			$this->Cell(22,3.8,$importe,0,1,'R');	
			
			$this->linea = $this->linea + 4.05; //3.836;
	}

	function setPie($subtotal='',$iva='',$total='',$nombrefirma='',$puestofirma='',$emailfirma='',$formapago='',$moneda='',$vigencia='',$tiempoentrega='',$garantia='',$notas='',$piva="15 % I.V.A"){
		
		$this->SetFont('Arial','B',6.5);
		$this->SetTextColor(23,23,150);
		
		$this->setXY(178.5,197);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(179,197);
		$this->Cell(22,3.8,$subtotal,0,1,'R');	
		
		$this->setXY(178.5,201);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(179,201);
		$this->Cell(22,3.8,$iva,0,1,'R');	
		
		
		$this->SetFont('Arial','B',8);
		$this->SetTextColor(17,11,11);
		$this->setXY(156,200.5);
		$this->Cell(22,3.8,$piva,0,1,'R');	
		
		
		$this->SetFont('Arial','B',6.5);
		$this->SetTextColor(23,23,150);
		$this->setXY(178.5,205);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(179,205);
		$this->Cell(22,3.8,$total,0,1,'R');
		
		$this->SetFont('Arial','B',7);
		$this->SetTextColor(0,0,0);
		$this->setXY(132,238);
		$this->Cell(76,3.8,$nombrefirma,0,1,'C');
		$this->setXY(132,241);
		$this->Cell(76,3.8,$puestofirma,0,1,'C');
		$this->setXY(132,244);
		$this->Cell(76,3.8,$emailfirma,0,1,'C');
		
		$this->SetFont('Arial','B',6.5);
		$this->SetTextColor(0,0,0);
		$this->setXY(35,216);
		$this->Write(5,$formapago);
		
	$M = substr  (  $moneda, 0 ,35);
	$M2 = substr  (  $moneda, 35 ,200);
		
		$this->setXY(35,220.5);
		$this->Write(5,$M);
		$this->setXY(35,223.5);
		$this->Write(5,$M2);
		
		$this->setXY(35,230);
		$this->Write(5,$vigencia);
		$this->setXY(35,236);
		$this->Write(5,$tiempoentrega);
		$this->setXY(35,243);
		$this->Write(5,$garantia);
		
		 $this->SetFont('Arial','',5);
		$this->setXY(15,252.5);
		$this->MultiCell(182,2.5,$notas,0,'L');
		
	}
	
		function resetLine(){
			$this->linea = 61;
		}
	
			function setFirma($img){
			$this->Image($img,152.5,216,0,20);
		}

	
	function createFondo(){
	
		$this->Image('tec/tec.jpg',15,5,56,20);
		$this->Image('tec/juan.jpg',119.752,12.2,70.418,12.492);
		$this->Image('tec/encabezado.jpg',10,27,190.825,20.378);
		$this->Image('tec/borde.jpg',10,48.5,190.825,146.602);
		$this->Image('tec/logos.jpg',10,199,148.812,7.294);
		$this->Image('tec/total.jpg',162,196.01,39.26,13.102);
		$this->Image('tec/piepagina.jpg',10,211,190.825);
	}
	
	function createFondo1(){
	
		$this->Image('tec/tec.jpg',15,5,56,20);
		$this->Image('tec/juan.jpg',119.752,12.2,70.418,12.492);
		$this->Image('tec/encabezado.jpg',10,27,190.825,20.378);
		$this->Image('tec/borde.jpg',10,48.5,190.825,146.602);
		$this->Image('tec/logos.jpg',10,199,148.812,7.294);
		$this->Image('tec/subtotal.jpg',162,200,39.26);
		//$this->Image('tec/piepagina.jpg',10,211,190.825);
		
		}
		
		function createFondo2(){
	
		$this->Image('tec/tec.jpg',15,5,56,20);
		$this->Image('tec/juan.jpg',119.752,12.2,70.418,12.492);
		$this->Image('tec/encabezado.jpg',10,27,190.825,20.378);
		$this->Image('tec/subtotal.jpg',162,49,39.26);
		$this->Image('tec/borde.jpg',10,56,190.825,146.602);
		$this->Image('tec/logos.jpg',10,204.5,148.812,7.294);
		$this->Image('tec/subtotal.jpg',162,205.01,39.26);
		//$this->Image('tec/piepagina.jpg',10,211,190.825);
		
		}
		
		function createFondo3(){
	
		$this->Image('tec/tec.jpg',15,5,56,20);
		$this->Image('tec/juan.jpg',119.752,12.2,70.418,12.492);
		$this->Image('tec/encabezado.jpg',10,27,190.825,20.378);
		$this->Image('tec/subtotal.jpg',162,49,39.26);
		$this->Image('tec/borde2.jpg',10,56,190.825,138.93);
		$this->Image('tec/logos.jpg',10,199,148.812,7.294);
		$this->Image('tec/total.jpg',162,196.01,39.26,13.102);
		$this->Image('tec/piepagina.jpg',10,211,190.825);
		
		}
		
		function createFondoOrden(){
		$this->Image('tec/tec.jpg',15,5,56,20);
		$this->Image('tec/juan.jpg',119.752,12.2,70.418,12.492);
		$this->Image('tec/encabezadoProveedor.jpg',10,27,190.825,20.378);
		$this->Image('tec/borde.jpg',10,48.5,190.825,146.602);
		$this->Image('tec/logos.jpg',10,199,148.812,7.294);
		$this->Image('tec/total.jpg',162,196.01,39.26,13.102);
		$this->Image('tec/piepaginap.jpg',10,211,190.825);	
		}

	function createFondoOrden1(){
	
		$this->Image('tec/tec.jpg',15,5,56,20);
		$this->Image('tec/juan.jpg',119.752,12.2,70.418,12.492);
		$this->Image('tec/encabezadoProveedor.jpg',10,27,190.825,20.378);
		$this->Image('tec/borde.jpg',10,48.5,190.825,146.602);
		$this->Image('tec/logos.jpg',10,199,148.812,7.294);
		$this->Image('tec/subtotal.jpg',162,200,39.26);
		//$this->Image('tec/piepagina.jpg',10,211,190.825);
		
		}
		
		function createFondoOrden2(){
	
		$this->Image('tec/tec.jpg',15,5,56,20);
		$this->Image('tec/juan.jpg',119.752,12.2,70.418,12.492);
		$this->Image('tec/encabezadoProveedor.jpg',10,27,190.825,20.378);
		$this->Image('tec/subtotal.jpg',162,49,39.26);
		$this->Image('tec/borde.jpg',10,56,190.825,146.602);
		$this->Image('tec/logos.jpg',10,204.5,148.812,7.294);
		$this->Image('tec/subtotal.jpg',162,205.01,39.26);
		//$this->Image('tec/piepagina.jpg',10,211,190.825);
		
		}
		
		function createFondoOrden3(){
		$this->Image('tec/tec.jpg',15,5,56,20);
		$this->Image('tec/juan.jpg',119.752,12.2,70.418,12.492);
		$this->Image('tec/encabezadoProveedor.jpg',10,27,190.825,20.378);
		$this->Image('tec/subtotal.jpg',162,49,39.26);
		$this->Image('tec/borde2.jpg',10,56,190.825,138.93);
		$this->Image('tec/logos.jpg',10,199,148.812,7.294);
		$this->Image('tec/total.jpg',162,196.01,39.26,13.102);
		$this->Image('tec/piepaginap.jpg',10,211,190.825);		
		}

		function paginacion($texto = ''){
		    $this->SetFont('Arial','',7);
			$this->setXY(160,265);
			$this->Cell(72.40,7,$texto,0,1,'C');
		}
		
		function subtotal1($subtotal=''){
		$this->setXY(179,200);
		$this->Cell(22,6,$this->signo,0,1,'L');
		$this->setXY(179,200);
		$this->Cell(22,6,$subtotal,0,1,'R');
		}
		
		function subtotal2($subtotal1='',$subtotal2=''){
		$this->setXY(179,49);
		$this->Cell(22,6,$this->signo,0,1,'L');
		$this->setXY(179,49);
		$this->Cell(22,6,$subtotal1,0,1,'R');
		$this->setXY(179,205);
		$this->Cell(22,6,$this->signo,0,1,'L');
		$this->setXY(179,205);
		$this->Cell(22,6,$subtotal2,0,1,'R');
		}
		
		function subtotal3($subtotal=''){
		$this->setXY(179,49);
		$this->Cell(22,6,$this->signo,0,1,'L');
		$this->setXY(179,49);
		$this->Cell(22,6,$subtotal,0,1,'R');
		}
		
		function catidadLetra($cantidad){
			$this->setXY(13,194);
			$this->Write(5,$cantidad);
		}
		
		function catidadLetra1($cantidad){
			$this->setXY(13,197);
			$this->Write(5,$cantidad);
			
		}
		
		function setCot($title){
			$this->setFont("Arial","IB","5");
			$this->setXY(153.5,32);
			$this->Write(5,$title);
		}
		
		function setTitle($title){
		$this->SetFont('Arial','B','6.5');
			$this->setXY(58,57.5);
			$this->Cell(79,3.8,$title,0,1,'C');
			
		}
		
			function setTitle2($title){
		$this->SetFont('Arial','B','6.5');
			$this->setXY(58,65.1);
			$this->Cell(79,3.8,$title,0,1,'C');
			
		}
		
		function setCostos($mensaje){
			$this->SetTextColor(255,0,0);
			$this->setXY(10,25);
			$this->SetFont('Arial','B','7');
			$this->Cell(79,3.8,$mensaje,0,1,'C');
			$this->SetTextColor(0,0,0);

		}
		
		function setDescuento($descuento,$cantidad){
			$this->setFont('Arial','B',6.5);
			$this->setXY(157.5,190.5);
			$this->Write(5,"DESCUENTO: ".$descuento);
			$this->setFont('Arial','B',6.5);
			$this->setXY(179,190.5);
			$this->Cell(22,5,$this->signo,0,1,'L');
			$this->setXY(179,190.5);
			$this->Cell(22,5,$cantidad,0,1,'R');	
		}
}

?>
