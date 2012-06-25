<?php 

class Field2{
		
		private $title;
		private $name;
		private $value;
		private $keyvalue;
		private $class;
		private $rows;
		private $size;
		private $cols;
		private $chekecd;
		private $selected;
		private $type;
		private $formateDate;
		private $listValues;
		
		public function Field2($name,$value,$title,$type,$keyvalue=NULL){
			$this->name = $name;
			$this->value = $value;
			$this->title = $title;
			$this->type = $type;
			$this->keyvalue = $keyvalue;
			$this->keyvalue = ($keyvalue != NULL ) ? $keyvalue : $name;
		}
		
		public function getTitle(){
			return $this->title;
		}
		
		public function setType($type){
			$this->type = $type;	
		}
		
		public function getHTML(){
			
			switch($this->type){
				case "text":
				case "varchar":
						$field = new TextField($this->name,$this->value,$this->class);
						$html = $field->getHTML();
					break;
				case "textarea":
					$field = new TextAreaField($this->name,$this->value,$this->class);
						$html = $field->getHTML();
					break;
				case "select":
					$field = new ChekBoxField($this->name,$this->value,$this->class);
						$html = $field->getHTML();
					break;
				case "checkbox":
					$field = new ChekBoxField($this->name,$this->value,$this->class);
						$html = $field->getHTML();
					break;
				case "radio":
					$field = new RadioField($this->name,$this->value,$this->class);
						$html = $field->getHTML();
					break;
				case "buttom":
						$field = new ButtonField($this->name,$this->value,$this->class);
						$html = $field->getHTML();
					break;
				case "submit":
					$field = new ButtonField($this->name,$this->value,"submit",$this->class);
						$html = $field->getHTML();
						break;
				case "date":
					$field = new dateField($this->name,$this->value,$this->formatDate,$this->class);
						$html = $field->getHTML();
						break;
				case "list":
						$field = new listField($this->name,$this->listValues,$this->class);
						$html = $field->getHTML();
						break;
				default:
						$field = new TextField($this->name,$this->value,$this->class);
						$html = $field->getHTML();
					break;
		
			}
		
			return $html;
		}



	public function getName(){
		return $this->name;
	}
	
	public function addListValues($listValues){
		$this->listValues = $listValues;
	}

}

class Field{
	protected $name;
	protected $value;
	protected $class;
	
	public function Field($name,$value=NULL,$class=NULL){
		$this->name=$name;
		$this->value=$value;
		$this->class =$class;
	} 

	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue($value){
		return $this->value;
	}

}

class TextField extends Field{
	
	protected $name;
	protected $value;
	protected $class;
	
	public function TextField($name,$value=NULL,$class=NULL){
		$this->name=$name;
		$this->value=$value;
		$this->class =$class;
	} 

	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue($value){
		return $this->value;
	}
	
	public function getHTML(){
		$html = "<input type=\"text\" name=\"".$this->name."\" class=\"".$this->class."\" size=\"".$this->size."\" value=\"".$this->value."\">";
		return $html;
	}


}


class listValue{
		
		private $key;
		private $value;
		
		public function listValue($key,$value){
			$this->key = $key;
			$this->value = $value;
		}
		
		public function getValue(){
			return $this->value;
		}
		
		public function getKey(){
			return $this->key;
		}
	
	} 


class listField extends Field{
	
	protected $name;
	protected $value;
	protected $class;
	protected $listValues;
	
	public function ListField($name,$listValues=NULL,$class=NULL){
		$this->name=$name;
		$this->class =$class;
		$this->listValues = $listValues;
	} 

	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setValues($listValues){
		$this->listValues = $listValues;
	}
	
	public function getValues(){
		return $this->listValues;
	}
	
	public function addValue($key,$val){
		$value = new listValue($key,$val);
		$listValues[]=$value;
	}
	

	public function getHTML(){
		$html = "<select name=\"".$this->name."\"> \n";
		
		foreach($this->listValues as $val){
			$html.="\t\t<option value=\"".$val->getKey()."\">".$val->getValue()."</option> \n";
		}
		
		$html.="</select>";
		return $html;
	}


}


class TextAreaField extends Field{
	
