<?php require_once('Connections/tecnocomm.php'); ?>
<?php
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsParaHoy = sprintf("SELECT t.* FROM tarea t, tarea_usuario tu WHERE tu.idusuario = %s AND tu.idtarea = t.idtarea AND t.fecharealizar = DATE(NOW()) ", $_SESSION['MM_Userid']);
$rsParaHoy = mysql_query($query_rsParaHoy, $tecnocomm) or die(mysql_error());
$row_rsParaHoy = mysql_fetch_assoc($rsParaHoy);
$totalRows_rsParaHoy = mysql_num_rows($rsParaHoy);
echo $totalRows_rsParaHoy;
?>