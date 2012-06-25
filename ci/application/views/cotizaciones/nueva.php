<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Nueva Cotizacion</title>
        <link href="<?php echo base_url(); ?>../style.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url(); ?>../style2.css" rel="stylesheet" type="text/css" />
        <script language="javascript" src="<?php echo base_url(); ?>js/funciones.js"></script>
        <script type="text/javascript">
            function change(name,size,lab){
                s = document.getElementById(name);
                obj = document.createElement('input')
                obj.type = 'text'
                obj.id = name;
                obj.name = name;
                obj.size = size;
                document.getElementById(lab).replaceChild(obj,s)
            }



            function mover(posy,posx)
            {
                var winl = (screen.width-posy)/2;
                var wint = (screen.height-posx)/2;
  
                if (parseInt(navigator.appVersion)>3)
                    top.resizeTo(posy,posx);
                top.moveTo(winl,wint);
            }
            //mover('1035','400');
            function activar(obj,val) {
                //dis = obj.selectedIndex==0 ? false : true;
                document.getElementById("cotext").disabled=val;
    
            } 

            function valida(){
    
                if (document.form1.idcliente.value==-1){
                    alert("Tiene que seleccionar un cliente ")
                    document.form1.idcliente.focus()
                    return false;
                } 
	
                if (document.form1.nombre.value.length==0){
                    alert("Tiene que escribir un identificador ")
                    document.form1.nombre.focus()
                    return false;
                } 
	
                if (document.form1.idip.value.length==0){
                    alert("Tiene que escribir una  ip ")
                    document.form1.idip.focus()
                    return false;
                } 
	
                if (document.form1.TIPO.value.length==0){
                    alert("Tiene que escribir un tipo de cambio ")
                    document.form1.TIPO.focus()
                    return false;
                } 
	
                if (document.form1.entrega.value.length==0){
                    alert("Tiene que escribir un tiempo de entrega ")
                    document.form1.entrega.focus()
                    return false;
                } 
	
                if (document.form1.suministro.value==-1){
                    alert("Tiene que seleccionar un tipo de suministro ")
                    document.form1.suministro.focus()
                    return false;
                } 
	
            }/////fin valida

        </script>
    </head>

    <body class="wrapper" onload="activar(this,true);">
        <form id="form1" name="form1" method="POST" action="<?php echo base_url(); ?>index.php/cotizaciones/set" onsubmit="return valida();">
            <table width="600" border="0" align="center" class="border distabla fondo">
                <tr>
                    <td width="1">&nbsp;</td>
                    <td colspan="2" align="center" class="titulos" background="<?php echo base_url(); ?>images/titulo.gif">INGRESE LOS DATOS</td>
                    <td width="1">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="5">&nbsp;</td>
                </tr>
                <input type="hidden" name="idip" value="<?php echo $idip; ?>" />
                <tr>
                    <td>&nbsp;</td>
                    <td width="180px" align="right">IDENTIFICADOR:</td>
                    <td><input name="nombre" type="text" class="form" id="nombre" size="40" /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right">MONEDA:</td>
                    <td><select name="moneda" class="form" id="moneda">
                            <option value="0">PESOS</option>
                            <option value="1">DOLARES</option>
                        </select></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right">TIPO DE CAMBIO:</td>
                    <td><input name="TIPO" type="text" class="form" id="TIPO" size="10" /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right">FORMA DE PAGO:</td>
                    <td><label id="fo"><select name="forma" id="forma" class="form">
                                <option value="CONTADO">CONTADO</option>
                                <option value="50 % ANTICIPO y 50% CONTRAENTREGA">50 % ANTICIPO y 50% CONTRAENTREGA</option>
                                <option value="50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE">50 % ANTICIPO Y ESTIMACIONES CONTRA AVANCE</option>
                                <option value="30 DIAS">30 DIAS</option>
                                <option onclick="change('forma',40,'fo');">Otro...</option>
                            </select></label></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right">VIGENCIA:&nbsp;&nbsp;&nbsp;<BR />
                    </td>
                    <td><label id="vig"><select name="vigencia" id="vigencia" class="form">
                                <option value="-1">Seleccionar</option>
                                <option value="PRECIOS SUJETOS A CAMBIO SIN PREVIO AVISO">PRECIOS SUJETOS A CAMBIO SIN PREVIO AVISO</option>
                                <option value="30 DIAS">30 DIAS</option>
                                <option onclick="change('vigencia',40,'vig');">Otro...</option>
                            </select></label></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right">TIEMPO ENTREGA:      </td>
                    <td><input name="entrega" type="text" class="form" id="entrega" size="40" /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right">GARANTIA:&nbsp;&nbsp;&nbsp;<BR /></td>
                    <td><label id="gar"><select name="garantia" id="garantia" class="form">
                                <option value="-1">SELECCIONAR</option>
                                <option value="1 AÑO MATERIAL Y MANO DE OBRA">1 AÑO MATERIAL Y MANO DE OBRA</option>
                                <option value="25 AÑOS PANDUIT">25 AÑOS PANDUIT</option>
                                <option onclick="change('garantia',40,'gar');">Otro...</option>
                            </select></label></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right">FACTOR DE UTILIDAD:      </td>
                    <td><input name="utilidad" type="text" class="form" id="utilidad" value="1" size="10" /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right">DESCUENTO:      </td>
                    <td><input name="descuento" type="text" class="form" id="descuento" value="0" size="10" /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right"><label>CONSECUTIVO:
                        </label></td>
                    <td><input name="textfield" type="text" class="form" id="textfield" value="<?php echo ($consecutivo+1) ?>" size="6" /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right" valign="top">NOTAS O COMENTARIOS:<br /></td>
                    <td><textarea name="notas" id="notas" cols="45" rows="5" class="form"></textarea> </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right" >TIPO DE SUMINISTRO:      </td>
                    <td><select name="suministro"  id="suministro">
                            <option value="-1" selected="selected" >SELECCIONAR</option>
                            <option value="0">SUMINISTRO E INSTALACION</option>
                            <option value="1">INSTALACION GLOBAL</option>
                            <option value="2" >SOLO SUMINISTRO</option>
                            <option value="3" >SOLO INSTALACION</option>
                        </select></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right">OBTENER DATOS DE OTRA COTIZACION: </td>
                    <td><label>
                            <input name="radiobutton" type="radio"  onclick="activar(this,false);" value="radiobutton"/>
                            SI</label>
                        <label>
                            <input name="radiobutton" type="radio" value="radiobutton" checked="checked" onclick="activar(this,true);"/>
                            NO</label></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right"><label>COTIZACION:

                        </label></td>
                    <td><select name="cotext" id="cotext">
                            <option value="-1">Ninguna</option>
                            <?php foreach($sub as $s): ?>
                            <option value="<?php echo $s["idsubcotizacion"]; ?>"><?php echo $s["identificador2"]; ?></option>
                            <?php endforeach; ?>
                        </select></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td><input type="button" name="button2" id="button2" value="Cancelar"  onclick="window.close();"/>      
                        <input type="submit" name="button" id="button" value="Aceptar" /></td>
                    <td>&nbsp;</td>
                </tr>
            </table> 
        </form>
    </body>
</html>