<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
<?php require_once('fpdf.php');?>
<?php define('FPDF_FONTPATH','font/'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "systemFail.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php 
class Conversacion extends FPDF{
	
	function Conversacion($orientation='P',$unit='mm',$format='A4'){
		//Llama al constructor de la clase padre
		$this->FPDF($orientation,$unit,$format);
		$this->setMargins(10,10,10);
		$this->SetAutoPageBreak(true,10);
	}
	
	
	function setEncabezado(){
		//imprime logo y datos de impresion	
	$this->SetFont('Arial','B',6);
	$this->Cell(180,3,'Alertas Tecnocomm',0);
	$this->Ln();
	$this->Cell(90,3,'Fecha Impresion '.date("d/m/Y G:i"),0,'','L');
	$this->Cell(90,3,'Pagina '.$this->PageNo(). ' de {nb}',0,'','R');
	$this->Ln();

	}


	
	function addConv($remitente=NULL,$destinatarios=array(),$mensaje=NULL,$estado=NULL,$prioridad=NULL,$fecha=NULL){
		
		$this->Cell(30,5,"Remitente: ","TL",0,"L");
		$this->Cell(120,5,$remitente,"TLR",0,"L");
		$this->Ln();
		$this->Cell(150,5,"Destinatarios:","TLR",0,"L");
		$this->Ln();
		$this->MultiCell(150,5,"\t\t".implode(",",$destinatarios),"TLR","L");
		$this->Cell(25,5,"Estado: ","TL",0,"L");
		$this->Cell(25,5,$estado,"TL",0,"L");
		$this->Cell(25,5,"Prioridad: ","TL",0,"L");
		$this->Cell(25,5,$prioridad,"TL",0,"L");
		$this->Cell(25,5,"Fecha: ","TL",0,"L");
		$this->Cell(25,5,formatDate($fecha),"TLR",0,"L");
		$this->Ln();
		$this->Cell(150,5,"Mensaje:","TLR",0,"L");
		$this->Ln();
		$this->MultiCell(150,3,"\t\t".$mensaje,"TLRB","L");
		//$this->Ln();
	}
	
	function addMsj($remitente=NULL,$fecha=NULL,$mensaje=NULL){
		$this->Cell(30,5,"Remitente: ","TL",0,"L");
		$this->Cell(70,5,$remitente,"TLR",0,"L");
		$this->Cell(20,5,"Fecha: ","TL",0,"L");
		$this->Cell(30,5,$fecha,"TLR",0,"L");
		$this->Ln();
		$this->Cell(150,5,"Mensaje:","TLR",0,"L");
		$this->Ln();
		$this->MultiCell(150,3,"\t\t".$mensaje,"TLRB","L");
		//$this->Ln();
	
	}
	
	function AcceptPageBreak(){
        return true;
	}

}
?>
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

if(isset($_GET['buscar']) && $_GET['buscar']!=""){
		
		$bus = sprintf("AND (c.mensaje like %s)",
					   GetSQLValueString("%".$_GET['buscar']."%","text"));
		
	}

if(isset($_GET['estado']) && $_GET['estado']!='-1'){
	
		$estado = sprintf("AND (c.estado = %s)",
							GetSQLValueString($_GET['estado'],"int"));
	
	}

if(isset($_GET['idconversacion']) && $_GET['idconversacion']!=""){
		$idconversacion = sprintf("AND (c.idconversacion = %s)",
							GetSQLValueString($_GET['idconversacion'],"int"));
	}
	if(isset($_GET['idusuario']) && $_GET['idusuario']!='-1'){
	$usuario = sprintf("AND (cd.destinatario = %s)",
							GetSQLValueString($_GET['idusuario'],"int"));
	}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuarios = "SELECT * FROM usuarios ORDER BY username ASC";
$rsUsuarios = mysql_query($query_rsUsuarios, $tecnocomm) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);

$maxRows_rsConversaciones = 30;
$pageNum_rsConversaciones = 0;
if (isset($_GET['pageNum_rsConversaciones'])) {
  $pageNum_rsConversaciones = $_GET['pageNum_rsConversaciones'];
}
$startRow_rsConversaciones = $pageNum_rsConversaciones * $maxRows_rsConversaciones;

