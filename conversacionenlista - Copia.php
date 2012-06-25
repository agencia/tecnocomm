<?php require_once('Connections/tecnocomm.php');
if (!isset($_GET['mod'])) {
	session_start();
	require_once('utils.php');
}
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

if(isset($_GET['buscar']) && $_GET['buscar']!=""){
		$bus = sprintf("c.mensaje like %s OR c.asunto like %s",
					   GetSQLValueString("%".$_GET['buscar']."%","text"),
					    GetSQLValueString("%".$_GET['buscar']."%","text"));
		if(isset($_GET['estado']) && ($_GET['estado'] == 4))
			$bus = "c.estado IN (0,4) AND";
		else
			$bus = (isset($_GET['estado']) && $_GET['estado'] >= 0) ? "c.estado = " . $_GET['estado'] . " AND": "";
	}

if((isset($_GET['estado']) && $_GET['estado']!='-1') && ($_GET['estado']!='3')){
	
		$estado = sprintf("AND (c.estado = %s)",
							GetSQLValueString($_GET['estado'],"int"));
	
}else{
	$estado = sprintf("AND (c.estado = 1 OR c.estado = 0)");
}
if(!isset($_GET['estado'])) {
	$_GET['estado'] = 4;
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
$query_rsConversaciones = sprintf("SELECT DISTINCT c.*, u.nombrereal As nombreremitente, cd.estado as edocd FROM conversacion c JOIN conversacion_destinatario cd ON c.idconversacion = cd.idconversacion JOIN usuarios u ON u.id = c.remitente WHERE (c.remitente = %s OR cd.destinatario = %s) %s GROUP BY c.idconversacion ORDER BY c.prioridad DESC,c.fechacreado ASC", GetSQLValueString($colname1_rsConversaciones, "int"),GetSQLValueString($colname2_rsConversaciones, "int"),$estado);
if(isset($_GET['buscar'])) {
	$query_rsConversaciones = sprintf("SELECT c.*, (SELECT nombrereal FROM usuarios WHERE c.remitente = id) FROM conversacion c WHERE %s (c.mensaje like '%s' OR c.asunto like '%s' OR c.idconversacion IN (SELECT cm.idconversacion FROM conversacion_mensaje cm WHERE cm.mensaje like '%s')) ", $bus, '%'.$_GET['buscar'] .'%', '%'.$_GET['buscar'] .'%', '%'.$_GET['buscar'] .'%');
} elseif ($_GET['estado'] == 0) { //Sin leer
	//$query_rsConversaciones = sprintf("SELECT DISTINCT c.*, u.nombrereal As nombreremitente, cd.estado as edocd FROM conversacion c JOIN conversacion_destinatario cd ON c.idconversacion = cd.idconversacion JOIN usuarios u ON u.id = c.remitente WHERE cd.estado = 0 AND (c.remitente = %s OR cd.destinatario = %s) %s %s ORDER BY c.prioridad DESC,c.fechacreado ASC", GetSQLValueString($colname1_rsConversaciones, "int"),GetSQLValueString($colname2_rsConversaciones, "int"),$bus,$estado);
	$query_rsConversaciones = sprintf("SELECT *, (SELECT nombrereal FROM usuarios WHERE c.remitente = id) as nombreremitente FROM conversacion c WHERE c.estado = 0 AND c.idconversacion IN (SELECT idconversacion FROM conversacion_destinatario WHERE estado = 0 AND destinatario = %s GROUP BY idconversacion)  GROUP BY idconversacion",$colname2_rsConversaciones);
} elseif ($_GET['estado'] == 4) {
	//$query_rsConversaciones = sprintf("SELECT DISTINCT c.*, u.nombrereal As nombreremitente, cd.estado as edocd FROM conversacion c JOIN conversacion_destinatario cd ON c.idconversacion = cd.idconversacion JOIN usuarios u ON u.id = c.remitente WHERE c.estado IN (0,4) AND (c.remitente = %s OR cd.destinatario = %s) %s %s ORDER BY c.prioridad DESC,c.fechacreado ASC", GetSQLValueString($colname1_rsConversaciones, "int"),GetSQLValueString($colname2_rsConversaciones, "int"),$bus,$estado);
//echo $query_rsConversaciones;
	$query_rsConversaciones = sprintf("SELECT *, (SELECT nombrereal FROM usuarios WHERE c.remitente = id) as nombreremitente FROM conversacion c WHERE c.estado IN (0,4) AND (c.idconversacion IN ( SELECT cd.idconversacion FROM conversacion_destinatario cd WHERE cd.destinatario = %s ) OR c.remitente = %s) GROUP BY idconversacion",$colname2_rsConversaciones,$colname2_rsConversaciones);
}
$query_limit_rsConversaciones = sprintf("%s LIMIT %d, %d", $query_rsConversaciones, $startRow_rsConversaciones, $maxRows_rsConversaciones);
$rsConversaciones = mysql_query($query_limit_rsConversaciones, $tecnocomm) or die(mysql_error() . " SQL: " . $query_rsConversaciones);
$row_rsConversaciones = mysql_fetch_assoc($rsConversaciones);

if (isset($_GET['totalRows_rsConversaciones'])) {
  $totalRows_rsConversaciones = $_GET['totalRows_rsConversaciones'];
} else {
  $all_rsConversaciones = mysql_query($query_rsConversaciones);
  $totalRows_rsConversaciones = mysql_num_rows($all_rsConversaciones);
}
$totalPages_rsConversaciones = ceil($totalRows_rsConversaciones/$maxRows_rsConversaciones)-1;

$queryString_rsConversaciones = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsConversaciones") == false && 
        stristr($param, "totalRows_rsConversaciones") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsConversaciones = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsConversaciones = sprintf("&totalRows_rsConversaciones=%d%s", $totalRows_rsConversaciones, $queryString_rsConversaciones);

$prioridad = array("mail.png","mailred.png");
$cestado = array("bred.png","byellow.png","bgreen.png");
?>
<?php if (!isset($_GET['mod'])) { ?>
<link href="style.css" rel="stylesheet" type="text/css">
<script src="js/jquery.js" language="javascript"> </script>
<script src="js/jqueryui.js" language="javascript"></script>
<script language="javascript"  src="js/funciones.js"></script>
<?php } ?>
<script language="javascript">
$(function(){
		   
		   $(".titalerta").click(function(e){
										  
			
				$(this).children(".showMsj").slideToggle();
			
			});

		$(".nuevMen").click(function(){
			$.get('conversacion.leida.php?id=' + this.id);
			alertas();
		});

		$(".titalerta").click(function(){
			$(".titalerta").removeClass("alertSelected");
			$(this).addClass("alertSelected");
			//alert(this.id);
		});
		$(".titalerta").mouseover(function(){
			//$(".titalerta").removeClass("alertSelected");
			$(this).addClass("alertHigh");
			//alert(this.id);
		});
		$(".titalerta").mouseout(function(){
			$(".titalerta").removeClass("alertHigh");
			//$(this).addClass("alertHigh");
			//alert(this.id);
		});
		   
		  /* 
		   $(".altConv").click(function(e){
				$("#lsAlertas ul li").removeClass("laS");
				$(this).addClass("laS");
				$.get('conversacion.ver.php',{idconversacion:$(this).attr('idconversacion')},
					function(data){
						$('#altContenido').html(data);
					});
										
				});
*/
		   });
</script>
<?php 
if(isset($_GET) && is_array($_GET))
	foreach($_GET as $kg => $g){
		$get .= $kg."=".$g."&";
		
	}
?>
<link href="style2.css" rel="stylesheet" type="text/css">
<?php if (!isset($_GET['mod'])) { ?>
<div class="wrapper"><?php } ?>
<a href="#" name="alertasinicio"></a>
<h1>Alertas</h1>
<?php if(!isset($_GET['mi'])) { ?>
<div id="opciones">
<ul>
<li><a href="conversacion.nueva.php" class="popup">Nueva Alerta</a></li>
<li><a href="conversacion.print.detallado.php?<?php echo $get;?>" class="popup"><img src="images/Imprimir2.png" align="middle" border="0" />Imprimir Detallado</a></li>
<li><a href="conversacion.print.encabezados.php?<?php echo $get;?>" class="popup"><img src="images/Imprimir2.png" align="middle" border="0" />Imprimir Encabezados</a></li>
</ul>
</div>
<?php } ?>
<div id="alertascontainer">
<table width="100%">
<tr><td></td><td align="right"><table border="0">
  <tr>
  <td align="right">Alertas de la <?php echo ($startRow_rsConversaciones + 1) ?> a la <?php echo min($startRow_rsConversaciones + $maxRows_rsConversaciones, $totalRows_rsConversaciones) ?> de <?php echo $totalRows_rsConversaciones ?></td>
    <td><?php if ($pageNum_rsConversaciones > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, 0, $queryString_rsConversaciones); ?>"><img src="images/First.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsConversaciones > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, max(0, $pageNum_rsConversaciones - 1), $queryString_rsConversaciones); ?>"><img src="images/Previous.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsConversaciones < $totalPages_rsConversaciones) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, min($totalPages_rsConversaciones, $pageNum_rsConversaciones + 1), $queryString_rsConversaciones); ?>"><img src="images/Next.gif" /></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsConversaciones < $totalPages_rsConversaciones) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, $totalPages_rsConversaciones, $queryString_rsConversaciones); ?>"><img src="images/Last.gif" /></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr><td valign="top" align="left" width="150px">
