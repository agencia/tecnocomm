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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsAvisos = 30;
$pageNum_rsAvisos = 0;
if (isset($_GET['pageNum_rsAvisos'])) {
  $pageNum_rsAvisos = $_GET['pageNum_rsAvisos'];
}
$startRow_rsAvisos = $pageNum_rsAvisos * $maxRows_rsAvisos;


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAvisos = ("SELECT * FROM avisos  ORDER BY prioridad,fecha,liberado DESC");
$query_limit_rsAvisos = sprintf("%s LIMIT %d, %d", $query_rsAvisos, $startRow_rsAvisos, $maxRows_rsAvisos);
$rsAvisos = mysql_query($query_limit_rsAvisos, $tecnocomm) or die(mysql_error());
$row_rsAvisos = mysql_fetch_assoc($rsAvisos);

if (isset($_GET['totalRows_rsAvisos'])) {
  $totalRows_rsAvisos = $_GET['totalRows_rsAvisos'];
} else {
  $all_rsAvisos = mysql_query($query_rsAvisos);
  $totalRows_rsAvisos = mysql_num_rows($all_rsAvisos);
}
$totalPages_rsAvisos = ceil($totalRows_rsAvisos/$maxRows_rsAvisos)-1;$maxRows_rsAvisos = 30;
$pageNum_rsAvisos = 0;
if (isset($_GET['pageNum_rsAvisos'])) {
  $pageNum_rsAvisos = $_GET['pageNum_rsAvisos'];
}
$startRow_rsAvisos = $pageNum_rsAvisos * $maxRows_rsAvisos;


$para='';
if(isset($_GET['para']) && $_GET['para']!=-1){
	$para=' and para='.$_GET['para'];
}

$de='';
if(isset($_GET['de']) && $_GET['de']!=-1){
	$de=' and de='.$_GET['de'];
}

$prio='';
if(isset($_GET['prioridad']) && $_GET['prioridad']!=-1){
	$prio=' and (prioridad='.$_GET['prioridad']."  and padre is null)";
}

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAvisos = ("SELECT *,(select nombrereal from usuarios where id=de ) as dee, (select nombrereal from usuarios where id=para ) as par FROM avisos WHERE  padre is null $para $de $prioridad  ORDER BY prioridad,fecha DESC");
$query_limit_rsAvisos = sprintf("%s LIMIT %d, %d", $query_rsAvisos, $startRow_rsAvisos, $maxRows_rsAvisos);
$rsAvisos = mysql_query($query_limit_rsAvisos, $tecnocomm) or die(mysql_error());
$row_rsAvisos = mysql_fetch_assoc($rsAvisos);

if (isset($_GET['totalRows_rsAvisos'])) {
  $totalRows_rsAvisos = $_GET['totalRows_rsAvisos'];
} else {
  $all_rsAvisos = mysql_query($query_rsAvisos);
  $totalRows_rsAvisos = mysql_num_rows($all_rsAvisos);
}
$totalPages_rsAvisos = ceil($totalRows_rsAvisos/$maxRows_rsAvisos)-1;



$queryString_rsAvisos = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsAvisos") == false && 
        stristr($param, "totalRows_rsAvisos") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsAvisos = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsAvisos = sprintf("&totalRows_rsAvisos=%d%s", $totalRows_rsAvisos, $queryString_rsAvisos);
$realizado=array(0=>"No",1=>"Si");

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsUsr = "SELECT * FROM usuarios ORDER BY nombrereal ASC";
$RsUsr = mysql_query($query_RsUsr, $tecnocomm) or die(mysql_error());
$row_RsUsr = mysql_fetch_assoc($RsUsr);
$totalRows_RsUsr = mysql_num_rows($RsUsr);
?>
<h1> Avisos </h1>
<div class="submenu"> <a href="nuevoAviso.php" onclick="NewWindow(this.href,'Nuevo Aviso',600,800,'yes'); return false;"> Aviso Nuevo </a>  &nbsp;&nbsp;&nbsp;<a href="nuevoAvisoMasivo.php" onclick="NewWindow(this.href,'Nuevo Aviso Masivo',600,800,'yes'); return false;"> Aviso Masivo </a></div>
<div class="buscar">
<form action="" method="get" name="buscar">
<label>Para
<select name="para" onchange="this.form.submit();">
  <option value="-1" <?php if (!(strcmp(-1, $_GET['para']))) {echo "selected=\"selected\"";} ?>>Seleccionar</option>
  <?php
