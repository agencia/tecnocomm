<?php 
require('fpdf.php');
define('FPDF_FONTPATH','font/');


class PDF2 extends FPDF{
	var $B;
	var $I;
	var $U;
	var $HREF;
	var $linea = 61;
	var $signo = '$';
	
	function PDF2($orientation='P',$unit='mm',$format='A4'){
		//Llama al constructor de la clase padre
		$this->FPDF($orientation,$unit,$format);
		$this->setMargins(0,0,0);
		//Iniciación de variables
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';
		$this->SetAutoPageBreak(false);
	}
	
	
	function setFondoConciliacion1Pagina($descuento = false){
	$this->Image('tec/conciliacion/logo.jpg',5,5,70,25);
		$this->Image('tec/conciliacion/datosrfc.jpg',200,12.2,70.418,12.492);
		$this->Image('tec/conciliacion/encabezado.jpg',5,33.2,286.5,20.3);
		$this->Image('tec/conciliacion/interlineadocorto.jpg',5,58,287.5,71);
		$this->Image('tec/conciliacion/logos.jpg',5,133,211,10);
		if($descuento ==  true){
			$this->Image('tec/conciliacion/totalDescuento.jpg',231,131.01,61,16);
		}else{
		$this->Image('tec/conciliacion/total.jpg',231,131.01,61,12);
		}
		$this->Image('tec/conciliacion/piepagina.jpg',5,148,285);
	
	}
	
	function setIP($ip=''){
			$this->SetFont('Arial','',8);
			$this->setXY(257,5);
			$this->Write(5,"IP:".$ip);
	}
	
	
	
	function setFondoConciliacion1de3(){	
		$this->Image('tec/conciliacion/logo.jpg',5,5,70,25);
		$this->Image('tec/conciliacion/datosrfc.jpg',200,12.2,70.418,12.492);
		$this->Image('tec/conciliacion/encabezado.jpg',5,33.2,286.5,20.3);
		$this->Image('tec/conciliacion/interlineadolargo.jpg',5,58,287,129.8);
		$this->Image('tec/conciliacion/logos.jpg',5,194,211,10);
		$this->Image('tec/conciliacion/subtotal.jpg',231,195,61.3,4.2);
	}
	
	function setFondoConciliacion2de3(){	
			$this->Image('tec/conciliacion/logo.jpg',5,5,70,25);
		$this->Image('tec/conciliacion/datosrfc.jpg',200,12.2,70.418,12.492);
		$this->Image('tec/conciliacion/encabezado.jpg',5,33.2,286.5,20.3);
		$this->Image('tec/conciliacion/interlineadolargo.jpg',5,62,287,129.8);
		$this->Image('tec/conciliacion/logos.jpg',5,194,211,10);
		$this->Image('tec/conciliacion/subtotal.jpg',230,56.3,61.3,4.2);
			$this->Image('tec/conciliacion/subtotal.jpg',231,195,61.3,4.2);
		}
	
	function setFondoConciliacion3de3($descuento = false){
			$this->Image('tec/conciliacion/logo.jpg',5,5,70,25);
		$this->Image('tec/conciliacion/datosrfc.jpg',200,12.2,70.418,12.492);
		$this->Image('tec/conciliacion/encabezado.jpg',5,33.2,286.5,20.3);
		$this->Image('tec/conciliacion/logos.jpg',5,62,211,10);
		if($descuento ==  true){
			$this->Image('tec/conciliacion/totalDescuento.jpg',231,61,61,16);
		}else{
		$this->Image('tec/conciliacion/total.jpg',231,60,61,12);
		}
		$this->Image('tec/conciliacion/piepagina.jpg',5,80,285);
	}
	
	function setTotalesConciliacion3de3(){
			
	}

function setEncabezadoConciliacion($nombre='',$direccion='',$contacto='',$telefono='',$ciudad='',$email='',$cotizacion='',$fecha=''){
			$this->SetFont('Arial','',8);
			$this->setXY(20,37.5);
			$this->Write(5,$nombre);
			
			$this->setXY(20,41.3);
			$this->Write(5,$direccion);
			
			$this->setXY(20,45.1);
			$this->Write(5,$contacto);
		
			$this->setXY(20,48.9);
			$this->Write(5,$telefono);
			
			$this->setXY(140,45.1);
			$this->Write(5,$ciudad);
			
			$this->setXY(140,48.9);
			$this->Write(5,$email);
			
			$this->setFont("Arial","IB","5");
			$this->setXY(245,37);
			$this->Write(5,"Cotizacion:");
			
			$this->SetFont('Arial','',8);
			$this->setXY(245,40);
			$this->Write(5,$cotizacion);
			
			$this->setXY(255,47);
			$this->Write(5,$fecha);
	}
	
