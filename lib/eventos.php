<?php 
	class evento{
	
	
	/*
		    1  Programadas
		    2 	Confirmadas
			3 	Reprogramadas
			4 	Eliminadas
			5 	Pendientes
			6 	Sin datos, incorrectos o incompletos
		    7 	Entrega Resultados
			8 	Llamada Recibida
	
	*/
	
	/*
	 	Detalles
		0.- Otro
		1.-
	
	*/
	
	private $tipoEvento;
	private $idUsuario;
	private $detalle;
	
	public function evento($tipoEvento,$idUsuario,$detalle){
		$this->tipoEvento = $tipoEvento;
		$this->idUsuario = $idUsuario;
		$this->detalle = $detalle;
	}
	
	
	
	public function registrar(){
	include('Connections/tecnocomm.php'); 
	
	$insertSQL =  sprintf("INSERT INTO log (evento,fecha,idusuario,detalle) VALUES(%s , NOW(), %s , '%s')",
											$this->tipoEvento,$this->idUsuario,$this->detalle);
											
										
	mysql_select_db($database_tecnocomm, $tecnocomm);
	$Result = mysql_query($insertSQL, $tecnocomm) or die(mysql_error());										
	}
	
	
	
}
?>