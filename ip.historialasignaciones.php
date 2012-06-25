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

$colname_rsTarea = "-1";
if (isset($_GET['idip'])) {
  $colname_rsTarea = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTarea = sprintf("SELECT * FROM tarea WHERE idip = %s", GetSQLValueString($colname_rsTarea, "int"));
$rsTarea = mysql_query($query_rsTarea, $tecnocomm) or die(mysql_error());
$row_rsTarea = mysql_fetch_assoc($rsTarea);
$totalRows_rsTarea = mysql_num_rows($rsTarea);

$colname_rsTareas = "-1";
if (isset($_GET['idip'])) {
  $colname_rsTareas = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTareas = sprintf("SELECT u.username, t.idtarea FROM tarea t JOIN tarea_usuario tu ON t.idtarea = tu.idtarea JOIN usuarios u ON u.id = tu.idusuario WHERE idip = %s", GetSQLValueString($colname_rsTareas, "int"));
$rsTareas = mysql_query($query_rsTareas, $tecnocomm) or die(mysql_error());
$row_rsTareas = mysql_fetch_assoc($rsTareas);
$totalRows_rsTareas = mysql_num_rows($rsTareas);


do{
	$usuarios[$row_rsTareas['idtarea']][] = $row_rsTareas['username'];
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));

?>

<h1>Historial De Asignaciones</h1>

<div class="distabla">

<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr>
<td>Fecha</td>
<td>Responsable</td>
<td>Comentarios</td>
</tr>
</thead>
<tbody>
  <?php do { ?>
    <tr>
      <td><?php echo formatDate($row_rsTarea['fechaasigno']); ?></td>
      <td>
        <?php 
if(is_array($usuarios[$row_rsTarea['idtarea']])){
	echo join(', ',$usuarios[$row_rsTarea['idtarea']]);
}
?>
        
      </td>
      <td class="<?php echo ($row_rsTarea['estado'] == 2) ? "realizado": ""; ?>">
        <?php $comentarios = json_decode($row_rsTarea['comentario'],true); ?>
        <?php 
if(is_array($comentarios)): ?>
        <ul>
          <?php foreach($comentarios as $comentario): ?>
          <li><?php 	echo $comentario['usuario']."|";
			echo $comentario['fecha']."|";
		echo $comentario['comentario'];?></li>
          <?php endforeach;?>
        </ul>
        <?php endif;?>
      </td>
    </tr>
    <?php } while ($row_rsTarea = mysql_fetch_assoc($rsTarea)); ?>
</tbody>

</table>

</div>

<?php
mysql_free_result($rsTarea);

mysql_free_result($rsTareas);
?>
