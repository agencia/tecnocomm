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

	function Tabla($orientation='L',$unit='mm',$format='A4'){
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
	$this->Cell(190,3,'LISTA DE PROYECTOS AUTORIZADOS DE TECNOCOMM',0);
	$this->Ln();
	$this->Cell(95,3,'Fecha Impresion '.date("d/m/Y G:i"),0,'','L');
	$this->Cell(95,3,'Pagina '.$this->PageNo(). ' de {nb}',0,'','R');
	$this->Ln();

}

function totales($totalpes=0, $totaldol=0, $totalban=0, $tipocambio=0,  $tip=false ){
	
	$this->Cell(217);
	$this->Cell(58,3,'Saldo Total Pesos:$'.$totalpes,1,1,'R');
	$this->Cell(217);
	$this->Cell(58,3,'Saldo Total Pesos:$'.$totaldol,1,1,'R');
	$this->Cell(217);
	$this->Cell(58,3,'Saldo Total Pesos:$'.$totalban,1,1,'R');
	
	if($tip==true){
		$dol=$totaldol*$tipocambio;
		$total=$totalpes+$dol+$totalban;
	}else{
		$total=$totalpes+$totalban;
		}
		
	$this->Cell(217);
	$this->Cell(58,3,'Saldo Total:$'.format_money($total),1,1,'R');
	
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


//////////////////////////////////////////////////////////////////////////////

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsIp = "SELECT i.*,sb.idcotizacion,sb.identificador2, sb.moneda, c.nombre,sb.nombre as descoti FROM ip i LEFT JOIN cliente c ON i.idcliente = c.idcliente JOIN cotizacion co ON co.idip = i.idip JOIN subcotizacion sb ON sb.idcotizacion = co.idcotizacion WHERE sb.estado = 3 AND i.estado < 2 ORDER BY fecha ASC";
$rsIp = mysql_query($query_rsIp, $tecnocomm) or die(mysql_error());
$row_rsIp = mysql_fetch_assoc($rsIp);
$totalRows_rsIp = mysql_num_rows($rsIp);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoInicial = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.identificador2, c.idip, sb.fecha,sb.idcotizacion, sb.monto  AS montocotizacion, sb.tipo AS tipocotizacion,sb.descuento  FROM subcotizacionarticulo sba,subcotizacion sb, cotizacion c WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 3 AND c.idcotizacion = sb.idcotizacion)";
$rsMontoInicial = mysql_query($query_rsMontoInicial, $tecnocomm) or die(mysql_error());
$row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial);
$totalRows_rsMontoInicial = mysql_num_rows($rsMontoInicial);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMontoConciliado = "SELECT sba.*,sb.moneda  AS monedaglobal,sb.identificador2, c.idip, sb.fecha,sb.idcotizacion,sb.monto AS montocotizacion, sb.tipo AS tipocotizacion,sb.descuento, sb.montoreal FROM subcotizacionarticulo sba,subcotizacion sb, cotizacion c WHERE sba.idsubcotizacion = sb.idsubcotizacion AND (sb.estado = 8 AND c.idcotizacion = sb.idcotizacion)";
$rsMontoConciliado = mysql_query($query_rsMontoConciliado, $tecnocomm) or die(mysql_error());
$row_rsMontoConciliado = mysql_fetch_assoc($rsMontoConciliado);
$totalRows_rsMontoConciliado = mysql_num_rows($rsMontoConciliado);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT f.*, SUM(df.cantidad * df.punitario) as total FROM factura f JOIN detallefactura df ON f.idfactura = df.idfactura WHERE f.estado = 1 GROUP BY f.idfactura";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);

do{
	 $pre =divisa($row_rsMontoInicial['precio_cotizacion'],$row_rsMontoInicial['moneda'],$row_rsMontoInicial['monedaglobal'],$row_rsMontoInicial['tipo_cambio']);
	 $manoobra = divisa($row_rsMontoInicial['mo'],$row_rsMontoInicial['moneda'],$row_rsMontoInicial['monedaglobal'],$row_rsMontoInicial['tipo_cambio']);	
	
	 if($row_rsMontoInicial['tipocotizacion']  == 0)
		$p = round(($pre * $row_rsMontoInicial['utilidad']) + $manoobra,2);
	else{	
		$p = round(($pre * $row_rsMontoInicial['utilidad']),2) ;
		}	
		
	$man[$row_rsMontoInicial['idsubcotizacion']] = $man[$row_rsMontoInicial['idsubcotizacion']] + ($manoobra*$row_rsMontoInicial['cantidad']);
	//$maninst = $maninst + ($manoobra*$row_rsMontoInicial['reall']);
	$sub[$row_rsMontoInicial['idsubcotizacion']] = $sub[$row_rsMontoInicial['idsubcotizacion']] + $row_rsMontoInicial['cantidad'] * $p; 
	//$subinst = $subinst + $row_rsPartidas['reall'] * $p;
	$cotizaciones[$row_rsMontoInicial['idsubcotizacion']] = $row_rsMontoInicial;
	
	//$inicial[$row_rsMontoInicial['idsuboctoizacion']] = $inicial[$row_rsMontoInicial['idsubcotizacion']] + ($row_rsMontoInicial['cantidad'] * $p);
	
}while($row_rsMontoInicial = mysql_fetch_assoc($rsMontoInicial));




