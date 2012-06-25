<?php require_once('utils.php');?>
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

$colname_rsPartidas = "-1";
if (isset($_GET['idproyecto_material'])) {
  $colname_rsPartidas = $_GET['idproyecto_material'];
  
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT * FROM proyecto_material_partida WHERE idproyecto_material = %s ORDER BY idsubcotizacionarticulo ASC", GetSQLValueString( $colname_rsPartidas,"int"));
$rsPartidas = mysql_query($query_rsPartidas, $tecnocomm) or die(mysql_error());
$row_rsPartidas = mysql_fetch_assoc($rsPartidas);
$totalRows_rsPartidas = mysql_num_rows($rsPartidas);

$colname_rsMovimientos = "-1";
if (isset($_GET['idproyecto_material'])) {
  $colname_rsMovimientos = $_GET['idproyecto_material'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMovimientos = sprintf("SELECT pm.*,(SELECT username FROM usuarios u WHERE u.id = pm.capturo) AS ncapturo,(SELECT username FROM usuarios u WHERE u.id = pm.precapturo) AS nprecapturo,(SELECT username FROM usuarios u WHERE u.id = pm.autorizo) AS nautorizo FROM proyecto_material_movimiento pm WHERE pm.idproyecto_material = %s", GetSQLValueString($colname_rsMovimientos, "int"));
$rsMovimientos = mysql_query($query_rsMovimientos, $tecnocomm) or die(mysql_error());
$row_rsMovimientos = mysql_fetch_assoc($rsMovimientos);
$totalRows_rsMovimientos = mysql_num_rows($rsMovimientos);

$colname_rsDetalle = "-1";
if (isset($_GET['idproyecto_material'])) {
  $colname_rsDetalle = $_GET['idproyecto_material'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDetalle = sprintf("SELECT pmd.*,pm.numero FROM proyecto_material_detalle pmd JOIN proyecto_material_movimiento pm  ON pm.idproyecto_material_movimiento = pmd.idproyecto_material_movimiento WHERE pm.idproyecto_material = %s", GetSQLValueString($colname_rsDetalle, "int"));
$rsDetalle = mysql_query($query_rsDetalle, $tecnocomm) or die(mysql_error());
$row_rsDetalle = mysql_fetch_assoc($rsDetalle);
$totalRows_rsDetalle = mysql_num_rows($rsDetalle);

//partidas
do{
	
	if($row_rsPartidas['pextra'] == 0)
		$partidas[$row_rsPartidas['idproyecto_material_partida']] = $row_rsPartidas;
	else
		$partidasextra[$row_rsPartidas['idproyecto_material_partida']] = $row_rsPartidas;
		
}while($row_rsPartidas = mysql_fetch_assoc($rsPartidas));

//movimientos
do{
	$movimientos[$row_rsMovimientos['numero']] = $row_rsMovimientos;
}while($row_rsMovimientos = mysql_fetch_assoc($rsMovimientos));


//detalle_movimientos
do{
		
	$detalles[$row_rsDetalle['numero']][$row_rsDetalle['idproyecto_material_partida']] = $row_rsDetalle;
		
}while($row_rsDetalle = mysql_fetch_assoc($rsDetalle));

?>
<link href="style2.css" rel="stylesheet" type="text/css">


<a href="ip.controlmaterial.print.php?idip=<?php echo $_GET['idip']; ?>&idproyecto_material=<?php echo $_GET['idproyecto_material'];?>" class="popup"><img src="images/Imprimir2.png" />Imprimir</a>

<div class="distabla smallfont">
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
<td valign="top">Partida</td>
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

<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=0" class="popup">Captura</a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=1" class="popup">Captura</a></td>
<td align="right" valign="middle"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=2" class="popup">Captura</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=3" class="popup"></a></td>
<td align="right" valign="middle"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=3" class="popup">Captura</a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=4" class="popup">Captura</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=5" class="popup"></a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyectomaterial'];?>&numero=5" class="popup">Captura</a></td>
<td align="right" valign="middle"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=6" class="popup">Captura</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&num=7" class="popup"></a></td>
<td align="right" valign="middle"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=7" class="popup">Captura</a></td>
<td align="right" valign="middle" class="f1"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=8" class="popup">Captura</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=8" class="popup"></a></td>
<td align="right" valign="middle" class="f1"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=9" class="popup">Captura</a><a href="ip.material.confirmar.php?idip=<?php echo $_GET['idip'];?>&amp;num=7" class="popup"></a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=10" class="popup">Captura</a></td>
<td align="right" valign="middle" class="f"><a href="ip.material.captura?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=11" class="popup">Captura</a></td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>

<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td valign="top">&nbsp;</td>
<td align="right" valign="middle">&nbsp;</td>
<td align="right" valign="middle" class="f">
<?php if(isset($movimientos[0]) && ($movimientos[0]['autorizo'] == '')){?>
<a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=0&idusuario=<?php echo $liderid;?>&idip=<?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[0]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a>
<?php } ?>
</td>
<td align="right" valign="middle" class="f">
<?php if(isset($movimientos[1]) && ($movimientos[1]['autorizo'] == '')){?>
<a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=1&idusuario=<?php echo $almacenid;?>&idip=<?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[1]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a>
<?php } ?>
</td>
<td align="right" valign="middle">
<?php if(isset($movimientos[2]) && ($movimientos[2]['autorizo'] == '')){?>
<span class="f"><a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=2&idusuario=<?php echo $liderid;?>&idip=<?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[2]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a></span>
<?php } ?>
</td>
<td align="right" valign="middle">
<?php if(isset($movimientos[3]) && ($movimientos[3]['autorizo'] == '')){?>
<span class="f"><a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=3&idusuario=<?php echo $almacenid;?>&idip=<?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[3]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a></span>
<?php } ?>

</td>
<td align="right" valign="middle" class="f">
<?php if(isset($movimientos[4]) && ($movimientos[4]['autorizo'] == '')){?>
<a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=4&idusuario=<?php echo $liderid;?>&idip=<?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[4]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a>
<?php } ?>
</td>
<td align="right" valign="middle" class="f">
<?php if(isset($movimientos[5]) && ($movimientos[5]['autorizo'] == '')){?>
<a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=5&idusuario=<?php echo $almacenid;?>&idip=<?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[5]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a>
<?php } ?>
</td>
<td align="right" valign="middle">
<?php if(isset($movimientos[6]) && ($movimientos[6]['autorizo'] == '')){?>
<span class="f"><a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=6&idusuario=<?php echo $liderid;?>&idip=<?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[6]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a></span>
<?php } ?>
</td>
<td align="right" valign="middle">
<?php if(isset($movimientos[7]) && ($movimientos[7]['autorizo'] == '')){?>
<span class="f"><a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=7&idusuario=<?php echo $almacenid;?>&idip=<?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[7]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a></span>
<?php } ?>
</td>
<td align="right" valign="middle" class="f1">
<?php if(isset($movimientos[8]) && ($movimientos[8]['autorizo'] == '')){?>
<a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=8&idusuario=<?php echo $liderid;?>&idip=<?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[8]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a></span>
<?php } ?>
</td>
<td align="right" valign="middle" class="f1">
<?php if(isset($movimientos[9]) && ($movimientos[9]['autorizo'] == '')){?>
<a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=9&idusuario=<?php echo $almacenid;?>&idip=<?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[9]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a>
<?php } ?>
</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle">
<?php if(isset($movimientos[10]) && ($movimientos[10]['autorizo'] == '')){?>
<span class="f"><a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=10&idip=<?php echo $_GET['idip']?><?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[10]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a></span>
<?php } ?>
</td>
<td align="right" valign="middle" class="f">
<?php if(isset($movimientos[11]) && ($movimientos[11]['autorizo'] == '')){?>
<a href="ip.controlmaterial.autorizar.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>&numero=11&idip=<?php echo $_GET['idip']?><?php echo $_GET['idip']?>&idproyecto_material_movimiento=<?php echo $movimientos[11]['idproyecto_material_movimiento'];?>" class="popup">Autorizar</a>
<?php } ?>
</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td align="right" valign="middle" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<?php $i=1;?>
<?php foreach($partidas as $kpartida => $partida){?>
<tr>
<td valign="top"><?php echo $i; $i++;?></td>
<td valign="top"><?php echo $partida['codigo'];?></td>
<td valign="top"><?php echo $partida['marca'];?></td>
<td valign="top"><?php echo $partida['descripcion'];?></td>
<td align="right" valign="middle"><?php echo $partida['cantidad'];?></td>
<td align="right" valign="middle" class="f"><?php echo $detalles[0][$kpartida]['cantidad']; $tsolicitado = $detalles[0][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f"><?php echo $detalles[1][$kpartida]['cantidad']; $tentregado = $detalles[1][$kpartida]['cantidad']; ?></td>
<td align="right" valign="middle"><?php echo $detalles[2][$kpartida]['cantidad'];  $tsolicitado = $tsolicitado + $detalles[2][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle"><?php echo $detalles[3][$kpartida]['cantidad']; $tentregado = $tentregado + $detalles[3][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f"><?php echo $detalles[4][$kpartida][$kpartida]['cantidad'];  $tsolicitado = $tsolicitado + $detalles[4]['cantidad'];?></td>
<td align="right" valign="middle" class="f"><?php echo $detalles[5][$kpartida]['cantidad']; $tentregado = $tentregado + $detalles[5][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle"><?php echo $detalles[6][$kpartida]['cantidad'];  $tsolicitado = $tsolicitado + $detalles[6][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle"><?php echo $detalles[7][$kpartida]['cantidad']; $tentregado = $tentregado + $detalles[7][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f1"><?php echo $detalles[8][$kpartida]['cantidad'];  $tsolicitado = $tsolicitado + $detalles[8]['cantidad'];?></td>
<td align="right" valign="middle" class="f1"><?php echo $detalles[9][$kpartida]['cantidad']; $tentregado = $tentregado + $detalles[9][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f2"><?php echo $tsolicitado;?></td>
<td align="right" valign="middle" class="f2"><?php echo $tentregado;?></td>
<td align="right" valign="middle"><?php echo $detalles[10][$kpartida]['cantidad']; $tdevuleto = $detalles[10][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f"><?php echo $detalles[11][$kpartida]['cantidad'];  $tdevuleto = $tdevuelto + $detalles[11][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f2"><?php echo $tdevuelto;?></td>
<td align="right" valign="middle" class="f2"><?php echo $tentregado - $tdevuelto;?></td>
<td valign="top"><div class="obs"></div></td>
</tr>
<?php } ?>


<?php if(is_array($partidasextra)){?>
<tr>
<td colspan="22" align="center" valign="top">Partidas extras no cotizadas</td>
</tr>



<?php foreach($partidasextra as $kpartida => $partida){?>
<tr>
<td valign="top"><?php echo $i; $i++;?></td>
<td valign="top"><?php echo $partida['codigo'];?></td>
<td valign="top"><?php echo $partida['marca'];?></td>
<td valign="top"><?php echo $partida['descripcion'];?></td>
<td align="right" valign="middle"><?php echo $partida['cantidad'];?></td>
<td align="right" valign="middle" class="f"><?php echo $detalles[0][$kpartida]['cantidad']; $tsolicitado = $detalles[0][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f"><?php echo $detalles[1][$kpartida]['cantidad']; $tentregado = $detalles[1][$kpartida]['cantidad']; ?></td>
<td align="right" valign="middle"><?php echo $detalles[2][$kpartida]['cantidad'];  $tsolicitado = $tsolicitado + $detalles[2][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle"><?php echo $detalles[3][$kpartida]['cantidad']; $tentregado = $tentregado + $detalles[3][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f"><?php echo $detalles[4][$kpartida][$kpartida]['cantidad'];  $tsolicitado = $tsolicitado + $detalles[4]['cantidad'];?></td>
<td align="right" valign="middle" class="f"><?php echo $detalles[5][$kpartida]['cantidad']; $tentregado = $tentregado + $detalles[5][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle"><?php echo $detalles[6][$kpartida]['cantidad'];  $tsolicitado = $tsolicitado + $detalles[6][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle"><?php echo $detalles[7][$kpartida]['cantidad']; $tentregado = $tentregado + $detalles[7][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f1"><?php echo $detalles[8][$kpartida]['cantidad'];  $tsolicitado = $tsolicitado + $detalles[8]['cantidad'];?></td>
<td align="right" valign="middle" class="f1"><?php echo $detalles[9][$kpartida]['cantidad']; $tentregado = $tentregado + $detalles[9][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f2"><?php echo $tsolicitado;?></td>
<td align="right" valign="middle" class="f2"><?php echo $tentregado;?></td>
<td align="right" valign="middle"><?php echo $detalles[10][$kpartida]['cantidad']; $tdevuleto = $detalles[10][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f"><?php echo $detalles[11][$kpartida]['cantidad'];  $tdevuleto = $tdevuelto + $detalles[11][$kpartida]['cantidad'];?></td>
<td align="right" valign="middle" class="f2"><?php echo $tdevuelto;?></td>
<td align="right" valign="middle" class="f2"><?php echo $tentregado - $tdevuelto;?></td>
<td valign="top"><div class="obs"></div></td>
</tr>
<?php }//fin de foreach partidasextra ?>
<?php }// fin de isarray?>
</tbody>
<tfoot>
<!-- 
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Pre Capturo:</td>
<td valign="top"><?php echo $movimientos[0]['nprecapturo']?></td>
<td valign="top" class="f"><?php echo $movimientos[1]['nprecapturo']?></td>
<td valign="top" class="f"><?php echo $movimientos[2]['nprecapturo']?></td>
<td valign="top"><?php echo $movimientos[3]['nprecapturo']?></td>
<td valign="top"><?php echo $movimientos[4]['nprecapturo']?></td>
<td valign="top" class="f"><?php echo $movimientos[5]['nprecapturo']?></td>
<td valign="top" class="f"><?php echo $movimientos[6]['nprecapturo']?></td>
<td valign="top"><?php echo $movimientos[7]['nprecapturo']?></td>
<td valign="top"><?php echo $movimientos[8]['nprecapturo']?></td>
<td valign="top" class="f2"><?php echo $movimientos[9]['nprecapturo']?></td>
<td valign="top" class="f2"><?php echo $movimientos[10]['nprecapturo']?></td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top"><?php echo $movimientos[11]['nprecapturo']?></td>
<td valign="top" class="f"><?php echo $movimientos[12]['nprecapturo']?></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
-->
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Capturo:</td>
<td valign="top"><?php echo $movimientos[0]['ncapturo']?></td>
<td valign="top" class="f"><?php echo $movimientos[1]['ncapturo']?></td>
<td valign="top" class="f"><?php echo $movimientos[2]['ncapturo']?></td>
<td valign="top"><?php echo $movimientos[3]['ncapturo']?></td>
<td valign="top"><?php echo $movimientos[4]['ncapturo']?></td>
<td valign="top" class="f"><?php echo $movimientos[5]['ncapturo']?></td>
<td valign="top" class="f"><?php echo $movimientos[6]['ncapturo']?></td>
<td valign="top"><?php echo $movimientos[7]['ncapturo']?></td>
<td valign="top"><?php echo $movimientos[8]['ncapturo']?></td>
<td valign="top" class="f2"><?php echo $movimientos[9]['ncapturo']?></td>
<td valign="top" class="f2"><?php echo $movimientos[10]['ncapturo']?></td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top"><?php echo $movimientos[11]['ncapturo']?></td>
<td valign="top" class="f"><?php echo $movimientos[12]['ncapturo']?></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Atorizo:</td>
<td valign="top"><?php echo $movimientos[0]['nautorizo']?></td>
<td valign="top" class="f"><?php echo $movimientos[1]['nautorizo']?></td>
<td valign="top" class="f"><?php echo $movimientos[2]['nautorizo']?></td>
<td valign="top"><?php echo $movimientos[3]['nautorizo']?></td>
<td valign="top"><?php echo $movimientos[4]['nautorizo']?></td>
<td valign="top" class="f"><?php echo $movimientos[5]['nautorizo']?></td>
<td valign="top" class="f"><?php echo $movimientos[6]['nautorizo']?></td>
<td valign="top"><?php echo $movimientos[7]['nautorizo']?></td>
<td valign="top"><?php echo $movimientos[8]['nautorizo']?></td>
<td valign="top" class="f2"><?php echo $movimientos[9]['nautorizo']?></td>
<td valign="top" class="f2"><?php echo $movimientos[10]['nautorizo']?></td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top"><?php echo $movimientos[11]['nautorizo']?></td>
<td valign="top" class="f"><?php echo $movimientos[12]['nautorizo']?></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
<tr>
<td colspan="3" valign="top">&nbsp;</td>
<td align="right" valign="top">Fecha:</td>
<td valign="top"><?php echo $movimientos[0]['fecha']?></td>
<td valign="top" class="f"><?php echo $movimientos[1]['fecha']?></td>
<td valign="top" class="f"><?php echo $movimientos[2]['fecha']?></td>
<td valign="top"><?php echo $movimientos[3]['fecha']?></td>
<td valign="top"><?php echo $movimientos[4]['fecha']?></td>
<td valign="top" class="f"><?php echo $movimientos[5]['fecha']?></td>
<td valign="top" class="f"><?php echo $movimientos[6]['fecha']?></td>
<td valign="top"><?php echo $movimientos[7]['fecha']?></td>
<td valign="top"><?php echo $movimientos[8]['fecha']?></td>
<td valign="top" class="f2"><?php echo $movimientos[9]['fecha']?></td>
<td valign="top" class="f2"><?php echo $movimientos[10]['fecha']?></td>
<td valign="top">&nbsp;</td>
<td valign="top" class="f">&nbsp;</td>
<td valign="top"><?php echo $movimientos[11]['fecha']?></td>
<td valign="top" class="f"><?php echo $movimientos[12]['fecha']?></td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top" class="f2">&nbsp;</td>
<td valign="top">&nbsp;</td>
</tr>
</tfoot>
</table>
</div>


<?php 
mysql_free_result($rsPartidas);

mysql_free_result($rsMovimientos);

mysql_free_result($rsDetalle);
?>
