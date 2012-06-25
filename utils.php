<?php
function format_money($number){

$number = round($number,2);

$p = (stripos($number,'.')?stripos($number,'.'):strlen($number));
$entero = substr($number,0,$p);
$decimal = substr($number,$p+1,strlen($number));


if(strlen($decimal)<2){
	switch(strlen($decimal)){
		case 0: $decimal=$decimal."00";
					break;
		case 1: $decimal=$decimal."0";
					break;
	}
}else{
	$decimal = substr($decimal,0,2);
}


//invertir cadena
$cadena = "";
for($i=0;$i<strlen($entero);$i++){
$cadena = substr($entero,$i,1).$cadena;
}

//separar por miles
$entero = "";
for($i=0;$i<strlen($cadena);$i++){

	if($i==3){
		$entero = substr($cadena,$i,1).",".$entero;
	}
	elseif($i==6){
		$entero = substr($cadena,$i,1)."'".$entero;
	}
	elseif($i==9){
		$entero = substr($cadena,$i,1).",".$entero;
	}
	elseif($i==12){
		$entero = substr($cadena,$i,1)."''".$entero;
	}else{
	$entero = substr($cadena,$i,1).$entero;
	}

}

return $entero.".".$decimal;
}


function formatDateShort($date){

$meses = array (1=>"Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic",'01'=>"Ene",'02'=>"Feb",'03'=>"Mar",'04'=>"Abr",'05'=>"May",'06'=>"Jun",'07'=>"Jul",'08'=>"Ago",'09'=>"Sep");

$d = explode("-",$date);
$date = $d[2]."-".$meses[$d[1]]."-".$d[0];

return $date;
}

function formatDateTimeShort($date){

$meses = array (1=>"Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic",'01'=>"Ene",'02'=>"Feb",'03'=>"Mar",'04'=>"Abr",'05'=>"May",'06'=>"Jun",'07'=>"Jul",'08'=>"Ago",'09'=>"Sep");

$date = split(" ",$date);
$d = split("-",$date[0]);
$date = $d[2]."-".$meses[$d[1]]. "-" . $d[0] . "<br />" . $date[1];

return $date;
}

function formatDate($date){
$meses = array (1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre",'01'=>"Enero",'02'=>"Febrero",'03'=>"Marzo",'04'=>"Abril",'05'=>"Mayo",'06'=>"Junio",'07'=>"Julio",'08'=>"Agosto",'09'=>"Septiembre");

$d = explode("-",$date);
$ty = explode(" ",$d[2]);
$date = $ty[0]."/".$meses[$d[1]]."/".$d[0];

return $date;
}


function  divisa($cantidad,$mo,$md,$tipodecambio){
	if($mo == $md){
		return $cantidad;
	}
	
	if($mo == 0 && $md == 1){
		if($cantidad != 0)	
	  		$c = ($cantidad / $tipodecambio);
		else
			$c = 0;
			
		return $c;
	}
	if($mo == 1 && $md == 0){
 	 	if($cantidad != 0)	
	  		$c = ($cantidad * $tipodecambio);
		else
			$c = 0;
			
		return $c;
		}
}

function permiso($usuario,$permiso){

include('Connections/tecnocomm.php');
$niv_Niveles = "-1";
if (isset($usuario)) {
  $niv_Niveles = $usuario;
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Niveles = sprintf("select * from autorizacion where nivel=%s", GetSQLValueString($niv_Niveles, "int"));
$Niveles = mysql_query($query_Niveles, $tecnocomm) or die(mysql_error());
$row_Niveles = mysql_fetch_assoc($Niveles);
$totalRows_Niveles = mysql_num_rows($Niveles);


do{
	$array_niveles[]=$row_Niveles['idlink'];
} while ($row_Niveles = mysql_fetch_assoc($Niveles));

if(in_array($permiso,$array_niveles)){
	return true;	
	}
else{
	return false;
	}


	
	}

$estadotareas = array(1 => 'realizado',2=>'finalizado', 3=>'reasignada');

?>