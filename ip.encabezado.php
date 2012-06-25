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

$colname_rsEncabezado = "-1";
if (isset($_GET['idip'])) {
  $colname_rsEncabezado = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsEncabezado = sprintf("SELECT i.idip, i.fecha, i.hora,i.descripcion,i.titulo, c.nombre AS nombrecliente, c.direccion, c.ciudad,c.abreviacion, co.nombre AS nombrecontacto, co.correo, co.telefono, co.telefono2,c.idcliente FROM ip i LEFT JOIN cliente c ON i.idcliente = c.idcliente LEFT JOIN contactoclientes co ON i.idcontacto = co.idcontacto WHERE i.idip = %s", GetSQLValueString($colname_rsEncabezado, "int"));
$rsEncabezado = mysql_query($query_rsEncabezado, $tecnocomm) or die(mysql_error());
$row_rsEncabezado = mysql_fetch_assoc($rsEncabezado);
$totalRows_rsEncabezado = mysql_num_rows($rsEncabezado);

$colname_rsAtendido = "-1";
if (isset($_GET['idip'])) {
  $colname_rsAtendido = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsAtendido = sprintf("SELECT u.nombrereal,u.username FROM ip i LEFT JOIN usuarios u ON i.idatendio = u.id WHERE idip = %s", GetSQLValueString($colname_rsAtendido, "int"));
$rsAtendido = mysql_query($query_rsAtendido, $tecnocomm) or die(mysql_error());
$row_rsAtendido = mysql_fetch_assoc($rsAtendido);
$totalRows_rsAtendido = mysql_num_rows($rsAtendido);

$colname_rsResponsable = "-1";
if (isset($_GET['idip'])) {
  $colname_rsResponsable = $_GET['idip'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsResponsable = sprintf("SELECT pp.*,u.nombrereal,u.username FROM ip i LEFT JOIN proyecto_personal pp ON i.idip = pp.idip LEFT JOIN usuarios u ON u.id = pp.idusuario WHERE i.idip = %s AND pp.estado = 1", GetSQLValueString($colname_rsResponsable, "int"));
$rsResponsable = mysql_query($query_rsResponsable, $tecnocomm) or die(mysql_error());
$row_rsResponsable = mysql_fetch_assoc($rsResponsable);
$totalRows_rsResponsable = mysql_num_rows($rsResponsable);
 

?>



<table width="80%" id="encip" cellpadding="3" cellspacing="0" class="encabezado" >
<tr>
  <td align="right" bgcolor="#CCCCCC">Nombre:</td>
  <td><?php echo $row_rsEncabezado['nombrecliente']; ?></td>
  <td align="right" bgcolor="#CCCCCC">Ip:</td>
  <td><span class="sip"><?php echo $row_rsEncabezado['idip']; ?> <a href="editar.ip.php?idip=<?php echo $_GET['idip'];?>" onclick="NewWindow(this.href,'editar Ip',400,400,'yes');return false;"><img src="images/Edit.png" width="24" height="24" border="0" align="absmiddle" title="Editar IP" /></a></span></td>
<tr>
  <td align="right" bgcolor="#CCCCCC">Direccion:</td>
  <td><?php echo $row_rsEncabezado['direccion']; ?></td>
  <td align="right" bgcolor="#CCCCCC">Fecha:</td>
  <td><?php echo formatDate($row_rsEncabezado['fecha']); ?> <?php echo $row_rsEncabezado['hora']; ?></td>
  </tr>
  <tr>
  <td align="right" bgcolor="#CCCCCC">Contacto:</td>
  <td><?php echo $row_rsEncabezado['nombrecontacto']; ?></td>
  <td align="right" bgcolor="#CCCCCC">Ciudad:</td>
  <td><?php echo $row_rsEncabezado['ciudad']; ?></td>
  </tr>
  <tr>
  <td align="right" bgcolor="#CCCCCC">Telefono:</td>
  <td><?php echo $row_rsEncabezado['telefono']; ?>/ <?php echo $row_rsEncabezado['telefono2']; ?></td>
  <td align="right" bgcolor="#CCCCCC">E-Mail:</td>
  <td><?php echo $row_rsEncabezado['correo']; ?> <a href="composemail.php">[Enviar email]</a></td>
  </tr>
    <tr>
    <td  align="right" bgcolor="#CCCCCC">Atendio por:</td>
    <td ><?php echo $row_rsAtendido['nombrereal']; ?> (<?php echo $row_rsAtendido['username']; ?>) <a href="composemail.php">[Enviar Alerta]</a></td>
    <td align="right" bgcolor="#CCCCCC" >Responsable:</td>
    <td >
        <?php echo $row_rsResponsable['nombrereal']; ?>
		  <a href="ip.personal.php?idip=<?php echo $_GET['idip'];?>" class="popup">[Administrar]</a></td>
  </tr>
  <tr>
  <td  align="right" bgcolor="#CCCCCC">Descripcion:</td>
  <td colspan="3" valign="top" ><?php echo $row_rsEncabezado['descripcion']; ?></td>
  </tr>

</table>
<?php
mysql_free_result($rsEncabezado);

mysql_free_result($rsAtendido);

mysql_free_result($rsResponsable);
?>