	protected $cols;
	protected  $rows;
	protected $name;
	protected $value;
	protected $class;
	
	public function TextAreaField($name,$value=NULL,$class=NULL){
		$this->name=$name;
		$this->value=$value;
		$this->class =$class;
	} 

	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue($value){
		return $this->value;
	}
	
	
	
	public function getHTML(){
		$html = "<textarea  name=\"".$this->name."\" class=\"".$this->class."\" cols=\"".$this->cols."\"  rows=\"".$this->rows."\">".$this->value."</textarea>";
		return $html;
	}


}



class ChekBoxField extends Field{
	
	protected $name;
	protected $value;
	protected $class;
	
	public function ChekBoxField($name,$value=NULL,$class=NULL){
		$this->name=$name;
		$this->value=$value;
		$this->class =$class;
	} 

	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue($value){
		return $this->value;
	}
	
	public function getHTML(){
		$html = "<input type=\"checkbox\" name=\"".$this->name."\" class=\"".$this->class."\" size=\"".$this->size."\" value=\"".$this->value."\">";
		return $html;
	}

}




class RadioField extends Field{

protected $name;
	protected $value;
	protected $class;
	
	public function ChekBoxField($name,$value=NULL,$class=NULL){
		$this->name=$name;
		$this->value=$value;
		$this->class =$class;
	} 

	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue($value){
		return $this->value;
	}


	public function getHTML(){
		$html = "<input type=\"text\" name=\"".$this->name."\" class=\"".$this->class."\" size=\"".$this->size."\" value=\"".$this->value."\">";
		return $html;
	}

}

class ButtonField extends Field{
protected $type="buttom"; //buttom or submit
protected $name;
	protected $value;
	protected $class;
	
	public function ButtonField($name,$value=NULL,$type,$class=NULL){
		$this->name=$name;
		$this->value=$value;
		$this->type = $type;
		$this->class =$class;
	} 

	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue($value){
		return $this->value;
	}
	

public function getHTML(){
		$html = "<input type=\"".$this->type."\" name=\"".$this->name."\" class=\"".$this->class."\" size=\"".$this->size."\" value=\"".$this->value."\">";
		return $html;
	}

}

class dateField extends Field{

	protected $name;
	protected $value;
	protected $class;
	protected $formatDate;
	
	public function dateField($name,$value=NULL,$formatDate=NULL,$class=NULL){
		$this->name=$name;
		$this->value=$value;
		$this->class =$class;
		$this->formatDate = (isset($formatDate))? $formatDate : "dd/mm/yyyy";
	} 

	public function setName($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue($value){
		return $this->value;
	}


	public function getHTML(){
		$html = "<input type=\"text\" value=\"".$this->value."\" readonly name=\"".$this->name."\"><input type=\"button\" value=\"Cal\" onclick=\"displayCalendar(document.forms[0].".$this->name.",'".$this->formatDate."',this)\">";
		return $html;
	}

}
	
	


class Form2{
	private $tabla;
	private $fields;
	private $method;
	private $action;
	private $name;
	
	
	public function Form2($name,$method,$action,$tabla=NULL,$fields=NULL){
		$this->name;
		$this->tabla = $tabla;
		$this->fields = $fields;
		$this->method = $method;
		$this->action = $action;
	}
	
	
	public function getHTML(){
		$html = "<form name=\"".$this->name."\" method=\"".$this->method."\" action=\"".$this->action."\"> \n <table>"	;
		foreach($this->fields as $field){
			$html .=" <tr><td>".$field->getTitle()."</td><td></td><td>".$field->getHTML()."</td></tr>";		
		}
		$html.="<div id=\"debug\"></div></form>";
	//	$html .= "<tr><td></td><td></td>".$fields['submit']->getHTML()."<td></td></tr></table></form>";
	
	return $html;
	}
	

	public function createFieldsFromTabla($dts){
		
		$fieldsobj = $dts->getFieldsObjects();
		
		foreach($fieldsobj as $fieldobj){
			$this->fields[$fieldobj->name] = new Field2($fieldobj->name," ",$fieldobj->name,$type);
		}
		
	}
	
