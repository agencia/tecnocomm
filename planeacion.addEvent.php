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
?>
<?php 

session_start();

if(isset($_POST['tipoelemento']) && $_POST['tipoelemento'] != "" ){
	
	
	
	switch($_POST['tipoelemento']){
		
		case 0: addCotizacion($_POST);
		break;
		case 1: addLevantamiento($_POST);
		break;
		case 2: addOrdenServicio($_POST);
		break;
		case 4: addAdministrativo($_POST);
		break;
		case 5: addFactura($_POST);
		break;
		
		case 6: addCuentaPorPagar($_POST);
		break;
		
		}
	
	
	
}

//print_r($_POST);


function addCotizacion($post){
	include("Connections/tecnocomm.php");
echo "entro a cotizaciones";
	
	if(isset($post['fecha'])  && $post['fecha'] != ""){
		
		
		$comentario[] = array('fecha'=>date('d/m/Y h:i'),
							  'usuario'=>$_SESSION['MM_Username'],
							  'comentario'=>$_POST['comentario']);
		$coment = json_encode($comentario);
		
		
		$err_insert = false;
		$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, idip, 
												   idcotizacion, idtareaanterior, comentario)
											 VALUES(%s, %s, NOW(), 0, %s, %s, %s, %s)",
											 GetSQLValueString($post['idjunta'],"int"),
											 GetSQLValueString($post['fecha'],"date"),
											 GetSQLValueString($post['idip'],"int"),
											 GetSQLValueString($post['valorreferencia'],"int"),
											 GetSQLValueString($post['idtarea'],"int"),
											 GetSQLValueString($coment,"text"));
		mysql_select_db($database_tecnocomm,$tecnocomm);
		$rs_insert = mysql_query($query_insert,$tecnocomm) or ($err_insert = true);
		if(!$err_insert){
			//ASIGNAR USUARIOS A TAREA
			
			$idtarea = mysql_insert_id();
			
			if(is_array($post['usuarios']))
			  foreach($post['usuarios'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_usuario(idtarea, idusuario) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
			
			if(is_array($post['subcontratistas']))
			  foreach($post['subcontratistas'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_subcontratista(idtarea, idsubcontratista) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
		
		
		}
		
		
	}



}


function addLevantamiento($post){
	include("Connections/tecnocomm.php");
echo "entro a levantamientos";
	
	if(isset($post['fecha'])  && $post['fecha'] != ""){
		
		
		$comentario[] = array('fecha'=>date('d/m/Y h:i'),
							  'usuario'=>$_SESSION['MM_Username'],
							  'comentario'=>$_POST['comentario']);
		$coment = json_encode($comentario);
		
		
		$err_insert = false;
		$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, idip, 
												   idlevantamiento, idtareaanterior, comentario)
											 VALUES(%s, %s, NOW(), 0, %s, %s, %s, %s)",
											 GetSQLValueString($post['idjunta'],"int"),
											 GetSQLValueString($post['fecha'],"date"),
											 GetSQLValueString($post['idip'],"int"),
											 GetSQLValueString($post['valorreferencia'],"int"),
											 GetSQLValueString($post['idtarea'],"int"),
											 GetSQLValueString($coment,"text"));
		mysql_select_db($database_tecnocomm,$tecnocomm);
		$rs_insert = mysql_query($query_insert,$tecnocomm) or ($err_insert = true);
		if(!$err_insert){
			//ASIGNAR USUARIOS A TAREA
			
			$idtarea = mysql_insert_id();
			
			if(is_array($post['usuarios']))
			  foreach($post['usuarios'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_usuario(idtarea, idusuario) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
			if(is_array($post['subcontratistas']))
			  foreach($post['subcontratistas'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_subcontratista(idtarea, idsubcontratista) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
		}
		
		
		
		
	}

}


function addOrdenServicio($post){
	include("Connections/tecnocomm.php");
echo "entro a ordenes servicio";
	
	if(isset($post['fecha'])  && $post['fecha'] != ""){
		
		
		$comentario[] = array('fecha'=>date('d/m/Y h:i'),
							  'usuario'=>$_SESSION['MM_Username'],
							  'comentario'=>$_POST['comentario']);
		$coment = json_encode($comentario);
		
		
		$err_insert = false;
		$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, idip, 
												   idordenservicio, idtareaanterior, comentario)
											 VALUES(%s, %s, NOW(), 0, %s, %s, %s, %s)",
											 GetSQLValueString($post['idjunta'],"int"),
											 GetSQLValueString($post['fecha'],"date"),
											 GetSQLValueString($post['idip'],"int"),
											 GetSQLValueString($post['valorreferencia'],"int"),
											 GetSQLValueString($post['idtarea'],"int"),
											 GetSQLValueString($coment,"text"));
		mysql_select_db($database_tecnocomm,$tecnocomm);
		$rs_insert = mysql_query($query_insert,$tecnocomm) or ($err_insert = true);
		if(!$err_insert){
			//ASIGNAR USUARIOS A TAREA
			
			$idtarea = mysql_insert_id();
			
			if(is_array($post['usuarios']))
			  foreach($post['usuarios'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_usuario(idtarea, idusuario) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
					if(is_array($post['subcontratistas']))
			  foreach($post['subcontratistas'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_subcontratista(idtarea, idsubcontratista) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
		
		
		}
		
		
	}
	}

function addAdministrativo($post){
	include("Connections/tecnocomm.php");
echo "entro a ordenes servicio";
	
	if(isset($post['fecha'])  && $post['fecha'] != ""){
		
		
		$comentario[] = array('fecha'=>date('d/m/Y h:i'),
							  'usuario'=>$_SESSION['MM_Username'],
							  'comentario'=>$_POST['comentario']);
		$coment = json_encode($comentario);
		
		
		$err_insert = false;
		$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, 
												    idtareaanterior, administrativo)
											 VALUES(%s, %s, NOW(), 0, %s, %s)",
											 GetSQLValueString($post['idjunta'],"int"),
											 GetSQLValueString($post['fecha'],"date"),
											 GetSQLValueString($post['idtarea'],"int"),
											 GetSQLValueString($_POST['comentario'],"text"));
		mysql_select_db($database_tecnocomm,$tecnocomm);
		$rs_insert = mysql_query($query_insert,$tecnocomm) or ($err_insert = true);
		if(!$err_insert){
			//ASIGNAR USUARIOS A TAREA
			
			$idtarea = mysql_insert_id();
			
			if(is_array($post['usuarios']))
			  foreach($post['usuarios'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_usuario(idtarea, idusuario) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
				if(is_array($post['subcontratistas']))
			  foreach($post['subcontratistas'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_subcontratista(idtarea, idsubcontratista) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
		
		
		}
		
		
	}
	}
	
function addFactura($post){
	include("Connections/tecnocomm.php");
echo "entro a ordenes servicio";
	
	if(isset($post['fecha'])  && $post['fecha'] != ""){
		
		
		$comentario[] = array('fecha'=>date('d/m/Y h:i'),
							  'usuario'=>$_SESSION['MM_Username'],
							  'comentario'=>$_POST['comentario']);
		$coment = json_encode($comentario);
		
		
		$err_insert = false;
		$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, idip, 
												   idfactura, idtareaanterior, comentario)
											 VALUES(%s, %s, NOW(), 0, %s, %s, %s, %s)",
											 GetSQLValueString($post['idjunta'],"int"),
											 GetSQLValueString($post['fecha'],"date"),
											 GetSQLValueString($post['idip'],"int"),
											 GetSQLValueString($post['valorreferencia'],"int"),
											 GetSQLValueString($post['idtarea'],"int"),
											 GetSQLValueString($coment,"text"));
		mysql_select_db($database_tecnocomm,$tecnocomm);
		$rs_insert = mysql_query($query_insert,$tecnocomm) or ($err_insert = true);
		if(!$err_insert){
			//ASIGNAR USUARIOS A TAREA
			
			$idtarea = mysql_insert_id();
			
			if(is_array($post['usuarios']))
			  foreach($post['usuarios'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_usuario(idtarea, idusuario) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
					if(is_array($post['subcontratistas']))
			  foreach($post['subcontratistas'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_subcontratista(idtarea, idsubcontratista) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
		
		}
		
		
	}
	}
	
function addCuentaPorPagar($post){
	include("Connections/tecnocomm.php");
echo "entro a ordenes servicio";
	
	if(isset($post['fecha'])  && $post['fecha'] != ""){
		
		
		$comentario[] = array('fecha'=>date('d/m/Y h:i'),
							  'usuario'=>$_SESSION['MM_Username'],
							  'comentario'=>$_POST['comentario']);
		$coment = json_encode($comentario);
		
		
		$err_insert = false;
		$query_insert = sprintf("INSERT INTO tarea(idjunta, fecharealizar, fechaasigno, estado, idip, 
												   idcuentaporpagar, idtareaanterior, comentario)
											 VALUES(%s, %s, NOW(), 0, %s, %s, %s, %s)",
											 GetSQLValueString($post['idjunta'],"int"),
											 GetSQLValueString($post['fecha'],"date"),
											 GetSQLValueString($post['idip'],"int"),
											 GetSQLValueString($post['valorreferencia'],"int"),
											 GetSQLValueString($post['idtarea'],"int"),
											 GetSQLValueString($coment,"text"));
		mysql_select_db($database_tecnocomm,$tecnocomm);
		$rs_insert = mysql_query($query_insert,$tecnocomm) or ($err_insert = true);
		if(!$err_insert){
			//ASIGNAR USUARIOS A TAREA
			
			$idtarea = mysql_insert_id();
			
			if(is_array($post['usuarios']))
			  foreach($post['usuarios'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_usuario(idtarea, idusuario) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
					
					if(is_array($post['subcontratistas']))
			  foreach($post['subcontratistas'] as $usuario){
					
						$err_insert = false;
						$query_insert = sprintf("INSERT INTO tarea_subcontratista(idtarea, idsubcontratista) VALUES(%s, %s)",
												GetSQLValueString($idtarea,"int"),
												GetSQLValueString($usuario,"int"));
						mysql_select_db($database_tecnocomm,$tecnocomm);
						$rs_insert = mysql_query($query_insert,$tecnocomm);
					
					
					}
		
		
		}
		
		
	}
	}
?>
