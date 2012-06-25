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
 
function valida($tabla,$idname,$id){
include('Connections/tecnocomm.php');
	
	$SQL=sprintf("SELECT * FROM %s WHERE %s=%s",$tabla,$idname,GetSQLValueString($id,"int"));
	//echo $SQL;
	mysql_select_db($database_tecnocomm, $tecnocomm);
	$data = mysql_query($SQL,$tecnocomm) or die (mysql_error());
	$pointer = 0;
	$countRows = mysql_num_rows($data);
	$countFields = mysql_num_fields($data);
	
	$rojo=0;
	for($j=0; $j< $countFields;$j++){
			mysql_data_seek($data,0);
			$value = mysql_fetch_array($data);
			if($value[$j]==""){
				$rojo++;
			}
	}
	//mysql_free_result($value);
	if($rojo>0){
		return "<img src=\"images/rojo.gif\" alt=\"nueva\" width=\"10\" height=\"10\" border=\"0\" align=\"middle\" title=\"FALTAN DATOS\" />   ";
	}
	else{
		return "<img src=\"images/verde.gif\" alt=\"nueva\" width=\"10\" height=\"10\" border=\"0\" align=\"middle\" title=\"DATOS CORRECTOS\" />   ";
	}

}


?>