
<form name="eliminarDetalle" method="post">
<table width="246" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td height="36" colspan="5" valign="top">Realmente Desea Eliminar este Pedido?</td>
  </tr>
  <tr>
    <td width="85" height="11"></td>
    <td width="71"></td>
    <td width="13"></td>
    <td width="68"></td>
    <td width="9"></td>
  </tr>
  <tr>
    <td height="21">&nbsp;</td>
    <td valign="top"><input type="button" name="del2" id="del2" value="Cancelar"></td>
    <td>&nbsp;</td>
    <td valign="top"><label>
      <input type="submit" name="del" id="del" value="Eliminar">
    </label></td>
    <td></td>
  </tr>
  <tr>
    <td height="22"></td>
    <td></td>
    <td></td>
    <td>&nbsp;</td>
    <td></td>
  </tr>
</table>
<input type="hidden" name="identificador" value="<?php echo $_GET['identificador'];?>">
</form>
