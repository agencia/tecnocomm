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
if(isset($_GET['consulta']) && $_GET['consulta']!=''){
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rs = stripslashes($_GET['consulta']);

$rs = mysql_query($query_rs, $tecnocomm) or die(mysql_error());
$row_rs = mysql_fetch_assoc($rs);
$totalRows_rs = mysql_num_rows($rs);
}
else{
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rs = "select * from empleado order by nombre";
$rs = mysql_query($query_rs, $tecnocomm) or die(mysql_error());
$row_rs = mysql_fetch_assoc($rs);
$totalRows_rs = mysql_num_rows($rs);

}
$edo=array(0=>"SOLTERO(A)",1=>"CASADO(A)",2=>"VIUDO(A)",3=>"UNION LIBRE",4=>"SEPARADO(A)");
$sexo=array(0=>"MASCULINO",1=>"FEMENINO");
$forma=array(0=>"EVENTUAL",1=>"TIEPO DETERMINADO",2=>"TIEMPO INDETERMINADO",3=>"HONORARIOS",4=>"PROYECTO");
?>
<?php 

require_once('fpdf.php');
define('FPDF_FONTPATH','font/');

class Imprimir extends FPDF{

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
	$this->Cell(180,3,'LISTA DE EMPLEADOS DE TECNOCOMM',0);
	$this->Ln();
	$this->Cell(90,3,'Fecha Impresion '.date("d/m/Y G:i"),0,'','L');
	$this->Cell(90,3,'Pagina '.$this->PageNo(). ' de {nb}',0,'','R');

}

function info($_info){
	//print_r($_proveedor);
	
	$this->Ln();
	$this->SetFillColor(255,0,0);
    $this->SetTextColor(255);
    $this->SetDrawColor(128,0,0);
    $this->SetLineWidth(.3);
   	$this->SetFont('Arial','B',6);
	$this->Cell(180,5,$_info[0],1,0,'L',true);
	$this->Ln();
	$this->SetTextColor(0);
	$this->SetFillColor(224,235,255);
	foreach($_info as $p){
	$this->Cell(180,3,$p,'LR',0,'L',$fill);
	$this->Ln();
	$fill = !$fill;
	}
	$this->Cell(180,1,'','T');
	$this->Ln();
}

}


do{

	$d[]="NOMBRE: ".$row_rs['nombre'];
	$d[]="FECHA DE NACIMIENTO: ".$row_rs['fechanacimiento'];
	$d[]="LUGAR DE NACIMIENTO: ".$row_rs['lugarnacimiento'];
	$d[]="SEXO: ".$sexo[$row_rs['sexo']];
	$d[]="ESTADO CIVIL: ".$edo[$row_rs['estadocivil']];
	$d[]="NOMBRE DE PAREJA: ".$row_rs['nombrepareja'];
	$d[]="TELEFONO: ".$row_rs['telefono']."  CELULAR: ".$row_rs['celular']."  CORREO: ".$row_rs['correo'];
	$d[]="DOMICILIO: ".$row_rs['domicilio'];
	$d[]="FORMA DE CONTRATO: ".$forma[$row_rs['formacontrato']];
	$d[]="FECHA DE INGRESO: ".$row_rs['fechaingreso']."  FECHA DE TERMINO: ".$row_rs['fechatermino'];
	$d[]="PUESTO: ".$row_rs['puesto']."  SUELDO: $".$row_rs['suledo'];
	$datos[] = $d;
	unset($d);
	


}while($row_rs = mysql_fetch_assoc($rs));

$p = new Imprimir();
$p->AliasNbPages();
$p->AddPage();
$p->titulo();
foreach($datos as $e){
$i++;
$p->info($e);
	if($i==7){
		$i=0;
		$p->AddPage();
		$p->titulo();
	}
}
$p->Output();

?>
<?php
mysql_free_result($rs);
?>