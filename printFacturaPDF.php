<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
<?php require_once('numtoletras.php');?>
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

$colname_rsFacutra = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsFacutra = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacutra = sprintf("SELECT * FROM factura f,cliente c WHERE f.idfactura = %s AND f.idcliente = c.idcliente", GetSQLValueString($colname_rsFacutra, "int"));
$rsFacutra = mysql_query($query_rsFacutra, $tecnocomm) or die(mysql_error());
$row_rsFacutra = mysql_fetch_assoc($rsFacutra);
$totalRows_rsFacutra = mysql_num_rows($rsFacutra);

$colname_rsDetalle = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsDetalle = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT * FROM detallefactura WHERE idfactura = %s", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);

$colname_rsCotizacion = "-1";
if (isset($_GET['idfactura'])) {
  $colname_rsCotizacion = $_GET['idfactura'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT sb.identificador2,sb.idsubcotizacion FROM facturacotizacion f,subcotizacion sb WHERE f.idcotizacion = sb.idsubcotizacion AND f.idfactura = %s", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);

?><?php
require('fpdf.php');
define('FPDF_FONTPATH','font/');

$signo = array("$","US$");

$pdf = new FPDF('P','mm',array(215,280));
$pdf->setMargins(5,5,5);
$pdf->AddPage();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(10,10,10);


$meses = array (1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre",'01'=>"Enero",'02'=>"Febrero",'03'=>"Marzo",'04'=>"Abril",'05'=>"Mayo",'06'=>"Junio",'07'=>"Julio",'08'=>"Agosto",'09'=>"Septiembre");

$fecha = split("-",utf8_decode($row_rsFacutra['fecha']));
//nombre
$pdf->SetXY(20,62);
$pdf->Cell(115,5,$row_rsFacutra['razonsocial'],0,1,'L');
//DIRECCION
$pdf->SetXY(20,68);
$pdf->Cell(115,5,$row_rsFacutra['direccionfacturacion'],0,1,'L');
//TELEFONO
$pdf->SetXY(20,75);
$pdf->Cell(50,5,$row_rsFacutra['telefono'],0,1,'L');
//CIUDAD
$pdf->SetXY(20,82);
$pdf->Cell(50,5,$row_rsFacutra['ciudadfacturacion'],0,1,'L');
//RFC
$pdf->SetXY(85,82);
$pdf->Cell(50,5,$row_rsFacutra['rfc'],0,1,'L');
//DIA
$pdf->SetXY(155,80);
$pdf->Cell(10,5,$fecha[2],0,1,'C');
//MES
$pdf->SetXY(170,80);
$pdf->Cell(20,5,$meses[$fecha[1]],0,1,'C');
//ANO
$pdf->SetXY(194,80);
$pdf->Cell(12,5,$fecha[0],0,1,'C');


			
$pdf->SetXY(180,5);
$pdf->Cell(12,5,"IP:".$row_rsFacutra['idip'],0,1,'C');
$par=0;
do{
$par++;

if(strlen(utf8_decode($row_rsDetalle['concepto']))<50){

$partida['partida'] = $par;
$partida['cantidad'] = $row_rsDetalle['cantidad'];
$partida['unidad'] = utf8_decode($row_rsDetalle['unidad']);
$partida['concepto'] = utf8_decode($row_rsDetalle['concepto']);
$partida['punitario'] = $row_rsDetalle['punitario'];
$partidas[] = $partida;

}else{
	
		unset($line);
		$countc = $num = 0;
		$palabras = split(" ",utf8_decode($row_rsDetalle['concepto']));
		
		foreach($palabras as $palabra){
		
		
		
				
						$long = strlen($palabra);
			
			if(($countc+$long) < 50){
			$countc = $countc+$long+1;
			}else{
			$num++;
			$countc=$long+1;
			}
				$line[$num]=$line[$num].$palabra." ";
				
				}
		
$partida['partida'] = $par;
$partida['cantidad'] = $row_rsDetalle['cantidad'];
$partida['unidad'] = $row_rsDetalle['unidad'];
$partida['concepto'] = $line[0];
$partida['punitario'] = $row_rsDetalle['punitario'];
				$partidas[] = $partida;
				for($i=1;$i<=$num;$i++){
			$partida['partida'] = "";
$partida['cantidad'] = "";
$partida['unidad'] = "";
$partida['concepto'] = $line[$i];
$partida['punitario'] = "";
				$partidas[] = $partida;
				}
		
		}

}while($row_rsDetalle = mysql_fetch_assoc($rsDetalle));


for($j=0;$j<5;$j++){
$partida['partida'] = "";
$partida['cantidad'] = "";
$partida['unidad'] = "";
$partida['concepto'] = "";
$partida['punitario'] = "";
$partidas[] = $partida;
}


/*
if($row_rsFacutra['tipo']==0){
        $linea = "SEGUN ORDEN DE SERVICIO: ".$row_rsFacutra['referencia']; 
		}
if($row_rsFacutra['tipo']==1){
$linea = "SEGUN COTIZACION: ".$row_rsCotizacion['identificador2']; 
}

if($row_rsFacutra['tipo']==2){
$linea = $row_rsFacutra['referencia']; 
}


*/

$partida['partida'] = "";
$partida['cantidad'] = "";
$partida['unidad'] = "";
$partida['concepto'] = $row_rsFacutra['referencia1'] ? "SEGUN COTIZACION: ". $row_rsFacutra['referencia1']:"";
$partida['punitario'] = "";
$partidas[] = $partida;

$partida['partida'] = "";
$partida['cantidad'] = "";
$partida['unidad'] = "";
$partida['concepto'] = $row_rsFacutra['referencia2'] ? "SEGUN ORDEN DE SERVICIO: ". $row_rsFacutra['referencia2']:"";
$partida['punitario'] = "";
$partidas[] = $partida;

$partida['partida'] = "";
$partida['cantidad'] = "";
$partida['unidad'] = "";
$partida['concepto'] = $row_rsFacutra['referencia3'] ? "SEGUN ORDEN DE COMPRA: ". $row_rsFacutra['referencia3']:"";
$partida['punitario'] = "";
$partidas[] = $partida;




$y = 100;
foreach($partidas as $partida){
//PARTIDA
	$pdf->SetXY(5,$y);
	$pdf->Cell(10,5,$partida['partida'],0,1,'C');
	//CANTIDAD
	$pdf->SetXY(20,$y);
	$pdf->Cell(15,5,$partida['cantidad'],0,1,'C');
	//UNIDAD
	$pdf->SetXY(34,$y);
	$pdf->Cell(14,5,$partida['unidad'],0,1,'C');
	//CONCEPTO
	$pdf->SetXY(50,$y);
	$pdf->Cell(95,5,$partida['concepto'],0,1,'L');
	//PRECIOUNITARIO
	if($partida['punitario']){
	$pdf->SetXY(152,$y);
	$pdf->Cell(10,5,$signo[$row_rsFacutra['moneda']],0,1,'L');
	$pdf->SetXY(152,$y);
	$pdf->Cell(26,5,$partida['punitario'],0,1,'R');
	}
	//IMPORTE
	if($partida['punitario']){
	$p = $partida['punitario']*$partida['cantidad'];
	$subtotal =  $subtotal + $p;
	
	$pdf->SetXY(178,$y);
	$pdf->Cell(10,5,$signo[$row_rsFacutra['moneda']],0,1,'R');
	$pdf->SetXY(178,$y);
	$pdf->Cell(27,5,format_money($p),0,1,'R');
	}
	$y = $y +5;
	

}



//SUBTOTAL
$pdf->SetXY(180,215);
$pdf->Cell(25,5,format_money($subtotal),0,1,'R');
//IVA
$iva = $subtotal*.16;
$pdf->SetXY(180,225);
$pdf->Cell(25,5,format_money($iva),0,1,'R');
//TOTAL

$total = $subtotal + $iva;
$pdf->SetXY(180,235);
$pdf->Cell(25,5,format_money($total),0,1,'R');

//CANTIDAD CON LETRA
$pdf->SetXY(10,220);
$pdf->MultiCell(120,5,num2letras(money_format('%i',$total),false,true,$row_rsFacutra['moneda']),0,'L');

$pdf->Output();


?>
<?php
mysql_free_result($rsFacutra);

mysql_free_result($rsDetalle);
?>
