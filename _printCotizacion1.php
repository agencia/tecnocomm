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

$counter = 0;
do{
$counter++;
 $long = strlen($row_rsDetalle['nombre']) / 45;

if($long < 1){
$codigo[] =$row_rsDetalle['codigo'];
$marca[] =$row_rsDetalle['marca'];
$descripcion[] = $row_rsDetalle['nombre'];
$cantidad[] = $row_rsDetalle['cantidad'];
$medida[] =$row_rsDetalle['medida'];
$precio[] = $row_rsDetalle['precio_cotizacion'];
$importe[] = $row_rsDetalle['cantidad'] *  $row_rsDetalle['precio_cotizacion'];
 }else{
	unset($line);
 	$comienzo = 0;
	$line = substr  ( $row_rsDetalle['nombre'], $comienzo ,45);
	$comienzo = $comienzo + 45 +1;

	$codigo[] =$row_rsDetalle['codigo'];
	$marca[] =$row_rsDetalle['marca'];
	$descripcion[] = $line;
	$cantidad[] = $row_rsDetalle['cantidad'];
	$medida[] =$row_rsDetalle['medida'];
	$precio[] = $row_rsDetalle['precio_cotizacion'];
	$importe[] = $row_rsDetalle['cantidad'] *  $row_rsDetalle['precio_cotizacion'];
	
	for($i=0;$i<$long;$i++){
		$line = substr  ( $row_rsDetalle['nombre'], $comienzo , 45);
		$comienzo = $comienzo + 45 +1;
		$codigo[] = " ";
		$marca[] =" ";
		$descripcion[] = $line;
		$cantidad[] = " ";
		$medida[] =" ";
		$precio[] =" ";
		$importe[] = " ";
	}
}

}while($row_rsDetalle = mysql_fetch_assoc($rsDetalle));

?>

<?php 

$moneda = array(0=>"Pesos",1=>"Dolares");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<a href="createPDF.php?url=printCotizacion.php?idsubcotizacion=<?php echo $_GET['idsubcotizacion'];?>" target="_blank">Obtener PDF</a> | Obtener Excel

