<?php require_once('Connections/tecnocomm.php'); ?>
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

$fecha1_RsDetalle = $hoy['ano']."-".$hoy['mes']."-".$hoy['dia'];
$fecha2_RsDetalle = $hoy1['ano']."-".$hoy1['mes']."-".$hoy1['dia'];







mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsDeben = sprintf("SELECT c.nombre,c.abreviacion,(select sum(punitario*cantidad) FROM detallefactura WHERE detallefactura.idfactura=f.idfactura) as mone from cliente c,factura f where c.idcliente=f.idcliente and f.estado=0 and (select sum(punitario*cantidad) from detallefactura where detallefactura.idfactura=f.idfactura) >0  ORDER BY mone desc", GetSQLValueString($fecha1_RsDetalle, "date"),GetSQLValueString($fecha2_RsDetalle, "date"));

$RsDeben = mysql_query($query_RsDeben, $tecnocomm) or die(mysql_error());
$row_RsDeben = mysql_fetch_assoc($RsDeben);
$totalRows_RsDeben=mysql_num_rows($RsDeben);


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

?>
<form name="consulta" method="get">
<table width="800" border="0" align="center"><MM_REPEATEDREGION SOURCE="@@rs@@"></tr>
</MM_REPEATEDREGION>
</table>
<input type="hidden" name="mod" value="porcobrar"/>
<table width="800" border="0" align="center">
  <tr>
    <td colspan="4" align="center" class="titulos">CUENTAS POR COBRAR</td>
  </tr>
  <tr>
    <td width="110">&nbsp;</td>
    <td width="281">&nbsp;</td>
    <td width="167">&nbsp;</td>
    <td width="224">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" align="center"><label>DIA:
        <select name="dia1" class="form" id="dia1">
          <?php for($a=1;$a<=31;$a++) { ?>
          <option value="<?php echo $a; ?>"<?php if($a == $hoy['dia']) { echo " selected=\"selected\" "; } ?>><?php echo $a; ?></option>
          <?php } ?>
        </select>
    </label>
      <label>MES:
      <select name="mes1" class="form" id="mes1">
        <?php for($a=1;$a<=12;$a++) { ?>
        <option value="<?php echo $a; ?>"<?php if($a == $hoy['mes']) { echo " selected=\"selected\" "; } ?>><?php echo $mes[$a]; ?></option>
        <?php } ?>
      </select>
      </label>
      <label>A&Ntilde;O:
      <input name="ano1" type="text" class="form" id="ano1" value="<?php echo date("Y")?>" size="8" />
      </label>
      <label>DIA:
      <select name="dia2" class="form" id="dia2">
        <?php for($a=1;$a<=31;$a++) { ?>
        <option value="<?php echo $a; ?>"<?php if($a == $hoy1['dia']) { echo " selected=\"selected\" "; } ?>><?php echo $a; ?></option>
        <?php } ?>
      </select>
      </label>
      <label>MES:
      <select name="mes2" class="form" id="mes2">
        <?php for($a=1;$a<=12;$a++) { ?>
        <option value="<?php echo $a; ?>"<?php if($a == $hoy1['mes']) { echo " selected=\"selected\" "; } ?>><?php echo $mes[$a]; ?></option>
        <?php } ?>
      </select>
      </label>
      <label>A&Ntilde;O
      <input name="ano2" type="text" class="form" id="ano2"  value="<?php echo date("Y")?>" size="8"/> 
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="button" id="button" value="&gt;" />
    </label></td>
  </tr>
  
  <tr >
    <td colspan="4">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php if ($totalRows_RsDeben > 0) { // Show if recordset not empty ?>
    <tr>
      <td colspan="2" align="center">NOMBRE </td>
      <td align="center">CANTIDAD</td>
      <td>&nbsp;</td>
    </tr>
    <?php } // Show if recordset not empty ?>
  <?php do { ?>
    <tr>
      <td colspan="2" align="center"><?php echo $row_RsDeben['nombre']; ?>(<?php echo $row_RsDeben['abreviacion']; ?>)</td>
      <td align="center"><?php if ($totalRows_RsDeben > 0) { ?>$<?php } // Show if recordset not empty ?><?php echo money_format('%i',$row_RsDeben['mone']*1.15); ?></td>
      <td>&nbsp;</td>
    </tr>
    <?php } while ($row_RsDeben = mysql_fetch_assoc($RsDeben)); ?>
  <?php if ($totalRows_RsDeben == 0) { // Show if recordset empty ?>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center">NO EXISTEN DATOS </td>
      <td>&nbsp;</td>
    </tr>
    <?php } // Show if recordset empty ?>
  
</table>

</form>
<?php


mysql_free_result($RsDeben);


?>
