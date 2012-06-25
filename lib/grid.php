<?php 

class Column{

private $name;
private $tipe;
private $title;
private $format;
private $hidden;
private $class;
private $width;

public function Column($name,$tipe=NULL,$title=NULL,$format=NULL,$class=NULL){
	$this->name=$name;
	$this->tiipe=$tipe;
	$this->title=$title;
	$this->format=$format;
	$this->hidden = false;
}

public function getName(){

	return $this->name;
}

public function getTipe(){
	return $this->tipe;
}


public function getTitle(){
	if($this->title != NULL)
		return $this->title;
	else
		return $this->name;
}

public function getFormat(){
	return $this->format;
}

public function setTitle($title){
	$this->title = $title;
}


public function setType($type){
	$this->type= $type;
}


public function setFormat($format){
	$this->format = $format;
}

public function setHidden($value){
	$this->hidden=$value;
}

public function isHidden(){
	return $this->hidden;
}

public function setClass($class){
	$this->class = $class;
}

public function getClass(){
	return $this->class;
}

public function setWidth($width){
	$this->width = $width;
}

public function getWidth(){
	return $this->width;
}


}

class GridBD{

private $dataSource;
private $columns; //array of object columns
private $titles;//en caso de null muestra el nombre del campo o de lo contrario el establecido
private $actions;//en caso de null no hace nada de lo contrario estable una accion al hacer click en el en la celda
private $classSelectedRow;
private $classRow;
private $countColumns;
private $titleClass;
private $paginationClassFirst;
private $paginationClassNext;
private $paginationClassBack;
private $paginationClassLast;
private $paginationStart;
private $paginationLimit;
private $pagination; //true no se muestra paginacion o false si esta activada la paginacion

public function GridBD($dataSource){
	$this->dataSource = $dataSource;
	//obtenemos las columnas del $dataSource
	$this->countColumns = $this->dataSource->getFieldsCount();
	for($i=0;$i<$this->countColumns;$i++){
		$col = $this->dataSource->getFieldObjectByIndex($i );
		$colobj = new Column($col->name,$col->type);
		$this->columns[$col->name] =$colobj;
	}
	
	$this->paginationStart = 0;
	$this->paginationLimit = $this->dataSource->getRowsCount();
	
}

public function setTitle($column,$title){
	$this->columns[$column]->setTitle($title);
}

public function setHidden($columns,$val){
	$this->columns[$columns]->setHidden($val);
}


public  function setTitles($arrayTitles){
	//por ejemplo titles=array(id=>"Identificador",nombrecliente=>"Nombre");

}


public function setPagination($start,$limit){
$this->paginationStart = $start;
$this->paginationLimit = $limit;
}


public function getHTML(){
	$html = "<table> \n<tr class=\"".$this->titleClass."\"> \n";

	foreach($this->columns as $col){
		if($col->isHidden() == false)
			$html.="<td class=\"".$col->getClass()."\" width=\"".$col->getWidth()."\">". $col->getTitle()."</td>";	
	}

	$html.="\n</tr>\n";
	
	$this->dataSource->setPosition($this->paginationStart);
	for ($i=$this->paginationStart;$i<$this->paginationLimit;$i++){	
		$html.="<tr  class=\"".$this->classRow."\"onmouseover=\"this.className='".$this->classSelectedRow."'\" onmouseout=\"this.className='".$this->classRow."'\">\n";
		foreach($this->columns as $col){
			if($col->isHidden() == false){
			$html.="<td>".$this->dataSource->getFieldValue($col->getName())."</td>";
			
			}
		}
		$html.="</tr>\n";
	$this->dataSource->nextRow();
	}

	$html.="</table>";

	return $html;
}

public function printGrid(){
	echo $this->getHTML();
}

public function setClassSelectedRow($class){
	$this->classSelectedRow =$class;
}

public function setClassRow($class){
	$this->classRow = $class;
}

public function setColumnClass($column,$class){
	$this->columns[$column]->setClass($class);
}

public function setColumnWidth($column,$width){
	$this->columns[$column]->setWidth($width);
}

public function setTitleClass($class){
	$this->titleClass = $class;
}

public function getCellValue($col,$row){

}

public function getColsCount(){


}

public function getRowsCount(){


}

public function setCellValue($col,$row){


}

public function setCellEvent($col,$row,$event,$action){



}


private function pagination(){

	

}

}


?>