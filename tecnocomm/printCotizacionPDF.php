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

$colname_rsCotizacion = "-1";
if (isset($_GET['idcliente'])) {
  $colname_rsCotizacion =$_GET['idcliente'];
}
$colname_rsCotizacion = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsCotizacion = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT * FROM subcotizacion WHERE idsubcotizacion = %s", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);

$colname_rsDetalle = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsDetalle = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT *, sb.moneda AS monedacotizacion FROM subcotizacionarticulo sb, articulo a WHERE idsubcotizacion = %s AND a.idarticulo = sb.idarticulo ORDER BY sb.idsubcotizacionarticulo", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);

$colname_rsCliente = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsCliente = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCliente = sprintf("SELECT cl.nombre,cl.direccion,cl.ciudad,cl.razonsocial,cl.direccionfacturacion FROM subcotizacion sb,cotizacion c,cliente cl WHERE sb.idcotizacion = c.idcotizacion AND cl.idcliente = c.idcliente AND sb.idsubcotizacion = %s", GetSQLValueString($colname_rsCliente, "int"));
$rsCliente = mysql_query($query_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);
$totalRows_rsCliente = mysql_num_rows($rsCliente);


$colname_rsContacto = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsContacto = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsContacto = sprintf("SELECT c.* FROM subcotizacion sb,contactoclientes c WHERE sb.contacto = c.idcontacto AND sb.idsubcotizacion = %s", GetSQLValueString($colname_rsContacto, "int"));
$rsContacto = mysql_query($query_rsContacto, $tecnocomm) or die(mysql_error());
$row_rsContacto = mysql_fetch_assoc($rsContacto);
$totalRows_rsContacto = mysql_num_rows($rsContacto);

$colname_rsFirma = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsFirma = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsFirma = sprintf("SELECT u.* FROM subcotizacion sb,usuarios u WHERE sb.usercreo = u.id AND sb.idsubcotizacion = %s", GetSQLValueString($colname_rsFirma, "int"));
$rsFirma = mysql_query($query_rsFirma, $tecnocomm) or die(mysql_error());
$row_rsFirma = mysql_fetch_assoc($rsFirma);
$totalRows_rsFirma = mysql_num_rows($rsFirma);

$colname_rsGlobal = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsGlobal = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsGlobal = sprintf("SELECT SUM(mo*cantidad) AS globaltotal FROM subcotizacionarticulo WHERE idsubcotizacion = %s", GetSQLValueString($colname_rsGlobal, "int"));
$rsGlobal = mysql_query($query_rsGlobal, $tecnocomm) or die(mysql_error());
$row_rsGlobal = mysql_fetch_assoc($rsGlobal);
$totalRows_rsGlobal = mysql_num_rows($rsGlobal);

