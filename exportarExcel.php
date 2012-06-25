<?php 
require_once("excel.php");
require_once("excel-ext.php");
?>
<?php
require_once('Connections/tecnocomm.php'); 
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsQuery = $_GET['query'];
$rsQuery = mysql_query($query_rsQuery, $tecnocomm) or die(mysql_error());
$totalRows_rsQuery = mysql_num_rows($rsQuery);

while($row_rsQuery = mysql_fetch_assoc($rsQuery)) {

    $data[] = $row_rsQuery;

}

createExcel("excel1.xls", $data);
exit;
?>