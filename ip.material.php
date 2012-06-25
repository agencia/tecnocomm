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

$colname_rs_controles = "-1";
if (isset($_GET['idip'])) {
  $colname_rs_controles = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rs_controles = sprintf("SELECT pm.*,sb.identificador2 FROM proyecto_material pm JOIN subcotizacion sb ON pm.idcotizacion = sb.idcotizacion AND sb.estado = 3 WHERE pm.idip = %s", GetSQLValueString($colname_rs_controles, "int"));
$rs_controles = mysql_query($query_rs_controles, $tecnocomm) or die(mysql_error());
$row_rs_controles = mysql_fetch_assoc($rs_controles);
$totalRows_rs_controles = mysql_num_rows($rs_controles);
 

?>
<script type="text/javascript">
	$(function() {
		$("#controlesmaterial").tabs({
			ajaxOptions: {
				error: function(xhr, status, index, anchor) {
					$(anchor.hash).html("Erro: No se puede mostrar el contenido, refresque la pagina he intente de nuevo");
				}
			}
		});
	});
	</script>




<div>
<ul><li><a href="ip.controlmaterial.nuevo.php?idip=<?php echo $_GET['idip']?>" class="popup">Crear Nuevo Control</a></li></ul>
</div>

<div id="controlesmaterial">
<ul>
<li><a href="#c1">Control Material - ></a></li>
<li><a href="ip.controlmaterial.detalle.php?idproyecto_material=<?php echo $row_rs_controles['idproyecto_material']; ?>&idip=<?php echo $_GET['idip'];?>" class="popup"><?php echo $row_rs_controles['identificador2']; ?></a></li>
</ul>

<div id="c1">
<p> Eliga Una Pestana-></p>
</div>

</div>

<?php
mysql_free_result($rs_controles);
?>
