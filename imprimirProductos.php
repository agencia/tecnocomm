<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
<?php
require_once('fpdf.php');
define('FPDF_FONTPATH','font/');
class Tabla extends FPDF{

	var $B;
var $I;
var $U;
var $HREF;
var $signo = '$';

	function PDF($orientation='P',$unit='mm',$format='A4'){
		//Llama al constructor de la clase padre
		$this->FPDF($orientation,$unit,$format);
		$this->setMargins(10,10,10);
		$this->SetAutoPageBreak(true,10);
		//Iniciación de variables
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';
	}

	function setHeader($cols,$ws,$aling){
		
		$this->cols = $cols;
		$this->ws = $ws;
		$this->aling = $aling;
	
	}
	
	function setData($data){
			$this->data = $data;
	}
	
	function generate(){
	$this->titulo();
		$this->SetFillColor(255,0,0);
    	$this->SetTextColor(255);
    	$this->SetDrawColor(128,0,0);
    	$this->SetLineWidth(.3);
   		$this->SetFont('Arial','B',6);
		$i=0;
		foreach($this->cols as $col){
			$this->Cell($this->ws[$i],5,$col,1,0,'C',1);
			$i++;
		}
		
		 //Restauración de colores y fuentes
   		 $this->SetFillColor(224,235,255);
    	$this->SetTextColor(0);
   	 	$this->SetFont('Arial','',6);
		$fill= false;
		$this->Ln();
		$line=$this->GetY();
		
		//for($j=0;$j<50;$j++){
		foreach($this->data as $row){
		
	
		
			$i=0;
				foreach($this->cols as $col){
				$this->Cell($this->ws[$i],3,$row[$i],'LR',0,$this->aling[$i],$fill);
			$i++;
			
		}
			
			if($j == 80){
			$this->Ln();
			$this->Cell(190,3,'','T',0);
			
				$this->AddPage();
				$this->titulo();
				$j=-1;
					$i=0;
					$this->SetFillColor(255,0,0);
    	$this->SetTextColor(255);
    	$this->SetDrawColor(128,0,0);
    	$this->SetLineWidth(.3);
   		$this->SetFont('Arial','',8);
		foreach($this->cols as $col){
			$this->Cell($this->ws[$i],7,$col,1,0,'C',1);
			$i++;
		}
			}
			$j++;
	$this->Ln();	
		 $this->SetFillColor(224,235,255);
    	$this->SetTextColor(0);
   	 	$this->SetFont('Arial','',6);	
			
        $fill=!$fill;
		}
		
	
		
		
	}
function titulo(){
	$this->SetFont('Arial','B',6);
	$this->Cell(190,3,'LISTA DE PRODUCTOS DE TECNOCOMM',0);
	$this->Ln();
	$this->Cell(95,3,'Fecha Impresion '.date("d/m/Y G:i"),0,'','L');
	$this->Cell(95,3,'Pagina '.$this->PageNo(). ' de {nb}',0,'','R');
	$this->Ln();

}

	
}

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

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
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
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
$query_rsProducto = "SELECT * FROM articulo ORDER BY nombre ASC";
$rsProducto = mysql_query($query_rsProducto, $tecnocomm) or die(mysql_error());
$row_rsProducto = mysql_fetch_assoc($rsProducto);
$totalRows_rsProducto = mysql_num_rows($rsProducto);

$signo = array('$','US$');

$i=0;
do{


	if(strlen($row_rsProducto['nombre'])<40){
		
		

	$data[$i][0] = $i+1;
	$data[$i][1] = utf8_decode($row_rsProducto['codigo']);
	$data[$i][2] = utf8_decode($row_rsProducto['marca']);
	$data[$i][3] = utf8_decode(trim($row_rsProducto['nombre']))." ".strlen($row_rsProducto['nombre']);
	$data[$i][4] = utf8_decode($row_rsProducto['medida']);
	$data[$i][5] = utf8_decode($row_rsProducto['empaque']);
	$data[$i][6] = format_money($row_rsProducto['precio']);
	$data[$i][7] = format_money($row_rsProducto['instalacion']);
	$data[$i][8] = $signo[$row_rsProducto['moneda']];
	
		
	}else{
	
	
	
	
	unset($line);//limpiamos lineas 	
	//separamos por palabras
	
	$letania = utf8_decode(trim($row_rsProducto['nombre'])." ".strlen($row_rsProducto['nombre']));
	
	$palabras = split(" ",$letania);
	
	$num = $long = $countc = 0;
	foreach($palabras as $palabra){
			
			$long = strlen($palabra);
			
			if(($countc+$long+1) < 40){
			$countc = $countc+$long+1;
			}else{
			$num++;
			$countc=$long;
			}
			
			$line[$num] = $line[$num]." ".$palabra;
	}
	
	$data[$i][0] = $i+1;
	$data[$i][1] = utf8_decode($row_rsProducto['codigo']);
	$data[$i][2] = utf8_decode($row_rsProducto['marca']);
	$data[$i][3] = $line[0];
	$data[$i][4] = utf8_decode($row_rsProducto['medida']);
	$data[$i][5] = utf8_decode($row_rsProducto['empaque']);
	$data[$i][6] = format_money($row_rsProducto['precio']);
	$data[$i][7] = format_money($row_rsProducto['instalacion']);
	$data[$i][8] = $signo[$row_rsProducto['moneda']];
	
	for($x=1;$x<count($line);$x++){
	$i++;
	$data[$i][0] = "";
	$data[$i][1] = "";
	$data[$i][2] = "";
	$data[$i][3] = $line[$x];
	$data[$i][4] = "";
	$data[$i][5] = "";
	$data[$i][6] = "";
	$data[$i][7] = "";
		$data[$i][8] = "";
	
	
	}
	
	
	}
	
	
	
	
$i++;
}while($row_rsProducto = mysql_fetch_assoc($rsProducto));

//print_r($data);

$pdf=new Tabla();
//Títulos de las columnas

$header=array('Id','Codigo','Marca','Descripcion','U. Medida','Empaque','P.Unitario','Instalacion','$');
$ws = array(10,25,25,60,10,15,20,20,5);
$aling = array('C','C','C','L','C','C','R','R','L');
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->setHeader($header,$ws,$aling);
$pdf->setData($data);
$pdf->generate();
$pdf->Output();
?>