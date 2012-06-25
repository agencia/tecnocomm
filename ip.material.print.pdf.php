<?php 
include("MPDF44/mpdf.php");

$mpdf=new mPDF('utf-8', 'Folio-L',4,0,10,10,10,10);

$mpdf->useOnlyCoreFonts = true;

$mpdf->SetDisplayMode('fullpage');

$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list

// LOAD a stylesheet
$url =  "http://".$_SERVER['HTTP_HOST']."/ip.material.print.php?idip=".$_GET['idip'];
$stylesheet = file_get_contents('style2.css');
$url =  "http://".$_SERVER['HTTP_HOST']."/ip.material.print.php?idip=".$_GET['idip'];
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text
$html = file_get_contents($url,2);
$mpdf->WriteHTML($html,2);



$mpdf->Output('mpdf.pdf','I');
exit;
?>