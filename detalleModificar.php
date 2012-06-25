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

if ((isset($_POST['identificador'])) && ($_POST['identificador'] != "") && (isset($_POST['del']))) {
  $deleteSQL = sprintf("DELETE FROM detalleproductoorden WHERE identificador=%s",
                       GetSQLValueString($_POST['identificador'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($deleteSQL, $tecnocomm) or die(mysql_error());
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$_POST['fechacompra']= $_POST['ano']."-".$_POST['mes']."-".$_POST['dia'];
$_POST['fechaentrega']= $_POST['ano2']."-".$_POST['mes2']."-".$_POST['dia2'];
  $insertSQL = sprintf("INSERT INTO detalleproductoorden (iddetalleorden, idproveedor, cantidadsolicitada, fechacompra, fechaentrega, unidad, costo) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['iddetalleorden'], "int"),
                       GetSQLValueString($_POST['idproveedor'], "int"),
                       GetSQLValueString($_POST['cantidadsolicitada'], "double"),
                       GetSQLValueString($_POST['fechacompra'], "date"),
                       GetSQLValueString($_POST['fechaentrega'], "date"),
                       GetSQLValueString($_POST['unidad'], "text"),
                       GetSQLValueString($_POST['costo'], "double"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());
}



if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "actualizarCantidad")) {
  $updateSQL = sprintf("UPDATE detalleproductoorden SET cantidadsurtida=%s WHERE identificador=%s",
                       GetSQLValueString($_POST['cantidad'], "double"),
                       GetSQLValueString($_POST['identificador'], "int"));

  mysql_select_db($database_tecnocomm, $tecnocomm);
  $Result1 = mysql_query($updateSQL, $tecnocomm) or die(mysql_error());
}


$colname_rsPartida = "-1";
if (isset($_GET['idpartida'])) {
  $colname_rsPartida = $_GET['idpartida'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartida = sprintf("SELECT d.*,p.nombrecomercial AS proveedor FROM detalleproductoorden d LEFT JOIN proveedor p ON d.idproveedor = p.idproveedor WHERE d.iddetalleorden = %s", GetSQLValueString($colname_rsPartida, "int"));
$rsPartida = mysql_query($query_rsPartida, $tecnocomm) or die(mysql_error());
$row_rsPartida = mysql_fetch_assoc($rsPartida);
$totalRows_rsPartida = mysql_num_rows($rsPartida);

$colname_rsPar = "-1";
if (isset($_GET['idpartida'])) {
  $colname_rsPar = $_GET['idpartida'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPar = sprintf("SELECT d.*,sba.descri,a.medida FROM detalleorden d,subcotizacionarticulo sba,articulo a WHERE identificador = %s AND sba.idsubcotizacionarticulo =d.idpartida AND sba.idarticulo = a.idarticulo", GetSQLValueString($colname_rsPar, "int"));
$rsPar = mysql_query($query_rsPar, $tecnocomm) or die(mysql_error());
$row_rsPar = mysql_fetch_assoc($rsPar);
$totalRows_rsPar = mysql_num_rows($rsPar);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Detalle de Proveedores</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="multibox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/mootools2.js"></script>
<script type="text/javascript" src="js/overlay.js"></script>
<script type="text/javascript" src="js/multibox.js"></script>
</head>

<body>
<table width="736" border="0" cellpadding="0" cellspacing="0" class="wrapper" align="center">
  <!--DWLayoutTable-->
  <tr>
    <td height="28" colspan="11" valign="top" class="titulos">Detalle de Partida</td>
  </tr>
  <tr>
    <td width="28" height="17"></td>
    <td width="28"></td>
    <td width="72"></td>
    <td width="20"></td>
    <td width="196"></td>
    <td width="108"></td>
    <td width="64"></td>
    <td width="71"></td>
    <td width="38"></td>
    <td width="73"></td>
    <td width="36"></td>
  </tr>
  
  <tr>
    <td height="20" colspan="3" align="right" valign="top">Descripcion:</td>
    <td>&nbsp;</td>
    <td colspan="3" valign="top"><?php echo $row_rsPar['descri']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td height="23" colspan="3" align="right" valign="top">Cantidad:</td>
    <td>&nbsp;</td>
    <td colspan="2" valign="top"><?php echo $row_rsPar['cantidad']; ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td height="23" colspan="3" align="right" valign="top">U. Medida:</td>
    <td></td>
    <td colspan="2" valign="top"><?php echo $row_rsPar['medida']; ?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="13"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="21"></td>
    <td>&nbsp;</td>
    <td colspan="3" valign="top">Compras a Proveedores</td>
    <td>&nbsp;</td>
    <td colspan="3" valign="top"><a href="nuevaPartida.php?idpartida=<?php echo $row_rsPar['identificador']; ?>&cantidad=<?php echo ($row_rsPar['cantidad']-$row_rsPar['almacen']); ?>&umedida=<?php echo $row_rsPar['medida']; ?>" rel="width:400,height:300,ajax:true,showControls:true," id="mb10" class="mb" title="AGREGAR NUEVO PROVEDOR">Agregar Proveedor</a>
      <div class="multiBoxDesc mb10"></div></td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
  
  
  
  
  

  
  
  <tr>
    <td height="148">&nbsp;</td>
    <td colspan="9" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="resaltarTabla">
      <!--DWLayoutTable-->
      <tr class="titleTabla">
        <td width="62" height="20" valign="top">Opciones</td>
          <td width="189" valign="top">Proveedor</td>
          <td width="55" valign="top">Req.</td>
          <td width="55" valign="top">Entreg.</td>
          <td width="60" valign="top">P.Unitario</td>
          <td width="75" valign="top">Importe</td>
          <td width="102" valign="top">Fecha Requerido</td>
          <td width="103" valign="top">Fecha Entregado</td>
        </tr>
      <tr>
        <td height="126" colspan="8" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <!--DWLayoutTable-->
          <?php if ($totalRows_rsPartida > 0) { // Show if recordset not empty ?>
		  <?php do { ?>
            <tr>
                <td width="57" height="21" align="right" valign="top"><a href="eliminarDetalle.php?identificador=<?php echo $row_rsPartida['identificador'];?>" rel="width:350,height:110,ajax:true,showControls:true," id="mb10" class="mb" title="Eliminar"><img src="images/eliminar.gif" width="19" height="19" title="Eliminar" alt="Eliminar"/></a></td>
                <td width="189" valign="top"><?php echo $row_rsPartida['proveedor'];?></td>
                <td width="55" valign="top"><?php echo $row_rsPartida['cantidadsolicitada'];?></td>
                <td width="56" valign="top">
                  <a href="detalleActualizar.php?identificador=<?php echo $row_rsPartida['identificador'];?>" rel="width:350,height:110,ajax:true,showControls:true," id="mb10" class="mb" title="Actualizar Cantidad"><?php $val = ( $row_rsPartida['cantidadsurtida'] > 0)?$row_rsPartida['cantidadsurtida']:0; echo $val;?>
                  </a>			  </td>
                <td width="60" valign="top"><?php echo $row_rsPartida['costo'];?></td>
                <td width="75" valign="top"><?php echo $row_rsPartida['costo']*$row_rsPartida['cantidadsolicitada'];?></td>
                <td width="100" valign="top"><?php echo $row_rsPartida['fechacompra'];?></td>
                <td width="105" valign="top"><?php echo $row_rsPartida['fechaentrega'];?></td>
</tr>
            <?php } while ($row_rsPartida = mysql_fetch_assoc($rsPartida)); ?><?php } // Show if recordset not empty ?>
          <tr>
            <td height="105">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>        </td>
        </tr>
      
      
    </table></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="17"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td height="21"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="2" valign="top"><a href="close.php">Aceptar</a></td>
    <td></td>
  </tr>
  <tr>
    <td height="16"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
<script type="text/javascript">
			var box = {};
			window.addEvent('domready', function(){
				box = new MultiBox('mb', {descClassName: 'multiBoxDesc', useOverlay: true});
			});
		</script>
</body>
</html>
<?php
mysql_free_result($rsPartida);

mysql_free_result($rsPar);
?>
