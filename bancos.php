<?php require_once('Connections/tecnocomm.php'); ?>
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

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSaldo = "select (select sum(importe) from banco where tipo=0 )-(select sum(importe) from banco where tipo=1 ) as saldo";
$RsSaldo = mysql_query($query_RsSaldo, $tecnocomm) or die(mysql_error());
$row_RsSaldo = mysql_fetch_assoc($RsSaldo);
$totalRows_RsSaldo = mysql_num_rows($RsSaldo);

$fecha1_RsDetalle = "-1";
if (isset($_GET['ano1'])) {
  $fecha1_RsDetalle = $_GET['ano1']."-".$_GET['mes1']."-".$_GET['dia1'];
}
$fecha2_RsDetalle = "-1";
if (isset($_GET['ano2'])) {
  $fecha2_RsDetalle = $_GET['ano2']."-".$_GET['mes2']."-".$_GET['dia2'];
}
if (isset($_GET['ano1'])) {
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsDepo = sprintf("SELECT * FROM banco WHERE  fecha between %s and %s ORDER BY fecha ASC",GetSQLValueString($fecha1_RsDetalle, "date"),GetSQLValueString($fecha2_RsDetalle, "date"));
$RsDepo = mysql_query($query_RsDepo, $tecnocomm) or die(mysql_error());
$row_RsDepo = mysql_fetch_assoc($RsDepo);
$totalRows_RsDepo = mysql_num_rows($RsDepo);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti = sprintf("select sum(importe) as retiros from banco where tipo=1 and fecha < %s  ORDER BY fecha ASC",GetSQLValueString($fecha1_RsDetalle, "date"));
$RSReti = mysql_query($query_RSReti, $tecnocomm) or die(mysql_error());
$row_RSReti = mysql_fetch_assoc($RSReti);
$totalRows_RSReti = mysql_num_rows($RSReti);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti1 = sprintf("select sum(importe) as depositos from banco where tipo=0 and fecha < %s  ORDER BY fecha ASC",GetSQLValueString($fecha1_RsDetalle, "date"));
$RSReti1 = mysql_query($query_RSReti1, $tecnocomm) or die(mysql_error());
$row_RSReti1 = mysql_fetch_assoc($RSReti1);
$totalRows_RSReti1 = mysql_num_rows($RSReti1);

$saldo=0;
$saldo=$row_RSReti1['depositos']-$row_RSReti['retiros'];
}
else{
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsDepo = sprintf("SELECT * FROM banco ORDER BY fecha ");
$RsDepo = mysql_query($query_RsDepo, $tecnocomm) or die(mysql_error());
$row_RsDepo = mysql_fetch_assoc($RsDepo);
$totalRows_RsDepo = mysql_num_rows($RsDepo);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti = sprintf("select sum(importe) as retiro from banco where tipo=1 ");
$RSReti = mysql_query($query_RSReti, $tecnocomm) or die(mysql_error());
$row_RSReti = mysql_fetch_assoc($RSReti);
$totalRows_RSReti = mysql_num_rows($RSReti);


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RSReti1 = sprintf("select sum(importe) as deposito from banco where tipo=0 ");
$RSReti1 = mysql_query($query_RSReti1, $tecnocomm) or die(mysql_error());
$row_RSReti1 = mysql_fetch_assoc($RSReti1);
$totalRows_RSReti1 = mysql_num_rows($RSReti1);

$saldo2=0;
$saldo2=$row_RSReti1['deposito']-$row_RSReti['retiro'];
}
?><?
if(isset($_GET['dia1']) && isset($_GET['mes1']) && isset($_GET['ano1'])&&isset($_GET['dia2']) && isset($_GET['mes2']) && isset($_GET['ano2'])){
	$hoy['dia'] = $_GET['dia1'];
	$hoy['mes'] = $_GET['mes1'];
	$hoy['ano'] = $_GET['ano1'];
	$hoy1['dia'] = $_GET['dia2'];
	$hoy1['mes'] = $_GET['mes2'];
	$hoy1['ano'] = $_GET['ano2'];
} else {
	$hoy['dia'] = date("j");
	$hoy['mes'] = date("n");
	$hoy['ano'] = date("Y");
	$hoy1['dia'] = date("j");
	$hoy1['mes'] = date("n");
	$hoy1['ano'] = date("Y");
}

