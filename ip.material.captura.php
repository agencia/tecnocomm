<?php require_once('utils.php');?>
<?php require_once('Connections/tecnocomm.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "systemFail.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
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


if(isset($_POST['guardar']) && $_POST['guardar'] == "true"){
	//validar si es precaptura o captura
	
	
	//guardar la informacion

	$insertSQL = sprintf("INSERT INTO proyecto_material_movimiento(idproyecto_material, fecha, numero, precapturo, capturo) VALUES(%s, NOW(), %s, %s, %s)",
						GetSQLValueString($_POST['idproyecto_material'],"int"),
						GetSQLValueString($_POST['numero'],"int"),
						GetSQLValueString($_POST['pcaptura'],"int"),
						GetSQLValueString($_POST['capturo'],"int"));

	mysql_select_db($database_tecnocomm,$tecnocomm);
	$resutl = mysql_query($insertSQL,$tecnocomm) or die(mysql_error());
	
	$idmovimiento = mysql_insert_id();
	
	$cantidades = $_POST['cantidad'];
	$obs = $_POST['obs'];
	
	foreach($cantidades as $kcant => $cant){
		
	$insertSQL = sprintf("INSERT INTO proyecto_material_detalle(idproyecto_material_movimiento, idproyecto_material_partida, cantidad, observacion) VALUES(%s, %s, %s, %s)",GetSQLValueString($idmovimiento,"int"),GetSQLValueString($kcant,"int"),GetSQLValueString($cant,"double"),GetSQLValueString($obs[$kcant],"text"));
		
	mysql_select_db($database_tecnocomm,$tecnocomm);
	$resutl = mysql_query($insertSQL,$tecnocomm) or die(mysql_error());
		
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

$colname_rsMaterial = "-1";
if (isset($_GET['idproyecto_material'])) {
  $colname_rsMaterial = $_GET['idproyecto_material'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMaterial = sprintf("SELECT * FROM proyecto_material WHERE idproyecto_material = %s", GetSQLValueString($colname_rsMaterial, "int"));
$rsMaterial = mysql_query($query_rsMaterial, $tecnocomm) or die(mysql_error());
$row_rsMaterial = mysql_fetch_assoc($rsMaterial);
$totalRows_rsMaterial = mysql_num_rows($rsMaterial);

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



//NIVEL DE USUARIO
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
if (isset($row_rsMaterial['idip'])) {
  $colname_rsIsLider = $row_rsMaterial['idip'];
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
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Capura De Material</title>
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/jqueryui.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
<script language="javascript">

$.extend($.expr[":"], {
    "containsNC": function(elem, i, match, array) {
        return (elem.textContent || elem.innerText || "").toLowerCase
().indexOf((match[3] || "").toLowerCase()) >= 0;
    }
});


$(function(){
		   
		   $(".filtrar").keyup(function(e){					
						$('.bus:not(:containsNC("'+$(this).val()+'"))').each(function(index,e){$(e).css('display','none')});										
						$('.bus:containsNC("'+$(this).val()+'")').each(function(index,e){$(e).css('display','table-row')});
						});
		   });

$(function(){	   
		   $('.ican').keyup(function(e){
			
			var c = $(this).attr('clave');
			var coti = $('input[name=cotizado\\['+c+'\\]]').val();
			var ts = $('input[name=tsolicitado\\['+c+'\\]]').val();
			
			//alert($(this).val());
			
			if((parseFloat(ts) + parseFloat($(this).val()) )> parseFloat(coti)){
			justificacion = prompt('Especifique una justificacion y de click en aceptar o cancele y edite a una cantidad mas pequeña', '', 'Alerta! Esta excediendo la cantidad Cotizada:');
										
   			var n = 'input[name=obs\\['+c+'\\]]';
   			$(n).val(justificacion);
			
			}//fin de if sobre pasa cantidad cotizada
			
			});//fin de evento key up
		   });//fin de function que contiene key up



</script>
<link href="style2.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php $_GET['idip'] = $row_rsMaterial['idip']; ?>
<?php include("ip.encabezado.php");?>
<?php
$numero = $_GET['numero'];

if(isset($movimientos[$numero]) && $movimientos[$numero]['autorizo'] > 0){
	$sepuede = false;
}else{
	switch($numero){
		case 0: $sepuede = true;
		break;
		case 2: 
		case 4:
		case 6:
		case 8:if($pcaptura == true || $lider == true){$sepuede = true;}
		break;
		case 1:
		case 3:
		case 5:
		case 7:
		case 9:if($almacen == true){$sepuede = true;}
		break;
		case 10:
		case 11:if($lider == true){$sepuede = true;}
		break;
		default: $sepuede = false;
	}
	
	
}

?>
<?php if($sepuede == true){?>
<label>Buscar Partidas</label>
<input type="text" name="filtrar" class="filtrar" size="45">
<ul>
<li><a href="ip.material.add.php?idproyecto_material=<?php echo $_GET['idproyecto_material'];?>" class="popup"><span>Agregar Partida No Cotizada</span></a></li>
</ul>
<div class="distabla smallfont">
<form name="frmCaptura" method="post">
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
<?php $i=1;?>
<?php foreach($partidas as $kpartida => $partida){?>
<?php $tsolicitado = 0;?>
<tr class="bus">
<td valign="top"><?php echo $i; $i++;?></td>
<td valign="top"><?php echo $partida['codigo'];?></td>
<td valign="top"><?php echo $partida['marca'];?></td>
<td valign="top"><?php echo $partida['descripcion'];?></td>
<td align="right" valign="middle"><?php echo $partida['cantidad'];?><input type="hidden" name="cotizado[<?php echo $kpartida;?>]" value="<?php echo $partida['cantidad']?>"></td>
<td align="right" valign="middle" class="f">
<?php if($numero != 0){?>
<?php echo $detalles[0][$kpartida]['cantidad']; $tsolicitado = 0;?>
<?php }else{ ?>
<input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
<?php } ?>
</td>
<td align="right" valign="middle" class="f"><?php if($numero != 1){?>
  <?php echo $detalles[1][$kpartida]['cantidad']; $tsolicitado += $detalles[1][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?></td>
<td align="right" valign="middle"><span class="f">
  <?php if($numero != 2){?>
  <?php echo $detalles[2][$kpartida]['cantidad']; $tsolicitado += $detalles[2][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?>
</span></td>
<td align="right" valign="middle"><span class="f">
  <?php if($numero != 3){?>
  <?php echo $detalles[3][$kpartida]['cantidad']; $tsolicitado += $detalles[3][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>" >
  <?php } ?>
</span></td>
<td align="right" valign="middle" class="f">
<?php if($numero != 4){?>
  <?php echo $detalles[4][$kpartida]['cantidad']; $tsolicitado += $detalles[4][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>"> 
  <?php } ?></td>
<td align="right" valign="middle" class="f">
<?php if($numero != 5){?>
  <?php echo $detalles[5][$kpartida]['cantidad']; $tsolicitado += $detalles[5][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?></td>
<td align="right" valign="middle"><span class="f">
  <?php if($numero != 6){?>
  <?php echo $detalles[6][$kpartida]['cantidad']; $tsolicitado += $detalles[6][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?>
</span></td>
<td align="right" valign="middle"><span class="f">
  <?php if($numero != 7){?>
  <?php echo $detalles[7][$kpartida]['cantidad']; $tsolicitado += $detalles[7][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?>
</span></td>
<td align="right" valign="middle" class="f1"><span class="f">
  <?php if($numero != 8){?>
  <?php echo $detalles[8][$kpartida]['cantidad']; $tsolicitado += $detalles[8][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>" >
  <?php } ?>
</span></td>
<td align="right" valign="middle" class="f1"><span class="f">
  <?php if($numero != 9){?>
  <?php echo $detalles[9][$kpartida]['cantidad']; $tsolicitado += $detalles[9][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican"clave="<?php echo $kpartida;?>">
  <?php } ?>
</span></td>
<td align="right" valign="middle" class="f2"><?php echo $tsolicitado;?><input type="hidden" name="tsolicitado[<?php echo $kpartida?>]" value="<?php echo $tsolicitado;?>"></td>
<td align="right" valign="middle" class="f2"><?php echo $tentregado;?></td>
<td align="right" valign="middle"><span class="f">
  <?php if($numero != 10){?>
  <?php echo $detalles[10][$kpartida]['cantidad']; $tsolicitado = $detalles[10][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?>
</span></td>
<td align="right" valign="middle" class="f">
<?php if($numero != 11){?>
  <?php echo $detalles[11][$kpartida]['cantidad']; $tsolicitado = $detalles[11][$kpartida]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?></td>
<td align="right" valign="middle" class="f2"><?php echo $tdevuelto;?></td>
<td align="right" valign="middle" class="f2"><?php echo $tentregado - $tdevuelto;?></td>
<td valign="top"><input type="text" name="obs[<?php echo $kpartida; ?>]" class="iobs"></td>
</tr>
<?php } //fin de for each partids ?>


<?php if(is_array($partidasextra)){?>
<tr><td colspan="22" align="center">Partidas extras no cotizadas</td></tr>
<?php foreach($partidasextra as $kpartida => $partida){?>
<?php $tsolicitado = 0;?>
<tr class="bus">
<td valign="top"><?php echo $i; $i++;?></td>
<td valign="top"><?php echo $partida['codigo'];?></td>
<td valign="top"><?php echo $partida['marca'];?></td>
<td valign="top"><?php echo $partida['descripcion'];?></td>
<td align="right" valign="middle"><?php echo $partida['cantidad'];?><input type="hidden" name="cotizado[<?php echo $kpartida;?>]" value="<?php echo $partida['cantidad']?>"></td>
<td align="right" valign="middle" class="f">
<?php if($numero != 0){?>
<?php echo $detalles[0]['cantidad']; $tsolicitado = 0;?>
<?php }else{ ?>
<input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
<?php } ?>
</td>
<td align="right" valign="middle" class="f"><?php if($numero != 1){?>
  <?php echo $detalles[1]['cantidad']; $tsolicitado += $detalles[1]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>" >
  <?php } ?></td>
<td align="right" valign="middle"><span class="f">
  <?php if($numero != 2){?>
  <?php echo $detalles[2]['cantidad']; $tsolicitado += $detalles[2]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>" >
  <?php } ?>
</span></td>
<td align="right" valign="middle"><span class="f">
  <?php if($numero != 3){?>
  <?php echo $detalles[3]['cantidad']; $tsolicitado += $detalles[3]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?>
</span></td>
<td align="right" valign="middle" class="f">
<?php if($numero != 4){?>
  <?php echo $detalles[4]['cantidad']; $tsolicitado += $detalles[4]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>"> 
  <?php } ?></td>
<td align="right" valign="middle" class="f">
<?php if($numero != 5){?>
  <?php echo $detalles[5]['cantidad']; $tsolicitado += $detalles[5]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?></td>
<td align="right" valign="middle"><span class="f">
  <?php if($numero != 6){?>
  <?php echo $detalles[6]['cantidad']; $tsolicitado += $detalles[6]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?>
</span></td>
<td align="right" valign="middle"><span class="f">
  <?php if($numero != 7){?>
  <?php echo $detalles[7]['cantidad']; $tsolicitado += $detalles[7]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?>
</span></td>
<td align="right" valign="middle" class="f1"><span class="f">
  <?php if($numero != 8){?>
  <?php echo $detalles[8]['cantidad']; $tsolicitado += $detalles[8]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?>
</span></td>
<td align="right" valign="middle" class="f1"><span class="f">
  <?php if($numero != 9){?>
  <?php echo $detalles[9]['cantidad']; $tsolicitado += $detalles[9]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?>
</span></td>
<td align="right" valign="middle" class="f2"><?php echo $tsolicitado;?><input type="hidden" name="tsolicitado[<?php echo $kpartida?>]" value="<?php echo $tsolicitado;?>"></td>
<td align="right" valign="middle" class="f2"><?php echo $tentregado;?></td>
<td align="right" valign="middle"><span class="f">
  <?php if($numero != 10){?>
  <?php echo $detalles[10]['cantidad']; $tsolicitado = $detalles[10]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>" >
  <?php } ?>
</span></td>
<td align="right" valign="middle" class="f">
<?php if($numero != 11){?>
  <?php echo $detalles[11]['cantidad']; $tsolicitado = $detalles[11]['cantidad'];?>
  <?php }else{ ?>
  <input type="text" name="cantidad[<?php echo $kpartida?>]" value="0" size="4" class="ican" clave="<?php echo $kpartida;?>">
  <?php } ?></td>
<td align="right" valign="middle" class="f2"><?php echo $tdevuelto;?></td>
<td align="right" valign="middle" class="f2"><?php echo $tentregado - $tdevuelto;?></td>
<td valign="top"><input type="text" name="obs[<?php echo $kpartida; ?>]" class="iobs"></td>
</tr>
<?php } //fin de foerach partidasextra?>
<?php } //fin de if partidas extra?>
</tbody>
<tfoot>
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
<input type="hidden" name="guardar" value="true">
<input type="hidden" name="idproyecto_material" value="<?php echo $_GET['idproyecto_material']?>">
<button type="submit"><span>Guardar Captura</span></button>
<?php if($lider == true || $almacen == true){?>
<input type="hidden" name="capturo" value="<?php echo $_SESSION['MM_Userid']?>">
<?php } ?>
<?php if($pcaptura == true){?>
<input type="hidden" name="pcapturo" value="<?php echo $_SESSION['MM_Userid']?>">
<input type="hidden" name="numero" value="<?php echo $numero;?>">
<?php } ?>
</form>
</div>
<?php }else{?>

<p>No se puede capturar, por que ya fue autorizada o bien no tiene suficientes permisos.</p>

<?php } ?>
</body>
</html>
<?php 
mysql_free_result($rsPartidas);

mysql_free_result($rsMovimientos);

mysql_free_result($rsDetalle);

mysql_free_result($rsNivelUsuario);

mysql_free_result($rsMaterial);
?>
