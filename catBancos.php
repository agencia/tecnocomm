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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsBancos = 30;
$pageNum_rsBancos = 0;
if (isset($_GET['pageNum_rsBancos'])) {
  $pageNum_rsBancos = $_GET['pageNum_rsBancos'];
}
$startRow_rsBancos = $pageNum_rsBancos * $maxRows_rsBancos;

if(isset($_GET['buscar']) and ($_GET['buscar']!="")){
	$sql.=" and  clave like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR institucion like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR numerodecuenta like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR clabe like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	$sql.=" OR funcionario like ".GetSQLValueString("%".$_GET['buscar']."%","text");
	
	
	
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsBancos = "SELECT * FROM bancos WHERE 1 $sql";
$query_limit_rsBancos = sprintf("%s LIMIT %d, %d", $query_rsBancos, $startRow_rsBancos, $maxRows_rsBancos);
$rsBancos = mysql_query($query_limit_rsBancos, $tecnocomm) or die(mysql_error());
$row_rsBancos = mysql_fetch_assoc($rsBancos);

if (isset($_GET['totalRows_rsBancos'])) {
  $totalRows_rsBancos = $_GET['totalRows_rsBancos'];
} else {
  $all_rsBancos = mysql_query($query_rsBancos);
  $totalRows_rsBancos = mysql_num_rows($all_rsBancos);
}
$totalPages_rsBancos = ceil($totalRows_rsBancos/$maxRows_rsBancos)-1;

$queryString_rsBancos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsBancos") == false && 
        stristr($param, "totalRows_rsBancos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsBancos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsBancos = sprintf("&totalRows_rsBancos=%d%s", $totalRows_rsBancos, $queryString_rsBancos);
?>
<h1> Bancos </h1>
<div class="submenu"> <a href="nuevoBanco.php" onclick="NewWindow(this.href,'Nuevo Banco',600,800,'yes'); return false;"> Banco Nuevo </a> </div>
<div class="buscar"><form name="buscar" action="" method="get">
<label><span>Buscar</span><input type="text" name="buscar" value="<?php echo $_GET['buscar'];?>"></label> <input name="enviar" type="submit" value="Buscar" />
<input type="hidden" name="mod" value="catbancos"/>
</form></div>
<a href="imprimirBancos.php?consulta=<?php echo $query_rsBancos;?>" target="_blank">IMPRIMIR CONSULTA DE BANCOS</a>
<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="5" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsBancos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, 0, $queryString_rsBancos); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsBancos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, max(0, $pageNum_rsBancos - 1), $queryString_rsBancos); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsBancos < $totalPages_rsBancos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, min($totalPages_rsBancos, $pageNum_rsBancos + 1), $queryString_rsBancos); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsBancos < $totalPages_rsBancos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, $totalPages_rsBancos, $queryString_rsBancos); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td>Opciones</td>
<td>Clave</td><td>Institucion</td><td>No. de Cuenta</td>
  <td>Contacto</td>
  </tr>
</thead>
<tbody>
  <?php do { ?>
    <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
    <td><a href="modificarBanco.php?idbanco=<?php echo $row_rsBancos['idbanco']; ?>" onclick="NewWindow(this.href,'Modificar Banco',800,800,'yes');return false;"><img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR DATOS DE BANCO"/></a><a href="eliminarBanco.php?id=<?php echo $row_rsBancos['idbanco']; ?>" onclick="NewWindow(this.href,'Eliminar Banco',800,800,'yes');return false;"><img src="images/eliminar.gif" width="24" height="24" border="0" title="ELIMINAR BANCO"/></a></td>
    <td><?php echo $row_rsBancos['clave']; ?></td><td><?php echo $row_rsBancos['institucion']; ?></td><td><?php echo $row_rsBancos['numerocuenta']; ?></td>
      <td><?php echo $row_rsBancos['tel1']; ?>/<?php echo $row_rsBancos['tel2']; ?></td>
      </tr>
    <?php } while ($row_rsBancos = mysql_fetch_assoc($rsBancos)); ?>
</tbody>
<tfoot>
<tr><td colspan="5" align="right">
    <table border="0">
      <tr>
        <td><?php if ($pageNum_rsBancos > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, 0, $queryString_rsBancos); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsBancos > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, max(0, $pageNum_rsBancos - 1), $queryString_rsBancos); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsBancos < $totalPages_rsBancos) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, min($totalPages_rsBancos, $pageNum_rsBancos + 1), $queryString_rsBancos); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsBancos < $totalPages_rsBancos) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsBancos=%d%s", $currentPage, $totalPages_rsBancos, $queryString_rsBancos); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr></tfoot>

</table>
</div>
<?php
mysql_free_result($rsBancos);
?>
