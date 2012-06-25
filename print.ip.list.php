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
		$this->SetFillColor(21,87,173);
    	$this->SetTextColor(255);
    	$this->SetDrawColor(21,87,173);
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
	$this->Cell(190,3,'LISTA DE IP DE TECNOCOMM',0);
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

if(isset($_GET['filtro']) && $_GET['filtro'] == 1){
	
	switch($_GET['estado']){
		
		case 0:
		case 1:
		case 2:
		if(isset($_GET['bus']) && $_GET['bus']!= ""){
			$query_rsIp = sprintf("SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente AND (c.nombre like %s OR c.abreviacion like %s OR i.descripcion LIKE %s OR i.idip = %s ) AND i.estado = %s  ORDER BY idip DESC",
								GetSQLValueString($_GET['bus'],"text"),
								GetSQLValueString($_GET['bus'],"text"),
								GetSQLValueString($_GET['bus'],"text"),
								GetSQLValueString($_GET['bus'],"text"),
								GetSQLValueString($_GET['estado'],"text"));
			
			
			
		}else{
			$query_rsIp = sprintf("SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente AND i.estado = %s  ORDER BY idip DESC",
								GetSQLValueString($_GET['estado'],"int"));
		}
		break;
		case -1:
		default:
	if(isset($_GET['bus']) && $_GET['bus']!= ""){
			$query_rsIp = sprintf("SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente AND (c.nombre like %s OR c.abreviacion like %s OR i.descripcion LIKE %s OR i.idip = %s ) ORDER BY idip DESC",
								GetSQLValueString($_GET['bus'],"text"),
								GetSQLValueString($_GET['bus'],"text"),
								GetSQLValueString($_GET['bus'],"text"),
								GetSQLValueString($_GET['bus'],"text"));
		}else{
			$query_rsIp = sprintf("SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente  ORDER BY idip DESC");
		}
	
	}
	
	
}else{
	$query_rsIp = "SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente ORDER BY idip DESC";
}




$currentPage = $_SERVER["PHP_SELF"];

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuario = "SELECT * FROM usuarios ORDER BY username ASC";
$rsUsuario = mysql_query($query_rsUsuario, $tecnocomm) or die(mysql_error());
$row_rsUsuario = mysql_fetch_assoc($rsUsuario);
$totalRows_rsUsuario = mysql_num_rows($rsUsuario);

$maxRows_rsIp = 30;
$pageNum_rsIp = 0;
if (isset($_GET['pageNum_rsIp'])) {
  $pageNum_rsIp = $_GET['pageNum_rsIp'];
}
$startRow_rsIp = $pageNum_rsIp * $maxRows_rsIp;

mysql_select_db($database_tecnocomm, $tecnocomm);
//$query_rsIp = "SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente ORDER BY idip DESC";
$query_limit_rsIp = sprintf("%s LIMIT %d, %d", $query_rsIp, $startRow_rsIp, $maxRows_rsIp);
$rsIp = mysql_query($query_limit_rsIp, $tecnocomm) or die(mysql_error());
$row_rsIp = mysql_fetch_assoc($rsIp);

if (isset($_GET['totalRows_rsIp'])) {
  $totalRows_rsIp = $_GET['totalRows_rsIp'];
} else {
  $all_rsIp = mysql_query($query_rsIp);
  $totalRows_rsIp = mysql_num_rows($all_rsIp);
}
$totalPages_rsIp = ceil($totalRows_rsIp/$maxRows_rsIp)-1;

$queryString_rsIp = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsIp") == false && 
        stristr($param, "totalRows_rsIp") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsIp = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsIp = sprintf("&totalRows_rsIp=%d%s", $totalRows_rsIp, $queryString_rsIp);


$estado=array(0=>"Abierto",1=>"Proceso",2=>"Finalizado");

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsResponsable = sprintf("SELECT pp.*,u.nombrereal,u.username FROM ip i LEFT JOIN proyecto_personal pp ON i.idip = pp.idip LEFT JOIN usuarios u ON u.id = pp.idusuario WHERE pp.estado = 1");
$rsResponsable = mysql_query($query_rsResponsable, $tecnocomm) or die(mysql_error());
$row_rsResponsable = mysql_fetch_assoc($rsResponsable);
$totalRows_rsResponsable = mysql_num_rows($rsResponsable);

do{
	
	$res[$row_rsResponsable['idip']]['name'] = $row_rsResponsable['username'];
	$res[$row_rsResponsable['idip']]['id'] = $row_rsResponsable['idusuario'];
	
}while($row_rsResponsable = mysql_fetch_assoc($rsResponsable));


$estadosIP = array("<img src=\"images/bred.png\">","<img src=\"images/byellow.png\">","<img src=\"images/bgreen.png\">");



//definimos variable usuario en caso de que aplique

$usuario = isset($_GET['usuario'])?$_GET['usuario']:-1;
//echo $query_rsIP;
$signo = array('$','US$');

$i=0;
do{
	$row_rsIP = $row_rsIp;
	if($res[$row_rsIp['idip']]['id'] == $_GET['usuario'] || $usuario == -1){

	

	if(strlen($row_rsIP['descripcion'])<40){
		
		

	
	$data[$i][0] = $row_rsIP['idip'];
	$data[$i][1] = utf8_decode($estado[$row_rsIP['estado']]);
	$data[$i][2] = utf8_decode($row_rsIP['nombre']);
	$data[$i][3] = utf8_decode(trim($row_rsIP['descripcion']));//." ".strlen($row_rsIP['descripcion']);
	$data[$i][4] = formatDate($row_rsIP['fecha']);
	$data[$i][5] = formatDate($row_rsIP['fechamovimiento']);
	$data[$i][6] = utf8_decode($row_rsIP['ultimomovimiento']);
	$data[$i][7] = utf8_decode($res[$row_rsIP['idip']]['name']);
	
	
		
	}else{
	
	
	
	
	unset($line);//limpiamos lineas 	
	//separamos por palabras
	
	$letania = utf8_decode(trim($row_rsIP['descripcion']));//." ".strlen($row_rsIP['descripcion']));
	
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
	
	$data[$i][0] = $row_rsIP['idip'];
	$data[$i][1] = utf8_decode($estado[$row_rsIP['estado']]);
	$data[$i][2] = utf8_decode($row_rsIP['nombre']);
	$data[$i][3] = $line[0];
	$data[$i][4] = formatDate($row_rsIP['fecha']);
	$data[$i][5] = formatDate($row_rsIP['fechamovimiento']);
	$data[$i][6] = utf8_decode($row_rsIP['ultimomovimiento']);
	$data[$i][7] = utf8_decode($res[$row_rsIP['idip']]['name']);
	
		
	
	for($x=1;$x<count($line);$x++){
	$i++;
	$data[$i][0] = "";
	$data[$i][1] = "";
	$data[$i][2] = "";
	$data[$i][3] = $line[$x];
	$data[$i][3] = "";
	$data[$i][4] = "";
	$data[$i][5] = "";
	$data[$i][6] = "";
	
		
	
	
	}
	
	
	}
	
	
	
	}
$i++;
}while($row_rsIp = mysql_fetch_assoc($rsIp));

//print_r($data);

$pdf=new Tabla();
//Títulos de las columnas

$header=array('IP','Estado','Cliente','Descripcion','F. Creacion','F. Ultimo Mov.','Ultimo Mov.','Responsable');
$ws = array(8,10,60,60,14,14,14,14);
$aling = array('C','C','L','L','C','C','C','C','C');
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->setHeader($header,$ws,$aling);
$pdf->setData($data);
$pdf->generate();
$pdf->Output();
?>