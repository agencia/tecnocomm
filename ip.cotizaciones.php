
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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_CoCli = 30;
$pageNum_CoCli = 0;
if (isset($_GET['pageNum_CoCli'])) {
  $pageNum_CoCli = $_GET['pageNum_CoCli'];
}
$startRow_CoCli = $pageNum_CoCli * $maxRows_CoCli;

if(isset($_GET['cliente']) and ($_GET['cliente']!="")){
$cliente=" and (cli.nombre like '%".$_GET['cliente']."%' OR	abreviacion like'%".$_GET['cliente']."%')";

}
else
{
$cliente=" ";
}

if(isset($_GET['identificador']) and ($_GET['identificador']!="")){
$identificador=" and sub.identificador2 like '%".$_GET['identificador']."%'";
}
else
{
$identificador=" ";
}

if(isset($_GET['estado']) and ($_GET['estado']!=-1)){
$estado=" and sub.estado=".$_GET['estado'];
}
else
{
$estado=" ";
}


mysql_select_db($database_tecnocomm, $tecnocomm);
	$query_CoCli = sprintf("SELECT *,sub.nombre as nom,sub.estado as edo  FROM subcotizacion sub,cotizacion co ,cliente cli 
	  where co.idip = %s AND sub.idcotizacion=co.idcotizacion and co.idcliente=cli.idcliente %s %s %s ORDER BY EXTRACT(YEAR FROM fecha) desc ,identificador DESC",$_GET['idip'],$cliente,$identificador,$estado);
	$query_limit_CoCli = sprintf("%s LIMIT %d, %d", $query_CoCli, 	
	$startRow_CoCli, $maxRows_CoCli);
	
	$CoCli = mysql_query($query_limit_CoCli, $tecnocomm) or die(mysql_error());
	$row_CoCli = mysql_fetch_assoc($CoCli);


if (isset($_GET['totalRows_CoCli'])) {
  $totalRows_CoCli = $_GET['totalRows_CoCli'];
} else {
  $all_CoCli = mysql_query($query_CoCli);
  $totalRows_CoCli = mysql_num_rows($all_CoCli);
}
$totalPages_CoCli = ceil($totalRows_CoCli/$maxRows_CoCli)-1;

$queryString_CoCli = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_CoCli") == false && 
        stristr($param, "totalRows_CoCli") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_CoCli = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_CoCli = sprintf("&totalRows_CoCli=%d%s", $totalRows_CoCli, $queryString_CoCli);
$mon=array(0=>"PESOS",1=>"DOLARES");
$signo = array(0=>"$",1=>"US$");
$state=array(1=>"ABIERTA",2=>"ENVIADA",3=>"AUTORIZADA",4=>"CONCILIADA",5=>"PAGADA",6=>"CONC ABIERTA",7=>"CONC ENVIADA",8=>"CONC AUTORIZADA",9=>"CONC PAGADA");?><form action="" method="get" name="form1" id="form1">
<table width="950" border="0" align="center">
  <tr>
    <td colspan="8" align="center" class="titulos">COTIZACIONES</td>
    <td width="95">&nbsp;</td>
  </tr>
  <tr>
    <td width="61">&nbsp;</td>
    <td width="174">&nbsp;</td>
    <td colspan="6" align="center" valign="middle">
<!--        <a href="ci/index.php/cotizaciones/nueva/<?php echo $_GET['idip']?>" onclick="NewWindow(this.href,'Nueva Cotizacion','650','600');return false"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" />NUEVA COTIZACION</strong>
        </a>-->
        <a href="cotizaciones_nueva_cliente_ip.php?idip=<?php echo $_GET['idip']?>" onclick="NewWindow(this.href,'Nueva Cotizacion','850','600','YES');return false"><strong><img src="images/bullet_16.jpg" alt="nueva" width="26" height="22" border="0" align="middle" />NUEVA COTIZACION</strong>
        </a>
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="6">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="6" align="center">
      <label>
        <strong>POR ESTADO</strong> 
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
        </label>    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="9" align="center">
      
        <strong>
        BUSCAR POR NOMBRE DE CLIENTE
        :
          <input name="cliente" type="text" class="form" id="cliente" value="<? echo $_GET['cliente']; ?>" />
     
          <label>
          <input type="submit" name="button" id="button" value="BUSCAR" />
          </label>
        </strong>    
      
        <strong>
        BUSCAR POR CONSECUTIVO:
          <input name="identificador" type="text" class="form" id="identificador" value="<? echo $_GET['identificador']; ?>" />
       
          <label>
          <input type="submit" name="button2" id="button2" value="BUSCAR" />
          </label>
        </strong>          </td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
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
    <td></td>
    <td>&nbsp;</td>
    <td colspan="2" align="center"><a href="<?php printf("%s?pageNum_CoCli=%d%s", $currentPage, 0, $queryString_CoCli); ?>">
    <?php if ($pageNum_CoCli > 0) { // Show if not first page ?>
      <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
</a><a href="<?php printf("%s?pageNum_CoCli=%d%s", $currentPage, max(0, $pageNum_CoCli - 1), $queryString_CoCli); ?>">
<?php if ($pageNum_CoCli > 0) { // Show if not first page ?>
  <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
  <?php } // Show if not first page ?>
</a><a href="<?php printf("%s?pageNum_CoCli=%d%s", $currentPage, min($totalPages_CoCli, $pageNum_CoCli + 1), $queryString_CoCli); ?>">
<?php if ($pageNum_CoCli < $totalPages_CoCli) { // Show if not last page ?>
  <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
  <?php } // Show if not last page ?>
</a><a href="<?php printf("%s?pageNum_CoCli=%d%s", $currentPage, $totalPages_CoCli, $queryString_CoCli); ?>">
<?php if ($pageNum_CoCli < $totalPages_CoCli) { // Show if not last page ?>
  <img src="images/Last.gif" alt="ultimo" width="24" height="24" border="0" />
  <?php } // Show if not last page ?>
</a></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="6">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <?php if ($totalRows_CoCli > 0) { // Show if recordset not empty ?>
    <tr class="titleTabla">
      <td align="center">ESTADO</td>
      <td align="center">OPCIONES</td>
      <td width="137" align="center" >CONSECUTIVO</td>
      <td width="87" align="center">FECHA</td>
      <td colspan="2" align="center">CLIENTE</td>
      <td width="166" align="center">IDENTIFICADOR</td>
      <td align="center">MONTO</td>
      <td align="center">IP</td>
    </tr>
    <?php } // Show if recordset not empty ?>

  <?php do { ?>
    <?php $crear_subconciliacion = "cotizacion.sub.php?idsubcotizacion=" . $row_CoCli['idsubcotizacion'] . "&type=1"; ?>
    <tr onmouseover="
	this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
      <td align="center" valign="top" width="61"><img src="images/state<?php echo $row_CoCli['edo'];?>.png" alt="editar" width="24" height="24"  border="0" title="<? echo $state[$row_CoCli['edo']];?>"/></td>
      <td align="right"><a href="#"  name="<?php echo $row_CoCli['idsubcotizacion']; ?>">
              <a href="cotizacion.detalle.ip.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>&idcliente=<?php echo $row_CoCli['idcliente'];?>&tipo=0&idip=<?php echo $row_CoCli['idip']; ?>" onclick="if(confirm('Estas seguro que deseas modificar esta cotizacion?')){NewWindow(this.href,'Modificar Cotizacion','950','980','yes');return false}else{return false;}"><?php if ($totalRows_CoCli > 0) { if(($row_CoCli['edo']==1)){?><img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR COTIZACION"/><? }}?></a>
              <a href="<?php echo $crear_subconciliacion; ?>" onclick="if(confirm('Estas seguro que deseas crear una subcotizacion ?')){NewWindow(this.href,'Modificar Cotizacion','950','980','yes');return false}else{return false;}">
                <?php if ($totalRows_CoCli > 0) {
                    //echo "q";
                       if($row_CoCli['edo']==2){
                           ?><img src="images/reeditar.png" alt="editar" width="24" title="GENERAR SUBCOTIZACION" height="24"  border="0"/>
                           <? }      
                           }?></a>
              <a href="cotizacion.sub.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>&type=2" onclick="if(confirm('Estas seguro que deseas crear una conciliacion ?')){NewWindow(this.href,'Modificar Cotizacion','950','980','yes');return false}else{return false;}"><?php if ($totalRows_CoCli > 0) {if($row_CoCli['edo']==3){?><img src="images/consiliar.png" alt="editar" width="24" height="24"  border="0" title="Conciliar Cotizacion"/><? }}?>
        </a><a href="printCotizacion.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>" onclick="NewWindow(this.href,'IMPRIMIR COTIZACION','1100','980','yes');return false"><img src="images/Imprimir2.png" width="24" height="24" border="0"  title="IMPRIMIR COTIZACION"/></a>
        <a href="cotizacionEnviada.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>" onclick="NewWindow(this.href,'ENVIAR Cotizacion','450','200','yes');return false"><?php if ($totalRows_CoCli > 0) {if(($row_CoCli['edo']==1)or($row_CoCli['edo']==2)or($row_CoCli['edo']==4)or($row_CoCli['edo']==5)){?><img src="images/state2.png" width="24" height="24" border="0" title="ENVIAR COTIZACION" /></a><? }}?><a href="cotizacionAutorizada.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>&estado=<? echo $row_CoCli['edo']; ?>" onclick="NewWindow(this.href,'Autorizar Cotizacion','450','200','yes');return false"><?php if ($totalRows_CoCli > 0) {if($row_CoCli['edo']==2){?><img src="images/state3.png" width="24" height="24" border="0" title="AUTORIZAR COTIZACION" /><? }}?></a><a href="cotizacionPagada.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>" onclick="NewWindow(this.href,'PAGAR Cotizacion','450','200','yes');return false"><?php if ($totalRows_CoCli > 0) { if(($row_CoCli['edo']==4)or($row_CoCli['edo']==3)){ if(in_array(13,$array_niveles)){?><img src="images/state5.png" width="24" height="24" border="0" title="PAGAR COTIZACION" /><? }}}?></a>
		
		
		<?php //////////////////////////////////////////////////////////////////////////    CONCIL.IACIONES
		$_SESSION['bandera']=0;
		?>
		
		
<a href="cotizacion.detalle.ip.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>&idcliente=<?php echo $row_CoCli['idcliente'];?>&tipo=0&idip=<?php echo $row_CoCli['idip']; ?>" onclick="if(confirm('Estas seguro que deseas modificar esta conciliacion?')){NewWindow(this.href,'Modificar conciliacion','950','980','yes');return false}else{return false;}">
<?php if ($totalRows_CoCli > 0) { if(($row_CoCli['edo']==6)){?>
    <img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR CONCILIACION"/><? }}?></a>
        <a href="cotizacion.sub.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>&type=3" onclick="if(confirm('Estas seguro que deseas crear una subconciliacion ?')){NewWindow(this.href,'Modificar Conciliacion','950','980','yes');return false}else{return false;}"><?php if ($totalRows_CoCli > 0) {if($row_CoCli['edo']==7){?><img src="images/reeditar.png" alt="editar" width="24" title="GENERAR SUBCONCILIACION" height="24"  border="0"/><? }}?></a>
        <a href="conciliacionEnviada.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>" onclick="NewWindow(this.href,'ENVIAR CONCILIACION','450','200','yes');return false"><?php if ($totalRows_CoCli > 0) {if(($row_CoCli['edo']==6)or($row_CoCli['edo']==7)or($row_CoCli['edo']==9)){?><img src="images/state2.png" width="24" height="24" border="0" title="ENVIAR CONCILIACION" /></a><? }}?>
        <a href="conciliacionAutorizada.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>&estado=<? echo $row_CoCli['edo']; ?>" onclick="NewWindow(this.href,'Autorizar Cotizacion','450','200','yes');return false"><?php if ($totalRows_CoCli > 0) {if($row_CoCli['edo']==7){?><img src="images/state3.png" width="24" height="24" border="0" title="AUTORIZAR CONCILIACION" /><? }}?></a><a href="conciliacionPagada.php?idsubcotizacion=<?php echo $row_CoCli['idsubcotizacion'];?>" onclick="NewWindow(this.href,'PAGAR Cotizacion','450','200','yes');return false">
        <?php if ($totalRows_CoCli > 0) { if(($row_CoCli['edo']==8)){ if(in_array(13,$array_niveles)){?><img src="images/state5.png" width="24" height="24" border="0" title="PAGAR CONCILIACION" /><? }}}?></a>		</td>
      <td align="center" class="printingStyle"><?php echo $row_CoCli['identificador2']; ?></td>
      <td align="center" class="printingStyle"><?php echo $row_CoCli['fecha']; ?></td>
      <td colspan="2" align="center" class="printingStyle"><?php echo $row_CoCli['nombre']; ?></td>
      <td align="center" class="printingStyle"><?php echo $row_CoCli['nom']; ?></td>
      <td align="center" class="printingStyle"><?php echo $signo[$row_CoCli['moneda']]; $idsubcotizacion =$row_CoCli['idsubcotizacion']; require("cotizacion.monto.php");?></td>
      <td align="center" class="printingStyle"><?php echo $row_CoCli['idip']; ?></td>
    </tr>
    <?php } while ($row_CoCli = mysql_fetch_assoc($CoCli)); ?>
  <?php if ($totalRows_CoCli == 0) { // Show if recordset empty ?>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="6" align="center">No hay cotizaciones realizadas</td>
        <td>&nbsp;</td>
      </tr>
      <?php } // Show if recordset empty ?>

      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td colspan="6" align="center">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td colspan="6" align="center"><a href="<?php printf("%s?pageNum_CoCli=%d%s", $currentPage, 0, $queryString_CoCli); ?>">
      <?php if ($pageNum_CoCli > 0) { // Show if not first page ?>
      <img src="images/First.gif" alt="primero" width="24" height="24" border="0" />
      <?php } // Show if not first page ?>
    </a><a href="<?php printf("%s?pageNum_CoCli=%d%s", $currentPage, max(0, $pageNum_CoCli - 1), $queryString_CoCli); ?>">
    <?php if ($pageNum_CoCli > 0) { // Show if not first page ?>
    <img src="images/Back.gif" alt="atras" width="24" height="24" border="0" />
    <?php } // Show if not first page ?>
    </a><a href="<?php printf("%s?pageNum_CoCli=%d%s", $currentPage, min($totalPages_CoCli, $pageNum_CoCli + 1), $queryString_CoCli); ?>">
    <?php if ($pageNum_CoCli < $totalPages_CoCli) { // Show if not last page ?>
    <img src="images/Forward.gif" alt="siguiente" width="24" height="24" border="0" />
    <?php } // Show if not last page ?>
    </a><a href="<?php printf("%s?pageNum_CoCli=%d%s", $currentPage, $totalPages_CoCli, $queryString_CoCli); ?>">
    <?php if ($pageNum_CoCli < $totalPages_CoCli) { // Show if not last page ?>
    <img src="images/Last.gif" alt="ultimo" width="24" height="24" border="0" />
    <?php } // Show if not last page ?>
    </a></td>
    <td>&nbsp;</td>
  </tr>
</table>
<input type="hidden" name="mod" value="cotizacion" />
</form>
<?php
mysql_free_result($CoCli);
?>
