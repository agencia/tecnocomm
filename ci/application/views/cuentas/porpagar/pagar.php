<script>
$(function() {
    $("#formPagar").submit(function(e){
        e.preventDefault();
        if($("#abono").val() == "" || $("#abono").val() == 0)
            alert("Ingrese una cantidad a pagar");
        else if($("#abono").val() > <?php echo $falta; ?>)
            alert("El monto a pagar debe ser menor o igual al monto adeudado");
        else {
            $.post($(this).attr("action"), $(this).serialize(), function(e){
                location.reload();
                window.opener.location.reload();
            });
        }
    });
});
</script>
<div>
    <form method="post" id="formPagar" action="<?php echo base_url() ?>index.php/cuentas/porpagar/registrarpago">
    <table width="400px" class="resaltarTabla">
        <thead>
            <tr class="titleTabla">
                <td colspan="2">Realizar pago</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tipo de pago</td>
                <td><select>
                        <?php foreach($tipopago as $t => $v): ?>
                        <option value="<?php echo $t ?>"><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select></td>
            </tr>
            <tr>
                <td>Referencia</td>
                <td><input type="text" name="referencia" /></td>
            </tr>
            <tr>
                <td>Adeudo inicial</td>
                <td>$ <?php echo $porpagar; ?></td>
            </tr>
            <tr>
                <td>Abonado</td>
                <td>$ <?php echo $pagado; ?></td>
            </tr>
            <tr>
                <td>Adeudo restante</td>
                <td>$ <?php echo $falta; ?></td>
            </tr>
            <tr>
                <td>Pagar</td>
                <td>
                    $ <input type="text" value="<?php echo $falta; ?>" name="monto" id="abono" />
                    <input type="hidden" value="<?php echo $idcuenta ?>" name="idcuenta" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Pagar" />
                </td>
            </tr>
        </tbody>
    </table>
</div>