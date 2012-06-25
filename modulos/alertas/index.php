<?php require_once('../../Connections/tecnocomm.php');
session_start();

$colname_rsConvSinLeer = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsConvSinLeer = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConvSinLeer = sprintf("SELECT * FROM conversacion c WHERE c.estado = 0 AND c.idconversacion IN (SELECT idconversacion FROM conversacion_destinatario WHERE estado <= 1 AND destinatario = %s GROUP BY idconversacion)  GROUP BY idconversacion",$colname_rsConvSinLeer);
$rsConvSinLeer = mysql_query($query_rsConvSinLeer, $tecnocomm) or die(mysql_error(). "<br />SQL: " . $query_rsConvSinLeer);
$totalRows_rsConvSinLeer = mysql_num_rows($rsConvSinLeer);


$colname_rsConvActivas = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsConvActivas = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConvActivas = sprintf("SELECT * FROM conversacion c WHERE c.estado IN (0,4) AND (c.idconversacion IN ( SELECT cd.idconversacion FROM conversacion_destinatario cd WHERE cd.destinatario = %s ) OR c.remitente = %s) GROUP BY idconversacion", $colname_rsConvActivas, $colname_rsConvActivas);
$rsConvActivas = mysql_query($query_rsConvActivas, $tecnocomm) or die(mysql_error(). "<br />SQL: " . $query_rsConvActivas);
$totalRows_rsConvActivas = mysql_num_rows($rsConvActivas);

$colname_rsConvRealizadas = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsConvRealizadas = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConvRealizadas = sprintf("SELECT * FROM conversacion c WHERE c.estado = 1 AND (c.idconversacion IN ( SELECT cd.idconversacion FROM conversacion_destinatario cd WHERE cd.destinatario = %s ) OR c.remitente = %s) GROUP BY idconversacion", $colname_rsConvRealizadas, $colname_rsConvRealizadas);
$rsConvRealizadas = mysql_query($query_rsConvRealizadas, $tecnocomm) or die(mysql_error(). "<br />SQL: " . $query_rsConvRealizadas);
$totalRows_rsConvRealizadas = mysql_num_rows($rsConvRealizadas);
?>
<?php if($_SESSION['mnuevos'] < $totalRows_rsConvSinLeer){ ?>
<script language="text/javascript">alert('Tienes ' + <?php echo $totalRows_rsConvSinLeer; ?> + ' sin leer');
NewWindow("conversacionenlista.php?estado=0","Mensajes",400,600,'yes');
</script>
<?php
} 	$_SESSION['mnuevos'] = $totalRows_rsConvSinLeer;?>
<span style="font-size:24px; color:#F00;padding-left:10px;">Alertas (<a href="index.php?mod=misalertas&estado=0" title="Sin leer"><span style="<?php echo ($totalRows_rsConvSinLeer > 0) ? "text-decoration:blink;": ""; ?>font-size:24px"><?php echo $totalRows_rsConvSinLeer; ?></span></a>/<a href="index.php?mod=misalertas&estado=4" title="En proceso"><?php echo $totalRows_rsConvActivas;?></a>/<a href="index.php?mod=misalertas&estado=1" title="Realizadas"><?php echo $totalRows_rsConvRealizadas; ?></a></label>)</span>