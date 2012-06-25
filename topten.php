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



$maxRows_RsCompran = 10;
$pageNum_RsCompran = 0;
if (isset($_GET['pageNum_RsCompran'])) {
  $pageNum_RsCompran = $_GET['pageNum_RsCompran'];
}
$startRow_RsCompran = $pageNum_RsCompran * $maxRows_RsCompran;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsCompran = sprintf("SELECT c.nombre,c.abreviacion,(select sum(punitario*cantidad) FROM detallefactura WHERE detallefactura.idfactura=f.idfactura) as mone from cliente c,factura f where c.idcliente=f.idcliente and f.estado=1 and (select sum(punitario*cantidad) from detallefactura where detallefactura.idfactura=f.idfactura) >0 and f.fecha between %s and %s ORDER BY mone desc", GetSQLValueString($fecha1_RsDetalle, "date"),GetSQLValueString($fecha2_RsDetalle, "date"));
$query_limit_RsCompran = sprintf("%s LIMIT %d, %d", $query_RsCompran, $startRow_RsCompran, $maxRows_RsCompran);
$RsCompran = mysql_query($query_limit_RsCompran, $tecnocomm) or die(mysql_error());
$row_RsCompran = mysql_fetch_assoc($RsCompran);

if (isset($_GET['totalRows_RsCompran'])) {
  $totalRows_RsCompran = $_GET['totalRows_RsCompran'];
} else {
  $all_RsCompran = mysql_query($query_RsCompran);
  $totalRows_RsCompran = mysql_num_rows($all_RsCompran);
}
$totalPages_RsCompran = ceil($totalRows_RsCompran/$maxRows_RsCompran)-1;

$maxRows_RsDeben = 10;
$pageNum_RsDeben = 0;
if (isset($_GET['pageNum_RsDeben'])) {
  $pageNum_RsDeben = $_GET['pageNum_RsDeben'];
}
$startRow_RsDeben = $pageNum_RsDeben * $maxRows_RsDeben;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsDeben = sprintf("SELECT c.nombre,c.abreviacion,(select sum(punitario*cantidad) FROM detallefactura WHERE detallefactura.idfactura=f.idfactura) as mone from cliente c,factura f where c.idcliente=f.idcliente and f.estado=0 and (select sum(punitario*cantidad) from detallefactura where detallefactura.idfactura=f.idfactura) >0  ORDER BY mone desc", GetSQLValueString($fecha1_RsDetalle, "date"),GetSQLValueString($fecha2_RsDetalle, "date"));
$query_limit_RsDeben = sprintf("%s LIMIT %d, %d", $query_RsDeben, $startRow_RsDeben, $maxRows_RsDeben);
$RsDeben = mysql_query($query_limit_RsDeben, $tecnocomm) or die(mysql_error());
$row_RsDeben = mysql_fetch_assoc($RsDeben);

if (isset($_GET['totalRows_RsDeben'])) {
  $totalRows_RsDeben = $_GET['totalRows_RsDeben'];
} else {
  $all_RsDeben = mysql_query($query_RsDeben);
  $totalRows_RsDeben = mysql_num_rows($all_RsDeben);
}
$totalPages_RsDeben = ceil($totalRows_RsDeben/$maxRows_RsDeben)-1;

$maxRows_RsArt = 10;
$pageNum_RsArt = 0;
if (isset($_GET['pageNum_RsArt'])) {
  $pageNum_RsArt = $_GET['pageNum_RsArt'];
}
$startRow_RsArt = $pageNum_RsArt * $maxRows_RsArt;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArt = "select a.nombre,count(a.idarticulo) as cant from articulo a, subcotizacionarticulo sa where a.idarticulo=sa.idarticulo order by cant desc";
$query_limit_RsArt = sprintf("%s LIMIT %d, %d", $query_RsArt, $startRow_RsArt, $maxRows_RsArt);
$RsArt = mysql_query($query_limit_RsArt, $tecnocomm) or die(mysql_error());
$row_RsArt = mysql_fetch_assoc($RsArt);

