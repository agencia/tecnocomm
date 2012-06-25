<body>
    <script>
	$(function() {
		$( "#tabs" ).tabs({
			ajaxOptions: {
				error: function( xhr, status, index, anchor ) {
					$( anchor.hash ).html(
						"Couldn't load this tab. We'll try to fix this as soon as possible. " +
						"If this wouldn't be a demo." );
				}
			}
		});
	});
	</script>
    <center>
        <div style="width: 800px;" class="wrapper">
        <div>
            <div id="tabs">
                    <ul>
                        <li><a href="#tabs-1">Estado Actual</a></li>
                        <li><a href="<?php echo base_url() ?>index.php/cuentas/porpagar/pagar/<?php echo $idfactura ?>">Realizar pago</a></li>
                        
                    </ul>
                    <div id="tabs-1">
                        <table class="resaltarTabla" cellspacing="2" cellpadding="3">
                            <thead>
                                <tr class="titleTabla">
                                    <td>Fecha</td>
                                    <td>Tipo de Pago</td>
                                    <td>Referencia</td>
                                    <td>Monto</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(is_array($abonos)) { ?>
                                <?php foreach($abonos as $a): ?>
                                <tr>
                                    <td><?php echo $a["fecha_es"] ?></td>
                                    <td><?php echo $tipopago[$a["tipopago"]] ?></td>
                                    <td><?php echo $a["referencia"] ?></td>
                                    <td><?php echo $a["monto"] ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php } else { ?>
                                <tr>
                                    <td colspan="3">No hay abonos para esta cuenta</td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="2"><strong>Total Abonado</strong></td>
                                    <td><strong>$ <?php echo $pagado; ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong>Monto inicial</strong></td>
                                    <td><strong>$ <?php echo $porpagar; ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong>Total a Pagar</strong></td>
                                    <td><strong>$ <?php echo $falta; ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
        </div>
    </center>
</body>