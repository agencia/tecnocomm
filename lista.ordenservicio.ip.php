<?php require_once('Connections/tecnocomm.php'); ?>
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

$ide_rsOrdenes = "-1";
if (isset($_GET['idip'])) {
  $ide_rsOrdenes = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsOrdenes = sprintf("SELECT *, DATE_FORMAT(fecha, '%%d-%%m-%%Y') as fecha FROM ordenservicio a WHERE a.idip=%s", GetSQLValueString($ide_rsOrdenes, "int"));
$rsOrdenes = mysql_query($query_rsOrdenes, $tecnocomm) or die(mysql_error());
$row_rsOrdenes = mysql_fetch_assoc($rsOrdenes);

  $totalRows_rsOrdenes = mysql_num_rows($rsOrdenes);

$mon=array(0=>"PESOS",1=>"DOLARES");
$signo = array(0=>"$",1=>"US$");
$state=array(1=>"ABIERTA",2=>"ENVIADA",3=>"AUTORIZADA",4=>"CONCILIADA",5=>"PAGADA",6=>"CONC ABIERTA",7=>"CONC ENVIADA",8=>"CONC AUTORIZADA",9=>"CONC PAGADA");?><form action="" method="get" name="form1" id="form1">
<table width="950" border="0" align="center">
  <tr>
    <td colspan="8" align="center" class="titulos">Ordenes de Servicio</td>
    <td width="95">&nbsp;</td>
  </tr>
  <tr>
    <td width="61">&nbsp;</td>
    <td width="174">&nbsp;</td>
    <td colspan="6" align="center" valign="middle">
<!--        <a href="ci/index.php/cotizaciones/nueva/<?php echo $_GET['idip']?>" onclick="NewWindow(this.href,'Nueva Cotizacion','650','600');return false"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" />NUEVA COTIZACION</strong>
        </a>-->
        <a href="nuevo.orden.servicio.php?ip=<?php echo $_GET['idip']?>" onclick="NewWindow(this.href,'Nueva Orden de Servicio','850','600','YES');return false"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" />NUEVA ORDEN DE SERVICIO</strong>
        </a>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td width="17">&nbsp;</td>
    <td width="150">&nbsp;</td>
    <td>&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="3">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php if ($totalRows_rsOrdenes > 0) { // Show if recordset not empty ?>
    <tr class="titleTabla">
      <td align="center">OPCIONES</td>
      <td align="center">CONSECUTIVO</td>
      <td width="137" align="center" >IDENTIFICADOR</td>
      <td width="87" align="center">FECHA</td>
      <td width="150" align="center">OBSERVACIONES</td>
      <td align="center">MONTO</td>
    </tr>

  <?php do { ?>
    <tr onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
      <td>
          <a href="editar.orden.detalle.php?idordenservicio=<?php echo $row_rsOrdenes['idordenservicio'];?>&idip=<?php echo $row_rsOrdenes['idip'];?>" onclick="if(confirm('Estas seguro que deseas modificar esta orden de servicio?')){NewWindow(this.href,'Modificar Orden','950','980','yes');return false}else{return false;}">
              <img src="images/Edit.png" border="0" title="Editar Orden de Servicio">
          </a>
          <a onclick="NewWindow(this.href,'IMPRIMIR Orden','1100','980','yes');return false;" href="printOrdenServicio.php?idordenservicio=<?php echo $row_rsOrdenes['idordenservicio'];?>">
            <img width="24" border="0" title="Imprimir Orden de servicio" alt="Imprimir" src="images/Imprimir2.png" />
          </a>
      </td>
            <td><?php echo $row_rsOrdenes['identificador']; ?></td>
            <td><?php echo $row_rsOrdenes['descripcionreporte']; ?></td>
            <td><?php echo $row_rsOrdenes['fecha']; ?></td>
            <td><?php echo $row_rsOrdenes['observaciones']; ?></td>
            <td>&nbsp;</td>
    </tr>
    <?php } while ($row_rsOrdenes = mysql_fetch_assoc($rsOrdenes)); ?>
        <?php } // Show if recordset not empty ?>

  <?php if ($totalRows_rsOrdenes == 0) { // Show if recordset empty ?>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="3" align="center">No hay ordenes de servicio realizadas</td>
        <td>&nbsp;</td>
      </tr>
      <?php } // Show if recordset empty ?>

      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="3" align="center">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>

</table>
</form>

<?php
mysql_free_result($rsOrdenes);
?>
