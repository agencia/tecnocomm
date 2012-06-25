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


if(isset($_POST['guardar']) && $_POST['guardar'] == true){
	//guardamos la salida
	 
	$cantidades = $_POST['cantidad'][$_POST['numero']];
	
	
	
	foreach($cantidades as $kcant => $cantidad){
			
			//verificamos que no este lla el registro
			mysql_select_db($database_tecnocomm,$tecnocomm);
			$sql=sprintf("SELECT * FROM material WHERE numero=%s AND idip=%s AND idsubcotizacion=%s",
						 		GetSQLValueString($_POST['numero'],"int"),
								GetSQLValueString($_POST['idip'],"int"),
								GetSQLValueString($kcant,"int"));
			
			$rs=mysql_query($sql,$tecnocomm);
			
			
			
			if(mysql_num_rows($rs) == 1){
			
				$mat = mysql_fetch_assoc($rs);
			
				$updateSQL = sprintf("UPDATE material SET cantidad = %s,  idusuario_confirmo = %s, fecha=NOW(), observaciones = %s WHERE idmaterial=%s",
									 		GetSQLValueString($cantidad,"double"),
											GetSQLValueString($_SESSION['MM_Userid'],"int"),
											GetSQLValueString($_POST['observaciones'][$kcant],"text"),
											GetSQLValueString($mat['idmaterial'],"int"));
				
				$rs = mysql_query($updateSQL,$tecnocomm) or die(mysql_error());
				
			}else{
			
			
			mysql_select_db($database_tecnocomm,$tecnocomm);
				$insertSQL = sprintf("INSERT INTO material(idsubcotizacion, cantidad, tipo, idusuario, fecha, numero, observaciones, idip) 
												  VALUES(%s, %s, %s, %s, NOW(), %s, %s, %s)",
												  GetSQLValueString($kcant,"int"),
												  GetSQLValueString($cantidad,"double"),
												  GetSQLValueString($_POST['tipo'],"int"),
												  GetSQLValueString($_SESSION['MM_Userid'],"int"),
												  GetSQLValueString($_POST['numero'],"int"),
												  GetSQLValueString($_POST['observaciones'][$kcant],"text"),
												  GetSQLValueString($_POST['idip'],"int"));
				$rs = mysql_query($insertSQL,$tecnocomm) or die(mysql_error());
			}
		
		}
		
		//actualizar numero de salida
		switch($_POST['numero']){
			
				case 1:
				case 3:
				case 4:
				case 5:  mysql_select_db($dabase_tecnocomm,$tecnocomm);
							$updateSQL = sprintf("UPDATE ip SET numero = %s WHERE idip = %s",
											 			GetSQLValueString($_POST['numero'] + 1,"int"),
														GetSQLValueString($_POST['idip'],"int"));
							$rsUpdate = mysql_query($updateSQL,$tecnocomm) or die(mysql_error());
				break;
				
			
			}
		
		header("Location: close.php");
}


$colname_rsPartidas = "-1";
if (isset($_GET['idip'])) {
  $colname_rsPartidas = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsPartidas = sprintf("SELECT s.*,a.codigo FROM subcotizacionarticulo s JOIN ip i ON s.idsubcotizacion = i.cotizacion LEFT JOIN articulo a ON a.idarticulo = s.idarticulo WHERE i.idip = %s", GetSQLValueString($colname_rsPartidas, "int"));
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


do{
	
	$material[$row_rsMaterial['idsubcotizacion']][$row_rsMaterial['numero']] = $row_rsMaterial['cantidad'];
	
}while($row_rsMaterial = mysql_fetch_assoc($rsMaterial));

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
				$almcen = true;
			}
			
		}while($row_rsNivelUsuario = mysql_fetch_assoc($rsNivelUsuario));
		
	}
}

//$nSalida = (isset($row_rsNumCaptura['numsalida']) && $row_rsNumCaptura['numsalida'] > 0)?$row_rsNumCaptura['numsalida']:0;
//$nEntrada = (isset($row_rsNumCaptura['numentrada']) && $row_rsNumCaptura['numentrada'] > 0)?$row_rsNumCaptura['numentrada']:0;

$numero = $_GET['num'];

function isEnabled($ns,$ni,$tipe,$li,$pc,$al){
		
		if($ns == $ni && $tipe == "so"  && ($li == true || $pc == true)){
				return "";
		}
		
		if($ns==$ni && $tipe == "en" && $al == true){
				return "";	
		}
		
		
		return "disabled=\"disabled\" readonly=\"readonly\"";
	
	
}

