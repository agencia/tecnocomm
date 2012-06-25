<?php require_once('Connections/tecnocomm.php'); ?>
<?php require_once('utils.php'); ?>
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

$colname_rsMensajes = "-1";
if (isset($_GET['idconversacion'])) {
  $colname_rsMensajes = $_GET['idconversacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsMensajes = sprintf("SELECT cm.*, u.nombrereal AS nremitente FROM conversacion_mensaje cm LEFT JOIN usuarios u ON u.id = cm.remitente  WHERE cm.idconversacion = %s", GetSQLValueString($colname_rsMensajes, "int"));
$rsMensajes = mysql_query($query_rsMensajes, $tecnocomm) or die(mysql_error());
$row_rsMensajes = mysql_fetch_assoc($rsMensajes);
$totalRows_rsMensajes = mysql_num_rows($rsMensajes);

$colname_rsConversacion = "-1";
if (isset($_GET['idconversacion'])) {
  $colname_rsConversacion = $_GET['idconversacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsConversacion = sprintf("SELECT c.*, u.nombrereal AS nremitente FROM conversacion c LEFT JOIN usuarios u ON c.remitente = u.id WHERE c.idconversacion = %s", GetSQLValueString($colname_rsConversacion, "int"));
$rsConversacion = mysql_query($query_rsConversacion, $tecnocomm) or die(mysql_error());
$row_rsConversacion = mysql_fetch_assoc($rsConversacion);
$totalRows_rsConversacion = mysql_num_rows($rsConversacion);

$colname_rsDestinatarios = "-1";
if (isset($_GET['idconversacion'])) {
  $colname_rsDestinatarios = $_GET['idconversacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsDestinatarios = sprintf("SELECT cd.*,u.nombrereal AS ndestinatario FROM conversacion_destinatario cd JOIN usuarios u ON cd.destinatario = u.id WHERE idconversacion = %s", GetSQLValueString($colname_rsDestinatarios, "int"));
$rsDestinatarios = mysql_query($query_rsDestinatarios, $tecnocomm) or die(mysql_error());
$row_rsDestinatarios = mysql_fetch_assoc($rsDestinatarios);
$totalRows_rsDestinatarios = mysql_num_rows($rsDestinatarios);

$colname_rsIsJasa = "-1";
if (isset($_GET['idconversacion'])) {
  $colname_rsIsJasa = $_GET['idconversacion'];
}
mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsIsJasa = sprintf("SELECT cd.* FROM conversacion_destinatario cd WHERE idconversacion = %s AND destinatario = 33", GetSQLValueString($colname_rsIsJasa, "int"));
$rsIsJasa = mysql_query($query_rsIsJasa, $tecnocomm) or die(mysql_error());
$row_rsIsJasa = mysql_fetch_assoc($rsIsJasa);
$totalRows_rsIsJasa = mysql_num_rows($rsIsJasa);

?>
<table width="100%" cellspacing="0" class="border">
<tr><td colspan="2">
<div class="opc">
<ul>
<?php if($row_rsConversacion['estado'] < 2){ ?>
<li><a href="conversacion.responder.php?idconversacion=<?php echo $_GET['idconversacion'];?>" class="popup boton">Responder</a></li>
<?php } ?>
<li><a href="conversacion.print.detallado.php?idconversacion=<?php echo $_GET['idconversacion'];?>" class="popup boton">Imprimir</a></li>
<?php 
if((isset($_GET['admin']) && $_GET['admin'] == 1) && (($totalRows_rsIsJasa == 0) || ($_SESSION['MM_Userid'] == 33))){
?>
<li><a href="conversacion.marcar.php?idconversacion=<?php echo $_GET['idconversacion'];?>" class="popup boton">Liberar</a></li>
<?php } ?>

<?php 
if((($row_rsConversacion['remitente'] == $_SESSION['MM_Userid']) && (($totalRows_rsIsJasa == 0) || ($_SESSION['MM_Userid'] == 33))) || ($_SESSION['MM_Userid'] == 33) || (($totalRows_rsDestinatarios ==2) && ($totalRows_rsIsJasa == 1))) {
?>
<li><a href="conversacion.marcar.php?idconversacion=<?php echo $_GET['idconversacion'];?>" class="popup boton">Liberar</a></li>
<?php } ?>
<?php if ($_GET['edo'] == 1) { ?>
<li><a href="conversacion.leida.php?edo=1&id=<?php echo $_GET['idconversacion'];?>" class="popup boton">Marcar como leida</a></li>
<?php } ?>
</ul>
</div></td></tr>
<tr>
<td valign="top" class="f1" width="100px">Origino Alerta:</td><td valign="top"><?php echo $row_rsConversacion['nremitente']; ?></td>
</tr>
<tr><td valign="top" class="f">Fecha:</td><td valign="top"><?php echo formatDate($row_rsConversacion['fechacreado']);?></td></tr>
<tr><td class="f">Folio:</td><td>M<?php echo $row_rsConversacion['idconversacion']; ?></td></tr>
<tr><td class="f">Ip:</td><td><?php if($row_rsConversacion['idip'] != ""){ ?><a href="index.php?mod=detalleip&idip=<?php echo $row_rsConversacion['idip']; ?>"><?php echo $row_rsConversacion['idip']; ?><?php } ?></td></tr>
<tr>
<td valign="top" class="f">Destinatarios:</td>
<td valign="top"><?php do { ?>
    <?php echo $row_rsDestinatarios['ndestinatario']; ?>,
    <?php } while ($row_rsDestinatarios = mysql_fetch_assoc($rsDestinatarios)); ?></td>
</tr>
<tr><td class="f">Asunto:</td><td><?php echo $row_rsConversacion['asunto']; ?></td></tr>
<tr><td valign="top" class="f">Mensaje Original:</td><td><div id="mensajeDialog"><?php echo $row_rsConversacion['mensaje']; ?></div></td></tr>
<?php if ($totalRows_rsMensajes > 0) { // Show if recordset not empty ?>
  <?php do { ?>
  <tr><td colspan="2" class="laS" align="center"><?php echo ($totalRows_rsDestinatarios != 1)? "Respuesta" : "Actualizaci&otilde;n";?></td></tr>
<?php if ($totalRows_rsDestinatarios  != 1) { ?><tr><td valign="top" class="f">Remitente:</td><td><?php echo $row_rsMensajes['nremitente']; ?></td></tr><?php } ?>
<tr><td valign="top" class="f"><?php echo ($totalRows_rsDestinatarios != 1)? "Fecha" : "Modificado";?></td><td><?php echo formatDate($row_rsMensajes['fecha']);?></td></tr>
<tr><td class="f">Folio:</td><td>M<?php echo $row_rsConversacion['idconversacion']; ?>R<?php echo $row_rsMensajes['idconversacion_mensaje']; ?></td></tr>
<tr><td valign="top" class="f">Mensaje:</td><td><?php echo $row_rsMensajes['mensaje']; ?></td></tr>

 <?php } while ($row_rsMensajes = mysql_fetch_assoc($rsMensajes)); ?>
  <?php } // Show if recordset not empty ?>
  <?php if ($totalRows_rsMensajes == 0) { // Show if recordset not empty ?>
  <tr><td colspan="2" align="center">No Hay Respuestas</td></tr>
  <?php } ?>
</table>
<?php 
mysql_free_result($rsMensajes);

mysql_free_result($rsConversacion);

mysql_free_result($rsDestinatarios);
?>
