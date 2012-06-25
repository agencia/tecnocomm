<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php');?>
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
$colname_rsPartidas = "-1";
if (isset($_GET['idip'])) {
  $colname_rsPartidas = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT s.* FROM subcotizacionarticulo s RIGHT JOIN ip i ON s.idsubcotizacion = i.cotizacion WHERE i.idip = %s ORDER BY s.idsubcotizacionarticulo ASC", GetSQLValueString($colname_rsPartidas, "int"));
$rsPartidas = mysql_query($query_rsPartidas, $tecnocomm) or die(mysql_error());
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);
$totalRows_rsPartidas = mysql_num_rows($rsPartidas);

$colname_rsNumCapura = "-1";
if (isset($_GET['idip'])) {
  $colname_rsNumCapura = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsNumCapura = sprintf("SELECT * FROM ip WHERE idip = %s", GetSQLValueString($colname_rsNumCapura, "int"));
$rsNumCapura = mysql_query($query_rsNumCapura, $tecnocomm) or die(mysql_error());
$row_rsNumCapura = mysql_fetch_assoc($rsNumCapura);
$totalRows_rsNumCapura = mysql_num_rows($rsNumCapura);

$colname_rsNivelUsuario = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname_rsNivelUsuario = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsNivelUsuario = sprintf("SELECT a.idlink FROM usuarios u JOIN nombres_accesos na ON u.responsabilidad = na.id JOIN autorizacion a ON a.nivel = na.id WHERE (a.idlink =25 OR a.idlink =26) AND u.id = %s", GetSQLValueString($colname_rsNivelUsuario, "int"));
$rsNivelUsuario = mysql_query($query_rsNivelUsuario, $tecnocomm) or die(mysql_error());
$row_rsNivelUsuario = mysql_fetch_assoc($rsNivelUsuario);
$totalRows_rsNivelUsuario = mysql_num_rows($rsNivelUsuario);

$colname_rsIsLider = "-1";
if (isset($_GET['idip'])) {
  $colname_rsIsLider = $_GET['idip'];
}
$colname2_rsIsLider = "-1";
if (isset($_SESSION['MM_Userid'])) {
  $colname2_rsIsLider = $_SESSION['MM_Userid'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsIsLider = sprintf("SELECT * FROM proyecto_personal pp WHERE idip = %s AND idusuario = %s AND estado = 1 AND rol = 3", GetSQLValueString($colname_rsIsLider, "int"),GetSQLValueString($colname2_rsIsLider, "int"));
$rsIsLider = mysql_query($query_rsIsLider, $tecnocomm) or die(mysql_error());
$row_rsIsLider = mysql_fetch_assoc($rsIsLider);
$totalRows_rsIsLider = mysql_num_rows($rsIsLider);

$colname_rsMaterial = "-1";
if (isset($_GET['idip'])) {
  $colname_rsMaterial = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMaterial = sprintf("SELECT * FROM material WHERE idip = %s ORDER BY numero ASC", GetSQLValueString($colname_rsMaterial, "int"));
$rsMaterial = mysql_query($query_rsMaterial, $tecnocomm) or die(mysql_error());
$row_rsMaterial = mysql_fetch_assoc($rsMaterial);
$totalRows_rsMaterial = mysql_num_rows($rsMaterial);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsUsuarios = "SELECT * FROM usuarios ORDER BY id ASC";
$rsUsuarios = mysql_query($query_rsUsuarios, $tecnocomm) or die(mysql_error());
$row_rsUsuarios = mysql_fetch_assoc($rsUsuarios);
$totalRows_rsUsuarios = mysql_num_rows($rsUsuarios);



do{
	
	$material[$row_rsMaterial['idsubcotizacion']][$row_rsMaterial['numero']] = $row_rsMaterial['cantidad'];
	$capturo[$row_rsMaterial['numero']] = $row_rsMaterial['idusuario'];
	$confirmo[$row_rsMaterial['numero']] = $row_rsMaterial['idusuario_confirmo'];
	$entrego[$row_rsMaterial['numero']] = $row_rsMaterial['idusuario'];
	$fecha[$row_rsMaterial['numero']] = $row_rsMaterial['fecha'];
}while($row_rsMaterial = mysql_fetch_assoc($rsMaterial));

do{
	$usuarios[$row_rsUsuarios['id']] = $row_rsUsuarios['username'];
}while($row_rsUsuarios = mysql_fetch_assoc($rsUsuarios));

//chekamos si es lider

$lider = false;
$pcaptura = false;
$almacen = false;

if($totalRows_rsIsLider > 0){
	
	$lider = true;
	
}else{//verificamos que sea supervisor o almacen
	if($totalRows_rsNivelUsuario > 0){
		//tiene permisos de
		do{
			if($row_rsNivelUsuario['idlink'] == 26){
				$pcaptura = true;
			}
			
			if($row_rsNivelUsuario['idlink'] == 25){
				$almacen = true;
			}
			
		}while($row_rsNivelUsuario = mysql_fetch_assoc($rsNivelUsuario));
		
	}
}

?>



<h1>SOLICITUD, ENTREGA Y DEVOLUCION INTERNA DE MATERIALES COTIZADOS</h1>


<?php include("ip.encabezado.php");?>

<div class="distabla smallfont">
<table cellpadding="0" cellspacing="0" >
<thead>
<tr>
<td colspan="5">&nbsp;</td>
<td colspan="8" align="center">SALIDAS</td>
<td colspan="2"></td>
<td colspan="4" align="center">DEVOLUCIONES</td>
<td></td>
<td></td><td></td>

</tr>
<tr>
<td colspan="3">&nbsp;</td>
<td>&nbsp;</td>
<td></td>
<td colspan="2" align="center">1a.</td>
<td colspan="2" align="center">2a.</td>
<td colspan="2" align="center">3a.</td>
<td colspan="2" align="center">4a.</td>
<td colspan="2" align="center"></td>
<td align="center">1a</td>
<td align="center">2a</td>
<td align="center">3a</td>
<td align="center">4a</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td valign="top">Item</td>
<td valign="top">Codigo</td>
<td valign="top">Marca</td>
<td valign="top">Descripcion<img src="images/espacio.gif" width="400px" height="1px" /></td>
<td valign="top">Cant.<br> Cotiz</td>
<td valign="top">CS</td>
<td valign="top">CE</td>
<td valign="top">CS</td>
<td valign="top">CE</td>
<td valign="top">CS</td>
<td valign="top">CE</td>
<td valign="top">CS</td>
<td valign="top">CE</td>
<td valign="top">TS</td>
<td valign="top">TE</td>
<td valign="top">D</td>
<td valign="top">D</td>
<td valign="top">D</td>
<td valign="top">D</td>
<td valign="top">TD</td>
<td valign="top">TC</td>
<td valign="top">Observaciones</td>
</tr>
</thead>
<tbody>
<?php if($pcaptura == true){?>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=1" class="popup">Captura</a></td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=3" class="popup"><span class="f">Captura</span></a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=5" class="popup">Captura</a></td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=7" class="popup"><span class="f">Captura</span></a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<?php }?>
<?php if($almacen == true){?>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=2" class="popup">Cargar</a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=4" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=6" class="popup">Cargar</a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=8" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=9&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=10&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=11&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=12&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<?php } ?><?php if($lider == true){?>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=3" class="popup">Cargar</a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=5" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=7" class="popup">Cargar</a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<?php } ?>
<?php if($almacen == true) ?>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.autorizar.php?idip=<?php echo $_GET['idip'];?>&amp;num=2" class="popup">Autorizar</a></td>
<td align="right" valign="middle"></td>
<td align="right" valign="middle"><a href="ip.material.autorizar.php?idip=<?php echo $_GET['idip'];?>&amp;num=4" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=4" class="popup"></a></td>
<td align="right" valign="middle" class="f"></td>
<td align="right" valign="middle" class="f"><a href="ip.material.autorizar.php?idip=<?php echo $_GET['idip'];?>&amp;num=6" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=6" class="popup"></a></td>
<td align="right" valign="middle"></td>
<td align="right" valign="middle"><a href="ip.material.autorizar.php?idip=<?php echo $_GET['idip'];?>&amp;num=8" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=8" class="popup"></a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Autorizar</a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup"></a></td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup"></a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup"></a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<?php // ?>

  <?php $i = 1; do {?>
    <tr>
      <td valign="top"><?php echo $i; $i++;?></td>
      <td valign="top">&nbsp;</td>
      <td valign="top"><?php echo $row_rsPartidas['marca1']; ?></td>
      <td valign="top"><?php echo $row_rsPartidas['descri']; ?>
      </td>
      <td align="right" valign="middle"><?php echo $row_rsPartidas['cantidad']; ?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][1];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][1];?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][2];$en = $material[$row_rsPartidas['idsubcotizacionarticulo']][2];?></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][3];$sol =$sol +  $material[$row_rsPartidas['idsubcotizacionarticulo']][3];?></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][4];$en =$en +  $material[$row_rsPartidas['idsubcotizacionarticulo']][4];?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][5];$sol =$sol + $material[$row_rsPartidas['idsubcotizacionarticulo']][5];?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][6];$en =$en + $material[$row_rsPartidas['idsubcotizacionarticulo']][6];?></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][7];$sol =$sol +  $material[$row_rsPartidas['idsubcotizacionarticulo']][7];?></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][8];$en = $en + $material[$row_rsPartidas['idsubcotizacionarticulo']][8];?></td>
      <td align="right" valign="middle" class="f2"><?php echo $sol;?></td>
      <td align="right" valign="middle" class="f2"><?php echo $en; ?></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][9];$dv =  $material[$row_rsPartidas['idsubcotizacionarticulo']][9];?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][10];$dv = $dv + $material[$row_rsPartidas['idsubcotizacionarticulo']][10];?></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][11];$dv = $dv + $material[$row_rsPartidas['idsubcotizacionarticulo']][11];?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][12];$dv = $dv + $material[$row_rsPartidas['idsubcotizacionarticulo']][12];?></td>
      <td align="right" valign="middle" class="f2"><?php echo $dv; ?></td>
      <td align="right" valign="middle" class="f2"><?php echo ($en -$dv)?></td>
      <td valign="top">&nbsp;</td>
    </tr>
    <?php } while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas)); ?>