	function comenzar($pags=1){
		
			switch($pags){
			
				case 1: $this->linea = 70.7;
						break;
				case 2: $this->linea =  70.7;
						break;
				case 3: $this->linea =  74.2;
						break;		
			}
		
		}
	
	function setTitleCoti($pags=1,$tit){
		$this->SetFont('Arial','','6.5');
			switch($pags){
			
				case 1: 
					$this->setXY(6,66.7);
					$this->Cell(260,3.8,$tit,0,1,'C');
					break;
				case 2: 
					$this->setXY(6,66.7);
					$this->Cell(260,3.8,$tit,0,1,'C');
					break;
				case 3: 
					$this->setXY(6,70.4);
					$this->Cell(260,3.8,$tit,0,1,'C');
					break;		
			}
	}
	
	function setContenido($partida='',$codigo='',$marca='',$descripcion='',$cantidad='',$unidad='',$precio='',$importe='',$cantidadinstalada='',$importeinstalado){
			$this->SetFont('Arial','','6.5');
			$this->setXY(5,$this->linea);
			$this->Cell(10,3.8,$partida,0,1,'C');
			
			$this->setXY(14.7,$this->linea);
			$this->Cell(19,3.8,$codigo,0,1,'C');
			
			$this->setXY(32.7,$this->linea);
			$this->Cell(19,3.8,$marca,0,1,'C');
			
			$this->setXY(52,$this->linea);
			$this->Cell(81,3.8,$descripcion,0,1,'L');
			
			$this->setXY(194.5,$this->linea);
			$this->Cell(12,3.8,$cantidad,0,1,'C');
			$this->setXY(206,$this->linea);
			$this->Cell(12,3.8,$cantidadinstalada,0,1,'C');
			
			$this->setXY(219,$this->linea);
			$this->Cell(12,3.8,$unidad,0,1,'C');
			
			
			if($precio){
			$this->setXY(230,$this->linea);
			$this->Cell(17,3.8,$this->signo,0,1,'L');
			}
			$this->setXY(230,$this->linea);
			$this->Cell(17,3.8,$precio,0,1,'R');
			
			
			
			if($importe){
			$this->setXY(247.5,$this->linea);
			$this->Cell(22,3.8,$this->signo,0,1,'L');
			}
			$this->setXY(247,$this->linea);
			$this->Cell(22,3.8,$importe,0,1,'R');
			
			
			if($importeinstalado){
			$this->setXY(269.5,$this->linea);
			$this->Cell(22,3.8,$this->signo,0,1,'L');
			}
			$this->setXY(269,$this->linea);
			$this->Cell(22,3.8,$importeinstalado,0,1,'R');
			$this->linea = $this->linea + 3.9;
	}
	
	
	function setTotalesDescuento($descuento='',$subtotal='',$iva='',$total='',$subtotal2='',$iva2='',$total2=''){
			
		
	}

	function setPieConciliacion($subtotal='',$iva='',$total='',$nombrefirma='',$puestofirma='',$emailfirma='',$formapago='',$moneda='',$vigencia='',$tiempoentrega='',$garantia='',$notas='',$subtotal2='',$iva2='',$total2='',$descuento="0",$descuentoinstalado=0,$llevadescuento=false){
	
		$this->SetFont('Arial','B',6.5);
		$this->SetTextColor(23,23,150);
		
		
		if($llevadescuento == true){
		//descuento
		$this->setXY(249,131);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,131);
		$this->Cell(22,3.8,$descuento,0,1,'R');	
		
		$this->setXY(249,135);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,135);
		$this->Cell(22,3.8,$subtotal,0,1,'R');	
		