$mes = array(
1 => "Enero",
2 => "Febrero",
3 => "Marzo",
4 => "Abril",
5 => "Mayo",
6 => "Junio",
7 => "Julio",
8 => "Agosto",
9 => "Septiembre",
10 => "Octubre",
11 => "Noviembre",
12 => "Diciembre"
);

$tipopago= array("CH","TR","EF","OT","TA");
?>
<style type="text/css">
<!--
.Estilo1 {color: #FF0000}
-->
</style>
<form name="consulta" method="get" action="index.php?mod=bancos">
<table width="1043" border="0" align="center">
  <tr>
    <td colspan="7" align="center" class="titulos">BANCOS</td>
  </tr>
  <tr>
    <td width="20">&nbsp;</td>
    <td width="116">&nbsp;</td>
    <td width="101">&nbsp;</td>
    <td width="296">&nbsp;</td>
    <td width="129">&nbsp;</td>
    <td width="129">&nbsp;</td>
    <td width="126">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><a href="registrarMovimiento.php?tipo=0" onclick="NewWindow(this.href,'nuevo usuario','400','250','yes');return false"><img src="images/Cobrar.png" width="24" height="24" border="0" align="middle"> REGISTRAR DEPOSITO</a> </td>
    <td colspan="4" rowspan="2"><label>Dia:
        <select name="dia1" class="form" id="dia1">
          <?php for($a=1;$a<=31;$a++) { ?>
          <option value="<?php echo $a; ?>"<?php if($a == $hoy['dia']) { echo " selected=\"selected\" "; } ?>><?php echo $a; ?></option>
          <?php } ?>
        </select>
    </label>
      <label>Mes:
      <select name="mes1" class="form" id="mes1">
        <?php for($a=1;$a<=12;$a++) { ?>
        <option value="<?php echo $a; ?>"<?php if($a == $hoy['mes']) { echo " selected=\"selected\" "; } ?>><?php echo $mes[$a]; ?></option>
        <?php } ?>
      </select>
      </label>
      <label>A&ntilde;o:
      <input name="ano1" type="text" class="form" id="ano1" value="<?php echo date("Y")?>" size="5" />
      </label><BR><BR>
      <label>Dia:
      <select name="dia2" class="form" id="dia2">
        <?php for($a=1;$a<=31;$a++) { ?>
        <option value="<?php echo $a; ?>"<?php if($a == $hoy1['dia']) { echo " selected=\"selected\" "; } ?>><?php echo $a; ?></option>
        <?php } ?>
      </select>
      </label>
      <label>Mes:
      <select name="mes2" class="form" id="mes2">
        <?php for($a=1;$a<=12;$a++) { ?>
        <option value="<?php echo $a; ?>"<?php if($a == $hoy1['mes']) { echo " selected=\"selected\" "; } ?>><?php echo $mes[$a]; ?></option>
        <?php } ?>
      </select>
      </label>
      <label>A&ntilde;o
      <input name="ano2" type="text" class="form" id="ano2"  value="<?php echo date("Y")?>" size="5"/>
      </label></td>
  </tr>
  <tr>
    <td colspan="3"><a href="registrarMovimiento.php?tipo=1" onclick="NewWindow(this.href,'nuevo usuario','400','250','yes');return false"><img src="images/Pagar.png" width="24" height="24" border="0" align="middle">REGISTRAR RETIRO </a></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="center"><input type="submit" name="button" id="button" value="Consultar" /></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><a href="impresionBancos.php?fecha1=<?php echo $hoy['ano'].'-'.$hoy['mes'].'-'.$hoy['dia'];?>&fecha2=<?php echo $hoy1['ano'].'-'.$hoy1['mes'].'-'.$hoy1['dia'];?>" onclick="NewWindow(this.href,'Impresion de reporte','400','250','yes');return false"><img src="images/Imprimir2.png" width="24" height="24" title="IMPRIMIR REPORTE DE BANCOS" /></a></td>
  </tr>
  <tr class="titleTabla">
    <td colspan="7">MOVIMIENTOS</td>
    </tr>
  
  <tr class="titleTabla">
    <td>IP</td>
    <td>REFERENCIA</td>
    <td>FECHA</td>
    <td>CONCEPTO</td>
    <td>RETIRO</td>
    <td>DEPOSITO</td>
    <td>SALDO</td>
  </tr>
  <?php $depo=0;$tod=0;$tor=0; $porp=0; $porc=0;  do { 
  ?>
    <tr>
      <td><?php echo $row_RsDepo['idip']; ?></td>
      <td><?php if($row_RsDepo['tipopago']==0 && $row_RsDepo['tipo']==0 && $row_RsDepo['aplica']==0){?><a href="banco.aplica.php?id=<?php echo $row_RsDepo['id'];?>" onclick="NewWindow(this.href,'banco aplica','400','250','yes');return false"><img src="images/Checkmark.png" width="24" height="24" border="0" title="<?php echo"FECHA DE COBRO:".$row_RsDepo['fechacobro']; ?>" /></a><? }?><label title="<?php if($row_RsDepo['tipopago']==0 && $row_RsDepo['tipo']==1 ){echo"FECHA DE COBRO:".$row_RsDepo['fechacobro']; }?>">
        <?php if($row_RsDepo['tipopago']==0 && $row_RsDepo['fechacobro']>=date('Y-m-d')){echo "*";} echo $tipopago[$row_RsDepo['tipopago']].": ".$row_RsDepo['referencia']; ?></label></td>
      <td><?php echo $row_RsDepo['fecha']; ?></td>
      <td><?php echo $row_RsDepo['concepto']; ?></td>
      <td><?php 
	  
	  if($row_RsDepo['tipo']==1){
		  if($row_RsDepo['tipopago']==0 && date('Y-m-d')>=$row_RsDepo['fechacobro']){
	  			$saldo-=$row_RsDepo['importe'];
				$tor+=$row_RsDepo['importe'];
		  }
		  elseif($row_RsDepo['fechacobro']==''){
			  $saldo-=$row_RsDepo['importe'];
				$tor+=$row_RsDepo['importe'];
			  }
		  else{
			  $porp+=$row_RsDepo['importe'];
			  }
		echo "$".format_money($row_RsDepo['importe']);
	  }
	  
	   ?></td>
      <td><?php 
	  if($row_RsDepo['tipo']==0){
		  if($row_RsDepo['tipopago']==0 && $row_RsDepo['aplica']==1){
		  		$saldo+=$row_RsDepo['importe'];
				$tod+=$row_RsDepo['importe'];
		  }
		  elseif($row_RsDepo['aplica']==-1){
			 	$saldo+=$row_RsDepo['importe'];
				$tod+=$row_RsDepo['importe'];
			  }
		else{
			 $porc+=$row_RsDepo['importe'];
			}
		echo "$".format_money($row_RsDepo['importe']);
	  }
	 
	  
	   ?></td>
      <td>$<?php echo format_money($saldo)?> <a href="editar.banco1.php?id=<?php echo $row_RsDepo['id'];?>" onclick="NewWindow(this.href,'nuevo usuario','400','250','yes');return false"><img src="images/Edit.png" width="24" height="24" border="0" title="MODIFICAR DATOS DEL MOVIMIENTO EN BANCOS" /></a></td>
    </tr>
    <?php 
	$depo+=$row_RsDepo['importe'];
	} while ($row_RsDepo = mysql_fetch_assoc($RsDepo)); ?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td colspan="3">&nbsp;</td>
  </tr>
  <?php $reti=0; do { ?>
    <?php 
	$reti+=$row_RSReti['importe'];
	} while ($row_RSReti = mysql_fetch_assoc($RSReti)); ?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><span class="Estilo1">TOTAL R.:</span></td>
    <td><span class="Estilo1">TOTAL D.:</span></td>
    <td><span class="Estilo1">SALDO:</span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td><span class="Estilo1">$<?php echo format_money($tor); ?></span></td>
    <td><span class="Estilo1">$<?php echo format_money($tod); ?></span></td>
    <td><span class="Estilo1">$<?php echo format_money($saldo); ?></span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td><span class="Estilo1">TOTAL X PAGAR:</span></td>
    <td><span class="Estilo1">TOTAL X COBRAR:</span></td>
    <td><span class="Estilo1">SALDO:</span></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="right">&nbsp;</td>
    <td><span class="Estilo1">$<?php echo format_money($porp); ?></span></td>
    <td><span class="Estilo1">$<?php echo format_money($porc); ?></span></td>
    <td><span class="Estilo1">$<?php echo format_money($saldo-$porp+$porc); ?></span></td>
    </tr>
</table>
<input name="mod" value="bancos" type="hidden" />
</form>

<?php
mysql_free_result($RsDepo);

mysql_free_result($RSReti);

mysql_free_result($RsSaldo);
?>