</tbody>
<tfoot>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Capturo:</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f"><?php echo $usuarios[$capturo[1]];?></td>
<td valign="top" class="f"></td>
<td valign="top"><?php echo $usuarios[$capturo[3]]?></td>
<td valign="top"></td>
<td valign="top" class="f"><?php echo $usuarios[$capturo[5]]?></td>
<td valign="top" class="f"></td>
<td valign="top"><?php echo $usuarios[$capturo[7]]?></td>
<td valign="top"></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>

<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Entrego:</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f"></td>
<td valign="top" class="f"><?php echo $usuarios[$entrego[2]]?></td>
<td valign="top">&nbsp;</td>
<td valign="top"><?php echo $usuarios[$entrego[4]]?></td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f"><?php echo $usuarios[$entrego[6]]?></td>
<td valign="top">&nbsp;</td>
<td valign="top"><?php echo $usuarios[$entrego[8]]?></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>

<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Recibio:</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f"></td>
<td valign="top" class="f"><?php echo $usuarios[$confirmo[2]]?></td>
<td valign="top">&nbsp;</td>
<td valign="top"><?php echo $usuarios[$confirmo[4]]?></td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f"><?php echo $usuarios[$confirmo[6]]?></td>
<td valign="top">&nbsp;</td>
<td valign="top"><?php echo $usuarios[$confirmo[8]]?></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>

