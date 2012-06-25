<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('numtoletras.php');?>
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
$colname_rsCotizacion = "75";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsCotizacion = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizacion = sprintf("SELECT *, sc.nombre AS titulocotizacion, u.nombrereal AS nombreusuario, u.puesto AS puestousuario, u.email AS emailusuario, u.firma AS firmausuario,cc.nombre AS nombrecliente,cl.nombre AS nombrecliente FROM cotizacion c LEFT JOIN subcotizacion sc ON (c.idcotizacion = sc.idcotizacion) LEFT JOIN cliente cl ON (c.idcliente = cl.idcliente) LEFT JOIN contactoclientes cc ON (sc.contacto = cc.idcontacto) LEFT JOIN usuarios u ON (sc.usercreo = u.id) WHERE sc.idsubcotizacion = %s", GetSQLValueString($colname_rsCotizacion, "int"));
$rsCotizacion = mysql_query($query_rsCotizacion, $tecnocomm) or die(mysql_error());
$row_rsCotizacion = mysql_fetch_assoc($rsCotizacion);
$totalRows_rsCotizacion = mysql_num_rows($rsCotizacion);

$colname_rsDetalle = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsDetalle = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT * FROM subcotizacionarticulo sb, articulo a WHERE idsubcotizacion = %s AND a.idarticulo = sb.idarticulo", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);

$colname_rsCliente = "-1";
if (isset($_GET['idsubcotizacion'])) {
  $colname_rsCliente = $_GET['idsubcotizacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCliente = sprintf("SELECT cl.nombre,cl.direccion,cl.ciudad FROM subcotizacion sb,cotizacion c,cliente cl WHERE sb.idcotizacion = c.idcotizacion AND cl.idcliente = c.idcliente AND sb.idsubcotizacion = %s", GetSQLValueString($colname_rsCliente, "int"));
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

$signo = array("$ ","$ ");
$counter = 0;
$par = 0;
do{
$counter++;
$par++;
 $long = strlen($row_rsDetalle['descri']) / 40;

if($long < 1){
$partida[] = $par;
$codigo[] =$row_rsDetalle['codigo'];
$marca[] =$row_rsDetalle['marca'];
$descripcion[] = $row_rsDetalle['descri'];
$cantidad[] = $row_rsDetalle['cantidad'];
$medida[] =$row_rsDetalle['medida'];
$precio[] = $signo[$row_rsCotizacion['moneda']].($row_rsDetalle['precio_cotizacion'] *  $row_rsDetalle['utilidad']);
$importe2[] = ($row_rsDetalle['cantidad'] *  $row_rsDetalle['precio_cotizacion'])*  $row_rsDetalle['utilidad'];
$importe[] = $signo[$row_rsCotizacion['moneda']].(($row_rsDetalle['cantidad'] *  $row_rsDetalle['precio_cotizacion'])*  $row_rsDetalle['utilidad']);
 }else{
	unset($line);
 	$comienzo = 0;
	$line = substr  ( $row_rsDetalle['descri'], $comienzo ,40);
	$comienzo = $comienzo + 40 ;

	$partida[] = $par;
	$codigo[] =$row_rsDetalle['codigo'];
	$marca[] =$row_rsDetalle['marca'];
	$descripcion[] = $line;
	$cantidad[] = $row_rsDetalle['cantidad'];
	$medida[] =$row_rsDetalle['medida'];
	$precio[] = $signo[$row_rsCotizacion['moneda']].($row_rsDetalle['precio_cotizacion'] *  $row_rsDetalle['utilidad']);
	$importe[] = $signo[$row_rsCotizacion['moneda']].(($row_rsDetalle['cantidad'] *  $row_rsDetalle['precio_cotizacion'])*  $row_rsDetalle['utilidad']);
	$importe2[] = ($row_rsDetalle['cantidad'] *  $row_rsDetalle['precio_cotizacion'])*  $row_rsDetalle['utilidad'];
	for($i=1;$i<$long;$i++){
		$counter++;
		$line = substr  ( $row_rsDetalle['descri'], $comienzo , 40);
		$comienzo = $comienzo + 40;
		$partida[]=" ";
		$codigo[] = " ";
		$marca[] =" ";
		$descripcion[] = $line;
		$cantidad[] = " ";
		$medida[] =" ";
		$precio[] =" ";
		$importe[] = " ";
		$importe2[] = " ";
	}
}

}while($row_rsDetalle = mysql_fetch_assoc($rsDetalle));



?>

<?php 
$forma=array(0=>"CONTADO",1=>"50 % ANTICIPO y 50% CONTRAENTREGA",2=>"50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE");
$moneda = array(0=>"MONEDA NACIONAL",1=>"DOLARES AMERICANOS");



function formatDate($date){
$meses = array (1=>"Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

$d = split("-",$date);
$date = $d[2]."-".$meses[$d[1]]."-".$d[0];

return $date;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Imprimir Cotizacion</title>
<style>

#back{
	position:absolute;
	left:0px;
	top:0px;
	z-index:0;
	padding-left:29px;
	padding-top:29px;
}

#front{

	position:absolute;
	left:0px;
	top:0px;
	z-index:1;

}


#back2{
	position:absolute;
	left:0px;
	top:1200px;
	z-index:2;
	padding-left:29px;
	padding-top:29px;

}

#front2{

	position:absolute;
	left:0px;
	top:1200px;
	z-index:3;

}


