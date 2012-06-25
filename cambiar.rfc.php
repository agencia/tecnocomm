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

$maxRows_Busqueda = 20;
$pageNum_Busqueda = 0;
if (isset($_GET['pageNum_Busqueda'])) {
  $pageNum_Busqueda = $_GET['pageNum_Busqueda'];
}
$startRow_Busqueda = $pageNum_Busqueda * $maxRows_Busqueda;

$colname_Busqueda = "-1";
if (isset($_GET['buscar'])) {
  $colname_Busqueda = $_GET['buscar'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Busqueda = sprintf("SELECT rfc, cliente.nombre, cliente.abreviacion, cliente.idcliente FROM cliente WHERE nombre = %s OR cliente.nombre like %s OR cliente.abreviacion like %s OR cliente.rfc like %s ", GetSQLValueString($colname_Busqueda, "text"),GetSQLValueString("%" . $colname_Busqueda . "%", "text"),GetSQLValueString("%" . $colname_Busqueda . "%", "text"),GetSQLValueString("%" . $colname_Busqueda . "%", "text"));
$query_limit_Busqueda = sprintf("%s LIMIT %d, %d", $query_Busqueda, $startRow_Busqueda, $maxRows_Busqueda);
$Busqueda = mysql_query($query_limit_Busqueda, $tecnocomm) or die(mysql_error());
$row_Busqueda = mysql_fetch_assoc($Busqueda);

if (isset($_GET['totalRows_Busqueda'])) {
  $totalRows_Busqueda = $_GET['totalRows_Busqueda'];
} else {
  $all_Busqueda = mysql_query($query_Busqueda);
  $totalRows_Busqueda = mysql_num_rows($all_Busqueda);
}
$totalPages_Busqueda = ceil($totalRows_Busqueda/$maxRows_Busqueda)-1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Buscar Clientes</title>
<script language="javascript" src="js/jquery.js"></script>
<link href="style2.css" rel="stylesheet" type="text/css" />
<link href="css/redmond/jquery.css"  rel="stylesheet" type="text/css"/>
<style>
.titleTabla td{
background:#00427E  url(images/titulo.gif) repeat-x top;
color:#FFFFFF;
}
</style>
<script>
$(function(){
	$(".select").live("click",function (){ 
		window.opener.document.myform.rfct.value = $(this).attr("rfc");
		window.opener.document.myform.idcliente.value = $(this).attr("href");
		window.close();
	 	return false;
   	 })});
</script>
</head>

<body>
<h1>Busque un cliente</h1>
<form action="cambiar.rfc.php">
<label>Cliente: <input type="text" name="buscar" /></label><input type="submit" value="Buscar" />
</form>
<?php if ($totalRows_Busqueda > 0) { // Show if recordset not empty ?>
  <table width="600" border="0" cellspacing="1" cellpadding="1">
    <tr class="titleTabla">
      <td width="355">Nombre</td>
      <td width="200">R.F.C</td>
      <td width="35">&nbsp;</td>
    </tr>
    <?php $a=0; ?>
    <?php do { ?>
      <tr <?php echo ($a%2) ? ' bgcolor="#F0F0F0"' : ''; ?> onmouseover="this.style.backgroundColor = '#E2EBF4';" onmouseout="this.style.backgroundColor = '';">
        <td><?php echo $row_Busqueda['nombre']; ?> (<?php echo $row_Busqueda['abreviacion']; ?>)</td>
        <td><?php echo $row_Busqueda['rfc']; ?></td>
        <td><a href="<?php echo $row_Busqueda['idcliente']; ?>" rfc="<?php echo $row_Busqueda['rfc']; ?>" class="select"><img src="images/state3.png" border="0" /></a></td>
      </tr>
      <?php $a++; ?>
      <?php } while ($row_Busqueda = mysql_fetch_assoc($Busqueda)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_Busqueda == 0) { // Show if recordset empty ?>
    <p>No hay resultados
      <?php } // Show if recordset empty ?>
</body>
</html>
<?php
mysql_free_result($Busqueda);
?>
