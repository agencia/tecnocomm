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

$colname_rsCatalago = "-1";
if (isset($_POST['buscar'])) {
  $colname_rsCatalago = $_POST['buscar'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsCatalago = sprintf("SELECT * FROM articulo WHERE nombre LIKE %s  OR codigo LIKE %s OR marca LIKE %s ORDER BY nombre LIMIT 30",					
							GetSQLValueString("%" . $colname_rsCatalago . "%", "text"),
							GetSQLValueString("%" . $colname_rsCatalago . "%", "text"),
							GetSQLValueString("%" . $colname_rsCatalago . "%", "text"));
$rsCatalago = mysql_query($query_rsCatalago, $tecnocomm) or die(mysql_error());
$row_rsCatalago = mysql_fetch_assoc($rsCatalago);
$totalRows_rsCatalago = mysql_num_rows($rsCatalago);

switch($_POST['opc']){
	case "material": $link = "ip.material.add.concepto.php?".$_POST['vgo'];
	break;
	case "levantamiento": $link = "ip.levantamiento.add.concepto.php?".$_POST['vgo'];
	break;
}

?>


                            
                             
                            
<div class="distabla">
<table width="85%" align="center" cellpadding="2" cellspacing="0">
<thead>
<tr>
<td width="50%">Descripcion</td>
<td width="15%">Marca</td>
<td width="15%">Codigo</td>
<td width="10%" align="right">Precio</td>
<td width="5%" align="right">Modificar</td>
<td width="5%" align="right">Opciones</td>
</tr>
</thead>
<?php do{ ?>
<tr>
<td width="50%" valign="top"><?php echo $row_rsCatalago['nombre']; ?></td>
<td width="15%" valign="top"><?php echo $row_rsCatalago['marca']; ?></td>
<td width="15%" valign="top"><?php echo $row_rsCatalago['codigo']; ?></td>
<td width="10%" align="right" valign="top"><?php echo $row_rsCatalago['precio']; ?></td>
<td width="5%" align="right" valign="top"><img src="images/Edit.png" align="Editar" title="Editar Concepto" border="0"></td>
<td width="5%" align="right" valign="top"><a href="<?php echo $link;?>&idarticulo=<?php echo $row_rsCatalago['idarticulo']; ?>" class="popup"><img src="images/Checkmark.png" alt="agregar" title="Agregar Concepto" border="0"></a></td>
</tr>
<?php }while($row_rsCatalago = mysql_fetch_assoc($rsCatalago));?>
</table>
</div>
<?
mysql_free_result($rsCatalago);
?>