.style1 {font-size: 20px}
</style>
<script language="javascript">
	function imprimir(){
		if(confirm("Desea Mandar Imprimir")){
			print();
		}
	}
</script>
</head>
<body style="font-size:10px; font-family:Arial, Helvetica, sans-serif; "  onload="imprimir()">
<?php for($i=0;$i<$counter;$i++) echo "<br><br><br><br>"?>
<div id="back"><img src="images/TECNOCOMM1.jpg" /></div>
<div id="back2"><img src="images/TECNOCOMM3.jpg" /></div>
<div id="front"><table width="800" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td width="48" height="126"></td>
    <td width="3"></td>
    <td width="47"></td>
    <td width="53"></td>
    <td width="144"></td>
    <td width="64"></td>
    <td width="10"></td>
    <td width="56"></td>
    <td width="41"></td>
    <td width="43"></td>
    <td width="27"></td>
    <td width="22"></td>
    <td width="29"></td>
    <td width="19">&nbsp;</td>
    <td width="18"></td>
    <td width="18"></td>
    <td width="32"></td>
    <td width="28"></td>
    <td width="41"></td>
    <td width="6"></td>
    <td width="1"></td>
    <td width="50"></td>
  </tr>
  <tr>
    <td height="12"></td>
    <td></td>
    <td></td>
    <td colspan="8" valign="top"><?php echo $row_rsCliente['nombre']; ?></td>
    <td></td>
    <td></td>
    <td colspan="3" rowspan="2" valign="middle">Cotizacion:</td>
    <td colspan="4" rowspan="2" valign="middle"><?php  $val = $row_rsCotizacion['identificador2']; echo $val; ?></td>
    <td></td>
    <td></td>
  </tr>
  
  
  <tr>
    <td height="8"></td>
    <td></td>
    <td></td>
    <td colspan="8" rowspan="2" valign="top" ><?php echo $row_rsCliente['direccion']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="7"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  <tr>
    <td height="6"></td>
    <td></td>
    <td></td>
    <td colspan="3" rowspan="2" valign="top"><?php echo $row_rsContacto['nombre']; ?></td>
    <td colspan="6" rowspan="2" valign="top"><?php echo $row_rsCliente['ciudad']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="9"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" rowspan="2" valign="middle"><?php echo formatDate($row_rsCotizacion['fecha']); ?></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="13"></td>
    <td></td>
    <td></td>
    <td colspan="2" rowspan="2" valign="top"><?php echo $row_rsContacto['telefono']; ?></td>
    <td></td>
    <td colspan="4" rowspan="2" valign="top"><?php echo $row_rsContacto['correo']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="2"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="41">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="525">&nbsp;</td>
    <td colspan="19" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td height="15" colspan="8" align="center" valign="middle"><?php echo $row_rsCotizacion['titulocotizacion']; ?></td>
      </tr>
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      <?php 
  $subtotal = 0;
  for($row=1;$row<=32;$row++){ 
  $subtotal = $subtotal + $importe2[$row-1];
  ?>
      
      <tr>
        <td width="37" height="15px" align="center" valign="bottom" style="padding-left:2px;"><?php echo $partida[$row-1];?></td>
      <td width="66" align="center" valign="bottom"  ><?php echo $codigo[$row-1];?></td>
      <td width="70" align="center" valign="bottom"  ><?php echo $marca[$row-1];?></td>
      <td width="298" valign="bottom"  style="padding-left:5px;"><?php echo $descripcion[$row-1];?></td>
      <td width="46" align="right" valign="bottom"  style="padding-left:2px;"><?php echo $cantidad[$row-1];?></td>
      <td width="42" align="center" valign="bottom"  style="padding-left:2px;"><?php echo $medida[$row-1];?></td>
      <td width="64" align="right" valign="bottom"  style="padding-left:2px;"><?php echo $precio[$row-1];?></td>
      <td width="78" align="right" valign="bottom"  style="padding-left:2px;"><?php echo $importe[$row-1];?></td>
      </tr>
      <?php }?>
      
    </table></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="18"></td>
    <td colspan="13" valign="middle">CANTIDAD CON LETRA: <? echo num2letras(money_format('%i',$subtotal*1.15),false,true,$row_rsCotizacion['moneda']); ?>  </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>

  
  <tr>
    <td height="8"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" align="right" valign="bottom"><?php  if($row_rsCotizacion['moneda']==0){echo "$ ";}if($row_rsCotizacion['moneda']==1){echo "USD ";} echo $subtotal;?></td>
    <td></td>
  </tr>
  <tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  

  
  
  
  <tr>
    <td height="11"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" align="right" valign="bottom"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td height="22"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  <tr>
    <td height="3"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="9" rowspan="6" align="center" valign="bottom"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="25"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="2"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="25"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="28"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="9"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" rowspan="2" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  <tr>
    <td height="16"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="11" rowspan="3" align="center" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="23"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="1"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="32"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="41"></td>
    <td>&nbsp;</td>
    <td colspan="17" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="51"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
