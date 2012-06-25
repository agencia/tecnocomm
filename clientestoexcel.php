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
$query_rsCliente = "SELECT * FROM cliente ORDER BY nombre ASC";
$rsCliente = mysql_query($query_rsCliente, $tecnocomm) or die(mysql_error());
$row_rsCliente = mysql_fetch_assoc($rsCliente);
$totalRows_rsCliente = mysql_num_rows($rsCliente);



header("Content-Type: application/vnd.ms-excel");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("content-disposition: attachment;filename=clientes.xls");

do{
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsContacto = "SELECT * FROM contactoclientes WHERE idcliente = ".$row_rsCliente['idcliente']." ORDER BY nombre ASC";
$rsContacto = mysql_query($query_rsContacto, $tecnocomm) or die(mysql_error());
$row_rsContacto = mysql_fetch_assoc($rsContacto);
$totalRows_rsContacto = mysql_num_rows($rsContacto);
echo "CLIENTE\n";

	$line = "";
foreach($row_rsCliente as $nombre => $row){
		echo   $nombre.": \t".$row;
		echo "\n";
	}


do{
if(is_array($row_rsContacto)){	
echo $line."CONTACTOS\n";
foreach($row_rsContacto as $nombre => $row){
		echo $nombre.":\t".$row;
		echo "\n";
	}
}	
	
}while($row_rsContacto = mysql_fetch_assoc($rsContacto));

	echo "\n";

}while($row_rsCliente = mysql_fetch_assoc($rsCliente));





mysql_free_result($rsCliente);

mysql_free_result($rsContacto);
?>