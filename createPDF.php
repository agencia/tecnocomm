<?php
/**
* HTML2PDFReport Generator Class
*
* @author  :  MA Razzaque Rupom <rupom_315@yahoo.com>, <rupom.bd@gmail.com>
*             Moderator, phpResource (http://groups.yahoo.com/group/phpresource/)
*             URL: http://www.rupom.info  
* @version :  1.0
* @date       05/06/2006 modified on 06/23/2006
* Purpose  :  Generating Pdf Report from HTML
*/

require_once('Html2PdfReport.class.php');
$obj = new Html2PdfReport();

//pdf version
$pdfVersion = '1.4'; //change it according to your need
/*
$pdfVersion = 1.3 for Acrobat Reader 4
$pdfVersion = 1.4 for Acrobat Reader 5
$pdfVersion = 1.5 for Acrobat Reader 6
*/

//set PDF version
$obj->setPdfVersion($pdfVersion);
$url = $_GET['url'];
//set URL of the HTML file which will be converted to PDF
$obj->setUrl($url);//change this according to your URL

//get the pdf report of the URL data        
$obj->getPdfReport(); 
?>