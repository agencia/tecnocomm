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

mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsTareas = "SELECT * FROM tarea GROUP BY fecharealizar";
$rsTareas = mysql_query($query_rsTareas, $tecnocomm) or die(mysql_error());
$row_rsTareas = mysql_fetch_assoc($rsTareas);
$totalRows_rsTareas = mysql_num_rows($rsTareas);

do{
	
	$tareas[$row_rsTareas['fecharealizar']] = $row_rsTareas;
	
}while($row_rsTareas = mysql_fetch_assoc($rsTareas));


$mes = (isset($_GET['mes']))?$_GET['mes']:date('m');
$ano = (isset($_GET['ano']))?$_GET['ano']:date('Y');

$primerdia = mktime(0,0,0,$mes,1,$ano);
$diainicio = date('w',$primerdia);
$diasdelmes = date('t',$primerdia);


$fechai = date('Y/m/d',$primerdia);
$fechaf = date('Y/m/d',mktime(0,0,0,$mes,date('t',$primerdia),$ano));


mysql_select_db($database_tecnocomm, $tecnocomm);
$query_rsJunta = sprintf("SELECT j.*, u.username FROM junta j LEFT JOIN junta_asistente ja ON j.idjunta = ja.idjunta JOIN usuarios u ON u.id = ja.idusuario ORDER BY fecha DESC",
						 GetSQLValueString($fechai,'date'),
						 GetSQLValueString($fechaf,'date'));

$rsJunta = mysql_query($query_rsJunta, $tecnocomm) or die(mysql_error());
$row_rsJunta = mysql_fetch_assoc($rsJunta);
$totalRows_rsJunta = mysql_num_rows($rsJunta);

do{
	
	$junta[$row_rsJunta['fecha']] = $row_rsJunta;
	
}while($row_rsJunta = mysql_fetch_assoc($rsJunta));

//obtener primer dia del mes

$j = 0;
$dia = 1;

for($i=0;$i<$diasdelmes+$diainicio;$i++){
	
	
	
	if($i >= $diainicio){
		
		$semanas[$j][] = $dia;
		
		$dia++;
		
		
		
		if(($i+1)%7 == 0){
			$j++; 	
		}
	}else{
		$semanas[$j][] = '';	
	}
	
	
}

if($mes < 12){
	$siguiente = $mes + 1;
	$sano = $ano;
}else{
	$siguiente = 1;
	$sano = $ano + 1;
	}
	
if($mes > 2){
	$atras = $mes - 1;
	$aano = $ano;
}else{
	$atras = 12;
	$aano = $ano - 1;
}


