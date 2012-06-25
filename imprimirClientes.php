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
$query_rsCliente = "SELECT * FROM cliente ORDER BY nombre ASC";
$rsCliente = mysql_query($query_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);
$totalRows_rsCliente = mysql_num_rows($rsCliente);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsContacto = "SELECT * FROM contactoclientes ORDER BY nombre ASC";
$rsContacto = mysql_query($query_rsContacto, $tecnocomm) or die(mysql_error());
$row_rsContacto = mysql_fetch_assoc($rsContacto);
$totalRows_rsContacto = mysql_num_rows($rsContacto);


?>
<?php 

require_once('fpdf.php');
define('FPDF_FONTPATH','font/');

class Cliente extends FPDF{

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
	$this->Cell(180,3,'LISTA DE CLIENTES DE TECNOCOMM',0);
	$this->Ln();
	$this->Cell(90,3,'Fecha Impresion '.date("d/m/Y G:i"),0,'','L');
	$this->Cell(90,3,'Pagina '.$this->PageNo(). ' de {nb}',0,'','R');

}

function clien($_cliente){
	//print_r($_proveedor);
	
	$this->Ln();
	$this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
   	$this->SetFont('Arial','B',6);
	$this->Cell(180,5,'CLIENTE:'.$_cliente[3],1,0,'L',true);
	$this->Ln();
	$this->SetTextColor(0);
	$this->SetFillColor(224,235,255);
	foreach($_cliente[0] as $p){
	$this->Cell(180,3,$p,'LR',0,'L',$fill);
	$this->Ln();
	$fill = !$fill;
	}
	$this->Cell(180,3,"FACTURACION",'LR',0,'C',true);
	$this->Ln();
	
	$fill=false;
	foreach($_cliente[1] as $p){
	$this->Cell(180,3,$p,'LR',0,'L',$fill);
	$this->Ln();
	$fill = !$fill;
	}

	
	if(is_array($_cliente[2]))
	foreach($_cliente[2] as $p){
	$this->Cell(180,3,"CONTACTO",'LR',0,'C',$fill);
	$this->Ln();
	$fill = !$fill;
	if(is_array($p))
	foreach($p as $dp){
	$this->Cell(180,3,$dp,'LR',0,'L',$fill);
	$this->Ln();
	$fill = !$fill;
	}
	}
	
	
	$this->Cell(180,1,'','T');
	$this->Ln();
}

}


	do{
		
		$contactos[$row_rsContacto['idcliente']][] = $row_rsContacto;
		
	
	}while($row_rsContacto = mysql_fetch_assoc($rsContacto));


do{

	$d[3]=" ".$row_rsCliente['nombre'];
	$d[0][1]="ABREVIACION: ".$row_rsCliente['abreviacion'];
	$d[0][2]="DIRECCION: ".$row_rsCliente['direccion']." ".$row_rsCliente['fraccionamiento']." CP: ".$row_rsCliente['cp']." ".$row_rsCliente['ciudad'].",".$row_rsCliente['estado'];
	
	$d[1][0]="RAZON SOCIAL: ".$row_rsCliente['razonsocial'];
	$d[1][1]="DIRECCION: ".$row_rsCliente['direccion'];
	$d[1][2]="RFC: ".$row_rsCliente['rfc'];
	
	$contacto = $contactos[$row_rsCliente['idcliente']];	
	$i=0;
	if(is_array($contacto))
	foreach($contacto as $con){
	$d[2][$i][0]="NOMBRE: ".$con['nombre'];
	$d[2][$i][1]="TELEFONOS: ".$con['telefono']."  y  ".$con['telefono2'];
	$d[2][$i][2]="EMAIL: ".$con['correo'];
	$d[2][$i][3]="PUESTO: ".$con['pusto'];

	$i++;
	}
	
	
	
	$datos[] = $d;
	unset($d);
	


}while($row_rsCliente = mysql_fetch_assoc($rsCliente));

$p = new Cliente();
$p->AliasNbPages();
$p->AddPage();
$p->titulo();
foreach($datos as $e){
$p->clien($e);
}
$p->Output();


?>
<?php

mysql_free_result($rsCliente);

mysql_free_result($rsContacto);
?>