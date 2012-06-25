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
$query_rs1 = "SELECT * FROM subcotizacion where idsubcotizacion=".$_GET['idsub'];
$rs1 = mysql_query($query_rs1, $tecnocomm) or die(mysql_error());
$row_rs1 = mysql_fetch_assoc($rs1);
$totalRows_rs1 = mysql_num_rows($rs1);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rs2 = "SELECT * FROM subcotizacionarticulo a,subcotizacionavance b where a.idsubcotizacionarticulo=b.idarticulo and a.idsubcotizacion=".$_GET['idsub']." order by b.fecha";
$rs2 = mysql_query($query_rs2, $tecnocomm) or die(mysql_error());
$row_rs2 = mysql_fetch_assoc($rs2);
$totalRows_rs2 = mysql_num_rows($rs2);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rs3 = "SELECT *,(select nombrereal from usuarios where id=idempleado) as nomemp FROM reporteavance where idsubcotizacion=".$_GET['idsub']." ORDER BY fecha";
$rs3 = mysql_query($query_rs3, $tecnocomm) or die(mysql_error());
$row_rs3 = mysql_fetch_assoc($rs3);
$totalRows_rs3 = mysql_num_rows($rs3);


?>
<?php 

require_once('fpdf.php');
define('FPDF_FONTPATH','font/');

class Reporte extends FPDF{

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
	$this->Cell(180,3,'LISTA DE AVANCES DE TECNOCOMM',0);
	$this->Ln();
	$this->Cell(90,3,'Fecha Impresion '.date("d/m/Y G:i"),0,'','L');
	$this->Cell(90,3,'Pagina '.$this->PageNo(). ' de {nb}',0,'','R');

}

function rep($_reporte){
	//print_r($_proveedor);
	
	$this->Ln();
	$this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
   	$this->SetFont('Arial','B',6);
	$this->Cell(180,5,'PROYECTO:'.$_reporte[3],1,0,'L',true);
	$this->Ln();
	$this->SetTextColor(0);
	$this->SetFillColor(224,235,255);
	foreach($_reporte[0] as $p){
	$this->Cell(180,3,$p,'LR',0,'L',$fill);
	$this->Ln();
	$fill = !$fill;
	}
	$this->Cell(180,3,"AVANCE  POR PARTIDA",'LR',0,'C',true);
	$this->Ln();
	
	if(is_array($_reporte[1]))
	foreach($_reporte[1] as $p){
	
	$fill = !$fill;
	if(is_array($p))
	foreach($p as $dp){
	$this->Cell(180,3,$dp,'LR',0,'L',$fill);
	$this->Ln();
	$fill = !$fill;
	}
	}

	$this->Cell(180,3,"AVANCE",'LR',0,'C',$fill);
	$this->Ln();
	if(is_array($_reporte[2]))
	foreach($_reporte[2] as $p){
	
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

////////////////partidas
	do{
		
		$avance1[$row_rs1['idsubcotizacion']][] = $row_rs2;
		
	
	}while($row_rs2 = mysql_fetch_assoc($rs2));
///////////////////////reportes
	do{
		
		$avance2[$row_rs1['idsubcotizacion']][] = $row_rs3;
		
	
	}while($row_rs3 = mysql_fetch_assoc($rs3));


do{

	$d[3]=" ".$row_rs1['identificador2'];
	$d[0][1]="NOMBRE: ".$row_rs1['nombre'];
	
	
	$a1 = $avance1[$row_rs1['idsubcotizacion']];	
	$i=0;
	if(is_array($a1))
	foreach($a1 as $con){
	$d[1][$i][0]="PARTIDA: ".$con['descri'];
	$d[1][$i][1]="FECHA: ".$con['fecha'];
	$d[1][$i][2]="CANTIDAD: ".$con['cantidad'];
	$d[1][$i][3]="COMENTARIO: ".$con['comentario'];	
	$i++;
	}
	
	$a2 = $avance2[$row_rs1['idsubcotizacion']];	
	$i=0;
	if(is_array($a2))
	foreach($a2 as $con1){
	$d[2][$i][0]="EMPLEADO: ".$con1['nomemp'];
	$d[2][$i][1]="FECHA: ".$con1['fecha'];
	$d[2][$i][2]="REPORTE: ".$con1['reporte'];
	

	$i++;
	}
	
	
	
	$datos[] = $d;
	unset($d);
	


}while($row_rs1 = mysql_fetch_assoc($rs1));

$p = new Reporte();
$p->AliasNbPages();
$p->AddPage();
$p->titulo();
foreach($datos as $e){
$p->rep($e);
}
$p->Output();


?>
<?php

mysql_free_result($rsCliente);

mysql_free_result($rsContacto);
?>