		$this->setXY(249,139);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,139);
		$this->Cell(22,3.8,$iva,0,1,'R');	
		
		$this->setXY(249,143);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,143);
		$this->Cell(22,3.8,$total,0,1,'R');
		
		
		//descuenot2
		$this->setXY(270,131);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,131);
		$this->Cell(22,3.8,$descuentoinstalado,0,1,'R');
		
		$this->setXY(270,135);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,135);
		$this->Cell(22,3.8,$subtotal2,0,1,'R');	
		
		$this->setXY(270,139);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,139);
		$this->Cell(22,3.8,$iva2,0,1,'R');	
		
		$this->setXY(270,143);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,143);
		$this->Cell(22,3.8,$total2,0,1,'R');
		}else{
			
		$this->setXY(249,131);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,132);
		$this->Cell(22,3.8,$subtotal,0,1,'R');	
		
		$this->setXY(249,135);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,135);
		$this->Cell(22,3.8,$iva,0,1,'R');	
		
		$this->setXY(249,139);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,139);
		$this->Cell(22,3.8,$total,0,1,'R');
		
		$this->setXY(270,131);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,132);
		$this->Cell(22,3.8,$subtotal2,0,1,'R');	
		
		$this->setXY(270,135);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,135);
		$this->Cell(22,3.8,$iva2,0,1,'R');	
		
		$this->setXY(270,139);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,139);
		$this->Cell(22,3.8,$total2,0,1,'R');
		}
		
		
		$this->SetFont('Arial','B',7);
		$this->SetTextColor(0,0,0);
		$this->setXY(207,174);
		$this->Cell(76,3.8,$nombrefirma,0,1,'C');
		$this->setXY(207,177);
		$this->Cell(76,3.8,$puestofirma,0,1,'C');
		$this->setXY(207,180);
		$this->Cell(76,3.8,$emailfirma,0,1,'C');
		
		
		$this->SetFont('Arial','B',6.5);
		$this->SetTextColor(0,0,0);
		$this->setXY(40,151);
		$this->Write(5,$formapago);
		
		$M = substr  (  $moneda, 0 ,35);
		$M2 = substr  (  $moneda, 35 ,200);
		
		$this->setXY(40,158);
		$this->Write(5,$M);
		$this->setXY(40,160.5);
		$this->Write(5,$M2);
		
		$this->setXY(40,165.5);
		$this->Write(5,$vigencia);
		$this->setXY(40,172);
		$this->Write(5,$tiempoentrega);
		$this->setXY(40,178);
		$this->Write(5,$garantia);
		
		 $this->SetFont('Arial','',5);
		$this->setXY(15,190);
		$this->MultiCell(182,2.5,$notas,0,'L');
		
	}
	
	function setPieConciliacion2($subtotal='',$iva='',$total='',$nombrefirma='',$puestofirma='',$emailfirma='',$formapago='',$moneda='',$vigencia='',$tiempoentrega='',$garantia='',$notas='',$subtotal2='',$iva2='',$total2='',$descuento="0",$descuentoinstalado=0,$llevadescuento=false){
		
		$this->SetFont('Arial','B',6.5);
		$this->SetTextColor(23,23,150);
		
		
		
		if($llevadescuento == true){
		
			$this->setXY(249,61);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,61);
		$this->Cell(22,3.8,$descuento,0,1,'R');	
		
		$this->setXY(249,65);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,65);
		$this->Cell(22,3.8,$subtotal,0,1,'R');	
		
		$this->setXY(249,69);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,69);
		$this->Cell(22,3.8,$iva,0,1,'R');	
		
		$this->setXY(249,73);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,73);
		$this->Cell(22,3.8,$total,0,1,'R');
		
			$this->setXY(270,61);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,61);
		$this->Cell(22,3.8,$descuentoinstalado,0,1,'R');	
		
		$this->setXY(270,65);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,65);
		$this->Cell(22,3.8,$subtotal2,0,1,'R');	
		
		$this->setXY(270,69);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,69);
		$this->Cell(22,3.8,$iva2,0,1,'R');	
		
		$this->setXY(270,73);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,73);
		$this->Cell(22,3.8,$total2,0,1,'R');
		}else{
		
		
		$this->setXY(249,60);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,60);
		$this->Cell(22,3.8,$subtotal,0,1,'R');	
		
		$this->setXY(249,64);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,64);
		$this->Cell(22,3.8,$iva,0,1,'R');	
		
		$this->setXY(249,68);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(247,68);
		$this->Cell(22,3.8,$total,0,1,'R');
		
		$this->setXY(270,60);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,60);
		$this->Cell(22,3.8,$subtotal2,0,1,'R');	
		
		$this->setXY(270,64);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,64);
		$this->Cell(22,3.8,$iva2,0,1,'R');	
		
		$this->setXY(270,68);
		$this->Cell(22,3.8,$this->signo,0,1,'L');
		$this->setXY(270,68);
		$this->Cell(22,3.8,$total2,0,1,'R');
	
		}
	
		$this->SetFont('Arial','B',7);
		$this->SetTextColor(0,0,0);
		$this->setXY(207,106);
		$this->Cell(76,3.8,$nombrefirma,0,1,'C');
		$this->setXY(207,109);
		$this->Cell(76,3.8,$puestofirma,0,1,'C');
		$this->setXY(207,112);
		$this->Cell(76,3.8,$emailfirma,0,1,'C');
		
		
		$this->SetFont('Arial','B',6.5);
		$this->SetTextColor(0,0,0);
		$this->setXY(40,83);
		$this->Write(5,$formapago);
		
		$M = substr  (  $moneda, 0 ,35);
		$M2 = substr  (  $moneda, 35 ,200);
		
		$this->setXY(40,89);
		$this->Write(5,$M);
		$this->setXY(40,92);
		$this->Write(5,$M2);
		
		$this->setXY(40,97);
		$this->Write(5,$vigencia);
		$this->setXY(40,104);
		$this->Write(5,$tiempoentrega);
		$this->setXY(40,110);
		$this->Write(5,$garantia);
		
		 $this->SetFont('Arial','',5);
		$this->setXY(15,123);
		$this->MultiCell(182,2.5,$notas,0,'L');
		
	}
	
		function setFirmaConciliacion1($img){
			$this->Image($img,225,153,0,20);
		}
		
		function setFirmaConciliacion2($img){
			$this->Image($img,225,85,0,20);
		}
		
		function setSubTotal12($cantidad1,$cantidad2){
				$this->setXY(247.5,195);
				$this->Cell(22,3.8,$cantidad1,0,1,'R');
				
				$this->setXY(269.5,195);
				$this->Cell(22,3.8,$cantidad2,0,1,'R');	
		}
		
		function setPagina($pag){
			
				$this->setXY(265,200);
				$this->Write(5,$pag);
		}
	
}