</div>
<div id="front2">
<table width="800" border="0" cellpadding="0" cellspacing="0" >
  <!--DWLayoutTable-->
  <tr>
    <td width="48" height="126"></td>
    <td width="3"></td>
    <td width="47"></td>
    <td width="53"></td>
    <td width="144"></td>
    <td width="64"></td>
    <td width="10"></td>
    <td width="56"></td>
    <td width="41"></td>
    <td width="43"></td>
    <td width="27"></td>
    <td width="22"></td>
    <td width="29"></td>
    <td width="19">&nbsp;</td>
    <td width="18">&nbsp;</td>
    <td width="18"></td>
    <td width="25"></td>
    <td width="7"></td>
    <td width="28"></td>
    <td width="41"></td>
    <td width="6"></td>
    <td width="1"></td>
    <td width="50"></td>
  </tr>
  <tr>
    <td height="12"></td>
    <td></td>
    <td></td>
    <td colspan="8" valign="top"><?php echo $row_rsCliente['nombre']; ?></td>
    <td></td>
    <td></td>
    <td colspan="3" rowspan="2" valign="middle">Cotizacion:</td>
    <td colspan="5" rowspan="2" valign="middle"><?php  $val = $row_rsCotizacion['identificador2']; echo $val; ?></td>
    <td></td>
    <td></td>
  </tr>
  
  
  <tr>
    <td height="8"></td>
    <td></td>
    <td></td>
    <td colspan="8" rowspan="2" valign="top" ><?php echo $row_rsCliente['direccion']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="7"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  <tr>
    <td height="6"></td>
    <td></td>
    <td></td>
    <td colspan="3" rowspan="2" valign="top"><?php echo $row_rsContacto['nombre']; ?></td>
    <td colspan="6" rowspan="2" valign="top"><?php echo $row_rsCliente['ciudad']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="9"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="5" rowspan="2" valign="middle"><?php echo formatDate($row_rsCotizacion['fecha']); ?></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="13"></td>
    <td></td>
    <td></td>
    <td colspan="2" rowspan="2" valign="top"><?php echo $row_rsContacto['telefono']; ?></td>
    <td></td>
    <td colspan="4" rowspan="2" valign="top"><?php echo $row_rsContacto['correo']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="2"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="11"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="20"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" align="right" valign="middle"><?php  if($row_rsCotizacion['moneda']==0){echo "$ ";}if($row_rsCotizacion['moneda']==1){echo "USD ";} echo $subtotal;?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="37"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="483"></td>
    <td colspan="20" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td height="15" colspan="8" align="center" valign="middle"><?php echo $row_rsCotizacion['titulocotizacion']; ?></td>
          </tr>
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      <?php 
  for($j=$row;$j<=$counter;$j++){ 
  $subtotal = $subtotal + $importe2[$row-1];
  ?>
      
      <tr>
        <td width="37" height="15" align="center" valign="bottom" style="padding-left:2px;"><?php echo $partida[$j-1];?></td>
            <td width="66" align="center" valign="bottom"  ><?php echo $codigo[$j-1];?></td>
            <td width="70" align="center" valign="bottom"  ><?php echo $marca[$j-1];?></td>
            <td width="298" valign="bottom"  style="padding-left:5px;"><?php echo $descripcion[$j-1];?></td>
            <td width="46" align="right" valign="bottom"  style="padding-left:2px;"><?php echo $cantidad[$j-1];?></td>
            <td width="42" align="center" valign="bottom"  style="padding-left:2px;"><?php echo $medida[$j-1];?></td>
            <td width="64" align="right" valign="bottom"  style="padding-left:2px;"><?php echo $precio[$j-1];?></td>
            <td width="78" align="right" valign="bottom"  style="padding-left:2px;"><?php echo $importe[$j-1];?></td>
          </tr>
      
      <?php }?>
      
    </table></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="9"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="18"></td>
    <td colspan="13" valign="middle">CANTIDAD CON LETRA: <? echo num2letras(money_format('%i',$subtotal*1.15),false,true,$row_rsCotizacion['moneda']); ?>  </td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="6"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  

  <tr>
    <td height="18"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" align="right" valign="bottom"><?php  if($row_rsCotizacion['moneda']==0){echo "$ ";}if($row_rsCotizacion['moneda']==1){echo "USD ";} echo $subtotal;?></td>
    <td></td>
  </tr>
  <tr>
    <td height="18"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" align="right" valign="bottom"><?php if($row_rsCotizacion['moneda']==0){echo "$";}if($row_rsCotizacion['moneda']==1){echo "USD ";}$iva=0; $iva = ($subtotal * .15); echo round($iva,2);?></td>
    <td></td>
  </tr>
  <tr>
    <td height="18"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" align="right" valign="bottom"><?php if($row_rsCotizacion['moneda']==0){echo "$";}if($row_rsCotizacion['moneda']==1){echo "USD ";}$total=($subtotal *1.15); echo round($total,2);?></td>
    <td></td>
  </tr>
  <tr>
    <td height="22"></td>
    <td></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  <tr>
    <td height="3"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="10" rowspan="6" align="center" valign="bottom"><img src="firmas/<?php echo $row_rsCotizacion['username'].".jpg"; ?>" style="max-width:100px;min-height:50px;"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="25"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" valign="middle"><?php echo $forma[$row_rsCotizacion['formapago']]; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="2"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="25"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" valign="middle"><?php echo $moneda[$row_rsCotizacion['moneda']]; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="28"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" valign="middle"><?php echo $row_rsCotizacion['vigencia']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="9"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" rowspan="2" valign="middle"><?php echo $row_rsCotizacion['tipoentrega']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  <tr>
    <td height="16"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="12" rowspan="3" align="center" valign="top"><span style="font-size:12px;font-style:italic">  <?php echo $row_rsFirma['nombrereal']; ?></span> <br />      <strong> <?php echo $row_rsFirma['puesto']; ?></strong> <br />
      <?php echo $row_rsFirma['email']; ?> </td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="23"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" valign="middle"><?php echo $row_rsCotizacion['garantia']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="1"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  <tr>
    <td height="32"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="41"></td>
    <td>&nbsp;</td>
    <td colspan="18" valign="top"><?php echo $row_rsCotizacion['notas']; ?></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="51"></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
</div>
</body>
</html>
<?php
mysql_free_result($rsCotizacion);

mysql_free_result($rsDetalle);

mysql_free_result($rsCliente);

mysql_free_result($rsContacto);

mysql_free_result($rsFirma);
?>