$colname1_rsConversaciones = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname1_rsConversaciones = $_SESSION['MM_Userid'];
}
$colname2_rsConversaciones = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname2_rsConversaciones = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConversaciones = sprintf("SELECT DISTINCT c.*, u.nombrereal As nombreremitente FROM conversacion c JOIN conversacion_destinatario cd ON c.idconversacion = cd.idconversacion JOIN usuarios u ON u.id = c.remitente WHERE 1=1  %s %s %s %s",
								  $bus,$estado,$usuario,$idconversacion);
$query_limit_rsConversaciones = sprintf("%s LIMIT %d, %d", $query_rsConversaciones, $startRow_rsConversaciones, $maxRows_rsConversaciones);
$rsConversaciones = mysql_query($query_limit_rsConversaciones, $tecnocomm) or die(mysql_error());
$row_rsConversaciones = mysql_fetch_assoc($rsConversaciones);

$query_limit_rsConversaciones = sprintf("%s LIMIT %d, %d", $query_rsConversaciones, $startRow_rsConversaciones, $maxRows_rsConversaciones);
$rsConversaciones = mysql_query($query_limit_rsConversaciones, $tecnocomm) or die(mysql_error());
$row_rsConversaciones = mysql_fetch_assoc($rsConversaciones);

if (isset($_GET['totalRows_rsConversaciones'])) {
  $totalRows_rsConversaciones = $_GET['totalRows_rsConversaciones'];
} else {
  $all_rsConversaciones = mysql_query($query_rsConversaciones);
  $totalRows_rsConversaciones = mysql_num_rows($all_rsConversaciones);
}
$totalPages_rsConversaciones = ceil($totalRows_rsConversaciones/$maxRows_rsConversaciones)-1;


$estado = array("Abierta","Atentendida","Liberada");
$prioridad = array("Alta","Normal");

$pdf = new Conversacion();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->setEncabezado();

do { 
//lista de mensajes
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMensajes = sprintf("SELECT cm.*, u.nombrereal AS nremitente FROM conversacion_mensaje cm LEFT JOIN usuarios u ON u.id = cm.remitente  WHERE cm.idconversacion = %s", GetSQLValueString($row_rsConversaciones['idconversacion'], "int"));
$rsMensajes = mysql_query($query_rsMensajes, $tecnocomm) or die(mysql_error());
$row_rsMensajes = mysql_fetch_assoc($rsMensajes);
$totalRows_rsMensajes = mysql_num_rows($rsMensajes);


//destinatarios..
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDestinatarios = sprintf("SELECT cd.*,u.nombrereal AS ndestinatario FROM conversacion_destinatario cd JOIN usuarios u ON cd.destinatario = u.id WHERE idconversacion = %s", GetSQLValueString($row_rsConversaciones['remitente'], "int"));
$rsDestinatarios = mysql_query($query_rsDestinatarios, $tecnocomm) or die(mysql_error());
$row_rsDestinatarios = mysql_fetch_assoc($rsDestinatarios);
$totalRows_rsDestinatarios = mysql_num_rows($rsDestinatarios);
unset($destinatarios);
do{
	
	$destinatarios[] = $row_rsDestinatarios['ndestinatario'];
	
}while($row_rsDestinatarios = mysql_fetch_assoc($rsDestinatarios));
		
$pdf->addConv($row_rsConversaciones['nombreremitente'],$destinatarios,$row_rsConversaciones['mensaje'],$estado[$row_rsConversaciones['estado']], $prioridad[$row_rsConversaciones['prioridad']],$row_rsConversaciones['fechacreado']);

if($totalRows_rsMensajes > 0){
	do{
		$pdf->addMsj($row_rsMensajes['nremitente'],formatDate($row_rsMensajes['fecha']),$row_rsMensajes['mensaje']);
		
	}while($row_rsMensajes = mysql_fetch_assoc($rsMensajes));	
}

$pdf->Ln();
$pdf->Ln();
}while($row_rsConversaciones = mysql_fetch_assoc($rsConversaciones)); 
 
$pdf->Output();
?>