/*
$test = new PDF2('L','mm','A4');
$test->AddPage();
$test->setFondoConciliacion1Pagina(true);
$test->setEncabezadoConciliacion('Leury Sanchez','Asientos #208','Leury Sanchez','4492223810','Teocaltiche','leuryemmanuel@yahoo.com.mx','C-001Ley09dfs','10 julio 2009');
$test->setTitle(1,'INSTALACION EN NO SE DONDE PUTAS Y QUE CHINGUE SU MADRE EL WEY QUE ES CULERO');
$test->comenzar(1);
$test-> setContenido('1','232435','TECNOCOMM','1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890','10','SERV','10.90','109.00','5','59.50');

$test->AddPage();
$test->setFondoConciliacion1de3();

$test->setTitle(2,'INSTALACION EN NO SE DONDE PUTAS Y QUE CHINGUE SU MADRE EL WEY QUE ES CULERO');
$test->comenzar(2);
$test-> setContenido('1','232435','TECNOCOMM','ROUTER CALIBRE 5','10','SERV','10.90','109.00','5','59.50');

$test->AddPage();
$test->setFondoConciliacion2de3();

$test->setTitle(3,'INSTALACION EN NO SE DONDE PUTAS Y QUE CHINGUE SU MADRE EL WEY QUE ES CULERO');
$test->comenzar(3);
$test-> setContenido('1','232435','TECNOCOMM','ROUTER CALIBRE 5','10','SERV','10.90','109.00','5','59.50');

$test->AddPage();
$test->setFondoConciliacion3de3();

$test-> setContenido('1','232435','TECNOCOMM','ROUTER CALIBRE 5','10','SERV','10.90','109.00','5','59.50');

$test->AddPage();
$test->setFondoConciliacion3de3(true);
//$test-> setContenido('1','232435','TECNOCOMM','ROUTER CALIBRE 5','10','SERV','10.90','109.00','5','59.50');
$test->setEncabezadoConciliacion('Leury Sanchez','Asientos #208','Leury Sanchez','4492223810','Teocaltiche','leuryemmanuel@yahoo.com.mx','C-001Ley09dfs','10 julio 2009');

$test->Output();
*/
?>