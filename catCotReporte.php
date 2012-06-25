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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsCotizaciones = 30;
$pageNum_rsCotizaciones = 0;
if (isset($_GET['pageNum_rsCotizaciones'])) {
  $pageNum_rsCotizaciones = $_GET['pageNum_rsCotizaciones'];
}
$startRow_rsCotizaciones = $pageNum_rsCotizaciones * $maxRows_rsCotizaciones;

$edo='';
if(isset($_GET['estado']) and $_GET['estado']!='-1'){

	$edo=" and estado=".$_GET['estado'];
}

$bus='';
if(isset($_GET['buscar']) and $_GET['buscar']!=''){
	$bus=" and (identificador2 like'%%".$_GET['buscar']."%%' or nombre like'%%".$_GET['buscar']."%%')";
}


$colname_rsCotizaciones = "-1";
if (isset($_GET['ano'])) {
  $colname_rsCotizaciones = (get_magic_quotes_gpc()) ? $_GET['ano'] : addslashes($_GET['ano']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCotizaciones = sprintf("SELECT idsubcotizacion, estado, idcotizacion, identificador, identificador2, contacto, fecha, nombre, (select nombre from cliente,cotizacion where cotizacion.idcotizacion=subcotizacion.idcotizacion and cliente.idcliente=cotizacion.idcliente) as nomcliente FROM subcotizacion WHERE EXTRACT(YEAR FROM fecha) = %s $edo $bus ORDER BY identificador ASC", $colname_rsCotizaciones);
$query_limit_rsCotizaciones = sprintf("%s LIMIT %d, %d", $query_rsCotizaciones, $startRow_rsCotizaciones, $maxRows_rsCotizaciones);
$rsCotizaciones = mysql_query($query_limit_rsCotizaciones, $tecnocomm) or die(mysql_error());
$row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones);

if (isset($_GET['totalRows_rsCotizaciones'])) {
  $totalRows_rsCotizaciones = $_GET['totalRows_rsCotizaciones'];
} else {
  $all_rsCotizaciones = mysql_query($query_rsCotizaciones);
  $totalRows_rsCotizaciones = mysql_num_rows($all_rsCotizaciones);
}
$totalPages_rsCotizaciones = ceil($totalRows_rsCotizaciones/$maxRows_rsCotizaciones)-1;

$queryString_rsCotizaciones = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsCotizaciones") == false && 
        stristr($param, "totalRows_rsCotizaciones") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsCotizaciones = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsCotizaciones = sprintf("&totalRows_rsCotizaciones=%d%s", $totalRows_rsCotizaciones, $queryString_rsCotizaciones);

$state=array(1=>"ABIERTA",2=>"ENVIADA",3=>"AUTORIZADA",4=>"CONCILIADA",5=>"PAGADA",6=>"CONC ABIERTA",7=>"CONC ENVIADA",8=>"CONC AUTORIZADA",9=>"CONC PAGADA");

?>
<h1> Cotizaciones </h1>
<div class="buscar"><form id="form1" name="form1" method="get" action=""><label><span>Buscar</span><input type="text" name="buscar" value="<?php echo $_GET['buscar'];?>"></label>
<label><span>A&ntilde;o</span></label>

  <label>
  <select name="ano" onchange="this.form.submit();">
  <option value="-1">Seleccionar Año</option>
  <?php for($i=2008;$i<=date("Y");$i++){ ?>
    <option value="<?php echo $i; ?>" <?php if ($_GET['ano']==$i){echo "selected='selected'";}?>><?php echo $i; ?></option>
	<?php } ?>
  </select>
  </label>
  Estado:
  <select name="estado" id="estado" onchange="this.form.submit();">
    <option value="-1" <?php if($_GET['estado']==-1){echo "selected='selected'";}?>>TODAS</option>
    <option value="1" <?php if($_GET['estado']==1){echo "selected='selected'";}?>>ABIERTA</option>
    <option value="2" <?php if($_GET['estado']==2){echo "selected='selected'";}?>>ENVIADA</option>
    <option value="3" <?php if($_GET['estado']==3){echo "selected='selected'";}?>>AUTORIZADA</option>
    <option value="4" <?php if($_GET['estado']==4){echo "selected='selected'";}?>>CONCILIADA</option>
    <option value="5" <?php if($_GET['estado']==5){echo "selected='selected'";}?>>PAGADA</option>
    <option value="6" <?php if($_GET['estado']==6){echo "selected='selected'";}?>>CONC ABIERTA</option>
    <option value="7" <?php if($_GET['estado']==7){echo "selected='selected'";}?>>CONC ENVIADA</option>
    <option value="8" <?php if($_GET['estado']==8){echo "selected='selected'";}?>>CONC AUTORIZADA</option>
    <option value="9" <?php if($_GET['estado']==9){echo "selected='selected'";}?>>CONC PAGADA</option>
  </select>
  <input type="hidden" name="mod" value="reportescot"/>
</form> 
</div>

<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="6" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsCotizaciones > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsCotizaciones=%d%s", $currentPage, 0, $queryString_rsCotizaciones); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsCotizaciones > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsCotizaciones=%d%s", $currentPage, max(0, $pageNum_rsCotizaciones - 1), $queryString_rsCotizaciones); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsCotizaciones < $totalPages_rsCotizaciones) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsCotizaciones=%d%s", $currentPage, min($totalPages_rsCotizaciones, $pageNum_rsCotizaciones + 1), $queryString_rsCotizaciones); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsCotizaciones < $totalPages_rsCotizaciones) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsCotizaciones=%d%s", $currentPage, $totalPages_rsCotizaciones, $queryString_rsCotizaciones); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>Consecutivo</td>
<td>Cliente</td>
<td>Descripcion</td>
<td>Fecha</td>
<td>Estado</td>
<td>Opciones</td>
  </tr>
