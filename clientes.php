<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td height="23" colspan="3" valign="top" class="titulos">Catalago de Clientes</td>
  </tr>
  <tr>
    <td width="146" height="31">&nbsp;</td>
    <td width="11">&nbsp;</td>
    <td width="807">&nbsp;</td>
  </tr>
  <tr>
    <td height="25" align="center" valign="middle"><a href="index.php?mod=clientes&option=nuevo"><img src="images/AddUser.png" width="24" height="25" align="absmiddle"> Nuevo Cliente</a></td>
  <td>&nbsp;</td>
  	<td rowspan="4" valign="top">
  
  	<?php
	$option = (isset($_GET['option']))?$_GET['option'] : "default";
	
	
	switch($option){
	
		case "nuevo":
					nuevoCliente();
				break;
		case "guardarNuevo":
					guardarCliente();
				break;
		case "listar":
					listarClientes();
				break;
		case "test";
					test();
				break;
		default:
				listarClientes();
				break;
	}
	
	function nuevoCliente(){
	require_once("lib/dataSource.php");
	require_once("lib/form2.php");
	require_once("tecconnection.php");
	$b = new Form2("nuevoCliente","post",$_SERVER['PHP_SELF']."?mod=clientes&option=guardarNuevo","cliente");
	$dts = new dataSourceSQL($connection,"SELECT * FROM cliente WHERE 2=3");
	$b->createFieldsFromTablaValues($dts);
	$field = new Field2("Aceptar","Aceptar","","submit");
	$b->addField($field);
	//$b->setFieldType("fecha","date");
	//$b->setFieldType("idcliente","list");
	//$dtsClientes = new dataSource($connection,"cliente");
	//$b->setFieldListValueDB("idcliente",$dtsClientes,"idcliente","nombre");
	echo $b->getHTML();	
	}
	
	function guardarNuevo(){
	require_once("lib/dataSource.php");
	require_once("lib/form2.php");
	require_once("tecconnection.php");
	$b = new Form2("nuevoCliente","post",$_SERVER['PHP_SELF']."?mod=clientes&option=guardarNuevo","cliente");
	$dts = new dataSourceSQL($connection,"SELECT * FROM cliente WHERE 2=3");
	$b->createFieldsFromTablaValues($dts);
	//$field = new Field2("enviar","enviar","","submit");
	//echo $b->getHTML();
	$dI = new dataInsert("cliente",$connection,$b->getValues());
	$h = $dI->excect();
	$a =  $dI->getError();
	if($h == 1)
		echo "dato Guardado";
	else
		echo "ubo un pinche error <br>".$a;
	
	}
	
	function test(){
	require_once("lib/dataSource.php");
	require_once("lib/form2.php");
	require_once("tecconnection.php");
	$dts = new dataSourceSQL($connection,"SELECT idcliente,idsubcotizacion,numfactura,fecha,tipo,moneda,monto FROM factura WHERE 2=3");
	$fieldsobj = $dts->getFieldsObjects();
		foreach($fieldsobj as $fieldobj){
			$val = ($dts->getFieldValue($fieldobj->name) != NULL)? getFieldValue($fieldobj->name) : "";
			echo $fieldobj->name." - ".$val."<br>";
			
			//$this->fields[$fieldobj->name] = new Field2($fieldobj->name,$dts->getFieldValue($fieldobj->name),$fieldobj->name,$type);
		}
	
	}
	
	function listarClientes(){
	
	
	}
	
		$SQL = "SELECT  idcliente,nombre,contacto,telefono,email FROM cliente";
	require_once("tecconnection.php");
	require_once("lib/dataSource.php");
	require_once("lib/grid.php");
	$dts = new dataSourceSQL($connection,$SQL);
	$grid= new GridBD($dts);
	$grid->setTitle("nombre","Nombre Comercial");
	$grid->setClassSelectedRow("box");
	$grid->setTitleClass("titleTabla");
	$grid->printGrid();

	
?>
  	</td>
  </tr>
  <tr>
    <td height="7"></td>
    <td></td>
  </tr>
  <tr>
    <td height="26" valign="top"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td></td>
  </tr>
  <tr>
    <td height="172">&nbsp;</td>
    <td></td>
  </tr>
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
</table>