$man=0;
$sub=0;
$signo = array("$ ","US$ ");
$counter = 0;
$par = 0;
do{
$counter++;
$par++;
 $long = strlen(trim($row_rsDetalle['descri']));


$pre =divisa($row_rsDetalle['precio_cotizacion'],$row_rsDetalle['monedacotizacion'],$row_rsCotizacion['moneda'],$row_rsCotizacion['tipo_cambio']);
	
$manoobra = divisa($row_rsDetalle['mo'],$row_rsDetalle['monedacotizacion'],$row_rsCotizacion['moneda'],$row_rsCotizacion['tipo_cambio']);


if($long < 56){
$partida[] = $par;
$codigo[] =$row_rsDetalle['codigo'];
$marca[] =$row_rsDetalle['marca'];
$descripcion[] = utf8_decode(trim($row_rsDetalle['descri']));
$cantidad[] = $row_rsDetalle['cantidad'];
$medida[] =$row_rsDetalle['medida'];

if($row_rsCotizacion['tipo']  == 0)
	$p = round(($pre * $row_rsDetalle['utilidad']) + $manoobra,2);
else{	
	$p = round(($pre * $row_rsDetalle['utilidad']),2) ;
}


$man = $man + ($manoobra*$row_rsDetalle['cantidad']);

$precio[] = money_format('%i', $p);
$importe2[] =money_format('%i',($row_rsDetalle['cantidad'] * $p));
$importe[] = money_format('%i',(($row_rsDetalle['cantidad'] * $p)));
$sub = $sub + money_format('%i',($row_rsDetalle['cantidad'] * $p));

 }else{
 	//mas lienas
		
	unset($line);//limpiamos lineas 	
	//separamos por palabras
	
	$palabras = split(" ",utf8_decode(trim($row_rsDetalle['descri'])));
	
	$num = $long = $countc = 0;
	foreach($palabras as $palabra){
			
			$long = strlen($palabra);
			
			if(($countc+$long) < 57){
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
	
	
	
	
	if($row_rsCotizacion['tipo']  == 0)
	$p = ($pre * $row_rsDetalle['utilidad']) + $manoobra;
else{	
	$p = ($pre * $row_rsDetalle['utilidad']) ;
}

$man = $man + ($manoobra*$row_rsDetalle['cantidad']);

$precio[] = money_format('%!n', $p);
$importe2[] =money_format('%!n',($row_rsDetalle['cantidad'] * $p));
$importe[] = money_format('%!n',(($row_rsDetalle['cantidad'] * $p)));
$sub = $sub + money_format('%!n',($row_rsDetalle['cantidad'] * $p));

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



//suministro Global
if($row_rsCotizacion['tipo']  == 1){
$counter++;
$par++;
$long = strlen($row_rsCotizacion['descrimano']);

if($long < 56){
$partida[] = $par;
$codigo[] = $row_rsCotizacion['codigo'];
$marca[] = $row_rsCotizacion['marca'];
$descripcion[] = utf8_decode($row_rsCotizacion['descrimano']);
$cantidad[] = $row_rsCotizacion['cantidad'];
$medida[] = $row_rsCotizacion['unidad'];

$precio[] = money_format('%!n',$row_rsCotizacion['monto']*$man);
$importe2[] = money_format('%!n',$row_rsCotizacion['cantidad']*$row_rsCotizacion['monto']*$man);
$importe[] =  money_format('%!n',$row_rsCotizacion['cantidad']*$row_rsCotizacion['monto']*$man);

$sub = $sub + $row_rsCotizacion['monto'];

 }else{
 	//mas lienas
		
	unset($line);//limpiamos lineas 	
	//separamos por palabras
	
	$palabras = split(" ",utf8_decode($row_rsCotizacion['descrimano']));
	
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

	$partida[] = $par;
$codigo[] = $row_rsCotizacion['codigo'];
$marca[] = $row_rsCotizacion['marca'];
$descripcion[] = $line[0];
$cantidad[] = $row_rsCotizacion['cantidad'];
$medida[] = $row_rsCotizacion['unidad'];


$precio[] = money_format('%!n',$row_rsCotizacion['monto']*$man);
$importe2[] = money_format('%!n',$row_rsCotizacion['cantidad']*$row_rsCotizacion['monto']*$man);
$importe[] =  money_format('%!n',$row_rsCotizacion['cantidad']*$row_rsCotizacion['monto']*$man);
	for($i=1;$i<count($line);$i++){
		$counter++;
		//$line = substr  (  $row_rsCotizacion['descrimano'], $comienzo , 60);
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

}//fin de global




require_once('numtoletras.php');


$forma=array(0=>"CONTADO",1=>"50 % ANTICIPO y 50% CONTRAENTREGA",2=>"50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE");
$moneda = array(0=>"MONEDA NACIONAL",1=>"DOLARES AMERICANOS PAGEDEROS EN M.N AL TIPO DE CAMBIO DE LA FECHA DE PAGO");





$paginas = ceil($counter/32);

switch($paginas){
	case 1: 	
	$pdf = new PDF();
	$pdf->signo=$signo[$row_rsCotizacion['moneda']];
	$pdf->AddPage();
	$pdf->createFondo();
	$pdf->setTitle($row_rsCotizacion['nombre']);
	//encabezado
	$pdf->setEncabezado(utf8_decode($row_rsCliente['razonsocial']),utf8_decode($row_rsCliente['direccionfacturacion']),utf8_decode($row_rsContacto['nombre']),utf8_decode($row_rsContacto['telefono']),utf8_decode($row_rsCliente['ciudad']),utf8_decode($row_rsContacto['correo']),utf8_decode($row_rsCotizacion['identificador2']),formatDate($row_rsCotizacion['fecha']));
	$pdf->setCot("COTIZACION: ");
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
	$pdf->catidadLetra(num2letras(money_format('%!n',$subtotal*1.15),false,true,$row_rsCotizacion['moneda']));
//Pie

$pdf->setPie(format_money($subtotal),format_money($subtotal*.15),format_money(($subtotal*1.15)),utf8_decode($row_rsFirma['nombrereal']),utf8_decode($row_rsFirma['puesto']),utf8_decode($row_rsFirma['email']),utf8_decode($row_rsCotizacion['formapago']),utf8_decode($moneda[$row_rsCotizacion['moneda']]),utf8_decode($row_rsCotizacion['vigencia']),utf8_decode($row_rsCotizacion['tipoentrega']),utf8_decode($row_rsCotizacion['garantia']),utf8_decode($row_rsCotizacion['notas']));

$pdf->paginacion("Pag 1/1");

$fir = "firmas/".$row_rsFirma['username'].".jpg";
if(file_exists($fir)){
$pdf->setFirma($fir);
}


$pdf->Output($row_rsCotizacion['identificador2'].'.pdf','I');
	
	break;
	
	case 2: 
	$pdf = new PDF();
	$pdf->signo=$signo[$row_rsCotizacion['moneda']];
	$pdf->AddPage();
	$pdf->createFondo1();
	$pdf->setTitle($row_rsCotizacion['nombre']);
	//encabezado
	$pdf->setEncabezado(utf8_decode($row_rsCliente['razonsocial']),utf8_decode($row_rsCliente['direccionfacturacion']),utf8_decode($row_rsContacto['nombre']),utf8_decode($row_rsContacto['telefono']),utf8_decode($row_rsCliente['ciudad']),utf8_decode($row_rsContacto['correo']),utf8_decode($row_rsCotizacion['identificador2']),formatDate($row_rsCotizacion['fecha']));
	$pdf->setCot("COTIZACION 1: ");
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
	$pdf->createFondo3();
	$pdf->setTitle2($row_rsCotizacion['nombre']);
	//encabezado
			$pdf->subtotal3(format_money($subtotal));
	$pdf->setEncabezado(utf8_decode($row_rsCliente['razonsocial']),utf8_decode($row_rsCliente['direccionfacturacion']),utf8_decode($row_rsContacto['nombre']),utf8_decode($row_rsContacto['telefono']),utf8_decode($row_rsCliente['ciudad']),utf8_decode($row_rsContacto['correo']),utf8_decode($row_rsCotizacion['identificador2']),formatDate($row_rsCotizacion['fecha']));
	$pdf->setCot("COTIZACION 2: ");
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
$pdf->catidadLetra(num2letras(money_format('%!n',$subtotal*1.15),false,true,$row_rsCotizacion['moneda']));
	//Pie

$pdf->setPie(format_money($subtotal),format_money($subtotal*.15),format_money(($subtotal*1.15)),utf8_decode($row_rsFirma['nombrereal']),utf8_decode($row_rsFirma['puesto']),utf8_decode($row_rsFirma['email']),utf8_decode($row_rsCotizacion['formapago']),utf8_decode($moneda[$row_rsCotizacion['moneda']]),utf8_decode($row_rsCotizacion['vigencia']),utf8_decode($row_rsCotizacion['tipoentrega']),utf8_decode($row_rsCotizacion['garantia']),utf8_decode($row_rsCotizacion['notas']));

	$pdf->paginacion("Pag 2/2");
$fir = "firmas/".$row_rsFirma['username'].".jpg";
if(file_exists($fir)){
$pdf->setFirma($fir);
}
	
$pdf->Output($row_rsCotizacion['identificador2'].'.pdf','I');

break;
	
	default : 
	
	$pdf = new PDF();
	$pdf->signo=$signo[$row_rsCotizacion['moneda']];
	$p = $paginas;
	
	//primera pagina
	$pdf->AddPage();
	$pdf->createFondo1();
$pdf->setTitle($row_rsCotizacion['nombre']);
	//encabezado
	$pdf->setEncabezado(utf8_decode($row_rsCliente['razonsocial']),utf8_decode($row_rsCliente['direccionfacturacion']),utf8_decode($row_rsContacto['nombre']),utf8_decode($row_rsContacto['telefono']),utf8_decode($row_rsCliente['ciudad']),utf8_decode($row_rsContacto['correo']),utf8_decode($row_rsCotizacion['identificador2']),formatDate($row_rsCotizacion['fecha']));
	$pdf->setCot("COTIZACION1: ");
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
			$pdf->createFondo2();
			$pdf->setTitle2($row_rsCotizacion['nombre']);
			$sub = $subtotal;
			//encabezado
			$pdf->setEncabezado(utf8_decode($row_rsCliente['razonsocial']),utf8_decode($row_rsCliente['direccionfacturacion']),utf8_decode($row_rsContacto['nombre']),utf8_decode($row_rsContacto['telefono']),utf8_decode($row_rsCliente['ciudad']),utf8_decode($row_rsContacto['correo']),utf8_decode($row_rsCotizacion['identificador2']),formatDate($row_rsCotizacion['fecha']));
			$pdf->setCot("COTIZACION ".($i+1).": ");
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
		$pdf->createFondo3();
		$pdf->setTitle2($row_rsCotizacion['nombre']);
		$pdf->subtotal3(format_money($subtotal));
		//encabezado
		$pdf->setEncabezado(utf8_decode($row_rsCliente['razonsocial']),utf8_decode($row_rsCliente['direccionfacturacion']),utf8_decode($row_rsContacto['nombre']),utf8_decode($row_rsContacto['telefono']),utf8_decode($row_rsCliente['ciudad']),utf8_decode($row_rsContacto['correo']),utf8_decode($row_rsCotizacion['identificador2']),formatDate($row_rsCotizacion['fecha']));
		$pdf->setCot("COTIZACION ".$p.": ");
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
		$pdf->catidadLetra(num2letras(money_format('%!n',$subtotal*1.15),false,true,$row_rsCotizacion['moneda']));
		
		//Pie

$pdf->setPie(format_money($subtotal),format_money($subtotal*.15),format_money(($subtotal*1.15)),utf8_decode($row_rsFirma['nombrereal']),utf8_decode($row_rsFirma['puesto']),utf8_decode($row_rsFirma['email']),utf8_decode($row_rsCotizacion['formapago']),utf8_decode($moneda[$row_rsCotizacion['moneda']]),utf8_decode($row_rsCotizacion['vigencia']),utf8_decode($row_rsCotizacion['tipoentrega']),utf8_decode($row_rsCotizacion['garantia']),utf8_decode($row_rsCotizacion['notas']));

			$pdf->paginacion("Pag ".$p."/".$p);
	$fir = "firmas/".$row_rsFirma['username'].".jpg";
if(file_exists($fir)){
$pdf->setFirma($fir);
}
			$pdf->Output($row_rsCotizacion['identificador2'].'.pdf','I');
}



mysql_free_result($rsCliente);

mysql_free_result($rsGlobal);

mysql_free_result($rsDetalle);

mysql_free_result($rsCotizacion);
?>