<?php
require('fpdf.php');
define('FPDF_FONTPATH','font/');


$pdf = new FPDF('P','mm',array(215,280));
$pdf->setMargins(0,0,0);
$pdf->AddPage();
//Set font
$pdf->SetFont('Arial','B',10);
//Move to 8 cm to the right
$pdf->Cell(80);
$pdf->SetXY(10,10);
//Texto centrado en una celda con cuadro  20*10 mm y salto de línea
$pdf->Cell(30,10,'texto en 10,10',1,1,'C');
$pdf->SetXY(45,10);
//Texto centrado en una celda con cuadro  20*10 mm y salto de línea
$pdf->Cell(30,10,'texto en 45,10',1,1,'C');
$pdf->SetXY(10,50);
//Texto centrado en una celda con cuadro  20*10 mm y salto de línea
$pdf->Cell(30,10,'texto en 10,50',1,1,'C');
$pdf->Output();


?>