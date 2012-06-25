<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php'); ?>
<?php include("fpdf.php"); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

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
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
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

if(isset($_GET['filtro']) and $_GET['filtro'] != '')
{
	$buscar = "AND p.nombrecomercial like '%%".$_GET['filtro']."%%'";
}
if(isset($_GET['filtro1']) and $_GET['filtro1'] != -1)
{
	$estado = " AND c.estado = ".$_GET['filtro1'];
}else
{
	$estado = " AND c.estado = 0";
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFacturas = "SELECT p.idproveedor, p.nombrecomercial, c.monto, c.moneda, c.fecha, c.fechavencimiento, c.nofactura, c.estado, DATEDIFF(c.fechavencimiento,NOW()) as diasdecredito FROM cuentasporpagar c LEFT JOIN proveedor p ON c.idproveedor = p.idproveedor WHERE 1 $estado $buscar ORDER BY diasdecredito";
$rsFacturas = mysql_query($query_rsFacturas, $tecnocomm) or die(mysql_error());
$row_rsFacturas = mysql_fetch_assoc($rsFacturas);
$totalRows_rsFacturas = mysql_num_rows($rsFacturas);

do{
	
	$clientes[$row_rsFacturas['idproveedor']]['nombrecomercial'] = $row_rsFacturas['nombrecomercial'];
	$clientes[$row_rsFacturas['idproveedor']][$row_rsFacturas['moneda']] = $clientes[$row_rsFacturas['idproveedor']][$row_rsFacturas['moneda']] + $row_rsFacturas['monto']; 
	$clientes[$row_rsFacturas['idproveedor']]['data'][] = $row_rsFacturas;
	
	$totales[$row_rsFacturas['moneda']] += $row_rsFacturas['monto'];
}while($row_rsFacturas = mysql_fetch_assoc($rsFacturas));

$pdf=new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->Ln();
$pdf->SetFont('Arial','B',6);
$pdf->SetTextColor(0);
$pdf->Cell(190,3,"TECNOCOMM",'',0,'C');
$pdf->Ln();
$pdf->Cell(190,3,"REPORTE DE CUENTAS POR PAGAR",'',0,'C');
$pdf->Ln();
$pdf->Cell(190,3,formatDate(date("Y-m-d")).date("h:i a"),'',0,'R');
$pdf->Ln();
$pdf->Ln();

foreach($clientes as $cliente) 
{
$pdf->Cell(114,3,$cliente['nombrecomercial'],'LTB',0,'L');
$pdf->Cell(38,3,"US$ ".format_money($cliente[1]),'TRB',0,'R');
$pdf->Cell(38,3,"US$ ".format_money($cliente[0]),'TRB',0,'R');
$pdf->Ln();
$pdf->Cell(38,3,"Estado",'LTRB',0,'C');
$pdf->Cell(38,3,"No Factura",'TRB',0,'C');		
$pdf->Cell(38,3,"Fecha",'TRB',0,'C');
$pdf->Cell(38,3,"Monto",'TRB',0,'C');
$pdf->Cell(38,3,"Vence En",'TRB',0,'C');
$pdf->Ln();
	foreach($cliente['data'] as $factura)
	{
		$pdf->Cell(38,3,$factura['estado'],'LB',0,'C');
		$pdf->Cell(38,3,$factura['nofactura'],'B',0,'C');
		$pdf->Cell(38,3,formatDate($factura['fecha']),'B',0,'C');
		$pdf->Cell(38,3,format_money($factura['monto']),'B',0,'R');
		$pdf->Cell(38,3,$factura['diasdecredito']." Dias",'RB',0,'R');
		$pdf->Ln();
	}
$pdf->Ln();
$pdf->Ln();
}
$pdf->Ln();
$pdf->Output();
?>
