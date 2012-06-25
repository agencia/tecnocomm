<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
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

$colname_rsEncabezado = "-1";
if (isset($_GET['idip'])) {
  $colname_rsEncabezado = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsEncabezado = sprintf("SELECT i.idip, i.fecha, i.hora,i.descripcion,i.titulo, c.nombre AS nombrecliente, c.direccion, c.ciudad, co.nombre AS nombrecontacto, co.correo, co.telefono, co.telefono2,c.idcliente FROM ip i LEFT JOIN cliente c ON i.idcliente = c.idcliente LEFT JOIN contactoclientes co ON i.idcontacto = co.idcontacto WHERE i.idip = %s", GetSQLValueString($colname_rsEncabezado, "int"));
$rsEncabezado = mysql_query($query_rsEncabezado, $tecnocomm) or die(mysql_error());
$row_rsEncabezado = mysql_fetch_assoc($rsEncabezado);
$totalRows_rsEncabezado = mysql_num_rows($rsEncabezado);

$colname_rsAtendido = "-1";
if (isset($_GET['idip'])) {
  $colname_rsAtendido = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAtendido = sprintf("SELECT u.nombrereal,u.username FROM ip i LEFT JOIN usuarios u ON i.idatendio = u.id WHERE idip = %s", GetSQLValueString($colname_rsAtendido, "int"));
$rsAtendido = mysql_query($query_rsAtendido, $tecnocomm) or die(mysql_error());
$row_rsAtendido = mysql_fetch_assoc($rsAtendido);
$totalRows_rsAtendido = mysql_num_rows($rsAtendido);

$colname_rsResponsable = "-1";
if (isset($_GET['idip'])) {
  $colname_rsResponsable = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsResponsable = sprintf("SELECT pp.*,u.nombrereal,u.username FROM ip i LEFT JOIN proyecto_personal pp ON i.idip = pp.idip LEFT JOIN usuarios u ON u.id = pp.idusuario WHERE i.idip = %s AND pp.estado = 1", GetSQLValueString($colname_rsResponsable, "int"));
$rsResponsable = mysql_query($query_rsResponsable, $tecnocomm) or die(mysql_error());
$row_rsResponsable = mysql_fetch_assoc($rsResponsable);
$totalRows_rsResponsable = mysql_num_rows($rsResponsable);
 

$colname_rsPartidas = "-1";
if (isset($_GET['idproyecto_material'])) {
  $colname_rsPartidas = $_GET['idproyecto_material'];
  
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT * FROM proyecto_material_partida WHERE idproyecto_material = %s ORDER BY idsubcotizacionarticulo ASC", GetSQLValueString( $colname_rsPartidas,"int"));
$rsPartidas = mysql_query($query_rsPartidas, $tecnocomm) or die(mysql_error());
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);
$totalRows_rsPartidas = mysql_num_rows($rsPartidas);

$colname_rsMovimientos = "-1";
if (isset($_GET['idproyecto_material'])) {
  $colname_rsMovimientos = $_GET['idproyecto_material'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMovimientos = sprintf("SELECT pm.*,(SELECT username FROM usuarios u WHERE u.id = pm.capturo) AS ncapturo,(SELECT username FROM usuarios u WHERE u.id = pm.precapturo) AS nprecapturo,(SELECT username FROM usuarios u WHERE u.id = pm.autorizo) AS nautorizo FROM proyecto_material_movimiento pm WHERE pm.idproyecto_material = %s", GetSQLValueString($colname_rsMovimientos, "int"));
$rsMovimientos = mysql_query($query_rsMovimientos, $tecnocomm) or die(mysql_error());
$row_rsMovimientos = mysql_fetch_assoc($rsMovimientos);
$totalRows_rsMovimientos = mysql_num_rows($rsMovimientos);

$colname_rsDetalle = "-1";
if (isset($_GET['idproyecto_material'])) {
  $colname_rsDetalle = $_GET['idproyecto_material'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT pmd.*,pm.numero FROM proyecto_material_detalle pmd JOIN proyecto_material_movimiento pm  ON pm.idproyecto_material_movimiento = pmd.idproyecto_material_movimiento WHERE pm.idproyecto_material = %s", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);

$colname_rs_controles = "-1";
if (isset($_GET['idproyecto_material'])) {
  $colname_rs_controles = $_GET['idproyecto_material'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rs_controles = sprintf("SELECT pm.*,sb.identificador2 FROM proyecto_material pm JOIN subcotizacion sb ON pm.idcotizacion = sb.idcotizacion AND sb.estado = 3 WHERE pm.idproyecto_material = %s", GetSQLValueString($colname_rs_controles, "int"));
$rs_controles = mysql_query($query_rs_controles, $tecnocomm) or die(mysql_error());
$row_rs_controles = mysql_fetch_assoc($rs_controles);
$totalRows_rs_controles = mysql_num_rows($rs_controles);

//partidas
do{
	
	if($row_rsPartidas['pextra'] == 0)
		$partidas[$row_rsPartidas['idproyecto_material_partida']] = $row_rsPartidas;
	else
		$partidasextra[$row_rsPartidas['idproyecto_material_partida']] = $row_rsPartidas;
		
}while($row_rsPartidas = mysql_fetch_assoc($rsPartidas));

//movimientos
do{
	$movimientos[$row_rsMovimientos['numero']] = $row_rsMovimientos;
}while($row_rsMovimientos = mysql_fetch_assoc($rsMovimientos));


//detalle_movimientos
do{
		
	$detalles[$row_rsDetalle['numero']][$row_rsDetalle['idproyecto_material_partida']] = $row_rsDetalle;
		
}while($row_rsDetalle = mysql_fetch_assoc($rsDetalle));
?>
<?php 
require_once('memimages.php');



$pdf=new MEM_IMAGE('L','mm','Legal');
$pdf->AddPage();
$pdf->Ln();
$pdf->SetFont('Arial','B',8);
$pdf->SetTextColor(0);
$pdf->Cell(25,5,'Nombre','LT',0,'R');
$pdf->Cell(150,5,$row_rsEncabezado['nombrecliente'],'LT',0,'L');
$pdf->Cell(25,5,'Ip:','LT',0,'R');
$pdf->Cell(10,5,$row_rsEncabezado['idip'],'LTR',0,'L');
$pdf->Cell(20,5,'Cotizacion:','LT',0,'R');
$pdf->Cell(30,5,$row_rs_controles['identificador2'],'LTR',0,'L');
$pdf->Ln();
$pdf->Cell(25,5,'Direccion','LT',0,'R');
$pdf->Cell(150,5,$row_rsEncabezado['direccion'],'LT',0,'L');
$pdf->Cell(25,5,'Fecha:','LT',0,'R');
$pdf->Cell(60,5,$row_rsEncabezado['fecha'],'LTR',0,'L');
$pdf->Ln();
$pdf->Cell(25,5,'Contacto','LT',0,'R');
$pdf->Cell(150,5,$row_rsEncabezado['nombrecontacto'],'LT',0,'L');
$pdf->Cell(25,5,'Ciudad:','LT',0,'R');
$pdf->Cell(60,5,$row_rsEncabezado['ciudad'],'LTR',0,'L');
$pdf->Ln();
$pdf->Cell(25,5,'Telefono(s)','LT',0,'R');
$pdf->Cell(150,5,$row_rsEncabezado['telefono'],'LT',0,'L');
$pdf->Cell(25,5,'E-Mail:','LT',0,'R');
$pdf->Cell(60,5,$row_rsEncabezado['correo'],'LTR',0,'L');
$pdf->Ln();
$pdf->Cell(25,5,'Atendido Por:','LT',0,'R');
$pdf->Cell(150,5,$row_rsAtendido['username'],'LT',0,'L');
$pdf->Cell(25,5,'Responsable:','LT',0,'R');
$pdf->Cell(60,5,$row_rsResponsable['username'],'LTR',0,'L');
$pdf->Ln();
$pdf->Cell(25,5,'Descripcion','LTB',0,'R');
$pdf->Cell(235,5,$row_rsEncabezado['descripcion'],'LTRB',0,'L');

//encabezdo
$imc = imagecreatetruecolor(30, 80);
$ims = imagecreatetruecolor(30, 80);
$ime = imagecreatetruecolor(30, 80);
$imd = imagecreatetruecolor(30, 80);
$imts = imagecreatetruecolor(30, 80);
$imtc = imagecreatetruecolor(30, 80);
$imtd = imagecreatetruecolor(30, 80);
$imte = imagecreatetruecolor(30, 80);

$red1 = imagecolorallocate($imc, 0xFF, 0xFF, 0xFF);
$black1 = imagecolorallocate($imc, 0x00, 0x00, 0x00);

$red2 = imagecolorallocate($ime, 0xFF, 0xFF, 0xFF);
$black2 = imagecolorallocate($ime, 0x00, 0x00, 0x00);

$red3 = imagecolorallocate($ime, 0xFF, 0xFF, 0xFF);
$black3 = imagecolorallocate($ime, 0x00, 0x00, 0x00);

$red4 = imagecolorallocate($imd, 0xFF, 0xFF, 0xFF);
$black4 = imagecolorallocate($imd, 0x00, 0x00, 0x00);

$red5 = imagecolorallocate($imts, 0xFF, 0xFF, 0xFF);
$black5 = imagecolorallocate($imts, 0x00, 0x00, 0x00);

$red6 = imagecolorallocate($imtc, 0xFF, 0xFF, 0xFF);
$black6 = imagecolorallocate($imtc, 0x00, 0x00, 0x00);

$red7 = imagecolorallocate($imtd, 0xFF, 0xFF, 0xFF);
$black7 = imagecolorallocate($imtd, 0x00, 0x00, 0x00);

$red8 = imagecolorallocate($imte, 0xFF, 0xFF, 0xFF);
$black8 = imagecolorallocate($imte, 0x00, 0x00, 0x00);



// Make the background red
imagefilledrectangle($imc, 0, 0, 30, 80, $red1);
imagefilledrectangle($ims, 0, 0, 30, 80, $red2);
imagefilledrectangle($ime, 0, 0, 30, 80, $red3);
imagefilledrectangle($imts, 0, 0, 30, 80, $red4);
imagefilledrectangle($imte, 0, 0, 30, 80, $red5);
imagefilledrectangle($imd, 0, 0, 30, 80, $red6);
imagefilledrectangle($imtd, 0, 0, 30, 80, $red7);
imagefilledrectangle($imtc, 0, 0, 30, 80, $red8);

// Path to our ttf font file
$font_file = './font/Arial.ttf';



// Draw the text 'PHP Manual' using font size 13
imagefttext($imc, 10, 90, 15, 70, $black1, $font_file, 'Cotizada');
imagefttext($ims, 10, 90, 15, 70, $black2, $font_file, 'Solicitado');
imagefttext($ime, 10, 90, 15, 70, $black3, $font_file, 'Enteregado');
imagefttext($imts,10, 90, 15, 70, $black4, $font_file, 'T. Solicitado');
imagefttext($imte,10, 90, 15, 70, $black5, $font_file, 'T. Entregado');
imagefttext($imd, 10, 90, 15, 70, $black6, $font_file, 'Devuelto');
imagefttext($imtd, 10, 90, 15, 70, $black7, $font_file, 'T. Devuelto');
imagefttext($imtc, 10, 90, 15, 70, $black8, $font_file, 'Conciliado');




$pdf->Ln();
$pdf->Ln();
$pdf->Cell(10,20,'Partida','LT',0,'C');
$pdf->Cell(20,20,'Codigo','LT',0,'C');
$pdf->Cell(20,20,'Marca','LT',0,'C');
$pdf->Cell(100,20,'Descripcion','LT',0,'C');
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($imc, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($ims, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($ime, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($ims, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($ime, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($ims, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($ime, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($ims, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($ime, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($ims, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($ime, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');

$pdf->GDImage($imts, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($imte, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');


$pdf->GDImage($imd, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');
$pdf->GDImage($imd, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LT',0,'C');


$pdf->GDImage($imtd, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,20,'','LTR',0,'C');
$pdf->GDImage($imtc, $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Ln();

//DETALLE DE PDF;
$i = 1;
$pdf->SetFont('Arial','',6);
$xo=$pdf->getX();
$y1 = $pdf->getY();
foreach($partidas as $kpartida => $partida){

$pdf->Cell(10,4,$i,'LT',0,'C');
$pdf->Cell(20,4,$partida['codigo'],'LT',0,'L');
$pdf->Cell(20,4,$partida['marca'],'LT',0,'L');
$pdf->Cell(100,4,$partida['descripcion'],'LT',0,'L');
$pdf->Cell(10,4,$partida['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[0][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[1][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[2][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[3][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[4][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[5][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[6][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[7][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[8][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[9][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$totalsolicitado,'LT',0,'R');
$pdf->Cell(10,4,$totalentregado,'LT',0,'R');
$pdf->Cell(10,4,$detalles[10][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[11][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$totaldevuelto,'LT',0,'R');
$pdf->Cell(10,4,$conciliado,'LTR',0,'R');
$pdf->Ln();


$i++;

}


if(is_array($partidasextra)){
$pdf->Ln();
$pdf->Cell(320,4,'Partidas Extras','LTR',0,'C');
$pdf->Ln();
foreach($partidasextra as $kpartida => $partida){


$pdf->Cell(10,4,$i,'LT',0,'C');
$pdf->Cell(20,4,$partida['codigo'],'LT',0,'L');
$pdf->Cell(20,4,$partida['marca'],'LT',0,'L');
$pdf->Cell(100,4,$partida['descripcion'],'LT',0,'L');
$pdf->Cell(10,4,$partida['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[0][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[1][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[2][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[3][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[4][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[5][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[6][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[7][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[8][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[9][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$totalsolicitado,'LT',0,'R');
$pdf->Cell(10,4,$totalentregado,'LT',0,'R');
$pdf->Cell(10,4,$detalles[10][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$detalles[11][$kpartida]['cantidad'],'LT',0,'R');
$pdf->Cell(10,4,$totaldevuelto,'LT',0,'R');
$pdf->Cell(10,4,$conciliado,'LTR',0,'R');
$pdf->Ln();


$i++;
}
}


$pdf->Cell(10,4,'','LT',0,'C');
$pdf->Cell(20,4,'','LT',0,'L');
$pdf->Cell(20,4,'','LT',0,'L');
$pdf->Cell(100,4,'','LT',0,'L');
$pdf->Cell(10,4,'Capturo','LT',0,'R');
$pdf->Cell(10,4,$movimientos[0]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[1]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[2]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[3]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[4]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[5]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[6]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[7]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[8]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[9]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,'','LT',0,'R');
$pdf->Cell(10,4,'','LT',0,'R');
$pdf->Cell(10,4,$movimientos[10]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[11]['ncapturo'],'LT',0,'R');
$pdf->Cell(10,4,'','LT',0,'R');
$pdf->Cell(10,4,'','LTR',0,'R');
$pdf->Ln();
$pdf->Cell(10,4,'','LT',0,'C');
$pdf->Cell(20,4,'','LT',0,'L');
$pdf->Cell(20,4,'','LT',0,'L');
$pdf->Cell(100,4,'','LT',0,'L');
$pdf->Cell(10,4,'Autorizo','LT',0,'R');
$pdf->Cell(10,4,$movimientos[0]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[1]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[2]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[3]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[4]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[5]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[6]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[7]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[8]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[9]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,'','LT',0,'R');
$pdf->Cell(10,4,'','LT',0,'R');
$pdf->Cell(10,4,$movimientos[10]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,$movimientos[11]['nautorizo'],'LT',0,'R');
$pdf->Cell(10,4,'','LT',0,'R');
$pdf->Cell(10,4,'','LTR',0,'R');
$pdf->Ln();
//FECHAS
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->Cell(20,25,'','LTB',0,'C');
$pdf->Cell(20,25,'','LTB',0,'C');
$pdf->Cell(100,25,'','LTB',0,'C');
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[0]['fecha']),  $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[1]['fecha']),  $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[2]['fecha']),  $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[3]['fecha']),  $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[4]['fecha']),  $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[5]['fecha']),  $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[6]['fecha']),  $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[7]['fecha']),  $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[8]['fecha']),  $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[9]['fecha']),  $pdf->GetX()-8, $pdf->GetY()+2,7);
$pdf->Cell(10,25,'','LTB',0,'C');


$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->Cell(10,25,'','LTB',0,'C');


$pdf->GDImage(getimF($movimientos[10]['fecha']), $pdf->GetX()-8, $pdf->GetY()+1,2.5);
$pdf->Cell(10,25,'','LTB',0,'C');
$pdf->GDImage(getimF($movimientos[11]['fecha']), $pdf->GetX()-8, $pdf->GetY()+1,2.5);
$pdf->Cell(10,25,'','LTB',0,'C');


$pdf->Cell(10,25,'','LTRB',0,'C');

$pdf->Ln();


$pdf->Output();


function getimF($date){
	$font_file = './font/Arial.ttf';
	$imf = imagecreatetruecolor(30, 80);
	$red = imagecolorallocate($imf, 0xFF, 0xFF, 0xFF);
	$black = imagecolorallocate($imf, 0x00, 0x00, 0x00);
	imagefilledrectangle($imf, 0, 0, 30, 80, $red);
	imagefttext($imf, 10, 90, 15, 80, $black, $font_file, $date);
	
	return $imf;
}
?>