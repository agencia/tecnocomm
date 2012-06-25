<?php 



/*class Field{
	private $name;
	private $type;
	private $value;
	private $primaryKey;
	
	public function Field($name,$type=NULL,$primaryKey=false,$value=NULL){
		$this->name = $name;
		$this->type = $type;
		$this->value = $value;
		$this->primaryKey = $primaryKey;
	}
	
	public function getValue(){
		return $this->value;
	}
	
	public function getName(){
	//formatear de acuerdo al tipo
		return $this->name;
	}
	
	public function setValue($value){
		$this->value=$value;
	}
	
	public function setType($type){
		$this->type=$type;
	}
	
	public function isPrimaryKey(){
		return $this->primaryKey;
	}
	
	public function setPrimaryKey($val = true){
		$this->primaryKey = $val;
	}
	
}



class dataInsert{

	private $tabla;
	private $fields;
	private $SQL;
	private $connection;

	public function dataInsert($tabla,$connection){
			$this->tabla = $tabla;
			$this->connection = $connection;
	}

	public function setValue($nameField,$value,$type=NULL){
		if(isset($this->fields[$nameField])){
			$this->fields[$nameField]->setValue($value);
			if($type){
				$this->fields[$nameField]->setType($type);
			}
		return true;
		}else
			return false;
	}
	
	public function addField($Field){
			$this->fields[$Field->getName()] = $Field;
	}
	
	public function insert(){
		
		$SQL = getSQL();
		mysql_query($SQL,$this->connection);
	}

	public function getSQL(){
	
		unset($fieldNames,$fieldValues);
		
		foreach($this->fields as $field){
			$fieldNames[] = $field->getName();
			$fieldValues[] = $field->getValue();
		}
		
		$names = join(",",$fieldNanmes);
	    $values = join(",",$fieldValues);
		
		$this->SQL = sprintf("INSERT INTO %s( %s ) VALUES( %s )",$this->tabla,$names,$values);
		
		return $this->SQL;
	}
	
	public function getId(){
	
	
	}


}


class dataUpdate{

	private $tabla;
	private $fields;
	private $SQL;
	private $connection;
	private $conditions;

public function dataUpdate($tabla,$connection){
			$this->tabla = $tabla;
			$this->connection = $connection;
}

public function setValue($fieldname,$value,$type=NULL){
if(isset($this->fields[$nameField])){
			$this->fields[$nameField]->setValue($value);
			if($type){
				$this->fields[$nameField]->setType($type);
			}
		return true;
		}else
			return false;

}

public function addField($field){
	$this->fields[$field->getName()] = $field;
}

public function addCondition($field){
	$this->conditions[$field->getName()] = $field;
}


public function getSQL(){
	
		unset($fieldNames,$fieldValues,$fieldConditions);
		
		foreach($this->fields as $field){
			$equals[] = $field->getName()."=".$field->getValue();
		}
		
		foreach($this->conditions as $condition){
			$conditions[] = $condition->getName()." = ".$condition->getValue();
		}
		
		
		$strequals = join(",",$equals);
		$strconditions = join(" AND ",$conditions);
		$this->SQL = sprintf("UPDATE %s SET %s WHERE %s",$this->tabla,$strequals,$strconditions);
		return $this->SQL;

}

public function update(){	
	$SQL = getSQL();
	mysql_query($SQL,$this->connection);
}

public function getFields(){
	return $this->fields;
}

public function getField($fieldName){
	return $this->fields[$fieldName];
}

public function getConditionFields

}
*/
?>