/*OBTENER TOTAL DE CONCILIACIONES*/
do{
	 $pre =divisa($row_rsMontoConciliado['precio_cotizacion'],$row_rsMontoConciliado['moneda'],$row_rsMontoConciliado['monedaglobal'],$row_rsMontoConciliado['tipo_cambio']);
	 $manoobra = divisa($row_rsMontoConciliado['mo'],$row_rsMontoConciliado['moneda'],$row_rsMontoConciliado['monedaglobal'],$row_rsMontoConciliado['tipo_cambio']);	
	
	 if($row_rsMontoConciliado['tipocotizacion']  == 0)
		$p = round(($pre * $row_rsMontoConciliado['utilidad']) + $manoobra,2);
	else{	
		$p = round(($pre * $row_rsMontoConciliado['utilidad']),2) ;
		}	
		
	$man2[$row_rsMontoConciliado['idsubcotizacion']] = $man2[$row_rsMontoConciliado['idsubcotizacion']] + ($manoobra*$row_rsMontoConciliado['reall']);
	//$maninst = $maninst + ($manoobra*$row_rsMontoInicial['reall']);
	$sub2[$row_rsMontoConciliado['idsubcotizacion']] = $sub2[$row_rsMontoConciliado['idsubcotizacion']] + $row_rsMontoConciliado['reall'] * $p; 
	//$subinst = $subinst + $row_rsPartidas['reall'] * $p;
	$conciliaciones[$row_rsMontoConciliado['idsubcotizacion']] = $row_rsMontoConciliado;
	
	//$inicial[$row_rsMontoInicial['idsuboctoizacion']] = $inicial[$row_rsMontoInicial['idsubcotizacion']] + ($row_rsMontoInicial['cantidad'] * $p);
	
}while($row_rsMontoConciliado = mysql_fetch_assoc($rsMontoConciliado));




/*LLENAR TOTALES DE COTIZACIONES*/
foreach($cotizaciones as  $idsubcotizacion => $cotizacion ){
	
	
	
	if($cotizacion['tipocotizacion'] == 1){
			$montoinicial[$cotizacion['idcotizacion']] = ($sub[$idsubcotizacion] + ($man[$idsubcotizacion]  * $cotizacion['montocotizacion'])) ;
	}else{
		$montoinicial[$cotizacion['idcotizacion']] = $sub[$idsubcotizacion];	
	}
		
		$descuento = ($montoinicial[$cotizacion['idcotizacion']] * $cotizacion['descuento'])/100;
		
		$montoinicial[$cotizacion['idcotizacion']] = $montoinicial[$cotizacion['idcotizacion']] - $descuento;
		
		$montoinicial[$cotizacion['idcotizacion']] = $montoinicial[$cotizacion['idcotizacion']] * 1.16;
		
		$idinicial[$cotizacion['idcotizacion']] = $idsubcotizacion;
	
		$mc[$cotizacion['idcotizacion']] = $cotizacion['monedaglobal'];
}


/*LLENAR TOTALES DE CONCILIACIONES*/
foreach($conciliaciones as  $idsubcotizacion => $conciliacion ){
	if($cotizacion['tipocotizacion'] == 1){
			$montofinal[$conciliacion['idcotizacion']] = ($sub2[$idsubcotizacion] + ($man2[$idsubcotizacion]  * $conciliacion['montoreal'])) ;
	}else{
		$montofinal[$conciliacion['idcotizacion']] = $sub2[$idsubcotizacion] ;	
	}
	
		$descuento = ($montofinal[$conciliacion['idcotizacion']] * $conciliacion['descuento'])/100;
		
		$montofinal[$conciliacion['idcotizacion']] = $montofinal[$conciliacion['idcotizacion']] - $descuento;
		$montofinal[$conciliacion['idcotizacion']] = $montofinal[$conciliacion['idcotizacion']] * 1.16;
	
	$idfinal[$conciliacion['idcotizacion']] = $idsubcotizacion;
}

do{
	
	//$facts[$row_rsFacturas['idfactura']] = $row_rsFacturas;
	$row_rsFacturas['total'] = $row_rsFacturas['total'] * (($row_rsFacturas['iva']/100)+1);
	$facturas[$row_rsFacturas['cotizacion']][] = $row_rsFacturas;
	
}while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));