	public function createFieldsFromTablaValues($dts){
		
		$fieldsobj = $dts->getFieldsObjects();
		foreach($fieldsobj as $fieldobj){
			$val = ($dts->getFieldValue($fieldobj->name) != NULL)? $dts->getFieldValue($fieldobj->name) : "";
			$this->fields[$fieldobj->name] = new Field2($fieldobj->name,$val,$fieldobj->name,$type);
		}
	
	}
	
	public function setFieldTitle($nameField,$title){
		$this->fields[$nameField]->setTitle($title);
	
	}
	
	public function addField($field){
		$this->fields[$field->getName()] = $field;
	}
	
	public function getValues(){
		foreach($this->fields as $field){
			$values[] = new Value($field->getName(),$this->method);
		}		
		return $values;	
	}
	
	public function setFieldType($nameField,$type){
		$this->fields[$nameField]->setType($type);
	}
	
	public function setFieldListValue($nameField,$listValues){
		$this->fields[$nameField]->addListValues($listValues);
	
	}	
	
	public function setFieldListValueDB($nameField,$dts,$fieldkey,$fieldvalue){
		$rows = $dts->getRowsCount();
		$dts->setPosition(0);
		for($i=0;$i<$rows;$i++){
				$list[] = new listValue($dts->getFieldValue($fieldkey),$dts->getFieldValue($fieldvalue));
		$dts->nextRow();
		}
		
		$this->fields[$nameField]->addListValues($list);
	
	
	}

	
	public function noPublicField($fieldname){
		
	}

}



class Value{
	
	private $value;
	private $name;
	private $method = "POST";

	public function Value($name,$method=NULL){
		$this->name = $name;
		$this->method = ($method != NULL) ? $method : $this->method;
		
		switch($this->method){
			case "POST":
							$this->value = $_POST[$this->name];
				break;
			case "GET":	
							$this->value = $_GET[$this->name];
				break;
			case "FILES":
						$this->value = $_FILES[$this->name];
				break;
		}
		
		}
		
	
	public function getValue(){
			return $this->value;
	}
	
	public function getName(){
		return $this->name;
	}
	
	
}

class dataUpdate{
	
	private $values;
	private $tabla;
	private $keyfield;
	private $keyvalue;
	private $connection;


	public function dataUpdate($tabla,$connection,$keyfield,$keyvalue,$values=NULL){
		$this->tabla = $tabla;
		$this->connection = $connection;
		$this->tkeyfield = $keyfield;
		$this->keyvalue = $keyvalue;
		$this->values = $values;
	}
	
	public function setValue(){
		
	
	}
	
	public function addValue(Value $value){
		$this->fields[$value->getName()] = $value;
	}
	
	public function excect(){
		
		foreach($values as $value){
			$sets[] = $value->getName()."=".$value->getValue();
		}
		
		$s = join(",",$sets);
		
		$this->SQL = sprintf("UPDATE %s SET %s WHERE %s=%s",$this->tabla,$s,$keyfield,$keyvalue);
		if(mysql_query($this-SQL,$this->connection)){
			return 1;
		}else
			return 0;
		
	}
	

}



class dataInsert{
	
	private $values;
	private $tabla;
	private $keyfield;
	private $keyvalue;
	private $connection;
	private $error;


	public function dataInsert($tabla,$connection,$values=NULL){
		$this->tabla = $tabla;
		$this->connection = $connection;
		$this->values = $values;
	}
	
	public function setValue(){
		
	
	}
	
	public function addValue(Value $value){
		$this->fields[$value->getName()] = $value;
	}
	
	public function excect(){
		
		foreach($this->values as $value){
			$f[] = $value->getName();
			$v[] = $value->getValue();
		}
		
		$s = join(",",$f);
		$ss = join(",",$v);
		
		$this->SQL = sprintf("INSERT INTO  %s (%s)  VALUES(%s) ",$this->tabla,$s,$ss);
		if(mysql_query($this->SQL,$this->connection)){
			return mysql_affected_rows();
		}else{
			$this->error = mysql_error();
			return NULL;
			}
		}
	
	
		public function getError(){
			return $this->error;
		}

}




?>
