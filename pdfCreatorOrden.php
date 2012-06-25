<?php
require('fpdf.php');
define('FPDF_FONTPATH','font/');




class PDF extends FPDF
{
var $B;
var $I;
var $U;
var $HREF;
var $linea = 117;
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
			$this->SetFont('Arial','B',8);
			$this->setXY(180,5);
			$this->Write(5,"IP:".$ip);
	}
	
	function setDatos($nopersonas='',$totalhrs='',$cargo='',$pendiente=''){
			$this->SetFont('Arial','',8);
			$this->setXY(105,96.5);
			$this->Write(5,$nopersonas);
			
			$this->SetFont('Arial','B',8);
			$this->setXY(149,96.5);
			$this->Write(5,$totalhrs);
			
			if($cargo==1){
				$this->SetFont('Arial','B',8);
				$this->setXY(184,96.5);
				$this->Write(5,"X");
			}elseif($cargo==0){
				$this->SetFont('Arial','B',8);
				$this->setXY(195,96.5);
				$this->Write(5,"X");
			}
			
			if($pendiente==1){
				$this->SetFont('Arial','B',8);
				$this->setXY(45,183);
				$this->Write(5,"X");
			}elseif($pendiente==0){
				$this->SetFont('Arial','B',8);
				$this->setXY(60,183);
				$this->Write(5,"X");
			}
			
			
	}


	function setEncabezado($cliente='',$os='',$domicilio='',$tel='',$reporto='',$fecha=''){
			$this->SetFont('Arial','',8);
			$this->setXY(18.5,33.5);
			$this->Write(5,$cliente);
			
			$this->setXY(135,33.5);
			$this->Write(5,"ORDEN DE SERVICIO: ".$os);
			
			$this->setXY(22,39.5);
			$this->Write(5,$domicilio);
			
			$this->setXY(170,39.5);
			$this->Write(5,$tel);
			
			$this->setXY(22,45.5);
			$this->Write(5,$reporto);
			
			$this->setXY(170,45);
			$this->Write(5,$fecha);
			
			
			
	}
	
	function descripcion($descripcion=''){
			$this->SetFont('Arial','','8');
			$this->setXY(8,59.5);
			$this->MultiCell(195,5.2,$descripcion,0,'L');
	}
	
	function trabajoRealizado($descripcion=''){
			$this->SetFont('Arial','','8');
			$this->setXY(8,78.5);
			$this->MultiCell(195,5.2,$descripcion,0,'L');
	}

	function setTotales($material='', $manoobra='', $importe='', $iva='', $total=''){
		$this->SetFont('Arial','B',8);
		$this->setXY(183,178.4);
		$this->Cell(23.5,5.4,$material,0,0,'R');
		$this->setXY(183,185);
		$this->Cell(23.5,5.4,$manoobra,0,0,'R');
		$this->setXY(183,190);
		$this->Cell(23.5,5.4,$importe,0,0,'R');
		$this->setXY(183,195.5);
		$this->Cell(23.5,5.4,$iva,0,0,'R');
		$this->setXY(183,202.2);
		$this->Cell(23.5,5.4,$total,0,0,'R');
		
	}
	
	function setDescripcion($descripcion=''){
			$this->SetFont('Arial','','8');
			$this->setXY(8,197.5);
			$this->MultiCell(155,5.2,$descripcion,0,'L');
	}
	
	function setObservaciones($descripcion=''){
			$this->SetFont('Arial','','8');
			$this->setXY(8,216.5);
			$this->MultiCell(195,5.2,$descripcion,0,'L');
	}
	
	function setGenero($genero) {
		$this->SetFont('Arial', '','6');
		$this->setXY(140, 271);
		$this->MultiCell(50,5.2,"Genero: " . $genero,0,'R');
	}

	
	function createFondo(){
	
		$this->Image('images/ordenServicio.jpg',0,0,210,299);
		
	}
	
	function addPartida($cantidad='',$codigo='',$marca='',$descripcion='',$pu='',$importe=''){
		$this->SetFont('Arial','',6.5);
		$this->setXY(4,$this->linea);
		$this->Cell(10,5.4,$cantidad,'',0,'C');
		$this->Cell(21,5.4,$codigo,'',0,'C');
		$this->Cell(21,5.4,$marca,'',0,'C');
		$this->MultiCell(105,5.4,$descripcion);
		$y = $this->getY();
		$this->setXY(158,$this->linea);
		$this->Cell(21,5.4,$pu,'',0,'R');
		$this->Cell(23.5,5.4,$importe,'',0,'R');
		
		
		$this->linea += ($y - $this->linea);
	}
	
	
		
		
}

?>
