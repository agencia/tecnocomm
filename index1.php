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

if (!isset($_SESSION)) {  
session_start();
}
/*
if(isset($_SESSION['MM_Username'])){
 header("Location: systemIndex.php");
}*/
$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "index.php";
  $MM_redirectLoginFailed = "systemFail.php";
  $MM_redirecttoReferrer = false;  mysql_select_db($database_tecnocomm, $tecnocomm);
    $LoginRS__query=sprintf("SELECT id, username,responsabilidad  FROM usuarios WHERE username='%s' AND password='%s' AND activar=1",    get_magic_quotes_gpc() ? $loginUsername : addslashes($loginUsername), $password); 
      $LoginRS = mysql_query($LoginRS__query, $tecnocomm) or die(mysql_error());
  $loginFoundUser1 = mysql_fetch_assoc($LoginRS);
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = $loginFoundUser1['responsabilidad'];
        //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
	$_SESSION['MM_Userid']	= $loginFoundUser1['id'];
    $_SESSION['MM_UserGroup'] = $loginStrGroup;
  $_SESSION['mnuevos'] = 0;
	          if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
	    }	require_once('lib/eventos.php');
	$evt = new evento(1,$_SESSION['MM_Userid'],"A ingresado al sistema");
	$evt->registrar();
    header("Location: " . $MM_redirectLoginSuccess );
  }  else {    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<html>
<head>
<title>Systema Tecnocomm</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style2.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<!-- Save for Web Slices (portadatecnocomm.psd) -->
<table id="Tabla_01" width="640" height="480" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="4">
			<img src="images/header.png" width="640" height="121" alt=""></td>
	</tr>
	<tr>
		<td rowspan="4">
			<img src="images/index1_02.png" width="1" height="359" alt=""></td>
		<td valign="top">
		<div id="myform">
        
        <form id="form1" name="form1" method="POST" action="<?php echo $loginFormAction; ?>">  
        <div>
        <label> Nombre de usuario:    <input name="username" type="text"  id="username" /></label>
        <label> Contrase&ntilde;a  
          <input name="password" type="password" class="form" id="password" /></label> 
</div>
<div class="botones">
<button type="submit" class="button"><span>Acceder</span></button>
  </div>       
  </form>
       
        </div>	
          </td>
		<td colspan="2">
			<img src="images/aviso.png" width="287" height="221" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/index1_05.png" width="352" height="1" alt=""></td>
		<td colspan="2">
			<img src="images/index1_06.png" width="287" height="1" alt=""></td>
	</tr>
	<tr>
		<td rowspan="2">
			<img src="images/footerizquierdo.png" width="352" height="137" alt=""></td>
		<td colspan="2">
			<img src="images/footerderecho.png" width="287" height="114" alt=""></td>
	</tr>
	<tr>
		<td>
			<img src="images/index1_09.png" width="74" height="23" alt=""></td>
		<td>
			<a href="http://www.leysoft.com.mx" target="_blank"><img src="images/firma.png" width="213" height="23" alt="" border="0"></a></td>
	</tr>
</table>
<!-- End Save for Web Slices -->
</body>
</html>