if (isset($_GET['totalRows_RsArt'])) {
  $totalRows_RsArt = $_GET['totalRows_RsArt'];
} else {
  $all_RsArt = mysql_query($query_RsArt);
  $totalRows_RsArt = mysql_num_rows($all_RsArt);
}
$totalPages_RsArt = ceil($totalRows_RsArt/$maxRows_RsArt)-1;


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
<table width="800" border="0" align="center">
  <tr>
    <td colspan="4" align="center" class="titulos">REPORTES TOP TEN </td>
  </tr>
  <tr>
    <td width="110">&nbsp;</td>
    <td width="281">&nbsp;</td>
    <td width="167">&nbsp;</td>
    <td width="224">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" align="center"><label>Dia:
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
      <input name="ano1" type="text" class="form" id="ano1" value="<?php echo date("Y")?>" size="8" />
      </label>
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
      <input name="ano2" type="text" class="form" id="ano2"  value="<?php echo date("Y")?>" size="8"/> 
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="button" id="button" value="&gt;" />
    </label></td>
  </tr>
  <tr class="titleTabla">
    <td colspan="4">CLIENTES QUE COMPRAN MAS </td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <?php if ($totalRows_RsCompran > 0) { // Show if recordset not empty ?>
    <tr>
      <td colspan="2" align="center">NOMBRE</td>
      <td align="center">CANTIDAD</td>
      <td>&nbsp;</td>
    </tr>
    <?php } // Show if recordset not empty ?>
  <?php do { ?>
    <tr>
      <td colspan="2" align="center"><?php echo $row_RsCompran['nombre']; ?>(<?php echo $row_RsCompran['abreviacion']; ?>)</td>
      <td align="center"><?php echo money_format('%i',$row_RsCompran['mone']*1.15); ?></td>
      <td>&nbsp;</td>
    </tr>
    <?php } while ($row_RsCompran = mysql_fetch_assoc($RsCompran)); ?>
  <?php if ($totalRows_RsCompran == 0) { // Show if recordset empty ?>
    <tr>
      <td>&nbsp;</td>
      <td colspan="2" align="center">NO EXISTEN DATOS </td>
      <td>&nbsp;</td>
    </tr>
    <?php } // Show if recordset empty ?>
  <tr class="titleTabla">
    <td colspan="4">CLIENTES QUE MAS DEBEN </td>
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
      <td align="center"><?php echo money_format('%i',$row_RsDeben['mone']*1.15); ?></td>
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
  <tr class="titleTabla">
    <td colspan="4">ARTICULOS MAS SOLICITADOS </td>
  </tr>
  <tr>
    <td colspan="4">&nbsp;</td>
  </tr>
  <?php if ($totalRows_RsArt > 0) { // Show if recordset not empty ?>
    <tr>
      <td colspan="2" align="center">NOMBRE</td>
      <td align="center">CANTIDAD</td>
      <td>&nbsp;</td>
    </tr>
    <?php } // Show if recordset not empty ?>
  <?php do { ?>
    <tr>
      <td colspan="2" align="center"><?php echo $row_RsArt['nombre']; ?></td>
      <td align="center"><?php echo $row_RsArt['cant']; ?></td>
      <td>&nbsp;</td>
    </tr>
    <?php } while ($row_RsArt = mysql_fetch_assoc($RsArt)); ?>
  <?php if ($totalRows_RsArt == 0) { // Show if recordset empty ?>
    <tr>
      <td colspan="3" align="center">NO EXISTEN DATOS </td>
      <td>&nbsp;</td>
    </tr>
    <?php } // Show if recordset empty ?>
</table>
<input type="hidden" name="mod" value="topten"/>

</form>
<?php
mysql_free_result($RsCompran);

mysql_free_result($RsDeben);

mysql_free_result($RsArt);
?>
