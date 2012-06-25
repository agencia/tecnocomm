<?
require('convertpdf/html2fpdf.php');
$pdf=new HTML2FPDF();
$pdf->AddPage();
$fp = fopen("usuarios.php","r");
$strContent = fread($fp, filesize("usuarios.php"));
fclose($fp);
$pdf->WriteHTML($strContent);
$pdf->Output("cotiza.pdf");
echo "PDF file is generated successfully!";
?>