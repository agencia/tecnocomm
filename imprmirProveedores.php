<?php require_once('Connections/tecnocomm.php'); ?>
<?php
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
$query_rsProveedor = "SELECT * FROM proveedor ORDER BY nombrecomercial ASC";
$rsProveedor = mysql_query($query_rsProveedor, $tecnocomm) or die(mysql_error());
$row_rsProveedor = mysql_fetch_assoc($rsProveedor);
$totalRows_rsProveedor = mysql_num_rows($rsProveedor);
?>
<?php 

require_once('fpdf.php');
define('FPDF_FONTPATH','font/');

class Proveedor extends FPDF{

var $data;


function PDF($orientation='P',$unit='mm',$format='A4'){
		//Llama al constructor de la clase padre
		$this->FPDF($orientation,$unit,$format);
		$this->setMargins(10,10,10);
		$this->SetAutoPageBreak(true,10);
	}

function setData($data){
	$this->data = $data;
}


function titulo(){
	$this->SetFont('Arial','B',6);
	$this->Cell(180,3,'LISTA DE PROVEEDORES DE TECNOCOMM',0);
	$this->Ln();
	$this->Cell(90,3,'Fecha Impresion '.date("d/m/Y G:i"),0,'','L');
	$this->Cell(90,3,'Pagina '.$this->PageNo(). ' de {nb}',0,'','R');

}

function prov($_proveedor){
	//print_r($_proveedor);
	
	$this->Ln();
	$this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
   	$this->SetFont('Arial','B',6);
	$this->Cell(180,5,'PROVEEDOR:'.$i,1,0,'L',true);
	$this->Ln();
	$this->SetTextColor(0);
	$this->SetFillColor(224,235,255);
	foreach($_proveedor as $p){
	$this->Cell(180,3,$p,'LR',0,'L',$fill);
	$this->Ln();
	$fill = !$fill;
	}
	$this->Cell(180,1,'','T');
	$this->Ln();
}

}


do{

	$d[]="NOMBRE COMERCIAL: ".$row_rsProveedor['nombrecomercial'];
	$d[]="RAZON SOCIAL: ".$row_rsProveedor['razonsocial'];
	$d[]="RFC: ".$row_rsProveedor['rfc'];
	$d[]="DIRECCION: ".$row_rsProveedor['domicilio']." ".$row_rsProveedor['faccionamiento']." ".$row_rsProveedor['cp'];
	$d[]="CIUDAD: ".$row_rsProveedor['ciudad'].",".$row_rsProveedor['estado'];
	$d[]="CONTACTO: ".$row_rsProveedor['contacto'];
	$d[]="TELEFONO: ".$row_rsProveedor['telefono'];
	$d[]="EMAIL: ".$row_rsProveedor['email'];
	$d[]="BANCO: ".$row_rsProveedor['banco'];
	$d[]="CTA. BANCARIA: ".$row_rsProveedor['ctabancaria']."  CLABE: ".$row_rsProveedor['clabe'];
	$datos[] = $d;
	unset($d);
	


}while($row_rsProveedor = mysql_fetch_assoc($rsProveedor));

$p = new Proveedor();
$p->AliasNbPages();
$p->AddPage();
$p->titulo();
foreach($datos as $e){
$i++;
$p->prov($e);
	if($i==7){
		$i=0;
		$p->AddPage();
		$p->titulo();
	}
}
$p->Output();

?>
<?php
mysql_free_result($rsProveedor);
?>