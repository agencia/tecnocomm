<?php


$numero = 1234.56;

format_money($numero) ;
format_money(123123213.453543) ;
format_money(1000) ;
format_money(4) ;


function format_money($number){

$p = stripos($number,'.');
$entero = substr($number,0,$p);
$decimal = substr($number,$p,strlen($number));

echo $entero." enetero";

echo $decimal." decimal";



}

?>


