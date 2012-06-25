<?php 
class BD{
private $connection;
private $host;
private $user;
private $pass;
private $port;



public function BD($host,$user,$pass,$bd){
	$this->connection=mysql_connect($host,$user,$pass,$port) or die (mysql_error());
	mysql_select_db($bd,$this->connection);
}

public function Connection(){
	return  $this->connection;
}

}


?>