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

$colname_rsConversacion = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsConversacion = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConversacion = sprintf("SELECT mc.idconversacion FROM msjconversacion mc, msjmiembros mm WHERE mc.idconversacion = mm.idconversacion  AND mm.idusuario = %s", GetSQLValueString($colname_rsConversacion, "int"));
$rsConversacion = mysql_query($query_rsConversacion, $tecnocomm) or die(mysql_error());
$row_rsConversacion = mysql_fetch_assoc($rsConversacion);
$totalRows_rsConversacion = mysql_num_rows($rsConversacion);

$colname_rsMesnajes = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsMesnajes = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMesnajes = sprintf("SELECT ms.fecha,ms.idconversacion, ms.mensaje, ms.idusuario,ms.idmensaje,( SELECT username FROM usuarios u WHERE u.id = ms.idusuario ) AS nombreusuario FROM msjmensajes ms, msjmiembros mm WHERE ms.idconversacion = mm.idconversacion  AND mm.idusuario = %s ORDER BY fecha ASC", GetSQLValueString($colname_rsMesnajes, "int"));
$rsMesnajes = mysql_query($query_rsMesnajes, $tecnocomm) or die(mysql_error());
$row_rsMesnajes = mysql_fetch_assoc($rsMesnajes);
$totalRows_rsMesnajes = mysql_num_rows($rsMesnajes);


do{
	
	$conversacion[$row_rsMesnajes['idconversacion']][] = $row_rsMesnajes;

	
}while($row_rsMesnajes = mysql_fetch_assoc($rsMesnajes));


?>
<script language="javascript">
$(function(){
		
		$(".msjplatica").click(function(e){
									
									$(this).find("ul:first").toggle();
									
									})
		   
		});
</script>
<h1> Centro de Alertas </h1>
<div class="submenu"> 
<ul>
<li><a href="nuevaAlerta.php" onclick="NewWindow(this.href,'Nuevo Aviso Masivo',600,800,'yes'); return false;"> Nueva Alerta</a></li>
</ul>
</div>

<div id="msjlist">
<?php foreach($conversacion as $idConv => $msjs){
?>	
<ul class="msjplatica">
<h3><span id="msjde"><?php echo $msjs[0]['fecha'];?> <?php echo $msjs[0]['nombreusuario']?></span><span id="msjlinks"></span><span id="msjmensaje"> Dijo: <?php echo $msjs[0]['mensaje']?></span></h3>
<ul class="msjmsj">
<?php foreach($msjs as $mensaje){?>
	<li><span id="msjde"><?php echo $mensaje['fecha'];?> <?php echo $mensaje['nombreusuario']?></span><span id="msjlinks"></span><span id="msjmensaje"> Dijo: <?php echo $mensaje['mensaje']?></span> </li>
  <?php } ?>  
<a href="responderAlerta.php?idconversacion=<?php echo $idConv;?>" onclick="NewWindow(this.href,'Responder',800,600,'yes'); return false;"> Responder </a>
</ul>
</ul>
<?php    
}?>
</div>
<?php

mysql_free_result($rsConversacion);

mysql_free_result($rsMesnajes);
?>
