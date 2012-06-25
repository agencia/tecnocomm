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

if(isset($_GET['dia1']) && isset($_GET['mes1']) && isset($_GET['ano1'])&&isset($_GET['dia2']) && isset($_GET['mes2']) && isset($_GET['ano2'])){
	$hoy['dia'] = $_GET['dia1'];
	$hoy['mes'] = $_GET['mes1'];
	$hoy['ano'] = $_GET['ano1'];
	$hoy1['dia'] = $_GET['dia2'];
	$hoy1['mes'] = $_GET['mes2'];
	$hoy1['ano'] = $_GET['ano2'];
} else {
	$hoy['dia'] = date("j");
	$hoy['mes'] = date("n");
	$hoy['ano'] = date("Y");
	$hoy1['dia'] = date("j");
	$hoy1['mes'] = date("n");
	$hoy1['ano'] = date("Y");
}

if(isset($_GET['usuario'])&&$_GET['usuario']!=''){
	if($_GET['usuario']!=-1){
		$usuario=" AND usuarios.id=".$_GET['usuario'];
	}
	else{
		$usuario="";
	}
}
else{
		$usuario="";
}

if(isset($_GET['nivel'])&&$_GET['nivel']!=''){
	if($_GET['nivel']!=-1){
		$nivel=" AND usuarios.responsabilidad=".$_GET['nivel'];
	}
	else{
		$nivel="";
	}
}
else{
		$nivel="";
}

if(isset($_GET['Movimiento'])&&$_GET['Movimiento']!=''){
	if($_GET['Movimiento']!=-1){
		$movimiento=" AND log.evento=".$_GET['Movimiento'];
	}
	else{
		$movimiento="";
	}
}
else{
		$movimiento="";
}


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUsr = "select * from usuarios order by nombrereal";
$RsUsr = mysql_query($query_RsUsr, $tecnocomm) or die(mysql_error());
$row_RsUsr = mysql_fetch_assoc($RsUsr);
$totalRows_RsUsr = mysql_num_rows($RsUsr);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsNivel = "select * from nombres_accesos order by nombre";
$RsNivel = mysql_query($query_RsNivel, $tecnocomm) or die(mysql_error());
$row_RsNivel = mysql_fetch_assoc($RsNivel);
$totalRows_RsNivel = mysql_num_rows($RsNivel);

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsMov = "select * from eventos order by id";
$RsMov = mysql_query($query_RsMov, $tecnocomm) or die(mysql_error());
$row_RsMov = mysql_fetch_assoc($RsMov);
$totalRows_RsMov = mysql_num_rows($RsMov);

