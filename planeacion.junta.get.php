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


$colname_rsJunta = "-1";
if (isset($_GET['idjunta'])) {
  $colname_rsJunta = $_GET['idjunta'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsJunta = sprintf("SELECT * FROM junta WHERE idjunta = %s", GetSQLValueString($colname_rsJunta, "int"));
$rsJunta = mysql_query($query_rsJunta, $tecnocomm) or die(mysql_error());
$row_rsJunta = mysql_fetch_assoc($rsJunta);
$totalRows_rsJunta = mysql_num_rows($rsJunta);


$colname_rsTarea = "-1";
if (isset($_GET['idjunta'])) {
  $colname_rsTarea = $_GET['idjunta'];
}
$colname2_rsTarea = "-1";
if (isset($_GET['fecha'])) {
  $colname2_rsTarea = $_GET['fecha'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTarea = sprintf("SELECT * FROM tarea LEFT JOIN cotizacion WHERE idjunta = %s OR fecharealizar = %s ", GetSQLValueString($colname_rsTarea, "int"),GetSQLValueString($colname2_rsTarea, "date"));
$rsTarea = mysql_query($query_rsTarea, $tecnocomm) or die(mysql_error());
$row_rsTarea = mysql_fetch_assoc($rsTarea);
$totalRows_rsTarea = mysql_num_rows($rsTarea);


?>
 
<h3><?php echo formatDate($_GET['fecha']); ?></h3>
<ul id="Ncoti">
<h4>Cotizaciones</h4>
<li>
<table width="100%" >
<tr><td>C-035-MAHLE</td><td align="right">Proceso</td></tr>
<tr><td colspan="2">UNIVERSIDAD AUTONOMA DE AGUASCALIENTES</td></tr>
<tr><td colspan="2">INSTALACION DE NODO DE RED</td></tr>
</table>
</li>
</ul>
<ul id="Nlev">
<h4>Levantamientos</h4>
<li></li>
</ul>
<ul id="Nos">
<h4>Ordenes Servicio</h4>
<li></li>
</ul>
</div>
</div>


<?php
mysql_free_result($rsTarea);
?>
