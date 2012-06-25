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
	
if(isset($_GET['idusuario']) && $_GET['idusuario']!='-1'){
	$usuario = sprintf("AND (cd.destinatario = %s)",
							GetSQLValueString($_GET['idusuario'],"int"));
	}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuarios = "SELECT * FROM usuarios ORDER BY username ASC";
$rsUsuarios = mysql_query($query_rsUsuarios, $tecnocomm) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);



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
$query_rsConversaciones = sprintf("SELECT DISTINCT c.*, u.nombrereal As nombreremitente FROM conversacion c JOIN conversacion_destinatario cd ON c.idconversacion = cd.idconversacion JOIN usuarios u ON u.id = c.remitente WHERE 1=1  %s %s %s ORDER BY c.prioridad DESC, c.fechacreado ASC",
								  $bus,$estado,$usuario);
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
$cestado = array("bred","byellow","bgreen");
?>

<script language="javascript">
$(function(){
		   
		   $(".titalerta").click(function(e){
										  
			
				$(this).children(".showMsj").slideToggle();
			
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
<a href="#" name="alertasinicio"></a>
<h1>Alertas</h1>
<div id="opciones">
<ul>
<li><a href="conversacion.nueva.php" class="popup">Nueva Alerta</a></li>
<li><a href="rconversacion.print.detallado.php?<?php echo $get;?>" class="popup"><img src="images/Imprimir2.png" align="middle" border="0" />Imprimir Detallado</a></li>
<li><a href="rconversacion.print.encabezado.php?<?php echo $get;?>" class="popup"><img src="images/Imprimir2.png" align="middle" border="0" />Imprimir Encabezados</a></li>
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
Usuario:
<select name="idusuario">
<option value="-1" <?php if (!(strcmp(-1, $_GET['idusuario']))) {echo "selected=\"selected\"";} ?>>Todos</option>
  <?php
do {  
?>
  <option value="<?php echo $row_rsUsuarios['id']?>"  <?php if (!(strcmp($row_rsUsuarios['id'], $_GET['idusuario']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsUsuarios['username']?></option>
  <?php
} while ($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios));
  $rows = mysql_num_rows($rsUsuarios);
  if($rows > 0) {
      mysql_data_seek($rsUsuarios, 0);
	  $row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
  }
?>
</select>
</label>
<label>
<button type="submit">Filtrar</button>
</label>
<input type="hidden" name="mod" value="ralertas" />
</form>
</div>
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
<input type="hidden" name="estado" value="<?php echo isset($_GET['estado'])?$_GET['estado']:"-1";?>" />
</form></li>
<li><a href="index.php?mod=misalertas&estado=0">Nuevos</a></li>
<li><a href="index.php?mod=misalertas&estado=4">En Proceso</a></li>
<li><a href="index.php?mod=misalertas&estado=1">Realizados</a></li>
<li><a href="index.php?mod=misalertas&estado=2">Liberados</a></li>
<li><a href="index.php?mod=misalertas&estado=-1">Todos</a></li>
</ul>
</div>
</td>
<td valign="top" align="left"><?php if ($totalRows_rsConversaciones > 0) { // Show if recordset not empty ?>
  <div id="alertashorizontal">
    <ul>
      <?php do { ?>
        <li idconversacion="<?php echo $row_rsConversaciones['idconversacion'];?>" class="titalerta">
          <div <?php if($row_rsConversaciones['prioridad'] == 1){echo "class=\"palta\"";}?>>
            <img src="images/<?php echo $prioridad[$row_rsConversaciones['prioridad']]?>" class="altEstado2" />
            <img src="images/<?php echo $cestado[$row_rsConversaciones['estado']]?>.png" class="altBandera2"/>
            <span class="altIp2"><?php echo isset($row_rsConversaciones['idip'])?"".$row_rsConversaciones['idip']:"&nbsp;"; ?></span>
            <span class="altFecha2"><?php echo formatDate($row_rsConversaciones['fechacreado']); ?></span>
            <span class="altRem2"><?php echo $row_rsConversaciones['nombreremitente']; ?></span>
            <span class="altAsunto"><?php echo $row_rsConversaciones['asunto']; ?></span>
            <span class="altMsj2"><?php echo substr($row_rsConversaciones['mensaje'],0,50); ?></span>
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
<?php
mysql_free_result($rsConversaciones);

mysql_free_result($rsUsuarios);
?>
