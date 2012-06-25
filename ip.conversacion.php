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

if(isset($_GET['buscar']) && $_GET['buscar']!=""){
		
		$bus = sprintf("AND (c.mensaje like %s)",
					   GetSQLValueString("%".$_GET['buscar']."%","text"));
		
	}

if(isset($_GET['estado']) && $_GET['estado']!='-1'){
	
		$estado = sprintf("AND (c.estado = %s)",
							GetSQLValueString($_GET['estado'],"int"));
	
	}

if(isset($_GET['prioridad']) && $_GET['prioridad']!='-1'){
	
		$prioridad = sprintf("AND (c.prioridad = %s)",
							GetSQLValueString($_GET['prioridad'],"int"));
	
	}



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
$query_rsConversaciones = sprintf("SELECT DISTINCT c.*, u.nombrereal As nombreremitente FROM conversacion c JOIN conversacion_destinatario cd ON c.idconversacion = cd.idconversacion JOIN usuarios u ON u.id = c.remitente WHERE c.idip = %s %s %s %s ORDER BY c.fechacreado DESC",
	GetSQLValueString($_GET['idip'],"int"),																																																							  $bus,$estado,$prioridad);
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


$cestado = array("bred","byellow","bgreen");
$prioridad = array("mail.png","mailred.png");
?>
<script language="javascript">
$(function(){
		   
		   $(".altConv").click(function(e){
				$("#lsAlertas ul li").removeClass("laS");
				$(this).addClass("laS");
				$.get('conversacion.ver.php',{idconversacion:$(this).attr('idconversacion')},
					function(data){
						$('#altContenido').html(data);
					});
										
				});
		   
		   
		   });
</script>
<link href="style2.css" rel="stylesheet" type="text/css">
<h1>Alertas</h1>
<div id="opciones">
<ul>
<li><a href="conversacion.nueva.php?idip=<?php echo $_GET['idip']?>" class="popup">Nueva Alerta</a></li>
<li><a href="ip.conversacion.print.detallado.php?idip=<?php echo $_GET['idip'];?>" class="popup"><img src="images/Imprimir2.png" align="middle" border="0" />Imprimir Detallado</a></li>
<li><a href="ip.conversacion.print.encabezados.php?idip=<?php echo $_GET['idip'];?>" class="popup"><img src="images/Imprimir2.png" align="middle" border="0" />Imprimir Encabezados</a></li>
</ul>
</div>
<div id="opciones">
<form name="frmFiltros" method="Get">
<label>
Buscar:
<input type="text" name="buscar" value="<?php echo isset($_GET['buscar'])?$_GET['buscar']:"";?>"/>
</label>
<label>
Estado:
<select name="estado">
  <option value="-1" <?php if (!(strcmp(-1, $_GET['estado']))) {echo "selected=\"selected\"";} ?>>Todos</option>
  <option value="0"  <?php if (!(strcmp(0, $_GET['estado']))) {echo "selected=\"selected\"";} ?>>Nuevos</option>
  <option value="1" <?php if (!(strcmp(1, $_GET['estado']))) {echo "selected=\"selected\"";} ?>>Realizados</option>
  <option value="2" <?php if (!(strcmp(2, $_GET['estado']))) {echo "selected=\"selected\"";} ?>>Liberados</option>
</select>
</label>
<label>
Prioridad
<select name="prioridad">
  <option value="-1" <?php if (!(strcmp(-1, $_GET['prioridad']))) {echo "selected=\"selected\"";} ?>>Todos</option>
  <option value="1" <?php if (!(strcmp(1, $_GET['prioridad']))) {echo "selected=\"selected\"";} ?>>Alta</option>
  <option value="0" <?php if (!(strcmp(0, $_GET['prioridad']))) {echo "selected=\"selected\"";} ?>>Normal</option>
</select>
</label>
<label>
<button type="submit">Filtrar</button>
</label>

<input type="hidden" name="mod" value="misalertas" />
</form>
</div>
<table width="100%%" cellpadding="0" cellspacing="0" align="center" class="border">
<tr><td align="right"><table border="0">
  <tr>
  <td>Alertas de la <?php echo ($startRow_rsConversaciones + 1) ?> a la <?php echo min($startRow_rsConversaciones + $maxRows_rsConversaciones, $totalRows_rsConversaciones) ?> de <?php echo $totalRows_rsConversaciones ?></td>
    <td><?php if ($pageNum_rsConversaciones > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, 0, $queryString_rsConversaciones); ?>#alertasinicio"><img src="images/First.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsConversaciones > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, max(0, $pageNum_rsConversaciones - 1), $queryString_rsConversaciones); ?>#alertasinicio"><img src="images/Previous.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsConversaciones < $totalPages_rsConversaciones) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, min($totalPages_rsConversaciones, $pageNum_rsConversaciones + 1), $queryString_rsConversaciones); ?>#alertasinicio"><img src="images/Next.gif" /></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsConversaciones < $totalPages_rsConversaciones) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, $totalPages_rsConversaciones, $queryString_rsConversaciones); ?>#alertasinicio"><img src="images/Last.gif" /></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td>
  <td>&nbsp;</td></tr>
<tr><td align="left" valign="top" width="600px">
<div id="lsAlertas">
<ul>
  <?php do { ?>
    <li class="altConv" idconversacion="<?php echo $row_rsConversaciones['idconversacion'];?>">
    <div <?php if($row_rsConversaciones['prioridad'] == 1){echo "class=\"palta\"";}?>>
    <img src="images/<?php echo $prioridad[$row_rsConversaciones['prioridad']]?>" class="altEstado" />
    <img src="images/<?php echo $cestado[$row_rsConversaciones['estado']]?>.png" class="altBandera"/>
    <span class="altRem"><?php echo $row_rsConversaciones['nombreremitente']; ?></span>
	<span class="altFecha"><?php echo formatDate($row_rsConversaciones['fechacreado']); ?></span>
    <span class="altMsj"><?php echo substr($row_rsConversaciones['mensaje'],0,120); ?></span>
    <span class="altIp"><?php echo isset($row_rsConversaciones['idip'])?"".$row_rsConversaciones['idip']:"&nbsp;"; ?></span>  
    </div>
    </li>
    <?php } while ($row_rsConversaciones = mysql_fetch_assoc($rsConversaciones)); ?>
    <li style="text-align:right"><table border="0" align="right">
  <tr>
  <td>Alertas de la <?php echo ($startRow_rsConversaciones + 1) ?> a la <?php echo min($startRow_rsConversaciones + $maxRows_rsConversaciones, $totalRows_rsConversaciones) ?> de <?php echo $totalRows_rsConversaciones ?></td>
    <td><?php if ($pageNum_rsConversaciones > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, 0, $queryString_rsConversaciones); ?>#alertasinicio"><img src="images/First.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsConversaciones > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, max(0, $pageNum_rsConversaciones - 1), $queryString_rsConversaciones); ?>#alertasinicio"><img src="images/Previous.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsConversaciones < $totalPages_rsConversaciones) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, min($totalPages_rsConversaciones, $pageNum_rsConversaciones + 1), $queryString_rsConversaciones); ?>#alertasinicio"><img src="images/Next.gif" /></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsConversaciones < $totalPages_rsConversaciones) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, $totalPages_rsConversaciones, $queryString_rsConversaciones); ?>#alertasinicio"><img src="images/Last.gif" /></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></li>
</ul>
</div>
</td>
<td align="left" valign="top">
<div id="altContenido">
<h3>Seleccione Una Alerta</h3>
</div>
</td>
</tr>
</table>
<?php
mysql_free_result($rsConversaciones);
?>
