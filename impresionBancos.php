<?php require_once('Connections/tecnocomm.php'); ?>
<?php
require_once('utils.php');
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
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsDepo = sprintf("SELECT * FROM banco WHERE  fecha between %s and %s ORDER BY fecha ASC",GetSQLValueString($_GET['fecha1'], "date"),GetSQLValueString($_GET['fecha2'], "date"));
$RsDepo = mysql_query($query_RsDepo, $tecnocomm) or die(mysql_error());
$row_RsDepo = mysql_fetch_assoc($RsDepo);
$totalRows_RsDepo = mysql_num_rows($RsDepo);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti = sprintf("select sum(importe) as retiros from banco where tipo=1 and fecha < %s  ORDER BY fecha ASC",GetSQLValueString($_GET['fecha1'], "date"));
$RSReti = mysql_query($query_RSReti, $tecnocomm) or die(mysql_error());
$row_RSReti = mysql_fetch_assoc($RSReti);
$totalRows_RSReti = mysql_num_rows($RSReti);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti1 = sprintf("select sum(importe) as depositos from banco where tipo=0 and fecha < %s  ORDER BY fecha ASC",GetSQLValueString($_GET['fecha1'], "date"));
$RSReti1 = mysql_query($query_RSReti1, $tecnocomm) or die(mysql_error());
$row_RSReti1 = mysql_fetch_assoc($RSReti1);
$totalRows_RSReti1 = mysql_num_rows($RSReti1);

$saldo=0;
$saldo=$row_RSReti1['depositos']-$row_RSReti['retiros'];

$tipopago= array("CH","TR","EF","OT","TA");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<script language="javascript" src="js/funciones.js"></script>
<style type="text/css">
<!--
.Estilo1 {
	color: #000000;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<table width="100%" align="center" bordercolor="#000000" cellspacing="0" border="1">
  <tr>
    <td colspan="6" align="LEFT">ESTADO DE CENTA TECNOCOMM<br>
    PERIDO DEL <?php echo formatDate($_GET['fecha1']); ?> AL<?php echo formatDate($_GET['fecha2']); ?>  <br /> IMPRESO:<?php echo formatDate(date("Y-n-j")); ?>&nbsp;&nbsp;<?php echo date("G:i A");?></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center" class="Estilo1">&nbsp;</td>
    <td align="center" class="Estilo1">&nbsp;</td>
    <td align="center" class="Estilo1">&nbsp;</td>
    <td align="center" class="Estilo1">&nbsp;</td>
  </tr>
  <tr>
    <td width="10%" align="center"><span class="Estilo1">FECHA</span></td>
    <td width="20%" align="center"><span class="Estilo1"> REFERENCIA</span></td>
    <td width="40%" align="center" class="Estilo1">CONCEPTO</td>
    <td width="10%" align="center" class="Estilo1">RETIRO</td>
    <td width="10%" align="center" class="Estilo1">DEPOSITO</td>
    <td width="10%" align="center" class="Estilo1">SALDO</td>
  </tr>
  <tr class="titleTabla">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>SALDO ANTERIOR</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right"><?php echo format_money($saldo);?></td>
  </tr>
  <?php $tod=0;$tor=0; $porp=0; do{ ?>
  <tr>
    <td align="left" valign="top"><?php if($row_RsDepo['tipopago']==0 && $row_RsDepo['fechacobro']>=date('Y-m-d')){echo "*";} echo formatDate($row_RsDepo['fecha']); ?></td>
    <td align="left" valign="top"><?php echo $tipopago[$row_RsDepo['tipopago']].": ".$row_RsDepo['referencia']; ?></td>
    <td align="left" valign="top"><?php echo $row_RsDepo['concepto']; ?></td>
    <td align="right" valign="top">
        <?php 
	  if($row_RsDepo['tipo']==1){
		  if(date('Y-m-d')>=$row_RsDepo['fechacobro']){
	  			$saldo-=$row_RsDepo['importe'];
				$tor+=$row_RsDepo['importe'];
		  }
		  else{
			  $porp+=$row_RsDepo['importe'];
			  }
	  	 echo format_money($row_RsDepo['importe']);
	  }
	  ?>
    </div></td>
    <td align="right" valign="top">
      <?php 
	  if($row_RsDepo['tipo']==0){
		  $saldo+=$row_RsDepo['importe'];
		  $tod+=$row_RsDepo['importe'];
	   echo format_money($row_RsDepo['importe']);
	  }
	  
	    
	  
	   ?>    </td>
    <td align="right" valign="top"><?php echo format_money($saldo)?></td>
  </tr>
  <?php }while($row_RsDepo = mysql_fetch_assoc($RsDepo));?>
  
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right"><span class="Estilo1">TOTAL R.:</span></td>
    <td align="right"><span class="Estilo1">TOTAL D.:</span></td>
    <td align="right"><span class="Estilo1">SALDO: </span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td align="right"><span class="Estilo1"><?php echo format_money($tor); ?></span></td>
    <td align="right"><span class="Estilo1"><?php echo format_money($tod); ?></span></td>
    <td align="right"><span class="Estilo1">$<?php echo format_money($saldo); ?></span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td colspan="2" align="right">&nbsp;</td>
    <td align="right">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td colspan="3" align="right"><span class="Estilo1">SALDO REAL:$<?php echo format_money($saldo-$porp); ?></span></td>
  </tr>
</table>
</body>
</html>