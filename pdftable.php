<?php
require('fpdf.php');
define('FPDF_FONTPATH','font/');




class PDF extends FPDF
{
var $B;
var $I;
var $U;
var $HREF;
var $headers;
var $rows;

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

	
}

?>
