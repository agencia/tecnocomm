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

$colname_rsTarea = "-1";
if (isset($_GET['idtarea'])) {
  $colname_rsTarea = $_GET['idtarea'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTarea = sprintf("SELECT * FROM tarea WHERE idtarea = %s", GetSQLValueString($colname_rsTarea, "int"));
$rsTarea = mysql_query($query_rsTarea, $tecnocomm) or die(mysql_error());
$row_rsTarea = mysql_fetch_assoc($rsTarea);
$totalRows_rsTarea = mysql_num_rows($rsTarea);

$colname_rs_usuariostareas = "-1";
if (isset($_GET['idtarea'])) {
  $colname_rs_usuariostareas = $_GET['idtarea'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rs_usuariostareas = sprintf("SELECT tu.* FROM tarea t JOIN tarea_usuario tu ON t.idtarea = tu.idtarea WHERE t.idtarea = %s", GetSQLValueString($colname_rs_usuariostareas, "int"));
$rs_usuariostareas = mysql_query($query_rs_usuariostareas, $tecnocomm) or die(mysql_error());
$totalRows_rs_usuariostareas = mysql_num_rows($rs_usuariostareas);
$row_rs_usuariostareas = mysql_fetch_assoc($rs_usuariostareas);

do{
	$usuarios[] = $row_rs_usuariostareas['idusuario'];
}while($row_rs_usuariostareas = mysql_fetch_assoc($rs_usuariostareas));



?>

<?php 
if(isset($_POST['comentar']) && $_POST['comentar'] == 'true'){
	//Guardar Comentario y actualizar status.
	
	$query_tarea = sprintf('SELECT *  FROM tarea WHERE idtarea = %s',
						   GetSQLValueString($_POST['idtarea'],"int"));
	mysql_select_db($database_tecnocomm,$tecnocomm);
	$rs_tarea = mysql_query($query_tarea, $tecnocomm) or die(mysql_error());
	
	$row_tarea = mysql_fetch_assoc($rs_tarea);
	
	
	$comentario = json_decode($row_tarea['comentario'],true);

	if(is_array($comentario)){
		
	
		$comentario[] = array('fecha'=>date('d/m/Y h:i'),
							  'usuario'=>$_SESSION['MM_Username'],
							  'comentario'=>htmlentities($_POST['comentario'],ENT_QUOTES | ENT_IGNORE, "UTF-8"));
		$coment = json_encode($comentario);
		
	
	}else{
		
		if(strlen($_POST['comentario']) > 0){
		$comentario[] = array('fecha'=>date('d/m/Y h:i'),
							  'usuario'=>$_SESSION['MM_Username'],
							  'comentario'=>htmlentities($_POST['comentario'],ENT_QUOTES | ENT_IGNORE, "UTF-8"));
		$coment = json_encode($comentario);
		}else{
		$coment = "";	
		}
	}
	
	$estado = isset($_POST['marcarrealizado'])?1: $row_tarea['estado'];
	
	if($estado == 1){
		$fecharealizo = date('Y-m-d');	
	}else{
		$fecharealizo = null;
	}

									
	$query_update = sprintf("UPDATE tarea SET estado=%s, comentario = '%s', fecharealizo = %s  WHERE idtarea = %s ",
							GetSQLValueString($estado,"int"),
							str_replace('\&#039;', '&#039;',$coment),
							GetSQLValueString($fecharealizo,"text"),
							GetSQLValueString($_POST['idtarea'],"int"));
	mysql_select_db($database_tecnocomm,$tecnocomm);
	$rs_update = mysql_query($query_update,$tecnocomm) or die(mysql_error());
	
	
}

?>



<form name="comentar" id="com" method="post">

<?php 

if(is_array($usuarios))
if(in_array($_SESSION['MM_Userid'],$usuarios)):?>
<label for="marcarrealizado">Marcar Tarea Como Realizada</label>
<input type="checkbox" name="marcarrealizado" id="marcarrealizado" />
<br />
<br />
<?php endif;?>
<label>Comentar</label>
<br />
<textarea name="comentario" style="width:600px;height:400px"></textarea>
<input type="hidden" name="idtarea" value="<?php echo $_GET['idtarea'];?>" />
<input type="hidden" name="comentar" value="true" />
</form>