do {  
?>
  <option value="<?php echo $row_RsUsr['id']?>"<?php if (!(strcmp($row_RsUsr['id'], $_GET['para']))) {echo "selected=\"selected\"";} ?>><?php echo $row_RsUsr['nombrereal']?></option>
  <?php
} while ($row_RsUsr = mysql_fetch_assoc($RsUsr));
  $rows = mysql_num_rows($RsUsr);
  if($rows > 0) {
      mysql_data_seek($RsUsr, 0);
	  $row_RsUsr = mysql_fetch_assoc($RsUsr);
  }
?></select></label>
<label>Envio
<select name="envio" id="select" onchange="this.form.submit();">
  <option value="-1" <?php if (!(strcmp(-1, $_GET['envio']))) {echo "selected=\"selected\"";} ?>>Seleccionar</option>
  <?php
do {  
?>
  <option value="<?php echo $row_RsUsr['id']?>"<?php if (!(strcmp($row_RsUsr['id'], $_GET['envio']))) {echo "selected=\"selected\"";} ?>><?php echo $row_RsUsr['nombrereal']?></option>
  <?php
} while ($row_RsUsr = mysql_fetch_assoc($RsUsr));
  $rows = mysql_num_rows($RsUsr);
  if($rows > 0) {
      mysql_data_seek($RsUsr, 0);
	  $row_RsUsr = mysql_fetch_assoc($RsUsr);
  }
?>
</select>
</label>
<label>Prioridad
<select name="prioridad" id="prioridad" onchange="this.form.submit();">
  <option value="-1" <?php if (!(strcmp(-1, $_GET['prioridad']))) {echo "selected=\"selected\"";} ?>>Seleccionar</option>
  <option value="0" <?php if (!(strcmp(0, $_GET['prioridad']))) {echo "selected=\"selected\"";} ?>>Normal</option>
  <option value="1" <?php if (!(strcmp(1, $_GET['prioridad']))) {echo "selected=\"selected\"";} ?>>Urgente</option>
</select>
</label>
<input type="hidden" name="mod" value="catavisosadmin"/>
</form>
</div>

<div id="distabla">
<table width="100%" cellpadding="0" cellspacing="0">
<thead>
<tr><td colspan="6" align="right"><table border="0">
  <tr>
    <td><?php if ($pageNum_rsAvisos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsAvisos=%d%s", $currentPage, 0, $queryString_rsAvisos); ?>"><img src="images/First.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsAvisos > 0) { // Show if not first page ?>
        <a href="<?php printf("%s?pageNum_rsAvisos=%d%s", $currentPage, max(0, $pageNum_rsAvisos - 1), $queryString_rsAvisos); ?>"><img src="images/Previous.gif"></a>
        <?php } // Show if not first page ?></td>
    <td><?php if ($pageNum_rsAvisos < $totalPages_rsAvisos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsAvisos=%d%s", $currentPage, min($totalPages_rsAvisos, $pageNum_rsAvisos + 1), $queryString_rsAvisos); ?>"><img src="images/Next.gif"></a>
        <?php } // Show if not last page ?></td>
    <td><?php if ($pageNum_rsAvisos < $totalPages_rsAvisos) { // Show if not last page ?>
        <a href="<?php printf("%s?pageNum_rsAvisos=%d%s", $currentPage, $totalPages_rsAvisos, $queryString_rsAvisos); ?>"><img src="images/Last.gif"></a>
        <?php } // Show if not last page ?></td>
  </tr>
</table></td></tr>
<tr>
<td width="7%">Opciones</td>
<td width="25%">Para</td>
<td width="22%">De</td>
<td width="9%">Fecha</td>
<td width="9%">Hora</td>
  <td width="28%">Realizado?</td>
  </tr>