<div id="alertasmenu">
<ul>
<li>
<form name="frmFiltros" method="Get">
<label>
Buscar:
<input type="text" name="buscar" value="<?php echo isset($_GET['buscar'])?$_GET['buscar']:"";?>" style="margin:0px;" />
</label>
<input type="hidden" name="mod" value="misalertas" />
<input type="hidden" name="estado" value="<?php echo (isset($_GET['estado'])) ? $_GET['estado'] : "-1";?>" />
</form></li>
<?php if (!isset($_GET['mod'])) { ?>
<li><a href="conversacionenlista.php?estado=0">Sin Leer</a></li>
<li><a href="conversacionenlista.php?estado=4">En Proceso</a></li>
<li><a href="conversacionenlista.php?estado=1">Realizados</a></li>
<li><a href="conversacionenlista.php?estado=2">Liberados</a></li>
<li><a href="conversacionenlista.php?estado=-1">Todos</a></li>
<?php } else { ?>
<li><a href="index.php?mod=misalertas&estado=0">Sin Leer</a></li>
<li><a href="index.php?mod=misalertas&estado=4">En Proceso</a></li>
<li><a href="index.php?mod=misalertas&estado=1">Realizados</a></li>
<li><a href="index.php?mod=misalertas&estado=2">Liberados</a></li>
<li><a href="index.php?mod=misalertas&estado=-1">Todos</a></li>
</div><?php } ?>
</ul>
</div>
</td>
<td valign="top" align="left"><?php if ($totalRows_rsConversaciones > 0) { // Show if recordset not empty ?>
  <div id="alertashorizontal">
    <ul>
      <?php do { ?>
        <li idconversacion="<?php echo $row_rsConversaciones['idconversacion'];?>" id="listAlerta" class="titalerta">
          <div id="<?php echo $row_rsConversaciones['idconversacion']; ?>" class="<?php echo ($row_rsConversaciones['prioridad'] == 1) ? "palta" : ""; echo ($row_rsConversaciones['edocd'] == 0) ? " nuevMen" : ""; ?>">
            <img src="images/<?php echo $prioridad[$row_rsConversaciones['prioridad']]?>" class="altEstado2" />
            <img src="images/<?php echo $cestado[$row_rsConversaciones['estado']]?>" class="altBandera2"/>
            <span class="altIp2"><?php echo isset($row_rsConversaciones['idip'])?"".$row_rsConversaciones['idip']:"&nbsp;"; ?></span>
            <span class="altFecha2"><?php echo formatDate($row_rsConversaciones['fechacreado']); ?></span>
            <span class="altRem2"><?php echo substr($row_rsConversaciones['nombreremitente'], 0,25); ?></span>
            <span class="altAsunto"><?php echo $row_rsConversaciones['asunto']; ?></span>
            <span class="altMsj2"><?php echo substr($row_rsConversaciones['mensaje'],0,40); ?></span>
          </div>
          <div class="showMsj" style="display:none">
            <?php $_GET['idconversacion'] =$row_rsConversaciones['idconversacion']; ?>
            <?php include("conversacion.ver.php");?>
          </div>
        </li>
        <?php } while ($row_rsConversaciones = mysql_fetch_assoc($rsConversaciones)); ?>
    </ul>
  </div>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_rsConversaciones == 0) { // Show if recordset empty ?>
   <div id="alertashorizontal">
   <p>
    No Hay Alertas Que Mostrar
    </p></div>
  <?php } // Show if recordset empty ?></td>
</tr>
<tr><td></td><td align="right"><table border="0">
  <tr>
  <td align="right">Alertas de la <?php echo ($startRow_rsConversaciones + 1) ?> a la <?php echo min($startRow_rsConversaciones + $maxRows_rsConversaciones, $totalRows_rsConversaciones) ?> de <?php echo $totalRows_rsConversaciones ?></td>
    <td><?php if ($pageNum_rsConversaciones > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, 0, $queryString_rsConversaciones); ?>"><img src="images/First.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsConversaciones > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, max(0, $pageNum_rsConversaciones - 1), $queryString_rsConversaciones); ?>"><img src="images/Previous.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsConversaciones < $totalPages_rsConversaciones) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, min($totalPages_rsConversaciones, $pageNum_rsConversaciones + 1), $queryString_rsConversaciones); ?>"><img src="images/Next.gif" /></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsConversaciones < $totalPages_rsConversaciones) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsConversaciones=%d%s", $currentPage, $totalPages_rsConversaciones, $queryString_rsConversaciones); ?>"><img src="images/Last.gif" /></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
</table>

<?php if (!isset($_GET['mod'])) { ?>
</div><?php } ?>
<?php
mysql_free_result($rsConversaciones);
?>