$fecha1_RsDetalle = "-1";
if (isset($_GET['ano1'])) {
  $fecha1_RsDetalle = $_GET['ano1']."-".$_GET['mes1']."-".$_GET['dia1'];
}
$fecha2_RsDetalle = "-1";
if (isset($_GET['ano2'])) {
  $fecha2_RsDetalle = $_GET['ano2']."-".$_GET['mes2']."-".$_GET['dia2'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsDetalle = sprintf("select *,(select nombre from nombres_accesos where nombres_accesos.id=usuarios.responsabilidad) as nivel  from usuarios,log where 1=1 and usuarios.id=log.idusuario %s  %s  %s and  log.fecha between %s and %s", $usuario, $nivel, $movimiento, GetSQLValueString($fecha1_RsDetalle, "date"),GetSQLValueString($fecha2_RsDetalle, "date"));

$RsDetalle = mysql_query($query_RsDetalle, $tecnocomm) or die(mysql_error());
$row_RsDetalle = mysql_fetch_assoc($RsDetalle);
$totalRows_RsDetalle = mysql_num_rows($RsDetalle);
?><?
$mes = array(
1 => "Enero",
2 => "Febrero",
3 => "Marzo",
4 => "Abril",
5 => "Mayo",
6 => "Junio",
7 => "Julio",
8 => "Agosto",
9 => "Septiembre",
10 => "Octubre",
11 => "Noviembre",
12 => "Diciembre"
);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>
<form name="consulta" method="get">
<table width="700" border="0" align="center" class="wrapper">
  <tr>
    <td colspan="4" align="center" class="titulos">REGISTRO DE MOVIMIENTOS EN EL SISTEMA</td>
  </tr>
  <tr>
    <td colspan="4" align="center">&nbsp;</td>
    </tr>
  <tr>
    <td colspan="4" align="center"><label>Dia:
        <select name="dia1" class="form" id="dia1">
          <?php for($a=1;$a<=31;$a++) { ?>
          <option value="<?php echo $a; ?>"<?php if($a == $hoy['dia']) { echo " selected=\"selected\" "; } ?>><?php echo $a; ?></option>
          <?php } ?>
        </select>
    </label>
      <label>Mes:
      <select name="mes1" class="form" id="mes1">
        <?php for($a=1;$a<=12;$a++) { ?>
        <option value="<?php echo $a; ?>"<?php if($a == $hoy['mes']) { echo " selected=\"selected\" "; } ?>><?php echo $mes[$a]; ?></option>
        <?php } ?>
      </select>
      </label>
      <label>A&ntilde;o:
      <input name="ano1" type="text" class="form" id="ano1" value="<?php echo date("Y")?>" size="8" />
      </label>
      <label>Dia:
      <select name="dia2" class="form" id="dia2">
        <?php for($a=1;$a<=31;$a++) { ?>
        <option value="<?php echo $a; ?>"<?php if($a == $hoy1['dia']) { echo " selected=\"selected\" "; } ?>><?php echo $a; ?></option>
        <?php } ?>
      </select>
      </label>
      <label>Mes:
      <select name="mes2" class="form" id="mes2">
        <?php for($a=1;$a<=12;$a++) { ?>
        <option value="<?php echo $a; ?>"<?php if($a == $hoy1['mes']) { echo " selected=\"selected\" "; } ?>><?php echo $mes[$a]; ?></option>
        <?php } ?>
      </select>
      </label>
      <label>A&ntilde;o
      <input name="ano2" type="text" class="form" id="ano2"  value="<?php echo date("Y")?>" size="8"/>
      </label></td>
    </tr>
  <tr>
    <td colspan="4" align="center">&nbsp;</td>
    </tr>
  <tr>
    <td colspan="4" align="center"><label>Usuario:
        <select name="usuario" class="form" id="usuario">
          <option value="-1" <?php if (!(strcmp(-1, $_GET['usuario']))) {echo "selected=\"selected\"";} ?>>Todos</option>
          <?php
do {  
?>
          <option value="<?php echo $row_RsUsr['id']?>"<?php if (!(strcmp($row_RsUsr['id'], $_GET['usuario']))) {echo "selected=\"selected\"";} ?>><?php echo $row_RsUsr['username']?></option>
          <?php
} while ($row_RsUsr = mysql_fetch_assoc($RsUsr));
  $rows = mysql_num_rows($RsUsr);
  if($rows > 0) {
      mysql_data_seek($RsUsr, 0);
	  $row_RsUsr = mysql_fetch_assoc($RsUsr);
  }
?>
        </select>
    Nivel:
    <select name="nivel" class="form" id="nivel">
      <option value="-1" <?php if (!(strcmp(-1, $_GET['nivel']))) {echo "selected=\"selected\"";} ?>>Todos</option>
      <?php
do {  
?>
      <option value="<?php echo $row_RsNivel['id']?>"<?php if (!(strcmp($row_RsNivel['id'], $_GET['nivel']))) {echo "selected=\"selected\"";} ?>><?php echo $row_RsNivel['nombre']?></option>
      <?php
} while ($row_RsNivel = mysql_fetch_assoc($RsNivel));
  $rows = mysql_num_rows($RsNivel);
  if($rows > 0) {
      mysql_data_seek($RsNivel, 0);
	  $row_RsNivel = mysql_fetch_assoc($RsNivel);
  }
?>
    </select>
    Movimiento:
    <select name="Movimiento" class="form" id="Movimiento">
      <option value="-1" <?php if (!(strcmp(-1, $_GET['Movimiento']))) {echo "selected=\"selected\"";} ?>>Todos</option>
      <?php
do {  
?>
      <option value="<?php echo $row_RsMov['id']?>"<?php if (!(strcmp($row_RsMov['id'], $_GET['Movimiento']))) {echo "selected=\"selected\"";} ?>><?php echo $row_RsMov['nombre']?></option>
      <?php
} while ($row_RsMov = mysql_fetch_assoc($RsMov));
  $rows = mysql_num_rows($RsMov);
  if($rows > 0) {
      mysql_data_seek($RsMov, 0);
	  $row_RsMov = mysql_fetch_assoc($RsMov);
  }
?>
    </select>
    </label></td>
    </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td align="center"><label></label></td>
    <td>&nbsp;</td>
    </tr>
  <tr>
    <td width="142">Total de eventos:<?php echo $totalRows_RsDetalle ?> </td>
    <td width="135">&nbsp;</td>
    <td width="144" align="center"><input type="submit" name="button" id="button" value="Consultar" /></td>
    <td width="259">&nbsp;</td>
    </tr>
  <tr class="titleTabla">
    <td align="center">USUARIO</td>
    <td align="center">FECHA</td>
    <td align="center">NIVEL</td>
    <td align="center">DETALLE</td>
    </tr>
  <?php do { ?>
    <tr>
      <td align="center"><?php echo $row_RsDetalle['username']; ?></td>
      <td align="center"><?php echo $row_RsDetalle['fecha']; ?></td>
      <td align="center"><?php echo $row_RsDetalle['nivel']; ?></td>
      <td align="center"><?php echo $row_RsDetalle['detalle']; ?></td>
    </tr>
    <?php } while ($row_RsDetalle = mysql_fetch_assoc($RsDetalle)); ?>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    </tr>
</table>
</form>
</body>
</html>
<?php
mysql_free_result($RsUsr);

mysql_free_result($RsNivel);

mysql_free_result($RsMov);

mysql_free_result($RsDetalle);
?>
