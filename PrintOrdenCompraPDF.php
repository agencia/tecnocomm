<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('pdfcreator.php');?>
<?php require_once('utils.php');?>
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


$colname_rsDetalle = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsDetalle = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT a.nombre,a.codigo,a.marca,dor.cantidad,dor.costo,a.medida FROM detalleorden dor,articulo a WHERE dor.idarticulo = a.idarticulo AND dor.idordencompra = %s", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);

$colname_rsProveedor = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsProveedor = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProveedor = sprintf("SELECT * FROM proveedor p,ordencompra o WHERE o.idproveedor = p.idproveedor AND o.idordencompra = %s ", GetSQLValueString($colname_rsProveedor, "int"));
$rsProveedor = mysql_query($query_rsProveedor, $tecnocomm) or die(mysql_error());
$row_rsProveedor = mysql_fetch_assoc($rsProveedor);
$totalRows_rsProveedor = mysql_num_rows($rsProveedor);

$colname_rsOrdenCompra = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsOrdenCompra = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenCompra = sprintf("SELECT * FROM ordencompra WHERE idordencompra = %s", GetSQLValueString($colname_rsOrdenCompra, "int"));
$rsOrdenCompra = mysql_query($query_rsOrdenCompra, $tecnocomm) or die(mysql_error());
$row_rsOrdenCompra = mysql_fetch_assoc($rsOrdenCompra);
$totalRows_rsOrdenCompra = mysql_num_rows($rsOrdenCompra);

$colname_rsFirma = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsFirma = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFirma = sprintf("SELECT u.* FROM  ordencompra od,usuarios u WHERE od.usercreo = u.id AND od.idordencompra = %s", GetSQLValueString($colname_rsFirma, "int"));
$rsFirma = mysql_query($query_rsFirma, $tecnocomm) or die(mysql_error());
$row_rsFirma = mysql_fetch_assoc($rsFirma);
$totalRows_rsFirma = mysql_num_rows($rsFirma);