<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Fecha:</td>
<td valign="top"></td>
<td valign="top" class="f"><?php echo formatDateShort($fecha[1])?></td>
<td valign="top" class="f"><?php echo formatDateShort($fecha[2])?></td>
<td valign="top"><?php echo  formatDateShort($fecha[3])?></td>
<td valign="top"><?php echo  formatDateShort($fecha[4])?></td>
<td valign="top" class="f"><?php echo  formatDateShort($fecha[5])?></td>
<td valign="top" class="f"><?php echo  formatDateShort($fecha[6])?></td>
<td valign="top"><?php echo  formatDateShort($fecha[7])?></td>
<td valign="top"><?php echo  formatDateShort($fecha[8])?></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
</tfoot>
</table>
</div><div class="distabla smallfont">
<table cellpadding="0" cellspacing="0" >
<thead>
<tr>
<td colspan="5">&nbsp;</td>
<td colspan="8" align="center">SALIDAS</td>
<td colspan="2"></td>
<td colspan="2" align="center">&nbsp;</td>
<td colspan="2" align="center">DEVOLUCIONES</td>
<td></td>
<td></td><td></td>

</tr>
<tr>
<td colspan="3">&nbsp;</td>
<td>&nbsp;</td>
<td></td>
<td colspan="2" align="center">1a.</td>
<td colspan="2" align="center">2a.</td>
<td colspan="2" align="center">3a.</td>
<td colspan="2" align="center">4a.</td>
<td colspan="2" align="center">5a</td>
<td colspan="2" align="center">&nbsp;</td>
<td align="center">1a</td>
<td align="center">2a</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td valign="top">Item</td>
<td valign="top">Codigo</td>
<td valign="top">Marca</td>
<td valign="top">Descripcion<img src="images/espacio.gif" width="400px" height="1px" /></td>
<td valign="top">Cant.<br> Cotiz</td>
<td valign="top"><img src="images/titulos/solicitada.png" /></td>
<td valign="top"><img src="images/titulos/entregada.png" /></td>
<td valign="top"><img src="images/titulos/solicitada.png" /></td>
<td valign="top"><img src="images/titulos/entregada.png" /></td>
<td valign="top"><img src="images/titulos/solicitada.png" /></td>
<td valign="top"><img src="images/titulos/entregada.png" /></td>
<td valign="top"><img src="images/titulos/solicitada.png" /></td>
<td valign="top"><img src="images/titulos/entregada.png" /></td>
<td valign="top"><img src="images/titulos/solicitada.png" /></td>
<td valign="top"><img src="images/titulos/entregada.png" /></td>
<td valign="top"><img src="images/titulos/totentregado.png" /></td>
<td valign="top"><img src="images/titulos/totentregado.png" /></td>
<td valign="top"><img src="images/titulos/devolucion.png" /></td>
<td valign="top"><img src="images/titulos/devolucion.png" /></td>
<td valign="top"><img src="images/titulos/totdevuelto.png" /></td>
<td valign="top"><img src="images/titulos/conciliacion.png" /></td>
<td valign="top">Observaciones</td>
</tr>
</thead>
<tbody>
<?php if($pcaptura == true){?>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=1" class="popup">Captura</a></td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=3" class="popup"><span class="f">Captura</span></a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=5" class="popup">Captura</a></td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=7" class="popup"><span class="f">Captura</span></a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f1"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=8" class="popup"><span class="f">Captura</span></a></td>
<td align="right" valign="middle" class="f1"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=7" class="popup"></a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<?php }?>
<?php if($almacen == true){?>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=2" class="popup">Cargar</a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=4" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=6" class="popup">Cargar</a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=8" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f1">&nbsp;</td>
<td align="right" valign="middle" class="f1"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=9" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f2"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=9&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f2"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=10&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=11&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=12&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<?php } ?><?php if($lider == true){?>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=3" class="popup">Cargar</a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=5" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=7" class="popup">Cargar</a></td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f1"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=8" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f1">&nbsp;</td>
<td align="right" valign="middle" class="f2"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f2"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Cargar</a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<?php } ?>
<?php if($almacen == true) ?>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.autorizar.php?idip=<?php echo $_GET['idip'];?>&amp;num=2" class="popup">Autorizar</a></td>
<td align="right" valign="middle"></td>
<td align="right" valign="middle"><a href="ip.material.autorizar.php?idip=<?php echo $_GET['idip'];?>&amp;num=4" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=4" class="popup"></a></td>
<td align="right" valign="middle" class="f"></td>
<td align="right" valign="middle" class="f"><a href="ip.material.autorizar.php?idip=<?php echo $_GET['idip'];?>&amp;num=6" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=6" class="popup"></a></td>
<td align="right" valign="middle"></td>
<td align="right" valign="middle"><a href="ip.material.autorizar.php?idip=<?php echo $_GET['idip'];?>&amp;num=8" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=8" class="popup"></a></td>
<td align="right" valign="middle" class="f1">&nbsp;</td>
<td align="right" valign="middle" class="f1"><a href="ip.material.autorizar.php?idip=<?php echo $_GET['idip'];?>&amp;num=9" class="popup">Autorizar</a></td>
<td align="right" valign="middle" class="f2"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Autorizar</a></td>
<td align="right" valign="middle" class="f2"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup"></a></td>
<td align="right" valign="middle"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup"></a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup">Autorizar</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=1&amp;tipe=dv" class="popup"></a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<?php // ?>

  <?php $i = 1; do { ?>
    <tr>
      <td valign="top"><?php echo $i; $i++;?></td>
      <td valign="top">&nbsp;</td>
      <td valign="top"><?php echo $row_rsPartidas['marca1']; ?></td>
      <td valign="top"><?php echo $row_rsPartidas['descri']; ?>
      </td>
      <td align="right" valign="middle"><?php echo $row_rsPartidas['cantidad']; ?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][1];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][1];?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][2];$en = $material[$row_rsPartidas['idsubcotizacionarticulo']][2];?></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][3];$sol =$sol +  $material[$row_rsPartidas['idsubcotizacionarticulo']][3];?></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][4];$en =$en +  $material[$row_rsPartidas['idsubcotizacionarticulo']][4];?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][5];$sol =$sol + $material[$row_rsPartidas['idsubcotizacionarticulo']][5];?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][6];$en =$en + $material[$row_rsPartidas['idsubcotizacionarticulo']][6];?></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][7];$sol =$sol +  $material[$row_rsPartidas['idsubcotizacionarticulo']][7];?></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][8];$en = $en + $material[$row_rsPartidas['idsubcotizacionarticulo']][8];?></td>
      <td align="right" valign="middle" class="f1"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][9];$sol = $sol + $material[$row_rsPartidas['idsubcotizacionarticulo']][9];?></td>
      <td align="right" valign="middle" class="f1"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][10];$en = $en + $material[$row_rsPartidas['idsubcotizacionarticulo']][10];?></td>
      <td align="right" valign="middle" class="f2"><span class="f1"><?php echo $sol;?></span></td>
      <td align="right" valign="middle" class="f2"><span class="f1"><?php echo $en; ?></span></td>
      <td align="right" valign="middle"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][11];$dv = $dv + $material[$row_rsPartidas['idsubcotizacionarticulo']][11];?></td>
      <td align="right" valign="middle" class="f"><?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][12];$dv = $dv + $material[$row_rsPartidas['idsubcotizacionarticulo']][12];?></td>
      <td align="right" valign="middle" class="f2"><?php echo $dv; ?></td>
      <td align="right" valign="middle" class="f2"><?php echo ($en -$dv)?></td>
      <td valign="top">&nbsp;</td>
    </tr>
    <?php } while ($row_rsPartidas = mysql_fetch_assoc($rsPartidas)); ?>
