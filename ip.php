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

if(isset($_GET['filtro']) && $_GET['filtro'] == 1){
	
	switch($_GET['estado']){
		
		case 0:
		case 1:
		case 2:
		if(isset($_GET['bus']) && $_GET['bus']!= ""){
			$query_rsIp = sprintf("SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente AND (c.nombre like %s OR c.abreviacion like %s OR i.descripcion LIKE %s OR i.idip = %s ) AND i.estado = %s  ORDER BY idip DESC",
								GetSQLValueString("%".$_GET['bus']."%","text"),
								GetSQLValueString("%".$_GET['bus']."%","text"),
								GetSQLValueString("%".$_GET['bus']."%","text"),
								GetSQLValueString("%".$_GET['bus']."%","text"),
								GetSQLValueString($_GET['estado'],"text"));
			
			
			
		}else{
			$query_rsIp = sprintf("SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente AND i.estado = %s  ORDER BY idip DESC",
								GetSQLValueString($_GET['estado'],"int"));
		}
		break;
		case -1:
		default:
	if(isset($_GET['bus']) && $_GET['bus']!= ""){
			$query_rsIp = sprintf("SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente AND (c.nombre like %s OR c.abreviacion like %s OR i.descripcion LIKE %s OR i.idip = %s ) ORDER BY idip DESC",
								GetSQLValueString("%".$_GET['bus']."%","text"),
								GetSQLValueString("%".$_GET['bus']."%","text"),
								GetSQLValueString("%".$_GET['bus']."%","text"),
								GetSQLValueString($_GET['bus']."%","text"));
		}else{
			$query_rsIp = sprintf("SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente  ORDER BY idip DESC");
		}
	
	}
	
	
}else{
	$query_rsIp = "SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente ORDER BY idip DESC";
}




$currentPage = $_SERVER["PHP_SELF"];

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuario = "SELECT * FROM usuarios ORDER BY username ASC";
$rsUsuario = mysql_query($query_rsUsuario, $tecnocomm) or die(mysql_error());
$row_rsUsuario = mysql_fetch_assoc($rsUsuario);
$totalRows_rsUsuario = mysql_num_rows($rsUsuario);

$maxRows_rsIp = 30;
$pageNum_rsIp = 0;
if (isset($_GET['pageNum_rsIp'])) {
  $pageNum_rsIp = $_GET['pageNum_rsIp'];
}
$startRow_rsIp = $pageNum_rsIp * $maxRows_rsIp;

mysql_select_db($database_tecnocomm, $tecnocomm);
//$query_rsIp = "SELECT c.nombre, c.clave, i.* FROM ip i, cliente c WHERE i.idcliente = c.idcliente ORDER BY idip DESC";
$query_limit_rsIp = sprintf("%s LIMIT %d, %d", $query_rsIp, $startRow_rsIp, $maxRows_rsIp);
$rsIp = mysql_query($query_limit_rsIp, $tecnocomm) or die(mysql_error());
$row_rsIp = mysql_fetch_assoc($rsIp);

if (isset($_GET['totalRows_rsIp'])) {
  $totalRows_rsIp = $_GET['totalRows_rsIp'];
} else {
  $all_rsIp = mysql_query($query_rsIp);
  $totalRows_rsIp = mysql_num_rows($all_rsIp);
}
$totalPages_rsIp = ceil($totalRows_rsIp/$maxRows_rsIp)-1;

$queryString_rsIp = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsIp") == false && 
        stristr($param, "totalRows_rsIp") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsIp = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsIp = sprintf("&totalRows_rsIp=%d%s", $totalRows_rsIp, $queryString_rsIp);




mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsResponsable = sprintf("SELECT pp.*,u.nombrereal,u.username FROM ip i LEFT JOIN proyecto_personal pp ON i.idip = pp.idip LEFT JOIN usuarios u ON u.id = pp.idusuario WHERE pp.estado = 1");
$rsResponsable = mysql_query($query_rsResponsable, $tecnocomm) or die(mysql_error());
$row_rsResponsable = mysql_fetch_assoc($rsResponsable);
$totalRows_rsResponsable = mysql_num_rows($rsResponsable);

do{
	
	$res[$row_rsResponsable['idip']]['name'] = $row_rsResponsable['username'];
	$res[$row_rsResponsable['idip']]['id'] = $row_rsResponsable['idusuario'];
	
}while($row_rsResponsable = mysql_fetch_assoc($rsResponsable));


$estadosIP = array("<img src=\"images/bred.png\">","<img src=\"images/byellow.png\">","<img src=\"images/bgreen.png\">");



//definimos variable usuario en caso de que aplique

$usuario = isset($_GET['usuario'])?$_GET['usuario']:-1;

if(isset($_GET) && is_array($_GET))
	foreach($_GET as $kg => $g){
			$get.= $kg."=".$g."&";
		}

