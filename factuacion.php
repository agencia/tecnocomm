<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td height="23" colspan="3" valign="top" class="titulos">Facturacion</td>
  </tr>
  <tr>
    <td width="140" height="20">&nbsp;</td>
    <td width="17">&nbsp;</td>
    <td width="794">&nbsp;</td>
  </tr>
  <tr>
    <td height="342" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
      <!--DWLayoutTable-->
      <tr>
        <td width="140" height="18" valign="top"><a href="<?php echo $_SERVER['PHP_SELF'];?>?mod=facturacion&option=nueva">Nueva Factura</a></td>
        </tr>
         <tr>
        <td width="140" height="18" valign="top"><a href="#">Cotizaciones Facturadas</a></td>
        </tr>
         <tr>
        <td width="140" height="18" valign="top"><a href="#">Facturas No Pagadas</a></td>
        </tr>
      <tr>
        <td height="324">&nbsp;</td>
        </tr>   
    </table></td>
    <td>&nbsp;</td>
    <td valign="top"><?php
	$option = (isset($_GET['option']))?$_GET['option'] : "default";
	
	
	switch($option){
	
		case "nueva":
					nuevaFactura();
				break;
		case "guardarNueva":
					guardarNueva();
				break;
		case "listar":
					listarFactura();
				break;
		case "test";
					test();
				break;
		default:
				echo "Eliga una opcion";
				break;
	}
	
	function nuevaFactura(){
	require_once("lib/dataSource.php");
	require_once("lib/form2.php");
	require_once("tecconnection.php");
	$b = new Form2("nuevaCotizacion","post",$_SERVER['PHP_SELF']."?mod=facturacion&option=guardarNueva","factura");
	$dts = new dataSourceSQL($connection,"SELECT idcliente,idsubcotizacion,numfactura,fecha,tipo,moneda,monto FROM factura WHERE 2=3");
	$b->createFieldsFromTablaValues($dts);
	$field = new Field2("enviar","enviar","","submit");
	$b->addField($field);
	$b->setFieldType("fecha","date");
	$b->setFieldType("idcliente","list");
	$dtsClientes = new dataSource($connection,"cliente");
	$b->setFieldListValueDB("idcliente",$dtsClientes,"idcliente","nombre");
	echo $b->getHTML();	
	}
	
	function guardarNueva(){
	require_once("lib/dataSource.php");
	require_once("lib/form2.php");
	require_once("tecconnection.php");
	$b = new Form2("nuevaCotizacion","POST",$_SERVER['PHP_SELF']."?mod=facturacion&option=guardarNueva","factura");
	$dts = new dataSourceSQL($connection,"SELECT idcliente,idsubcotizacion,numfactura,fecha,tipo,moneda,monto FROM factura");
	$b->createFieldsFromTablaValues($dts);
	//$field = new Field2("enviar","enviar","","submit");
	//echo $b->getHTML();
	$dI = new dataInsert("factura",$connection,$b->getValues());
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
	
	
	/*
	$SQL = "SELECT co.consecutivo,cl.nombre,telefono,contacto,concat(\"<a href='facturando.php?id=\",co.idcliente,\"' onClick=\\\"NewWindow(this.href,'Facturando',500,300,'yes');return false;\\\"> Facturar </a>\") AS Facturar FROM cotizacion co,cliente cl WHERE co.idcliente = cl.idcliente";
	require_once("tecconnection.php");
	require_once("lib/dataSource.php");
	require_once("lib/grid.php");
	$dts = new dataSourceSQL($connection,$SQL);
	$grid= new GridBD($dts);
	$grid->setTitle("nombre","Nombre");
	$grid->setClassSelectedRow("box");
	$grid->setTitleClass("titleTabla");
	$grid->printGrid();
*/
?>
</td>
  </tr>
  
  
</table>
