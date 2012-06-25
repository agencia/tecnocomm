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

$maxRows_rsAvance = 30;
$pageNum_rsAvance = 0;
if (isset($_GET['pageNum_rsAvance'])) {
  $pageNum_rsAvance = $_GET['pageNum_rsAvance'];
}
$startRow_rsAvance = $pageNum_rsAvance * $maxRows_rsAvance;



$colname_rsAvance = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsAvance = $_SESSION['MM_Userid'];
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAvance = sprintf("SELECT *,(select identificador2 from subcotizacion where idsubcotizacion=subcotizacionlider.idsubcotizacion) as proyecto,(select nombre from subcotizacion where idsubcotizacion=subcotizacionlider.idsubcotizacion) as nombre FROM subcotizacionlider WHERE idusuario = %s ", GetSQLValueString($colname_rsAvance, "int"));
$query_limit_rsAvance = sprintf("%s LIMIT %d, %d", $query_rsAvance, $startRow_rsAvance, $maxRows_rsAvance);
$rsAvance = mysql_query($query_limit_rsAvance, $tecnocomm) or die(mysql_error());
$row_rsAvance = mysql_fetch_assoc($rsAvance);

if (isset($_GET['totalRows_rsAvance'])) {
  $totalRows_rsAvance = $_GET['totalRows_rsAvance'];
} else {
  $all_rsAvance = mysql_query($query_rsAvance);
  $totalRows_rsAvance = mysql_num_rows($all_rsAvance);
}
$totalPages_rsAvance = ceil($totalRows_rsAvance/$maxRows_rsAvance)-1;

$queryString_rsAvance = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsAvance") == false && 
        stristr($param, "totalRows_rsAvance") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsAvance = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsAvance = sprintf("&totalRows_rsAvance=%d%s", $totalRows_rsAvance, $queryString_rsAvance);
?>
<h1> Detalle de Avances</h1>
<div class="submenu"> </div>
<div class="buscar"><label><span>Buscar</span><input type="text" name="buscar"></label></div>

<div id="distabla">
 
    <table width="100%" cellpadding="0" cellspacing="0">
      <thead>
        <tr>
          <td colspan="3" align="right"><table border="0">
            <tr>
                <td><?php if ($pageNum_rsAvance > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_rsAvance=%d%s", $currentPage, 0, $queryString_rsAvance); ?>"><img src="images/First.gif"></a>
                    <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_rsAvance > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_rsAvance=%d%s", $currentPage, max(0, $pageNum_rsAvance - 1), $queryString_rsAvance); ?>"><img src="images/Previous.gif"></a>
                    <?php } // Show if not first page ?></td>
              <td><?php if ($pageNum_rsAvance < $totalPages_rsAvance) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_rsAvance=%d%s", $currentPage, min($totalPages_rsAvance, $pageNum_rsAvance + 1), $queryString_rsAvance); ?>"><img src="images/Next.gif"></a>
                    <?php } // Show if not last page ?></td>
              <td><?php if ($pageNum_rsAvance < $totalPages_rsAvance) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_rsAvance=%d%s", $currentPage, $totalPages_rsAvance, $queryString_rsAvance); ?>"><img src="images/Last.gif"></a>
                    <?php } // Show if not last page ?></td>
            </tr>
          </table></td>
        </tr>
		<?php if ($totalRows_rsAvance > 0) { // Show if recordset empty ?>
        <tr>
          <td width="7%" >Opciones</td>
          <td width="65%" >Proyecto</td>
      <td width="28%">Identificador</td>
      </tr>
		<?php } // Show if recordset empty ?>
      </thead>
      <tbody>
        <?php do { ?>
          <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
            <td><a href="catAvances.php?id=<?php echo $row_rsAvance['idsubcotizacion']; ?>" onclick="NewWindow(this.href,'Nuevo Banco',600,800,'yes'); return false;"><img src="images/Agregar.png" width="24" height="24" title="AGREGAR AVANCE"></a><a href="imprimirReporteAvance.php?idsub=<?php echo $row_rsAvance['idsubcotizacion'];?>" onclick="NewWindow(this.href,'IMPRIMIR Avance','1100','980','yes');return false;"><img src="images/Imprimir2.png" alt="" width="24" height="24" border="0"  title="IMPRIMIR AVANCE"/></a></td>
            <td><?php echo $row_rsAvance['nombre']; ?></td>
            <td><?php echo $row_rsAvance['proyecto']; ?></td>
          </tr>
          <?php } while ($row_rsAvance = mysql_fetch_assoc($rsAvance)); ?>
		   <?php if ($totalRows_rsAvance == 0) { // Show if recordset empty ?>
        <tr>
          <td colspan="3" align="center"> NO ESTA PARTICIPANDO EN NINGUN PROYECTO </td>
        </tr>
		  <?php } // Show if recordset empty ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" align="right">
            <table border="0">
              <tr>
                <td><?php if ($pageNum_rsAvance > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_rsAvance=%d%s", $currentPage, 0, $queryString_rsAvance); ?>"><img src="images/First.gif" /></a>
                    <?php } // Show if not first page ?></td>
                <td><?php if ($pageNum_rsAvance > 0) { // Show if not first page ?>
                    <a href="<?php printf("%s?pageNum_rsAvance=%d%s", $currentPage, max(0, $pageNum_rsAvance - 1), $queryString_rsAvance); ?>"><img src="images/Previous.gif" /></a>
                    <?php } // Show if not first page ?></td>
                <td><?php if ($pageNum_rsAvance < $totalPages_rsAvance) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_rsAvance=%d%s", $currentPage, min($totalPages_rsAvance, $pageNum_rsAvance + 1), $queryString_rsAvance); ?>"><img src="images/Next.gif" /></a>
                    <?php } // Show if not last page ?></td>
                <td><?php if ($pageNum_rsAvance < $totalPages_rsAvance) { // Show if not last page ?>
                    <a href="<?php printf("%s?pageNum_rsAvance=%d%s", $currentPage, $totalPages_rsAvance, $queryString_rsAvance); ?>"><img src="images/Last.gif" /></a>
                    <?php } // Show if not last page ?></td>
              </tr>
          </table></td>
        </tr>
      </tfoot>
        </table>
</div>
  
<?php
mysql_free_result($rsAvance);
?>
