<?php require_once('Connections/tecnocomm.php'); ?>
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

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsProductos = "SELECT * FROM articulo ORDER BY idarticulo ASC";
$rsProductos = mysql_query($query_rsProductos, $tecnocomm) or die(mysql_error());
$row_rsProductos = mysql_fetch_assoc($rsProductos);
$totalRows_rsProductos = mysql_num_rows($rsProductos);


header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=catalago.xls");

foreach($row_rsProductos as $nombre => $row){
		$line  = $line.$nombre."\t";
	}
echo $line."\n";

do{

	$line = "";
	foreach($row_rsProductos as $row){
		$line  = $line.$row."\t";
	}
	
	echo $line."\n";

}while($row_rsProductos = mysql_fetch_assoc($rsProductos));

mysql_free_result($rsProductos);
?>
