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

$colname_rsHerramienta = "-1";
if (isset($_GET['idip'])) {
  $colname_rsHerramienta = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsHerramienta = sprintf("SELECT h.*,ph.*,u.username FROM proyecto_herramienta ph JOIN herramienta h ON ph.idherramienta = h.id  LEFT JOIN usuarios u ON u.id = ph.responsable WHERE idip = %s ORDER BY fechasalida ASC", GetSQLValueString($colname_rsHerramienta, "int"));
$rsHerramienta = mysql_query($query_rsHerramienta, $tecnocomm) or die(mysql_error());
$row_rsHerramienta = mysql_fetch_assoc($rsHerramienta);
$totalRows_rsHerramienta = mysql_num_rows($rsHerramienta);
?>
<h1>Herramienta</h1>
<p>Muestra los movimientos de herramienta en el proyecto.</p>
<div id="opciones">
<ul>
	<li><a href="ip.herramienta.prestar.php?idip=<?php echo $_GET['idip']?>" class="popup">Dar salida de herramienta</a></li>
</ul>
</div>
<select>
<option>Herrmaienta No Devuelta</option>
<option>Herrmaienta Devuelta</option>
<option>Toda la herramienta</option>
</select>
<div id="distabla">
<table cellpadding="2" cellspacing="0" width="100%">
<thead>
<tr>
<td>Clave Herramienta</td>
<td>Descripcion</td>
<td>Responsable</td>
<td>Fecha Salida</td>
<td>Fecha Devuelta</td>
<td>Observaciones</td>
<td>Opciones</td>
</tr>
</thead>



<tbody>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsHerramienta['id']; ?></td>
      <td><?php echo $row_rsHerramienta['descripcion']; ?></td>
      <td><?php echo $row_rsHerramienta['username']; ?></td>
      <td><?php echo $row_rsHerramienta['fechasalida']; ?></td>
      <td><?php echo $row_rsHerramienta['fechadevuelto']; ?></td>
      <td><?php echo $row_rsHerramienta['observaciones']; ?></td>
      <td>
      <?php if($row_rsHerramienta['fechadevuelto'] == ''): ?>
      <a href="ip.herramienta.regresar.php?idprestamo=<?php echo $row_rsHerramienta['idproyecto_herramienta']; ?>" class="popup">Regresar</a>
      <?php endif; ?>
      </td>
    </tr>
    <?php } while ($row_rsHerramienta = mysql_fetch_assoc($rsHerramienta)); ?>
</tbody>

</table>
</div>
<?php
mysql_free_result($rsHerramienta);
?>