</thead>
<tbody>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
    <td><?php echo $row_rsCotizaciones['identificador2']; ?></td>
    <td><?php echo $row_rsCotizaciones['nomcliente']; ?></td>
    <td><?php echo $row_rsCotizaciones['nombre']; ?></td>
    <td><?php echo $row_rsCotizaciones['fecha']; ?></td>
    <td><img src="images/state<?php echo $row_rsCotizaciones['estado'];?>.png" alt="editar" width="24" height="24"  border="0" title="<? echo $state[$row_rsCotizaciones['estado']];?>"/></td>
    <td><a href="eliminarCotizacion.php?idsubcotizacion=<?php echo $row_rsCotizaciones['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Eliminar Cotizacion','850','600','YES');return false"><img src="images/eliminar.gif" width="19" height="19" border="0" title="Eliminar Cotizacion" /></a><a href="modificarCotiEstado.php?idsubcotizacion=<?php echo $row_rsCotizaciones['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Modificar Cotizacion','850','600','YES');return false"><img src="images/Edit.png" width="24" height="24" border="0" title="Cambiar Estado" /></a></td>
      </tr>
    <?php } while ($row_rsCotizaciones = mysql_fetch_assoc($rsCotizaciones)); ?>
</tbody>
<tfoot>
<tr><td colspan="6" align="right">
    <table border="0">
      <tr>
        <td><?php if ($pageNum_rsCotizaciones > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsCotizaciones=%d%s", $currentPage, 0, $queryString_rsCotizaciones); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsCotizaciones > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsCotizaciones=%d%s", $currentPage, max(0, $pageNum_rsCotizaciones - 1), $queryString_rsCotizaciones); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsCotizaciones < $totalPages_rsCotizaciones) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsCotizaciones=%d%s", $currentPage, min($totalPages_rsCotizaciones, $pageNum_rsCotizaciones + 1), $queryString_rsCotizaciones); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsCotizaciones < $totalPages_rsCotizaciones) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsCotizaciones=%d%s", $currentPage, $totalPages_rsCotizaciones, $queryString_rsCotizaciones); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr></tfoot>
</table>
</div>
<?php
mysql_free_result($rsCotizaciones);
?>