?>
<script>
$(function(){

	$(".infirst").focus();

});
</script>
<h1> Ip </h1>
<div id="submenu">
<ul>
<li><a href="nuevoIP.php" class="popup">Nuevo Ip</a></li>
<li><a href="print.ip.list.php?<?php echo $get;?>"  target="_blank">Imprimir lista de Ip</a></li>
</ul>
</div>
<div id="">
<form name="filtros" method="get">
<label>
Buscar: <input type="text" name="bus" value="<?php echo (isset($_GET['bus']))?$_GET['bus']:"";?>" class="infirst"/>
</label>

<br />
<label>
Estado: <select name="estado">
<option  value="-1" selected="selected" <?php if($_GET['estado'] == -1 || !isset($_GET['esado']) ){echo "selected\"selected\"";}?>>Todos</option>
<option value="0" <?php if($_GET['estado'] == 0 && isset($_GET['estado'])){echo "selected\"selected\"";}?>>Abiertos</option>
<option value="1" <?php if($_GET['estado'] == 1 && isset($_GET['estado'])){echo "selected\"selected\"";}?>>En Proceso</option>
<option value="2" <?php if($_GET['estado'] == 2 && isset($_GET['estado'])){echo "selected\"selected\"";}?>>Cerrado</option>
</select>
</label>
<br />
<label>
Usuario: <select name="usuario">
<option value="-1">Todos</option>
  <?php
do {  
?>
  <option value="<?php echo $row_rsUsuario['id']?>"<?php if (!(strcmp($row_rsUsuario['id'], $_GET['usuario']))) {echo "selected=\"selected\"";} ?>><?php echo $row_rsUsuario['username']?></option>
  <?php
} while ($row_rsUsuario = mysql_fetch_assoc($rsUsuario));
  $rows = mysql_num_rows($rsUsuario);
  if($rows > 0) {
      mysql_data_seek($rsUsuario, 0);
	  $row_rsUsuario = mysql_fetch_assoc($rsUsuario);
  }
?>
</select>
</label>
<br />
<label>
<input type="submit" value="filtrar" />
</label>
<input type="hidden" name="mod" value="ip" />
<input type="hidden" name="filtro" value="1" />
</form>
</div>
<div id="distabla">
<table width="100%" cellpadding="1" cellspacing="0">
<thead>
<tr>
<td colspan="10" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsIp > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsIp=%d%s", $currentPage, 0, $queryString_rsIp); ?>"><img src="images/First.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsIp > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsIp=%d%s", $currentPage, max(0, $pageNum_rsIp - 1), $queryString_rsIp); ?>"><img src="images/Previous.gif" /></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsIp < $totalPages_rsIp) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsIp=%d%s", $currentPage, min($totalPages_rsIp, $pageNum_rsIp + 1), $queryString_rsIp); ?>"><img src="images/Next.gif" /></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsIp < $totalPages_rsIp) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsIp=%d%s", $currentPage, $totalPages_rsIp, $queryString_rsIp); ?>"><img src="images/Last.gif" /></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td>
</tr>
<tr>
  <td>Opciones</td>
  <td></td>
  <td>IP</td>
  <td>Cliente</td>
  <td>Descripcion</td>
  <td>Fecha Creacion</td>
  <td>Fecha Ultimo Mov.</td>
  <td>Ultimo Movimiento</td>
  <td>Responsable</td>
  <td></td></tr>
</thead>
<tbody>
  <?php do { ?>
<?php if($res[$row_rsIp['idip']]['id'] == $_GET['usuario'] || $usuario == -1){?>
    <tr>
    <td><a href="index.php?mod=detalleip&idip=<?php echo $row_rsIp['idip']; ?>"><img src="images/Edit.png"></a><?php if($row_rsIp['estado']!=2){?><a href="ip.fin.php?idip=<?php echo $row_rsIp['idip']; ?>" class="popup"><img src="images/state3.png" width="24" height="24" border="0" /></a><? }?></td>
    <td><?php echo $estadosIP[$row_rsIp['estado']];?></td>
    <td><?php echo $row_rsIp['idip']; ?></td>
    <td><?php echo $row_rsIp['nombre']; ?></td>
      <td><?php echo $row_rsIp['descripcion']; ?></td>
      <td><?php echo formatDate($row_rsIp['fecha']); ?></td>
      <td><?php echo  formatDate($row_rsIp['fechamovimiento']); ?></td>
      <td><?php echo $row_rsIp['ultimomovimiento']; ?></td>
      <td><?php echo $res[$row_rsIp['idip']]['name']; ?></td>
      <td></td></tr>
     <?php } //fin de if usuario?>
    <?php } while ($row_rsIp = mysql_fetch_assoc($rsIp)); ?>
</tbody>
</table>
</div>
</div>
<?php
mysql_free_result($rsIp);

mysql_free_result($rsUsuario);
?>
