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




function cadena($id){
$num=$id;

$lon=strlen($num);

if($lon==1){$cad="000".$num;}
if($lon==2){$cad="00".$num;}
if($lon==3){$cad="0".$num;}
if($lon==4){$cad=$num;}

return $cad;
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Rs1 = "select * from cliente";
$Rs1 = mysql_query($query_Rs1, $tecnocomm) or die(mysql_error());
$row_Rs1 = mysql_fetch_assoc($Rs1);
$totalRows_Rs1 = mysql_num_rows($Rs1);
do { 

$c=cadena($row_Rs1['idcliente']);
		
	  $updateSQL = sprintf("UPDATE cliente SET clave=%s WHERE idcliente=%s",
                       GetSQLValueString('CL'.$c, "text"),
                       GetSQLValueString($row_Rs1['idcliente'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

      } while ($row_Rs1 = mysql_fetch_assoc($Rs1)); 
mysql_free_result($Rs1);	  
	  ////////////////////////////////////////////////
	  
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Rs1 = "select * from activos";
$Rs1 = mysql_query($query_Rs1, $tecnocomm) or die(mysql_error());
$row_Rs1 = mysql_fetch_assoc($Rs1);
$totalRows_Rs1 = mysql_num_rows($Rs1);
do { 

$c=cadena($row_Rs1['id']);
		
	  $updateSQL = sprintf("UPDATE activos SET clave=%s WHERE id=%s",
                       GetSQLValueString('AC'.$c, "text"),
                       GetSQLValueString($row_Rs1['id'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

      } while ($row_Rs1 = mysql_fetch_assoc($Rs1)); 

mysql_free_result($Rs1);

/////////////////////////////////////////////////////////////////

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Rs1 = "select * from articulo";
$Rs1 = mysql_query($query_Rs1, $tecnocomm) or die(mysql_error());
$row_Rs1 = mysql_fetch_assoc($Rs1);
$totalRows_Rs1 = mysql_num_rows($Rs1);
do { 

$c=cadena($row_Rs1['idarticulo']);
		
	  $updateSQL = sprintf("UPDATE articulo SET clave=%s WHERE idarticulo=%s",
                       GetSQLValueString('CO'.$c, "text"),
                       GetSQLValueString($row_Rs1['idarticulo'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

      } while ($row_Rs1 = mysql_fetch_assoc($Rs1)); 

mysql_free_result($Rs1);

/////////////////////////////////////////////////////////////////

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Rs1 = "select * from bancos";
$Rs1 = mysql_query($query_Rs1, $tecnocomm) or die(mysql_error());
$row_Rs1 = mysql_fetch_assoc($Rs1);
$totalRows_Rs1 = mysql_num_rows($Rs1);
do { 

$c=cadena($row_Rs1['idbanco']);
		
	  $updateSQL = sprintf("UPDATE bancos SET clave=%s WHERE idbanco=%s",
                       GetSQLValueString('BA'.$c, "text"),
                       GetSQLValueString($row_Rs1['idbanco'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

      } while ($row_Rs1 = mysql_fetch_assoc($Rs1)); 

mysql_free_result($Rs1);

/////////////////////////////////////////////////////////////////

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Rs1 = "select * from empleado";
$Rs1 = mysql_query($query_Rs1, $tecnocomm) or die(mysql_error());
$row_Rs1 = mysql_fetch_assoc($Rs1);
$totalRows_Rs1 = mysql_num_rows($Rs1);
do { 

$c=cadena($row_Rs1['idempleado']);
		
	  $updateSQL = sprintf("UPDATE empleado SET clave=%s WHERE idempleado=%s",
                       GetSQLValueString('EM'.$c, "text"),
                       GetSQLValueString($row_Rs1['idempleado'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

      } while ($row_Rs1 = mysql_fetch_assoc($Rs1)); 

mysql_free_result($Rs1);


/////////////////////////////////////////////////////////////////

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Rs1 = "select * from herramienta";
$Rs1 = mysql_query($query_Rs1, $tecnocomm) or die(mysql_error());
$row_Rs1 = mysql_fetch_assoc($Rs1);
$totalRows_Rs1 = mysql_num_rows($Rs1);
do { 

$c=cadena($row_Rs1['id']);
		
	  $updateSQL = sprintf("UPDATE herramienta SET clave=%s WHERE id=%s",
                       GetSQLValueString('HE'.$c, "text"),
                       GetSQLValueString($row_Rs1['id'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

      } while ($row_Rs1 = mysql_fetch_assoc($Rs1)); 

mysql_free_result($Rs1);

/////////////////////////////////////////////////////////////////

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Rs1 = "select * from subcontratistas";
$Rs1 = mysql_query($query_Rs1, $tecnocomm) or die(mysql_error());
$row_Rs1 = mysql_fetch_assoc($Rs1);
$totalRows_Rs1 = mysql_num_rows($Rs1);
do { 

$c=cadena($row_Rs1['id']);
		
	  $updateSQL = sprintf("UPDATE subcontratistas SET clave=%s WHERE id=%s",
                       GetSQLValueString('SU'.$c, "text"),
                       GetSQLValueString($row_Rs1['id'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

      } while ($row_Rs1 = mysql_fetch_assoc($Rs1)); 

mysql_free_result($Rs1);

/////////////////////////////////////////////////////////////////

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_Rs1 = "select * from proveedor";
$Rs1 = mysql_query($query_Rs1, $tecnocomm) or die(mysql_error());
$row_Rs1 = mysql_fetch_assoc($Rs1);
$totalRows_Rs1 = mysql_num_rows($Rs1);
do { 

$c=cadena($row_Rs1['idproveedor']);
		
	  $updateSQL = sprintf("UPDATE proveedor SET clave=%s WHERE idproveedor=%s",
                       GetSQLValueString('PR'.$c, "text"),
                       GetSQLValueString($row_Rs1['idproveedor'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());

      } while ($row_Rs1 = mysql_fetch_assoc($Rs1)); 

mysql_free_result($Rs1);
?>