</tbody>
<tfoot>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Capturo:</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f"><?php echo $usuarios[$capturo[1]];?></td>
<td valign="top" class="f"></td>
<td valign="top"><?php echo $usuarios[$capturo[3]]?></td>
<td valign="top"></td>
<td valign="top" class="f"><?php echo $usuarios[$capturo[5]]?></td>
<td valign="top" class="f"></td>
<td valign="top"><?php echo $usuarios[$capturo[7]]?></td>
<td valign="top"></td>
<td valign="top" class="f2"><?php echo $usuarios[$capturo[8]]?></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>

<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Entrego:</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f"></td>
<td valign="top" class="f"><?php echo $usuarios[$entrego[2]]?></td>
<td valign="top">&nbsp;</td>
<td valign="top"><?php echo $usuarios[$entrego[4]]?></td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f"><?php echo $usuarios[$entrego[6]]?></td>
<td valign="top">&nbsp;</td>
<td valign="top"><?php echo $usuarios[$entrego[8]]?></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2"><?php echo $usuarios[$entrego[9]]?></td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>

<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Recibio:</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f"></td>
<td valign="top" class="f"><?php echo $usuarios[$confirmo[2]]?></td>
<td valign="top">&nbsp;</td>
<td valign="top"><?php echo $usuarios[$confirmo[4]]?></td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f"><?php echo $usuarios[$confirmo[6]]?></td>
<td valign="top">&nbsp;</td>
<td valign="top"><?php echo $usuarios[$confirmo[8]]?></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2"><?php echo $usuarios[$confirmo[9]]?></td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>

