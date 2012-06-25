<?php 

class TextField{
	private $name;
	private $value;
	private $size;
	private $class;
	
	public function TextField($name,$value=NULL,$size=NULL,$class=NULL){
		$this->name = $name;
		$this->value = $value;
		$this->size   = $size;
		$this->class = $class;
	}
	
	public function getHTML(){
		$html = "<input type=\"text\" name=\"".$this->name."\" class=\"".$this->class."\" size=\"".$this->size."\" value=\"".$this->value."\">";
		return $html;
	}


	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue(){
		return $this->value;
	}


}

class TextAreaField{
	private $name;
	private $value;
	private $cols;
	private $rows;
	
	public function TextAreaField($name,$value=NULL,$col=NULL){
	
	}
	
	
	public function getHTML(){
		$html = "<textarea  name=\"".$this->name."\" class=\"".$this->class."\" cols=\"".$this->cols."\"  rows=\"".$this->rows."\">".$this->value."</textarea>";
		return $html;
	}


	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue(){
		return $this->value;
	}
}

class ChekBoxField{
	private $name;
	private $value;
	
	
	public function getHTML(){
		$html = "<input type=\"text\" name=\"".$this->name."\" class=\"".$this->class."\" size=\"".$this->size."\" value=\"".$this->value."\">";
		return $html;
	}


	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue(){
		return $this->value;
	}
}



class RadioField{
	private $name;
	private $value;
	
	public function getHTML(){
		$html = "<input type=\"text\" name=\"".$this->name."\" class=\"".$this->class."\" size=\"".$this->size."\" value=\"".$this->value."\">";
		return $html;
	}


	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue(){
		return $this->value;
	}


}

class ButtonField{

public function getHTML(){
		$html = "<input type=\"text\" name=\"".$this->name."\" class=\"".$this->class."\" size=\"".$this->size."\" value=\"".$this->value."\">";
		return $html;
	}


	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue(){
		return $this->value;
	}


}

class ListField{

public function getHTML(){
		$html = "<input type=\"text\" name=\"".$this->name."\" class=\"".$this->class."\" size=\"".$this->size."\" value=\"".$this->value."\">";
		return $html;
	}


	public function setValue($value){
		$this->value = $value;
	}
	
	public function getValue(){
		return $this->value;
	}


}

 class Option{
		private $value;
		private $key;
		private $selectd;
	} 

class ListFieldBD{
	
	private $dataSource;
	private $options;
	
	public function ListFieldBD($dataSource,$keycolumn,$keyvalue){
	}
	
	
	public function getHTML(){
	
	}
	
	
	public function printListFieldBD(){
		echo getHTML();
	}



}



class FormUpdate{


public function Form($dataUpdate){
	$this->dataUpdate = $dataUpdate;
}


public function hiddeField($nameField,$value=true){

}

public function setTitleField(){


}

public function setHitField($nameField,$hitText){


}

public function setSelectField($nameField,$options){


}



}




?>