</thead>
<tbody>
  <?php do { 
  
  $colname_RsConversacion = "-1";
if (isset($row_rsAvisos['id'])) {
  $colname_RsConversacion = $row_rsAvisos['id'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_RsConversacion = sprintf("SELECT *,(select nombrereal from usuarios where id=de) as dice FROM avisos WHERE padre = %s ORDER BY id ASC", GetSQLValueString($colname_RsConversacion, "int"));
$RsConversacion = mysql_query($query_RsConversacion, $tecnocomm) or die(mysql_error());
$row_RsConversacion = mysql_fetch_assoc($RsConversacion);
$totalRows_RsConversacion = mysql_num_rows($RsConversacion);


//echo $query_RsConversacion;
  
  ?>
      <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
        <td><a href="modificarAviso1.php?id=<?php echo $row_rsAvisos['id']; ?>" onclick="NewWindow(this.href,'Modificar Aviso',800,800,'yes');return false;"><img src="images/Edit.png" alt="editar" width="24" height="24"  border="0" title="MODIFICAR AVISO"/></a><?php if($row_rsAvisos['prioridad']==0){?><img src="images/verde.gif" border="0" align="middle" title="Prioridad Normal" /><?php } else{?><img src="images/rojo.gif" border="0" align="middle" title="Prioridad URGENTE" /> <?php }?></td>
        <td><?php echo $row_rsAvisos['par']; ?></td>
        <td><?php echo $row_rsAvisos['dee']; ?></td>
        <td><?php echo $row_rsAvisos['fecha']; ?></td>
        <td><?php echo $row_rsAvisos['hora']; ?></td>
        <td><?php echo $realizado[$row_rsAvisos['realizado']]; ?></td>
      </tr>
      <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
        <td align="right">Mensaje</td>
        <td colspan="5"><?php echo $row_rsAvisos['mensaje']; ?></td>
      </tr>
      <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
        <td align="right">&nbsp;</td>
        <td colspan="5">&nbsp;</td>
      </tr>
      <?php if ($totalRows_RsConversacion>0){?>
      <?php do{?>
      <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
        <td align="right"><?php if($row_RsConversacion['prioridad']==0){?><img src="images/verde.gif" border="0" align="middle" title="Prioridad Normal" /><?php } else{?><img src="images/rojo.gif" border="0" align="middle" title="Prioridad URGENTE" /> <?php }?></td>
        <td colspan="5" class="fond"><?php echo $row_RsConversacion['dice']?> dice:</td>
      </tr>
      <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?> class="border">
        <td align="right"></td>
        <td colspan="5"><?php echo $row_RsConversacion['mensaje']?></td>
      </tr>
      <tr <?php $i = !$i; echo "class=\"fondo".$i,"\""; ?>>
        <td align="right">&nbsp;</td>
        <td colspan="5" align="right">Ultimo mensaje <?php echo $row_RsConversacion['fecha']?> a las <?php echo $row_RsConversacion['hora']?></td>
      </tr>
      
      <?php } while ($row_RsConversacion = mysql_fetch_assoc($RsConversacion)); }?> 
       <tr >
    <td align="right">&nbsp;</td>
    <td colspan="5"><a href="respuestaAviso.php?padre=<?php echo $row_rsAvisos['id']; ?>&id=<?php echo $row_rsAvisos['id']; ?>" onclick="NewWindow(this.href,'Responder Aviso',800,800,'yes');return false;"><img src="images/consiliar.png" border="0" align="middle" title="Responder a conversacion" />Responder</a></td>
      </tr>
    <?php } while ($row_rsAvisos = mysql_fetch_assoc($rsAvisos)); ?>
</tbody>
<tfoot>
<tr><td colspan="6" align="right">
    <table border="0">
      <tr>
        <td><?php if ($pageNum_rsAvisos > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsAvisos=%d%s", $currentPage, 0, $queryString_rsAvisos); ?>"><img src="images/First.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsAvisos > 0) { // Show if not first page ?>
            <a href="<?php printf("%s?pageNum_rsAvisos=%d%s", $currentPage, max(0, $pageNum_rsAvisos - 1), $queryString_rsAvisos); ?>"><img src="images/Previous.gif" /></a>
            <?php } // Show if not first page ?></td>
        <td><?php if ($pageNum_rsAvisos < $totalPages_rsAvisos) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsAvisos=%d%s", $currentPage, min($totalPages_rsAvisos, $pageNum_rsAvisos + 1), $queryString_rsAvisos); ?>"><img src="images/Next.gif" /></a>
            <?php } // Show if not last page ?></td>
        <td><?php if ($pageNum_rsAvisos < $totalPages_rsAvisos) { // Show if not last page ?>
            <a href="<?php printf("%s?pageNum_rsAvisos=%d%s", $currentPage, $totalPages_rsAvisos, $queryString_rsAvisos); ?>"><img src="images/Last.gif" /></a>
            <?php } // Show if not last page ?></td>
      </tr>
    </table></td></tr></tfoot>
</table>
</div>
<?php
mysql_free_result($rsAvisos);

mysql_free_result($RsConversacion);

mysql_free_result($RsUsr);
?>