<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Fecha:</td>
<td valign="top"></td>
<td valign="top" class="f"><?php echo formatDateShort($fecha[1])?></td>
<td valign="top" class="f"><?php echo formatDateShort($fecha[2])?></td>
<td valign="top"><?php echo  formatDateShort($fecha[3])?></td>
<td valign="top"><?php echo  formatDateShort($fecha[4])?></td>
<td valign="top" class="f"><?php echo  formatDateShort($fecha[5])?></td>
<td valign="top" class="f"><?php echo  formatDateShort($fecha[6])?></td>
<td valign="top"><?php echo  formatDateShort($fecha[7])?></td>
<td valign="top"><?php echo  formatDateShort($fecha[8])?></td>
<td valign="top" class="f2"><?php echo  formatDateShort($fecha[8])?></td>
<td valign="top" class="f2"><?php echo  formatDateShort($fecha[9])?></td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
</tfoot>
</table>
</div>


<?php 
mysql_free_result($rsPartidas);

mysql_free_result($rsNumCapura);

mysql_free_result($rsNivelUsuario);

mysql_free_result($rsIsLider);

mysql_free_result($rsMaterial);

mysql_free_result($rsUsuarios);
?>


<?php 
mysql_free_result($rsPartidas);

mysql_free_result($rsNumCapura);

mysql_free_result($rsNivelUsuario);

mysql_free_result($rsIsLider);

mysql_free_result($rsMaterial);

mysql_free_result($rsUsuarios);
?>