/*OBTENER SALDO EN BANCOS*/
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti = sprintf("select sum(importe) as retiros from banco where tipo=1 ORDER BY fecha ASC",GetSQLValueString($fecha1_RsDetalle, "date"));
$RSReti = mysql_query($query_RSReti, $tecnocomm) or die(mysql_error());
$row_RSReti = mysql_fetch_assoc($RSReti);
$totalRows_RSReti = mysql_num_rows($RSReti);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti1 = sprintf("select sum(importe) as depositos from banco where tipo=0 ORDER BY fecha ASC",GetSQLValueString($fecha1_RsDetalle, "date"));
$RSReti1 = mysql_query($query_RSReti1, $tecnocomm) or die(mysql_error());
$row_RSReti1 = mysql_fetch_assoc($RSReti1);
$totalRows_RSReti1 = mysql_num_rows($RSReti1);

$saldoenbancos=$row_RSReti1['depositos']-$row_RSReti['retiros'];


$moneda = array("$","US$");
$i=0;
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   do {
    
	  	
			$mi = $montoinicial[$row_rsIp['idcotizacion']]; 
	  
	  	if(isset($_GET['tipocambio']) && $_GET['tipocambio'] != '' && $mc[$row_rsIp['idcotizacion']] == 1){
				$mi = ($mi * $_GET['tipocambio']);
				$m = $moneda[0];
			}else{
				$m = $moneda[$mc[$row_rsIp['idcotizacion']]];
			}
		 	
			 format_money($mi);
		
	  	
			$mmf = $montofinal[$row_rsIp['idcotizacion']]; 
	  
	  	if(isset($_GET['tipocambio']) && $_GET['tipocambio'] != '' && $mc[$row_rsIp['idcotizacion']] == 1){
				$mmf = ($mmf * $_GET['tipocambio']);
				$m = $moneda[0];
			}else{
				$m = $moneda[$mc[$row_rsIp['idcotizacion']]];
			}
		 	
			 format_money($mmf);
		 
			//facturas
			$totfact = 0;
	
			if((isset($_GET['tipocambio']) && $_GET['tipocambio'] != "") || $mc[$row_rsIp['idcotizacion']] == 0){
				if(is_array($facturas[$row_rsIp['idcotizacion']]))
				foreach($facturas[$row_rsIp['idcotizacion']] as $factura){
				$totfact += $factura['total']; 			
				}
			}else{
				if(is_array($facturas[$row_rsIp['idcotizacion']]))
				foreach($facturas[$row_rsIp['idcotizacion']] as $factura){
				
				if($factura['tipocambio'] != 0)
					$totfact += $factura['total'] / $factura['tipocambio'];
				
				}
				
				
			}
		
			 format_money($totfact);
		
	
			
		 
	  	
		if($mmf > 0)
			$saldo = $mmf - $totfact;
		else
			$saldo = $mi - $totfact;
			
			
		if($mc[$row_rsIp['idcotizacion']] == 0){
			$totalpesos += $saldo;
		}else{
			$totaldolares += $saldo;
			
		}
		
	  	 format_money($saldo);
		
	  
	  
	  if($saldo > 0){
			$pr = ($mff * 100)/ $saldo;  
  	}else{
		  	$pr = 0;
		}
			
	   
	  
	  
	   
	 
	 
	
	    
	   
	   
	  ///////////datos
	  
	$data[$i][0] = formatDate($row_rsIp['fecha']); 
	$data[$i][1] = $row_rsIp['idip'];
	$data[$i][2] = utf8_decode(trim($row_rsIp['nombre']));//." ".strlen($row_rsIP['descripcion']);
	$data[$i][3] = utf8_decode($row_rsIp['identificador2']);
	$data[$i][4] = utf8_decode($row_rsIp['descoti']);
	$data[$i][5] = $moneda[$mc[$row_rsIp['idcotizacion']]];
	$data[$i][6] = format_money($mi);
	$data[$i][7] = format_money($mmf);
	$data[$i][8] = format_money($totfact);
	$data[$i][9] = format_money($saldo);
	$data[$i][10] = round($pr,2)."%";
	
	$i++;
	  
     } while ($row_rsIp = mysql_fetch_assoc($rsIp)); 




//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//print_r($data);
$tc=false;
if(isset($_GET['tipocambio'])&&$_GET['tipocambio']!=''){
	$tc=true;
	$tcval=$_GET['tipocambio'];
	}


$pdf=new Tabla();
//Títulos de las columnas

$header=array('Fecha','IP','Cliente','Cotizacion','Descripcion','Moneda','Inicial','Conciliado','Facturado','Saldo','%');
$ws = array(15,6,60,40,70,10,16,16,16,16,10);
$aling = array('C','C','L','C','L','C','R','R','R','R','C',);
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->setHeader($header,$ws,$aling);
$pdf->setData($data);
$pdf->generate();
$pdf->totales(format_money($totalpesos),format_money($totaldolares),format_money($saldoenbancos),$tcval,$tc);
$pdf->Output();
?>