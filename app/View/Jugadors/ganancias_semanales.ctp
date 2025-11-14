<?= $this->Html->css(
	array(
		'/vendors/select2/css/select2.min',
		'/vendors/datatables/css/scroller.bootstrap.min',
		'/vendors/datatables/css/colReorder.bootstrap.min',
		'/vendors/datatables/css/dataTables.bootstrap.min',
		'pages/dataTables.bootstrap',
		'plugincss/responsive.dataTables.min',
		'pages/tables',
		'/vendors/datepicker/css/bootstrap-datepicker.min',
	),
	array('inline'=>false));
?>
<?php
	$formas_pago=array(
		'Efectivo'=>'Efectivo',
		'Transferencia'=>'Transferencia',
		'Mixto'=>'Mixto',
	);
?>

<div class="outer" style="width: 86vw;">
	<div class="inner bg-container">
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-header bg-white">
						Registrar Ganancias Semana <?= (date('W')-1)."-".date('Y')?>
					</div>
					<div class="card-body p-t-50">
						<?= $this->Form->create('Ganancia',array('url'=>array('action'=>'add','controller'=>'ganancias')))?>
						<table class="table-striped table-bordered table-hover table m-t-15" style="width:100%">
							<thead>
								<tr>
									<th>Agencia</th>
									<th>Usuario</th>
									<th>Jugador</th>
									<th>Balance Anterior</th>
									<th>Monto Semana</th>
									<th>Descuento</th>
									<th>Total Semana</th>
									<th>Balance</th>
									<th>Comisión</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$i=0;
									$agencia = '';
									foreach ($jugadores as $jugador):
										if ($agencia != $jugador['Comisionista']['usuario']):
											echo "<tr><td colspan='9' style='background-color: black; color:white; text-align: center'>".$jugador['Comisionista']['usuario']."</td></tr>";
											$agencia = $jugador['Comisionista']['usuario'];
										endif;
								?>
									<tr>
										<td><?= $jugador['Comisionista']['usuario']?>(<?= $jugador['Jugador']['comision_comisionista']?>%)</td>
										<td><?= $jugador['Jugador']['usuario']?></td>
										<td><?= $jugador['Jugador']['nombre']?></td>
										<td data-filter="<?= $jugador['Saldo']>0 ? "Ganador": "Deudor"?>" style="<?=  $jugador['Saldo']>0 ? "color:red;font-weight:bold;": "color:green;font-weight:bold;"?>">$<?= number_format($jugador['Saldo'],2)?></td>
										<td>
											<?= $this->Form->input('monto_'.$i,array('id'=>'monto_'.$i,'type'=>'number','class'=>'form-control','div'=>'col-md-10','placeholder'=>'Ganancia / Pérdida','onchange'=>'javascript:validarTotal('.$i.','.$jugador['Jugador']['descuento_2'].','.$jugador['Saldo'].')','label'=>false))?>
											<?= $this->Form->hidden('monto_dd_'.$i,array('id'=>'monto_dd_'.$i))?>
											<?= $this->Form->hidden('jugador_id_'.$i,array('value'=>$jugador['Jugador']['id']))?>
										</td>
										<td><?= $jugador['Jugador']['descuento_2']?>%</td>
										<td id="total_<?= $i?>"></td>
										<td id="saldo_<?= $i?>"></td>
										<td>
											<?= $this->Form->hidden('comision_value_'.$i,array('id'=>'comision_value_'.$i,'value'=>($jugador['Jugador']['comision_comisionista']/100)))?>
											<?= $this->Form->hidden('comisionista_id_'.$i,array('id'=>'comisionista_id_'.$i,'value'=>$jugador['Comisionista']['id']))?>
											<?= $this->Form->input('comision_'.$i,array('id'=>'comision_'.$i,'type'=>'number','class'=>'form-control','div'=>'col-md-10','readonly'=>true,'label'=>false))?>
										</td>
									</tr>
									<?php $i++;?>
								<?php endforeach;?>
							<?= $this->Form->hidden('semana',array('value'=>date("W")-1))?>
							<?= $this->Form->hidden('anio',array('value'=>date("Y")))?>
							<?= $this->Form->hidden('contador',array('id'=>'contador','value'=>$i))?>
							</tbody>
							<tfoot>
							<tr style="font-weight: bold; background-color: #f7f7f7;">
								<td colspan="4" style="text-align: right;">TOTALES:</td>
								<td id="total-monto-semana">$0.00</td>
								<td></td>
								<td id="total-total-semana">$0.00</td>
								<td colspan="2"></td>
							</tr>
							<tr>
								<td colspan="9"><?= $this->Form->submit('Registrar Ganancias',array('class'=>'btn btn-success','confirm'=>'¿Deseas registrar estas ganancias para estos jugadores?'))?></td>
							</tr>
							</tfoot>
						</table>
						<?= $this->Form->end()?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	function registrarPago(){
		$('#addPago').modal('show');
	}

	function redondearDecena(numero) {
		// 1. Manejo del Signo y Parte Entera:
		//    Usamos Math.sign para preservar el signo y Math.abs para aplicar la lógica al valor positivo.
		const signo = Math.sign(numero);
		const absoluto = Math.abs(Math.floor(numero));

		// 2. Obtener la Unidad.
		let ultimoDigito = absoluto % 10;
		let resultadoAbsoluto;

		if (ultimoDigito <= 4) {
			// Corresponde a MULTIPLO.INFERIOR (para 0-4)
			resultadoAbsoluto = absoluto - ultimoDigito;
		} else if (ultimoDigito >= 6) {
			// Corresponde a MULTIPLO.SUPERIOR (para 6-9)
			resultadoAbsoluto = absoluto + (10 - ultimoDigito);
		} else { // Cuando ultimoDigito === 5
			// Corresponde a SI(VALOR(DERECHA(ABS(F2),1))=5,F2,...)
			// Si el último dígito es 5, se mantiene.
			resultadoAbsoluto = absoluto;
		}

		// 3. Restaurar el signo original de F2.
		return resultadoAbsoluto * signo;
	}

	function updateFooterTotals(){
		var totalMontoSemana = 0;
		var totalTotalSemana = 0;

		// Obtenemos el número total de filas/jugadores del campo hidden
		var contador = Number(document.getElementById('contador').value);

		for (let i = 0; i < contador; i++) {
			// Sumar Monto Semana (El valor bruto del input)
			var montoElement = document.getElementById('monto_' + i);
			if(montoElement){
				totalMontoSemana += Number(montoElement.value) || 0; // || 0 para manejar inputs vacíos
			}

			// Sumar Total Semana (El valor ya calculado, guardado en el hidden monto_dd)
			var totalElement = document.getElementById('monto_dd_' + i);
			if(totalElement){
				totalTotalSemana += Number(totalElement.value) || 0;
			}
		}

		// Formatear y actualizar el footer
		document.getElementById('total-monto-semana').innerHTML = "$" + totalMontoSemana.toFixed(2);
		document.getElementById('total-total-semana').innerHTML = "$" + totalTotalSemana.toFixed(2);
	}

	function validarTotal(row,descuento, saldo_anterior){
		// La lógica de cálculo ya existente...

		var total = Number(document.getElementById('monto_'+row).value);
		var saldo_anterior = Number (saldo_anterior);
		// ... (toda tu lógica de cálculo de total y redondeo)

		if(Number(document.getElementById('monto_'+row).value) < 0){
			total_raw = Number(document.getElementById('monto_'+row).value) * ((100-descuento)/100);
			total = redondearDecena(total_raw);
		}else{
			total = redondearDecena(Number(document.getElementById('monto_'+row).value));
			//total = Number(document.getElementById('monto_'+row).value);
		}
		var saldo = Number(total+saldo_anterior);
		var estilo_pagar = "color:red";
		var estilo_total = "color:darkgreen";
		if (total < 0){
			estilo_pagar = "color:darkgreen";
			document.getElementById('comision_'+row).value = Math.floor((total * document.getElementById('comision_value_'+row).value)*-1);
		}else{
			document.getElementById('comision_'+row).value = 0;
		}
		if(saldo > 0){
			estilo_total = "color:red";
		}
		document.getElementById('total_'+row).innerHTML = "<span style='"+estilo_pagar+"'>"+"$"+total +"</span>";
		document.getElementById('saldo_'+row).innerHTML = "<span style='"+estilo_total+"'>"+"$"+saldo+"</span>";
		document.getElementById('monto_dd_'+row).value = total;

		// 🚨 ¡Paso CLAVE! Llama a la nueva función al final del cálculo
		updateFooterTotals();
	}