$disabled = "disabled=\"disabled\" readonly=\"readonly\"";


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Confirmar Datos</title>
<script language="javascript" src="js/jquery.js"></script>
<script language="javascript" src="js/onpopup.js"></script>
<script language="javascript" src="js/funciones.js"></script>
<link href="style2.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h1>SOLICITUD, ENTREGA Y DEVOLUCION INTERNA DE MATERIALES COTIZADOS</h1>
<?php include("ip.encabezado.php");?>

<?php if($_GET['num'] == $row_rsNumCapura['numero'] && $_GET['num'] < 9){ 
$permitir = true;
}
?>

<?php if($_GET['num'] == 2 && $row_rsNumCapura['numero'] == 0){ 
$permitir = true;
}
?>

<?php if($_GET['num'] == 1 && $row_rsNumCapura['numero'] == 0){ 
$permitir = true;
}
?>

<?php if($_GET['num'] == $row_rsNumCapura['devolucion'] && $_GET['num'] > 8){
$permitir = true;
}?>

<?php if($permitir == false){?>

<p> El numero de solicitud o entrega esta cerrado </p>

<?php } ?>

<?php if($permitir == true){?>

<ul>
<li><a href="ip.material.add.php" class="popup">Partida Extra</a></li>
</ul>
<form name="frmMateriales" method="post" action="">
<div class="distabla smallfont">
<table cellspacing="0">
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
  <?php do { ?>
    <tr>
      <td valign="top">&nbsp;</td>
      <td valign="top"><?php echo $row_rsPartidas['codigo']; ?></td>
      <td valign="top"><?php echo $row_rsPartidas['marca1']; ?></td>
      <td valign="top"><?php echo $row_rsPartidas['descri']; ?></td>
      <td valign="top"><?php echo $row_rsPartidas['cantidad']; ?></td>
      <td valign="top"><input name="cantidad[1][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][1];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][1];?>"  size="3"  <?php if($numero != 1){echo $disabled;}?>></td>
      <td valign="top"><input name="cantidad[2][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][2];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][2];?>" size="3"  <?php if($numero != 2){echo $disabled;}?>></td>
      <td valign="top"><input name="cantidad[3][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][3];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][3];?>" size="3"  <?php if($numero != 3 ){echo $disabled;}?>></td>
      <td valign="top"><input name="cantidad[4][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][4];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][4];?>" size="3" <?php if($numero != 4){echo $disabled;}?>> </td>
      <td valign="top"><input name="cantidad[5][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][5];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][5];?>" size="3"  <?php if($numero != 5){echo $disabled;}?>></td>
      <td valign="top"><input name="cantidad[6][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][6];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][6];?>" size="3"  <?php if($numero != 6){echo $disabled;}?>></td>
      <td valign="top"><input name="cantidad[7][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][7];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][7];?>" size="3"  <?php if($numero != 7){echo $disabled;}?>></td>
      <td valign="top"><input name="cantidad[8][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][8];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][8];?>" size="3"   <?php if($numero != 8){echo $disabled;}?>></td>
      <td valign="top"><input name="cantidad[9][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][9];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][9];?>" size="3"   <?php if($numero != 9){echo $disabled;}?> /></td>
      <td valign="top"><input name="cantidad[10][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][10];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][10];?>" size="3"   <?php if($numero != 10){echo $disabled;}?> /></td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top"><input name="cantidad[11][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][11];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][11];?>" size="3"   <?php if($numero != 11){echo $disabled;}?>></td>
      <td valign="top"><input name="cantidad[12][<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive" value="<?php echo $material[$row_rsPartidas['idsubcotizacionarticulo']][12];$sol = $material[$row_rsPartidas['idsubcotizacionarticulo']][12];?>" size="3"   <?php if($numero != 12){echo $disabled;}?>></td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top"><input name="observaciones[<?php echo $row_rsPartidas['idsubcotizacionarticulo']; ?>]" type="text" class="iactive"></td>
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
<input type="hidden" name="guardar" value="true" />
<input type="hidden" name="numero" value="<?php echo $_GET['num'];?>" />
<input type="hidden" name="tipe" value="<?php echo $_GET['tipe'];?>" />
<input type="hidden" name="idip" value="<?php echo $_GET['idip']; ?>" />
<button type="submit"> Guardar </button>
</form>
<?php } ?>
</body>
</html>