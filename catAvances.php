<?php require_once('Connections/tecnocomm.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "systemFail.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

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
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
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
$ide_rsAvance = "-1";
if (isset($_GET['id'])) {
  $ide_rsAvance = $_GET['id'];
}
$maxRows_rsAvance = 10;
$pageNum_rsAvance = 0;
if (isset($_GET['pageNum_rsAvance'])) {
  $pageNum_rsAvance = $_GET['pageNum_rsAvance'];
}
$startRow_rsAvance = $pageNum_rsAvance * $maxRows_rsAvance;

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAvance = "SELECT *,(select identificador2 from subcotizacion where idsubcotizacion=reporteavance.idsubcotizacion) as proyecto FROM reporteavance WHERE idempleado = $colname_rsAvance and idsubcotizacion=$ide_rsAvance ";
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

$maxRows_RsArticulos = 30;
$pageNum_RsArticulos = 0;
if (isset($_GET['pageNum_RsArticulos'])) {
  $pageNum_RsArticulos = $_GET['pageNum_RsArticulos'];
}
$startRow_RsArticulos = $pageNum_RsArticulos * $maxRows_RsArticulos;

$colname_RsArticulos = "-1";
if (isset($_GET['id'])) {
  $colname_RsArticulos = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsArticulos = sprintf("SELECT * FROM subcotizacionarticulo WHERE idsubcotizacion = %s ORDER BY idsubcotizacionarticulo ASC", $colname_RsArticulos);
$query_limit_RsArticulos = sprintf("%s LIMIT %d, %d", $query_RsArticulos, $startRow_RsArticulos, $maxRows_RsArticulos);
$RsArticulos = mysql_query($query_limit_RsArticulos, $tecnocomm) or die(mysql_error());
$row_RsArticulos = mysql_fetch_assoc($RsArticulos);

if (isset($_GET['totalRows_RsArticulos'])) {
  $totalRows_RsArticulos = $_GET['totalRows_RsArticulos'];
} else {
  $all_RsArticulos = mysql_query($query_RsArticulos);
  $totalRows_RsArticulos = mysql_num_rows($all_RsArticulos);
}
$totalPages_RsArticulos = ceil($totalRows_RsArticulos/$maxRows_RsArticulos)-1;



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

$queryString_RsPartExtra = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_RsPartExtra") == false && 
        stristr($param, "totalRows_RsPartExtra") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_RsPartExtra = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_RsPartExtra = sprintf("&totalRows_RsPartExtra=%d%s", $totalRows_RsPartExtra, $queryString_RsPartExtra);

$queryString_RsArticulos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_RsArticulos") == false && 
        stristr($param, "totalRows_RsArticulos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_RsArticulos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_RsArticulos = sprintf("&totalRows_RsArticulos=%d%s", $totalRows_RsArticulos, $queryString_RsArticulos);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsPorce = sprintf("SELECT sum(cantidad) as cant FROM subcotizacionarticulo WHERE idsubcotizacion = %s ORDER BY idsubcotizacionarticulo ASC", $_GET['id']);
$RsPorce = mysql_query($query_RsPorce, $tecnocomm) or die(mysql_error());
$row_RsPorce = mysql_fetch_assoc($RsPorce);
$totalRows_RsPorce = mysql_num_rows($RsPorce);

$maxRows_RsPartExtra = 20;
$pageNum_RsPartExtra = 0;
if (isset($_GET['pageNum_RsPartExtra'])) {
  $pageNum_RsPartExtra = $_GET['pageNum_RsPartExtra'];
}
$startRow_RsPartExtra = $pageNum_RsPartExtra * $maxRows_RsPartExtra;

$ide_RsPartExtra = "-1";
if (isset($_GET['id'])) {
  $ide_RsPartExtra = $_GET['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsPartExtra = sprintf("SELECT *,(select nombre FROM articulo WHERE idarticulo=partidaextra.idarticulo) as nomart, (select marca from articulo where idarticulo=partidaextra.idarticulo) as marca FROM partidaextra where idsubcotizacion=%s", GetSQLValueString($ide_RsPartExtra, "int"));
$query_limit_RsPartExtra = sprintf("%s LIMIT %d, %d", $query_RsPartExtra, $startRow_RsPartExtra, $maxRows_RsPartExtra);
$RsPartExtra = mysql_query($query_limit_RsPartExtra, $tecnocomm) or die(mysql_error());
$row_RsPartExtra = mysql_fetch_assoc($RsPartExtra);

if (isset($_GET['totalRows_RsPartExtra'])) {
  $totalRows_RsPartExtra = $_GET['totalRows_RsPartExtra'];
} else {
  $all_RsPartExtra = mysql_query($query_RsPartExtra);
  $totalRows_RsPartExtra = mysql_num_rows($all_RsPartExtra);
}
$totalPages_RsPartExtra = ceil($totalRows_RsPartExtra/$maxRows_RsPartExtra)-1;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Detalle de Avance</title>
<link href="style2.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
 <script type="text/javascript" src="js/jqueryui.js"></script>
<script language="javascript">

$(document).ready(function(){
					$(".ocultar").click(function(){
										
											id = $(this).attr('id');
											
											if($("#detalle"+id).css('display') == "none"){
												$("#detalle"+id).css("display","block");
											}else{
											$("#detalle"+id).css("display","none");
											}
											
											
											})		   
});

</script>  
<script src="js/valid.js"></script>
<script language="javascript"  src="js/funciones.js"></script>
<style type="text/css">
<!--
.Estilo1 {color: #FFFFFF}
-->
.style1 {
	color: #990000;
	font-size: 10px;
}
.ocultar tbody{
	display:none;
}

</style>
</head>

<body>
<h1> Detalle de Avances</h1>
<div class="submenu"> <a href="nuevoAvance.php?id=<?php echo $_GET['id']; ?>" onclick="NewWindow(this.href,'Nuevo Reporte',600,800,'yes'); return false;"> Nuevo Avance</a> </div>
<div class="buscar"><label><span>Buscar</span><input type="text" name="buscar"></label></div>

<div id="distabla">
 
    <table width="100%" cellpadding="0" cellspacing="0">
      <thead>
        <tr>
          <td colspan="4" align="right"><table border="0">
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
          <td width="21%">Proyecto</td>
      <td width="11%">Fecha</td>
      <td width="11%">Hora</td>
        <td width="57%">Reporte</td>
        </tr>
		<?php } // Show if recordset empty ?>
      </thead>
      <tbody>
      <?php $suma = 0;?>
        <?php do { ?>
          <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
            <td><?php echo $row_rsAvance['proyecto']; ?></td>
            <td><?php echo $row_rsAvance['fecha']; ?></td>
            <td><?php echo $row_rsAvance['hora']; ?></td>
            <td><?php echo $row_rsAvance['reporte']; ?></td>
          </tr>
       <?php } while ($row_rsAvance = mysql_fetch_assoc($rsAvance)); ?>
		   <?php if ($totalRows_rsAvance == 0) { // Show if recordset empty ?>
        <tr>
          <td colspan="4" align="center"> NO HAY AVANCES REPORTADOS </td>
        </tr>
		  <?php } // Show if recordset empty ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="4" align="right">
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
  <h1> Detalle de Avances sobre Partidas</h1>
  <div id="distabla">
 
    <table width="100%" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <thead>
        <tr>
          <td height="30" colspan="4" align="right"><table border="0">
            <tr>
                <td><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, 0, $queryString_RsArticulos); ?>">
                  <?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
                    <img src="images/First.gif" border="0">
              <?php } // Show if not first page ?></a> </td>
              <td><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, max(0, $pageNum_RsArticulos - 1), $queryString_RsArticulos); ?>">
                <?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
                  <img src="images/Previous.gif" border="0">
              <?php } // Show if not first page ?></a> </td>
              <td><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, min($totalPages_RsArticulos, $pageNum_RsArticulos + 1), $queryString_RsArticulos); ?>">
                <?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?>
                  <img src="images/Next.gif" border="0">
              <?php } // Show if not last page ?></a> </td>
              <td><a href="<?php printf("%s?pageNum_rsAvance=%d%s", $currentPage, $totalPages_rsAvance, $queryString_rsAvance); ?>">
			  <?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?><img src="images/Last.gif" border="0"><?php } // Show if not last page ?></a> </td>
            </tr>
          </table></td>
        </tr>
		
        <tr>
          <td>Partida</td>
          <td>Marca</td>
          <td>Cantidad a Instalar </td>
          <td>Avance</td>
        </tr>
      </thead>
      <tbody>
	 <?php $j = 0;?>
        <?php do {
			
			$j++;
			$colname_RsDetalle = "-1";
if (isset($row_RsArticulos['idsubcotizacionarticulo'])) {
  $colname_RsDetalle = (get_magic_quotes_gpc()) ? $row_RsArticulos['idsubcotizacionarticulo'] : addslashes($row_RsArticulos['idsubcotizacionarticulo']);
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsDetalle = sprintf("SELECT * FROM subcotizacionavance WHERE idarticulo = %s", $colname_RsDetalle);
$RsDetalle = mysql_query($query_RsDetalle, $tecnocomm) or die(mysql_error());
$row_RsDetalle = mysql_fetch_assoc($RsDetalle);
$totalRows_RsDetalle = mysql_num_rows($RsDetalle);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsSuma = sprintf("SELECT sum(cantidad) as cant FROM subcotizacionavance WHERE idarticulo = %s", $row_RsArticulos['idsubcotizacionarticulo']);
$RsSuma = mysql_query($query_RsSuma, $tecnocomm) or die(mysql_error());
$row_RsSuma = mysql_fetch_assoc($RsSuma);
$totalRows_RsSuma = mysql_num_rows($RsSuma);


			?>
            <table width="100%" border="0" class="ocultar" id="table<?php echo $j;?>">
  <thead class="f">
          <tr>
              <td width="60%"><?php if($row_RsArticulos['cantidad']-$row_RsSuma['cant']==0){?><img src="images/rojo.gif" width="10" height="10" /><? } if($row_RsArticulos['cantidad']-$row_RsSuma['cant']>0){?><img src="images/amarillo.gif" width="10" height="10" /><? }if($row_RsSuma['cant']>$row_RsArticulos['cantidad']){?><img src="images/verde.gif" width="10" height="10" /><? }?><?php echo $row_RsArticulos['descri']; ?></td>
              <td width="10%"><?php echo $row_RsArticulos['marca1']; ?></td>
              <td  width="5%"><?php echo $row_RsArticulos['cantidad']; ?></td>
              <td  width="25%">Instalados <?php if ($row_RsSuma['cant']!=''){echo $row_RsSuma['cant'];}else{echo '0';}?> de <?php echo $row_RsArticulos['cantidad']; ?><a href="nuevoAvancePartida.php?idart=<?php echo $row_RsArticulos['idsubcotizacionarticulo']; ?>" onclick="NewWindow(this.href,'Nuevo Reporte',600,800,'yes'); return false;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/Agregar.png" width="24" height="24" title="Agregar" border="0" /></a></td>
        </tr>
		</thead>	
		   <?php $suma += $row_RsSuma['cant'];?>
		             
  <tbody id="detalletable<?php echo $j;?>">           
  <tr  <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
     <td >Fecha</td>
    <td >Cantidad Instalada</td>
    <td>Comentario</td>
    <td></td>
  </tr>

  
  <?php do { ?>
  <tr>
    <td><?php echo $row_RsDetalle['fecha']; ?></td>
    <td><?php echo $row_RsDetalle['cantidad']; ?></td>
    <td><?php echo $row_RsDetalle['comentario']; ?></td>
    <td></td>
  </tr>
   <?php } while ($row_RsDetalle = mysql_fetch_assoc($RsDetalle)); ?>
   </tbody>
  
</table>

   
			 
			 
	<?php } while ($row_RsArticulos = mysql_fetch_assoc($RsArticulos)); ?>
     
	  </tbody>
	  
      <tfoot>
        <tr>
          <td height="30" colspan="4" align="right">
            <table border="0">
              <tr>
              <td><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, 0, $queryString_RsArticulos); ?>">
                  <?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
                    <img src="images/First.gif" border="0">
                <?php } // Show if not first page ?></a> </td>
              <td><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, max(0, $pageNum_RsArticulos - 1), $queryString_RsArticulos); ?>">
                <?php if ($pageNum_RsArticulos > 0) { // Show if not first page ?>
                  <img src="images/Previous.gif" border="0">
                <?php } // Show if not first page ?></a> </td>
              <td><a href="<?php printf("%s?pageNum_RsArticulos=%d%s", $currentPage, min($totalPages_RsArticulos, $pageNum_RsArticulos + 1), $queryString_RsArticulos); ?>">
                <?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?>
                  <img src="images/Next.gif" border="0">
                <?php } // Show if not last page ?></a> </td>
              <td><a href="<?php printf("%s?pageNum_rsAvance=%d%s", $currentPage, $totalPages_rsAvance, $queryString_rsAvance); ?>">
			  <?php if ($pageNum_RsArticulos < $totalPages_RsArticulos) { // Show if not last page ?><img src="images/Last.gif" border="0"><?php } // Show if not last page ?></a> </td>
              </tr>
          </table></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <!-- partidas extras ------------------------------------------------------------------------------>
   <h1> Detalle de Avances sobre Partidas Extras <a href="partextra_buscar_articulo.php?idsubcotizacion=<?php echo $_GET['id']; ?>" onclick="NewWindow(this.href,'Nueva Partida Extra',600,800,'yes'); return false;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/Agregar.png" width="24" height="24" title="Agregar Partida Extra" border="0" /></a></h1>
<div id="distabla">
  
    <table width="100%" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <thead>
        <tr>
          <td height="30" colspan="4" align="right"><table border="0">
              <tr>
                <td><a href="<?php printf("%s?pageNum_RsPartExtra=%d%s", $currentPage, 0, $queryString_RsPartExtra); ?>">
                  <?php if ($pageNum_RsPartExtra > 0) { // Show if not first page ?>
                    <img src="images/First.gif" border="0" />
                    <?php } // Show if not first page ?>
                            </a> </td>
                <td><a href="<?php printf("%s?pageNum_RsPartExtra=%d%s", $currentPage, max(0, $pageNum_RsPartExtra - 1), $queryString_RsPartExtra); ?>">
                  <?php if ($pageNum_RsPartExtra > 0) { // Show if not first page ?>
                    <img src="images/Previous.gif" border="0" />
                    <?php } // Show if not first page ?>
                            </a> </td>
                <td><a href="<?php printf("%s?pageNum_RsPartExtra=%d%s", $currentPage, min($totalPages_RsPartExtra, $pageNum_RsPartExtra + 1), $queryString_RsPartExtra); ?>">
                  <?php if ($pageNum_RsPartExtra < $totalPages_RsPartExtra) { // Show if not last page ?>
                    <img src="images/Next.gif" border="0" />
                    <?php } // Show if not last page ?>
                            </a> </td>
                <td><a href="<?php printf("%s?pageNum_RsPartExtra=%d%s", $currentPage, $totalPages_RsPartExtra, $queryString_RsPartExtra); ?>">
                  <?php if ($pageNum_RsPartExtra < $totalPages_RsPartExtra) { // Show if not last page ?>
                    <img src="images/Last.gif" border="0" />
                    <?php } // Show if not last page ?>
                            </a></td>
              </tr>
                        </table></td>
        </tr>
        <tr>
          <td>Partida</td>
          <td>Marca</td>
          <td>Cantidad a Instalar </td>
          <td>Comentario</td>
        </tr>
            </thead>
      <tbody>
        <?php do { ?>
            
          <tr>
            <td width="46%" valign="top"><?php echo $row_RsPartExtra['nomart']; ?></td>
            <td width="11%" valign="top"><?php echo $row_RsPartExtra['marca']; ?></td>
            <td  width="14%" valign="top"><?php echo $row_RsPartExtra['cantidad_a']; ?></td>
            <td  width="29%" valign="top"><?php echo $row_RsPartExtra['comentario']; ?></td>
          </tr>
          <?php } while ($row_RsPartExtra = mysql_fetch_assoc($RsPartExtra)); ?>
          <?php if ($totalRows_RsPartExtra == 0) { // Show if recordset empty ?>
          <tr>
            <td colspan="4" align="center" valign="top">No hay partidas extras</td>
            </tr>
             <?php } // Show if recordset empty ?>
      </tbody>      
      <tfoot>
        <tr>
          <td height="30" colspan="4" align="right"><table border="0">
              <tr>
                <td><a href="<?php printf("%s?pageNum_RsPartExtra=%d%s", $currentPage, 0, $queryString_RsPartExtra); ?>">
                  <?php if ($pageNum_RsPartExtra > 0) { // Show if not first page ?>
                    <img src="images/First.gif" border="0" />
                    <?php } // Show if not first page ?>
                            </a> </td>
                <td><a href="<?php printf("%s?pageNum_RsPartExtra=%d%s", $currentPage, max(0, $pageNum_RsPartExtra - 1), $queryString_RsPartExtra); ?>">
                  <?php if ($pageNum_RsPartExtra > 0) { // Show if not first page ?>
                    <img src="images/Previous.gif" border="0" />
                    <?php } // Show if not first page ?>
                            </a> </td>
                <td><a href="<?php printf("%s?pageNum_RsPartExtra=%d%s", $currentPage, min($totalPages_RsPartExtra, $pageNum_RsPartExtra + 1), $queryString_RsPartExtra); ?>">
                  <?php if ($pageNum_RsPartExtra < $totalPages_RsPartExtra) { // Show if not last page ?>
                    <img src="images/Next.gif" border="0" />
                    <?php } // Show if not last page ?>
                            </a> </td>
                <td><a href="<?php printf("%s?pageNum_RsPartExtra=%d%s", $currentPage, $totalPages_RsPartExtra, $queryString_RsPartExtra); ?>">
                  <?php if ($pageNum_RsPartExtra < $totalPages_RsPartExtra) { // Show if not last page ?>
                    <img src="images/Last.gif" border="0" />
                    <?php } // Show if not last page ?>
                            </a></td>
              </tr>
                        </table></td>
        </tr>
            </tfoot>
      </table>
   
</div>
  

<h1> Porcentaje de Avance con respecto a cantidad de partidas Cotizadas: <?php echo @round(($suma*100)/$row_RsPorce['cant'],2)?>%</h1>
  </body>
</html>
<?php
mysql_free_result($rsAvance);

mysql_free_result($RsArticulos);

mysql_free_result($RsDetalle);

mysql_free_result($RsPartExtra);
?>