$colname_rsOrdenCompra2 = "-1";
if (isset($_GET['idordencompra'])) {
  $colname_rsOrdenCompra2 = $_GET['idordencompra'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenCompra2 = sprintf("SELECT c.idip FROM ordencompra o,subcotizacion s,cotizacion c WHERE o.idcotizacion=s.idsubcotizacion and s.idcotizacion=c.idcotizacion and  idordencompra = %s", GetSQLValueString($colname_rsOrdenCompra2, "int"));
$rsOrdenCompra2 = mysql_query($query_rsOrdenCompra2, $tecnocomm) or die(mysql_error());
$row_rsOrdenCompra2 = mysql_fetch_assoc($rsOrdenCompra2);
$totalRows_rsOrdenCompra2 = mysql_num_rows($rsOrdenCompra2);


$sub=0;
$signo = array("$ ","US$ ");
$counter = 0;
$par = 0;
do{
$counter++;
$par++;
 $long = strlen(utf8_decode(trim($row_rsDetalle['nombre'])));

if($long < 56){
$partida[] = $par;
$codigo[] =$row_rsDetalle['codigo'];
$marca[] =$row_rsDetalle['marca'];
$descripcion[] = utf8_decode(trim($row_rsDetalle['nombre']));
$cantidad[] = $row_rsDetalle['cantidad'];
$medida[] =$row_rsDetalle['medida'];
$precio[] = money_format('%i', $row_rsDetalle['costo']);
$importe2[] =money_format('%i',($row_rsDetalle['cantidad'] * $row_rsDetalle['costo']));
$importe[] = money_format('%i',(($row_rsDetalle['cantidad'] * $row_rsDetalle['costo'])));
$sub = $sub + money_format('%i',($row_rsDetalle['cantidad'] * $row_rsDetalle['costo']));

 }else{
 	//mas lienas
		
	unset($line);//limpiamos lineas 	
	//separamos por palabras
	
	$palabras = split(" ",utf8_decode(trim($row_rsDetalle['nombre'])));
	
	$num = $long = $countc = 0;
	foreach($palabras as $palabra){
			
			$long = strlen($palabra);
			
			if(($countc+$long) < 56){
			$countc = $countc+$long+1;
			}else{
			$num++;
			$countc=$long+1;
			}
			
			$line[$num] = $line[$num].$palabra." ";
	}
	
	
	/*
 	$comienzo = 0;
	$line = substr  ( $row_rsDetalle['descri'], $comienzo ,60);
	$comienzo = $comienzo + 60 ;
	*/
	
	
$partida[] = $par;
$codigo[] =$row_rsDetalle['codigo'];
$marca[] =$row_rsDetalle['marca'];
$descripcion[] = $line[0];
$cantidad[] = $row_rsDetalle['cantidad'];
$medida[] =$row_rsDetalle['medida'];
$precio[] = money_format('%i', $row_rsDetalle['costo']);
$importe2[] =money_format('%i',($row_rsDetalle['cantidad'] * $row_rsDetalle['costo']));
$importe[] = money_format('%i',(($row_rsDetalle['cantidad'] * $row_rsDetalle['costo'])));
$sub = $sub + money_format('%i',($row_rsDetalle['cantidad'] * $row_rsDetalle['costo']));

	for($i=1;$i<count($line);$i++){
		$counter++;
		//$line = substr  ( $row_rsDetalle['descri'], $comienzo , 60);
		//$comienzo = $comienzo + 60 ;
		$partida[]="";
		$codigo[] = "";
		$marca[] ="";
		$descripcion[] = $line[$i];
		$cantidad[] = "";
		$medida[] ="";
		$precio[] ="";
		$importe[] = "";
		$importe2[] = "";
	}
}

}while($row_rsDetalle = mysql_fetch_assoc($rsDetalle));



require_once('numtoletras.php');


$forma=array(0=>"CONTADO",1=>"50 % ANTICIPO y 50% CONTRAENTREGA",2=>"50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE");
$moneda = array(0=>"MONEDA NACIONAL",1=>"DOLARES AMERICANOS PAGEDEROS EN M.N AL TIPO DE CAMBIO DE LA FECHA DE PAGO");





$paginas = ceil($counter/32);

switch($paginas){
	case 1: 	
	$pdf = new PDF();
	$pdf->signo=$signo[$row_rsProveedor['moneda']];
	$pdf->AddPage();
	$pdf->createFondoOrden();
	$pdf->setIP($row_rsOrdenCompra2['idip']);
	$pdf->setTitle($row_rsCotizacion['nombre']);
	//encabezado
	$pdf->setEncabezado(utf8_decode($row_rsProveedor['nombrecomercial']),utf8_decode($row_rsProveedor['domicilio']),utf8_decode($row_rsProveedor['contacto']),utf8_decode($row_rsProveedor['telefono']),utf8_decode($row_rsProveedor['ciudad']),utf8_decode($row_rsProveedor['email']),utf8_decode($row_rsOrdenCompra['identificador']),formatDate($row_rsOrdenCompra['fecha']));
	$pdf->setCot("O. DE COMPRA: ");
	//detalle         
	
		 $subtotal = 0;
		  for($row=1;$row<=$counter;$row++){ 
		  
		  if(strlen($precio[$row-1])  <= 1 ){
		  	$pre = "";
			$imp = "";
		  }else{
		  	$pre = format_money($precio[$row-1]);
			$imp = format_money($importe[$row-1]);
		  }
		  
		  $subtotal = $subtotal + $importe2[$row-1];
			$pdf->setContenido( $partida[$row-1],$codigo[$row-1],$marca[$row-1],$descripcion[$row-1],$cantidad[$row-1],$medida[$row-1],$pre,$imp);
		}
		
	if($row_rsCotizacion['descuento']){
$cant = money_format('%!n',(0 - ($subtotal * ($row_rsCotizacion['descuento']/100))));
$pdf->setDescuento($row_rsCotizacion['descuento']."%",format_money($cant));
	$subtotal = $subtotal + $cant;
}
	$pdf->catidadLetra(num2letras(money_format('%!n',$subtotal*1.16),false,true,$row_rsOrdenCompra['moneda']));
//Pie

$pdf->setPie(format_money($subtotal),format_money($subtotal*.16),format_money(($subtotal*1.16)),utf8_decode($row_rsFirma['nombrereal']),utf8_decode($row_rsFirma['puesto']),utf8_decode($row_rsFirma['email']),utf8_decode($row_rsOrdenCompra['formapago']),utf8_decode($moneda[$row_rsOrdenCompra['moneda']]),utf8_decode($row_rsOrdenCompra['vigencia']),utf8_decode($row_rsOrdenCompra['tiempoentrega']),utf8_decode($row_rsOrdenCompra['descuento']),utf8_decode($row_rsOrdenCompra['notas']));

$pdf->paginacion("Pag 1/1");

$fir = "firmas/".$row_rsFirma['username'].".jpg";
if(file_exists($fir)){
$pdf->setFirma($fir);
}


$pdf->Output($row_rsOrdenCompra['identificador'].'.pdf','I');
	
	break;
	
	case 2: 
	$pdf = new PDF();
	$pdf->signo=$signo[$row_rsProveedor['moneda']];
	$pdf->AddPage();
	$pdf->createFondoOrden1();
	$pdf->setIP($row_rsOrdenCompra2['idip']);
	$pdf->setTitle($row_rsCotizacion['nombre']);
	//encabezado
	$pdf->setEncabezado(utf8_decode($row_rsProveedor['nombrecomercial']),utf8_decode($row_rsProveedor['domicilio']),utf8_decode($row_rsProveedor['contacto']),utf8_decode($row_rsProveedor['telefono']),utf8_decode($row_rsProveedor['ciudad']),utf8_decode($row_rsProveedor['email']),utf8_decode($row_rsOrdenCompra['identificador']),formatDate($row_rsOrdenCompra['fecha']));
	$pdf->setCot("O. DE COMPRA 1: ");
	//detalle
	
		 $subtotal = 0;

		  for($row=1;$row<=32;$row++){ 
		  
		    if(strlen($precio[$row-1])  <= 1 ){
		  	$pre = "";
			$imp = "";
		  }else{
		  	$pre = format_money($precio[$row-1]);
			$imp = format_money($importe[$row-1]);
		  }
		  
		  $subtotal = $subtotal + $importe2[$row-1];
			$pdf->setContenido( $partida[$row-1],$codigo[$row-1],$marca[$row-1],$descripcion[$row-1],$cantidad[$row-1],$medida[$row-1],$pre,$imp);
		}//$pdf->catidadLetra(num2letras(money_format('%!n',$subtotal),false,true,$row_rsCotizacion['moneda']));
		
		$pdf->subtotal1(format_money($subtotal));
		$pdf->paginacion("Pag 1/2");
		$pdf->resetLine();
 $pdf->AddPage();
 	$pdf->setContenido();
			$pdf->setContenido();
	$pdf->createFondoOrden3();
	$pdf->setIP($row_rsOrdenCompra2['idip']);
	$pdf->setTitle2($row_rsCotizacion['nombre']);
	//encabezado
			$pdf->subtotal3(format_money($subtotal));
	$pdf->setEncabezado(utf8_decode($row_rsProveedor['nombrecomercial']),utf8_decode($row_rsProveedor['domicilio']),utf8_decode($row_rsProveedor['contacto']),utf8_decode($row_rsProveedor['telefono']),utf8_decode($row_rsProveedor['ciudad']),utf8_decode($row_rsProveedor['email']),utf8_decode($row_rsOrdenCompra['identificador']),formatDate($row_rsOrdenCompra['fecha']));
	$pdf->setCot("O. DE COMPRA 2: ");
	//detalle
	
		  for($row=33;$row<=$counter;$row++){ 
		  
		  	  
		    if(strlen($precio[$row-1])  <= 1 ){
		  	$pre = "";
			$imp = "";
		  }else{
		  	$pre = format_money($precio[$row-1]);
			$imp = format_money($importe[$row-1]);
		  }
		  
		  $subtotal = $subtotal + $importe2[$row-1];
			$pdf->setContenido( $partida[$row-1],$codigo[$row-1],$marca[$row-1],$descripcion[$row-1],$cantidad[$row-1],$medida[$row-1],$pre,$imp);
		}
		
		if($row_rsCotizacion['descuento']){
$cant = money_format('%!n',(0 - ($subtotal * ($row_rsCotizacion['descuento']/100))));
$pdf->setDescuento($row_rsCotizacion['descuento']."%",$cant);
	$subtotal = $subtotal + $cant;
}
$pdf->catidadLetra(num2letras(money_format('%!n',$subtotal*1.16),false,true,$row_rsOrdenCompra['moneda']));
	//Pie

$pdf->setPie(format_money($subtotal),format_money($subtotal*.16),format_money(($subtotal*1.16)),utf8_decode($row_rsFirma['nombrereal']),utf8_decode($row_rsFirma['puesto']),utf8_decode($row_rsFirma['email']),utf8_decode($row_rsOrdenCompra['formapago']),utf8_decode($moneda[$row_rsOrdenCompra['moneda']]),utf8_decode($row_rsOrdenCompra['vigencia']),utf8_decode($row_rsOrdenCompra['tiempoentrega']),utf8_decode($row_rsOrdenCompra['descuento']),utf8_decode($row_rsOrdenCompra['notas']));

	$pdf->paginacion("Pag 2/2");
$fir = "firmas/".$row_rsFirma['username'].".jpg";
if(file_exists($fir)){
$pdf->setFirma($fir);
}
	
$pdf->Output($row_rsOrdenCompra['identificador'].'.pdf','I');

break;
	
	default : 
	
	$pdf = new PDF();
	$pdf->signo=$signo[$row_rsProveedor['moneda']];
	$p = $paginas;
	
	//primera pagina
	$pdf->AddPage();
	$pdf->createFondoOrden1();
	$pdf->setIP($row_rsOrdenCompra2['idip']);
$pdf->setTitle($row_rsCotizacion['nombre']);
	//encabezado
	
	$pdf->setEncabezado(utf8_decode($row_rsProveedor['nombrecomercial']),utf8_decode($row_rsProveedor['domicilio']),utf8_decode($row_rsProveedor['contacto']),utf8_decode($row_rsProveedor['telefono']),utf8_decode($row_rsProveedor['ciudad']),utf8_decode($row_rsProveedor['email']),utf8_decode($row_rsOrdenCompra['identificador']),formatDate($row_rsOrdenCompra['fecha']));
	$pdf->setCot("O. DE COMPRA 1: ");
	//detalle
		 $subtotal = $row = 0;
		  for($i=0;$i<32;$i++){ 
		  
			  		    if(strlen($precio[$row])  <= 1 ){
		  	$pre = "";
			$imp = "";
		  }else{
		  	$pre = format_money($precio[$row]);
			$imp = format_money($importe[$row]);
		  }
		  
		  $subtotal = $subtotal + $importe2[$row];
			$pdf->setContenido( $partida[$row],$codigo[$row],$marca[$row],$descripcion[$row],$cantidad[$row],$medida[$row],$pre,$imp);
			$row++;
		}//$pdf->catidadLetra(num2letras(money_format('%!n',$subtotal),false,true,$row_rsCotizacion['moneda']));
		
		$pdf->subtotal1(format_money($subtotal));
		$pdf->paginacion("Pag 1/".$p);
		//fin primera pagina
		
		//paginas medias
		
		$paginas=$paginas-1;
		for($i=1;$i<$paginas;$i++){
			//fondo 2		
			$pdf->resetLine();
			$pdf->AddPage();
			$pdf->setContenido();
			$pdf->setContenido();
			$pdf->createFondoOrden2();
			$pdf->setIP($row_rsOrdenCompra2['idip']);
			$pdf->setTitle2($row_rsCotizacion['nombre']);
			$sub = $subtotal;
			//encabezado
			$pdf->setEncabezado(utf8_decode($row_rsProveedor['nombrecomercial']),utf8_decode($row_rsProveedor['domicilio']),utf8_decode($row_rsProveedor['contacto']),utf8_decode($row_rsProveedor['telefono']),utf8_decode($row_rsProveedor['ciudad']),utf8_decode($row_rsProveedor['email']),utf8_decode($row_rsOrdenCompra['identificador']),formatDate($row_rsOrdenCompra['fecha']));
	$pdf->setCot("O. DE COMPRA ".($i+1).": ");
			
			//detalle
				  for($y=0;$y<32;$y++){ 	
				  		    if(strlen($precio[$row])  <= 1 ){
		  	$pre = "";
			$imp = "";
		  }else{
		  	$pre = format_money($precio[$row]);
			$imp = format_money($importe[$row]);
		  }
				    
				  $subtotal = $subtotal + $importe2[$row];
					$pdf->setContenido( $partida[$row],$codigo[$row],$marca[$row],$descripcion[$row],$cantidad[$row],$medida[$row],$pre,$imp);
					$row++;
				}//$pdf->catidadLetra1(num2letras(money_format('%!n',$subtotal),false,true,$row_rsCotizacion['moneda']));
				
					$pdf->subtotal2(format_money($sub),format_money($subtotal));
					$pag = "Pag ".($i+1)."/".$p;
					$pdf->paginacion($pag);
		
		
		}
		
		
		
		
		$pdf->resetLine();	
 		$pdf->AddPage();
		$pdf->setContenido();
		$pdf->setContenido();
		$pdf->createFondoOrden3();
		$pdf->setIP($row_rsOrdenCompra2['idip']);
		$pdf->setTitle2($row_rsCotizacion['nombre']);
		$pdf->subtotal3(format_money($subtotal));
		//encabezado
		$pdf->setEncabezado(utf8_decode($row_rsProveedor['nombrecomercial']),utf8_decode($row_rsProveedor['domicilio']),utf8_decode($row_rsProveedor['contacto']),utf8_decode($row_rsProveedor['telefono']),utf8_decode($row_rsProveedor['ciudad']),utf8_decode($row_rsProveedor['email']),utf8_decode($row_rsOrdenCompra['identificador']),formatDate($row_rsOrdenCompra['fecha']));
		$pdf->setCot("O. DE COMPRA ".$p.": ");
	//detalle
	
		  for($i=0;$i<32;$i++){ 
				  		    if(strlen($precio[$row])  <= 1 ){
		  	$pre = "";
			$imp = "";
		  }else{
		  	$pre = format_money($precio[$row]);
			$imp = format_money($importe[$row]);
		  }
		  
		  	$subtotal = $subtotal + $importe2[$row];
			$pdf->setContenido( $partida[$row],$codigo[$row],$marca[$row],$descripcion[$row],$cantidad[$row],$medida[$row],$pre,$imp);
			$row++;
			}
		
		if($row_rsCotizacion['descuento']){
$cant = money_format('%!n',(0 - ($subtotal * ($row_rsCotizacion['descuento']/100))));
$pdf->setDescuento($row_rsCotizacion['descuento']."%",$cant);
	$subtotal = $subtotal + $cant;
}
		$pdf->catidadLetra(num2letras(money_format('%!n',$subtotal*1.16),false,true,$row_rsOrdenCompra['moneda']));
		
		//Pie

$pdf->setPie(format_money($subtotal),format_money($subtotal*.16),format_money(($subtotal*1.16)),utf8_decode($row_rsFirma['nombrereal']),utf8_decode($row_rsFirma['puesto']),utf8_decode($row_rsFirma['email']),utf8_decode($row_rsOrdenCompra['formapago']),utf8_decode($moneda[$row_rsOrdenCompra['moneda']]),utf8_decode($row_rsOrdenCompra['vigencia']),utf8_decode($row_rsOrdenCompra['tiempoentrega']),utf8_decode($row_rsOrdenCompra['descuento']),utf8_decode($row_rsOrdenCompra['notas']));

			$pdf->paginacion("Pag ".$p."/".$p);
	$fir = "firmas/".$row_rsFirma['username'].".jpg";
if(file_exists($fir)){
$pdf->setFirma($fir);
}
			$pdf->Output($row_rsOrdenCompra['identificador'].'.pdf','I');
}



mysql_free_result($rsDetalle);

mysql_free_result($rsProveedor);

mysql_free_result($rsOrdenCompra);

mysql_free_result($rsFirma);
?>