$meses = array(1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

?>
<h1>Planeacion | <?php echo formatDate(date('Y-m-d'));?></h1>
<div id="opciones">
<ul>
<li><a href="planeacion.junta.nueva.php" class="popup">Abrir Nueva Reunion</a></li>
<li><a href="planeacion.gral.pendientes.print.php" class="popup"><img src="images/Imprimir2.png" border="none" />Imprimir Tareas Pendientes</a></li>
</ul>
</div>

<table width="100%">
<thead>
<tr>
<td colspan="6" align="center">
<h3><?php echo $meses[$mes];?></h3>
</td>
<td align="right">
<a href="index.php?mod=planeacion&mes=<?php echo $atras?>&ano=<?php echo $aano;?>">Atras</a> 
| 
<a href="index.php?mod=planeacion&mes=<?php echo $siguiente?>&ano=<?php echo $sano;?>">Siguiente</a>
</td>
</tr>
<tr>
<td align="center" width="14%">Domingo</td>
<td align="center" width="14%">Lunes</td>
<td align="center" width="14%">Martes</td>
<td align="center" width="14%">Miercoles</td>
<td align="center" width="14%">Jueves</td>
<td align="center" width="14%">Viernes</td>
<td align="center" width="14%">Sabado</td>
</tr>
</thead>
<tbody>
<?php foreach($semanas as $semana):?>
<tr>

<?php 

	if(isset($semana[0])  && $semana[0]!=''){

	$date = date('Y-m-d',mktime(0,0,0,$mes,$semana[0],$ano));
	
	if(isset($junta[$date])){
		$texto = 'background-color:#ccc;';	
		
		$link = '<a href="planeacion.junta2.php?idjunta='.$junta[$date]['idjunta'].'" class="popup">'.$semana[0].'</a>';
		
	}else{
		$testo = '';
		$link = $semana[0];
		
		//preguntar si existen tareas asignadas
		if(isset($tareas[$date])){
			$texto = 'background-color:#cfc;';
			$link = '<a href="planeacion.ver.php?fecha='.$date.'" class="popup">'.$semana[0].'</a>';
		}
	}
	
	}else{
		$link = '';	
	}
	
?>
<td align="center" valign="center" style="border:#39F 1px solid; height:70px; font-size:18px;<?php echo $texto;?>">

<?php echo $link;?>

</td>

<?php 

	if(isset($semana[1])  && $semana[1]!=''){

	$date = date('Y-m-d',mktime(0,0,0,$mes,$semana[1],$ano));
	
	if(isset($junta[$date])){
		$texto = 'background-color:#ccc;';	
		$link = '<a href="planeacion.junta2.php?idjunta='.$junta[$date]['idjunta'].'" class="popup">'.$semana[1].'</a>';
	}else{
		$texto = '';
		$link = $semana[1];
		
		//preguntar si existen tareas asignadas
		if(isset($tareas[$date])){
			$texto = 'background-color:#cfc;';
			$link = '<a href="planeacion.ver.php?fecha='.$date.'" class="popup">'.$semana[1].'</a>';
		}
		
	}
	
	}else{
		$link = '';	
	}
?>
<td align="center" valign="center" style="border:#39F 1px solid; height:70px; font-size:18px;<?php echo $texto;?>">
<?php echo $link;?>
</td>


<?php 

	if(isset($semana[2])  && $semana[2]!=''){

	$date = date('Y-m-d',mktime(0,0,0,$mes,$semana[2],$ano));
	
	if(isset($junta[$date])){
		$texto = 'background-color:#ccc;';	
		$link = '<a href="planeacion.junta2.php?idjunta='.$junta[$date]['idjunta'].'" class="popup">'.$semana[2].'</a>';
	}else{
		$texto = '';
		$link = $semana[2];
		
			//preguntar si existen tareas asignadas
		if(isset($tareas[$date])){
			$texto = 'background-color:#cfc;';
			$link = '<a href="planeacion.ver.php?fecha='.$date.'" class="popup">'.$semana[2].'</a>';
		}
	}
	
	}else{
		$link = '';	
	}
?>
<td align="center" valign="center" style="border:#39F 1px solid; height:70px; font-size:18px;<?php echo $texto;?>">
<?php echo $link;?>
</td>

<?php 

	if(isset($semana[3])  && $semana[3]!=''){

	$date = date('Y-m-d',mktime(0,0,0,$mes,$semana[3],$ano));
	
	if(isset($junta[$date])){
		$texto = 'background-color:#ccc;';	
		$link = '<a href="planeacion.junta2.php?idjunta='.$junta[$date]['idjunta'].'" class="popup">'.$semana[3].'</a>';
	}else{
		$texto = '';
		$link = $semana[3];
		
			//preguntar si existen tareas asignadas
		if(isset($tareas[$date])){
			$texto = 'background-color:#cfc;';
			$link = '<a href="planeacion.ver.php?fecha='.$date.'" class="popup">'.$semana[3].'</a>';
		}
	}
	
	}else{
		$link = '';	
	}
?>
<td align="center" valign="center" style="border:#39F 1px solid; height:70px; font-size:18px;<?php echo $texto;?>">
<?php echo $link;?>
</td>

<?php 

	if(isset($semana[4])  && $semana[4]!=''){

	$date = date('Y-m-d',mktime(0,0,0,$mes,$semana[4],$ano));
	
	if(isset($junta[$date])){
		$texto = 'background-color:#ccc;';
		$link = '<a href="planeacion.junta2.php?idjunta='.$junta[$date]['idjunta'].'" class="popup">'.$semana[4].'</a>';
	}else{
		$texto = '';
		$link = $semana[4];
		
			//preguntar si existen tareas asignadas
		if(isset($tareas[$date])){
			$texto = 'background-color:#cfc;';
			$link = '<a href="planeacion.ver.php?fecha='.$date.'" class="popup">'.$semana[4].'</a>';
		}
	}
	
	}else{
		$link = '';	
	}
?>
<td align="center" valign="center" style="border:#39F 1px solid; height:70px; font-size:18px;<?php echo $texto;?>">
<?php echo $link;?>
</td>

<?php 

	if(isset($semana[5])  && $semana[5]!=''){

	$date = date('Y-m-d',mktime(0,0,0,$mes,$semana[5],$ano));
		
	if(isset($junta[$date])){
		$texto = 'background-color:#ccc;';
		$link = '<a href="planeacion.junta2.php?idjunta='.$junta[$date]['idjunta'].'" class="popup">'.$semana[5].'</a>';
	}else{
		$texto = '';
		$link = $semana[5];
		
			//preguntar si existen tareas asignadas
		if(isset($tareas[$date])){
			$texto = 'background-color:#cfc;';
			$link = '<a href="planeacion.ver.php?fecha='.$date.'" class="popup">'.$semana[5].'</a>';
		}
	}
	
	}else{
		$link = '';	
	}
?>
<td align="center" valign="center" style="border:#39F 1px solid; height:70px; font-size:18px;<?php echo $texto;?>">
<?php echo $link;?>
</td>
<?php 

	if(isset($semana[6])  && $semana[6]!=''){

	$date = date('Y-m-d',mktime(0,0,0,$mes,$semana[6],$ano));
	
	if(isset($junta[$date])){
		$texto = 'background-color:#ccc;';	
		$link = '<a href="planeacion.junta2.php?idjunta='.$junta[$date]['idjunta'].'" class="popup">'.$semana[6].'</a>';
	}else{
		$texto = '';
		$link = $semana[6];
		
			//preguntar si existen tareas asignadas
		if(isset($tareas[$date])){
			$texto = 'background-color:#cfc;';
			$link = '<a href="planeacion.ver.php?fecha='.$date.'" class="popup">'.$semana[6].'</a>';
		}
	}
	
	}else{
		$link = '';	
	}
?>
<td align="center" valign="center" style="border:#39F 1px solid; height:70px; font-size:18px;<?php echo $texto;?>">
<?php echo $link;?>
</td>
</tr>
<?php endforeach;?>
</tbody>
</table>

<?php
mysql_free_result($rsTareas);
?>