<body style="font-size:10px; font-family:Arial, Helvetica, sans-serif" >
<?php if($counter <= 35) { ?>
<table width="800" border="0" cellpadding="0" cellspacing="0" background="images/TECNOCOMM.jpg" >
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
    <td colspan="8" valign="top"><?php echo $row_rsCotizacion['nombrecliente']; ?></td>
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
    <td colspan="8" rowspan="2" valign="top" ><?php echo $row_rsCotizacion['direccion']; ?></td>
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
    <td colspan="2" rowspan="2" valign="top"><?php echo $row_rsCotizacion['nombrecliente']; ?></td>
    <td></td>
    <td colspan="6" rowspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
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
    <td></td>
    <td colspan="4" rowspan="2" valign="middle"><?php echo $row_rsCotizacion['fecha']; ?></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="13"></td>
    <td></td>
    <td></td>
    <td colspan="2" rowspan="2" valign="top"><?php echo $row_rsCotizacion['telefono']; ?></td>
    <td></td>
    <td colspan="4" rowspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
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
        <td height="15" colspan="10" align="center" valign="middle"><?php echo $row_rsCotizacion['titulocotizacion']; ?></td>
      </tr>
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      <?php 
  $subtotal = 0;
  for($row=1;$row<=$counter;$row++){ 
  $subtotal = $subtotal + $importe[$row-1];
  ?>
      
      <tr>
        <td width="37" height="15" valign="bottom" style="padding-left:4px;"><?php echo $row;?></td>
      <td width="66" valign="bottom"  style="padding-left:5px;"><?php echo $codigo[$row-1];?></td>
      <td width="70" valign="bottom"  style="padding-left:5px;"><?php echo $marca[$row-1];?></td>
      <td width="298" valign="bottom"  style="padding-left:5px;"><?php echo $descripcion[$row-1];?></td>
      <td width="9"></td>
      <td width="37" valign="bottom"  style="padding-left:5px;"><?php echo $cantidad[$row-1];?></td>
      <td width="9"></td>
      <td width="33" valign="bottom"  style="padding-left:5px;"><?php echo $medida[$row-1];?></td>
      <td width="64" align="right" valign="bottom"  style="padding-left:5px;"><?php echo $precio[$row-1];?></td>
      <td width="78" align="right" valign="bottom"  style="padding-left:5px;"><?php echo $importe[$row-1];?></td>
      </tr>
      <?php }?>
      
    </table></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="18"></td>
    <td colspan="13" valign="middle">CANTIDAD CON LETRA: <? echo num2letras(round(($subtotal *1.15)),false,false); ?>  <?php echo $moneda[$row_rsCotizacion['moneda']]; ?>   </td>
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
    <td colspan="4" align="right" valign="bottom"><?php echo $subtotal;?></td>
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
    <td colspan="4" align="right" valign="bottom"><?php $iva=0; $iva = ($subtotal * .15); echo round($iva,2);?></td>
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
    <td colspan="4" align="right" valign="bottom"><?php $total=($subtotal *1.15); echo round($total,2);?></td>
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
    <td colspan="9" rowspan="6" align="center" valign="bottom"><img src="firmas/<?php echo $row_rsCotizacion['firmausuario']; ?>" style="max-width:230px;min-height:90px;"></td>
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
    <td colspan="3" valign="middle"><?php echo $row_rsCotizacion['formapago']; ?></td>
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
    <td colspan="3" valign="middle"><?php echo $row_rsCotizacion['moneda']; ?></td>
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
    <td colspan="11" rowspan="3" align="center" valign="top"><span style="font-size:12px;font-style:italic">  <?php echo $row_rsCotizacion['nombreusuario']; ?></span> <br />      <strong> <?php echo $row_rsCotizacion['puestousuario']; ?></strong> <br />
      <?php echo $row_rsCotizacion['emailusuario']; ?> </td>
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
  </tr>
  <tr>
    <td height="41"></td>
    <td>&nbsp;</td>
    <td colspan="17" valign="top"><?php echo $row_rsCotizacion['notas']; ?></td>
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
<p>
  <?php } else{?>
</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<table width="800" border="0" cellpadding="0" cellspacing="0" background="images/TECNOCOM.jpg" >
  <!--DWLayoutTable-->
  <tr>
    <td width="47" height="126"></td>
    <td width="11"></td>
    <td width="27"></td>
    <td width="13"></td>
    <td width="53"></td>
    <td width="70"></td>
    <td width="74"></td>
    <td width="64"></td>
    <td width="10"></td>
    <td width="56"></td>
    <td width="41"></td>
    <td width="43"></td>
    <td width="9"></td>
    <td width="18"></td>
    <td width="22"></td>
    <td width="9"></td>
    <td width="20"></td>
    <td width="19">&nbsp;</td>
    <td width="18">&nbsp;</td>
    <td width="48">&nbsp;</td>
    <td width="30">&nbsp;</td>
    <td width="41">&nbsp;</td>
    <td width="6">&nbsp;</td>
    <td width="51"></td>
  </tr>
  <tr>
    <td height="12"></td>
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
    <td colspan="6" rowspan="2" valign="middle">Cotizacion 1:</td>
    <td></td>
  </tr>
  
  <tr>
    <td height="8"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="10" rowspan="2" valign="top" style="font-size:12px"><?php echo $row_rsCotizacion['direccion']; ?></td>
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
    <td></td>
    <td colspan="3" rowspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td colspan="7" rowspan="2" valign="top"><?php echo $row_rsCotizacion['ciudad']; ?></td>
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
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" rowspan="2" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="13"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3" rowspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td colspan="4" rowspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
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
    <td height="68"></td>
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
    <td></td>
  </tr>
  <tr>
    <td height="15"></td>
    <td colspan="22" align="center" valign="middle">asdsa</td>
    <td></td>
  </tr>
  <tr>
    <td height="15"></td>
    <td colspan="2" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td colspan="2" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td colspan="7" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td colspan="3" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td colspan="2" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td colspan="2" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td colspan="3" valign="middle"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
  </tr>
  
  
  
  
  
  <tr>
    <td height="493"></td>
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
    <td>&nbsp;</td>
    <td></td>
    <td></td>
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
    <td colspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
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
    <td colspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
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
    <td colspan="2" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="26"></td>
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
    <td>&nbsp;</td>
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
    <td colspan="10" rowspan="5" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="25"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" valign="top"><?php echo $row_rsCotizacion['formapago']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="25"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" valign="top"><?php echo $row_rsCotizacion['moneda']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="28"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" valign="top"><?php echo $row_rsCotizacion['vigencia']; ?></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="8"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" rowspan="3" valign="top"><?php echo $row_rsCotizacion['tipoentrega']; ?></td>
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
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="15"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="12" rowspan="3" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  <tr>
    <td height="23"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" valign="top"><?php echo $row_rsCotizacion['garantia']; ?></td>
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
  </tr>
  <tr>
    <td height="28"></td>
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
    <td></td>
  </tr>
  <tr>
    <td height="15"></td>
    <td></td>
    <td colspan="20" valign="top"><?php echo $row_rsCotizacion['notas']; ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="15"></td>
    <td></td>
    <td colspan="20" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="14"></td>
    <td></td>
    <td colspan="20" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="47"></td>
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
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
<?php } ?>
</body>
</html>
<?php
mysql_free_result($rsCotizacion);

mysql_free_result($rsDetalle);
?>