</script>

<?php
echo $this->Html->script(
	array(
		'/vendors/select2/js/select2',
		'/vendors/datatables/js/jquery.dataTables.min',
		'pluginjs/dataTables.tableTools',
		'/vendors/datatables/js/dataTables.colReorder',
		'/vendors/datatables/js/dataTables.bootstrap.min',
		'/vendors/datatables/js/dataTables.buttons.min',
		'pluginjs/jquery.dataTables.min',
		'/vendors/datatables/js/dataTables.responsive.min',
		'/vendors/datatables/js/dataTables.rowReorder.min',
		'/vendors/datatables/js/buttons.colVis.min',
		'/vendors/datatables/js/buttons.html5.min',
		'/vendors/datatables/js/buttons.bootstrap.min',
		'/vendors/datatables/js/buttons.print.min',
		'/vendors/datatables/js/dataTables.scroller.min',
		'/vendors/moment/js/moment.min',
		'/vendors/datepicker/js/bootstrap-datepicker.min'
	),
	array('inline'=>false));
?>

<script>
	'use strict';
	$(document).ready(function() {
		$('.fecha').datepicker({
			format: 'dd-mm-yyyy',
			todayHighlight: true,
			autoclose: true,
			orientation:"bottom"
		});
		updateFooterTotals();
	}();

</script>
