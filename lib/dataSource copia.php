<?php 

class dataSource{

private $SQL;
private $connection;
private $data;
private $pointer; //apunta al registro actual
private $countRows; //total de registros
private $countFields;//total de columnas
private $fields;


public function dataSource($connection,$tabla){
	$this->SQL=sprintf("SELECT * FROM %s",$tabla);
	$this->connection = $connection;
	$this->data = mysql_query($this->SQL,$this->connection) or die (mysql_error());
	$this->pointer = 0;
	$this->countRows = mysql_num_rows($this->data);
	$this->countFields = mysql_num_fields($this->data);
	
		for($i=0; $i< $this->countFields;$i++){
		$this->fields[] = mysql_fetch_field($this->data, $i);
	}

}


public  function getFieldValue($field){
	mysql_data_seek($this->data,$this->pointer);
	$data = mysql_fetch_array($this->data);
	return $data[$field];
}

public function getFieldNames(){

	foreach($this->fields as $obj){
		$names[] = $obj->name;
	}
	return $names;
}

public function getFieldsObjects(){
	return $this->fields;
}

public function getFieldObjectByIndex($index){
	return $this->fields[$index];
}

public function getFieldObjectByName($name){
//lo buscamos y lo regresamos

}


public function getFieldsCount(){
	return  $this->countFields;
}

public  function firstRow(){
//se posiciona en la primera fila
$this->pointer = 0;

}

public function lastRow(){
//se posiciona en la ultima fila
$this->pointer = $this->countRows - 1;
}


public function nextRow(){
//se posiciona en la siguiente fila regresa el contador de la fila o false en caso de no estar en la ultima fila
$this->pointer = $this->pointer + 1;

}

public function prevRow(){
//se posiciona en la fila anterior reresa el contador de la fila o false en caso de ser la primera fila
$this->pointer = $this->pointer - 1;
}

public function getRowsCount(){
	return $this->countRows;

}



public  function getFieldValue($field){
	mysql_data_seek($this->data,$this->pointer);
	$data = mysql_fetch_array($this->data);
	return $data[$field];
}

public function getFieldNames(){

	foreach($this->fields as $obj){
		$names[] = $obj->name;
	}
	return $names;
}

public function getFieldsObjects(){
	return $this->fields;
}

public function getFieldObjectByIndex($index){
	return $this->fields[$index];
}

public function getFieldObjectByName($name){
//lo buscamos y lo regresamos

}


public function getFieldsCount(){
	return  $this->countFields;
}

public  function firstRow(){
//se posiciona en la primera fila
$this->pointer = 0;

}

public function lastRow(){
//se posiciona en la ultima fila
$this->pointer = $this->countRows - 1;
}


public function nextRow(){
//se posiciona en la siguiente fila regresa el contador de la fila o false en caso de no estar en la ultima fila
$this->pointer = $this->pointer + 1;

}

public function prevRow(){
//se posiciona en la fila anterior reresa el contador de la fila o false en caso de ser la primera fila
$this->pointer = $this->pointer - 1;
}

public function getRowsCount(){
	return $this->countRows;

}



}


class dataSourceSQL?{

private $SQL;
private $connection;
private $data;
private $pointer; //apunta al registro actual
private $countRows; //total de registros
private $countFields;//total de columnas
private $fields;

	public function dataSourceSQL($connection,$SQL){
	$this->SQL=$SQL;
	$this->connection = $connection;
	$this->data = mysql_query($this->SQL,$this->connection) or die (mysql_error());
	$this->pointer = 0;
	$this->countRows = mysql_num_rows($this->data);
	$this->countFields = mysql_num_fields($this->data);
	
		for($i=0; $i< $this->countFields;$i++){
		$this->fields[] = mysql_fetch_field($this->data, $i);
	}

}


public  function getFieldValue($field){
	mysql_data_seek($this->data,$this->pointer);
	$data = mysql_fetch_array($this->data);
	return $data[$field];
}

public function getFieldNames(){

	foreach($this->fields as $obj){
		$names[] = $obj->name;
	}
	return $names;
}

public function getFieldsObjects(){
	return $this->fields;
}

public function getFieldObjectByIndex($index){
	return $this->fields[$index];
}

public function getFieldObjectByName($name){
//lo buscamos y lo regresamos

}


public function getFieldsCount(){
	return  $this->countFields;
}

public  function firstRow(){
//se posiciona en la primera fila
$this->pointer = 0;

}

public function lastRow(){
//se posiciona en la ultima fila
$this->pointer = $this->countRows - 1;
}


public function nextRow(){
//se posiciona en la siguiente fila regresa el contador de la fila o false en caso de no estar en la ultima fila
$this->pointer = $this->pointer + 1;

}

public function prevRow(){
//se posiciona en la fila anterior reresa el contador de la fila o false en caso de ser la primera fila
$this->pointer = $this->pointer - 1;
}

public function getRowsCount(){
	return $this->countRows;

}



}

?>