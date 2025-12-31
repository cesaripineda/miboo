<?= $this->Html->css(
	array(
		'bootstrap.min',
		'/vendors/daterangepicker/css/daterangepicker',
	),
	array('inline'=>false));
?>
<style>
	.kpi-box { border-radius: 12px; padding: 25px; color: white; margin-bottom: 25px; min-height: 140px; transition: transform 0.2s; }
	.kpi-box:hover { transform: translateY(-5px); }
	.kpi-value { font-size: 28px; font-weight: 800; display: block; margin-top: 10px; }
	.kpi-title { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }

	/* Colores solicitados */
	.bg-bruto { background-color: #007bff; } /* Azul */
	.bg-gastos-op { background-color: #fd7e14; } /* Naranja */
	.bg-operativo { background-color: #28a745; } /* Verde */
	.bg-pendiente { background-color: #6f42c1; } /* Morado */
	.bg-pct-gastos { background-color: #dc3545; } /* Rojo */
	.bg-pct-utilidad { background-color: #ffc107; color: #333; } /* Amarillo */

	.filter-card { background: #fff; padding: 20px; border-radius: 10px; border: 1px solid #eee; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
</style>

<div class="container-fluid" style="padding: 30px;">
	<h2 style="margin-bottom: 25px; font-weight: 700;">📊 Panel de Control Financiero</h2>

	<div class="filter-card">
		<?php echo $this->Form->create('Dashboard', array('class' => 'form-inline')); ?>
		<div class="form-group">
			<label style="margin-right: 15px; font-weight: bold;">Rango de Fecha:</label>
			<div class="input-group">
				<input type="text" id="date_selector" class="form-control" style="width: 280px; border-radius: 5px;">
				<span class="input-group-btn" style="padding-left: 10px;">
                        <?php echo $this->Form->button('Filtrar datos', array('class' => 'btn btn-dark')); ?>
                    </span>
			</div>
		</div>
		<?php
		echo $this->Form->input('fecha_inicio', array('type' => 'hidden', 'id' => 'start_val'));
		echo $this->Form->input('fecha_fin', array('type' => 'hidden', 'id' => 'end_val'));
		?>
		<?php echo $this->Form->end(); ?>
	</div>

	<div class="row">
		<div class="col-md-4">
			<div class="kpi-box bg-bruto">
				<span class="kpi-title">Resultado Bruto</span>
				<span class="kpi-value"><?php echo $this->Number->currency($I1_ResBruto); ?></span>
			</div>
		</div>

		<div class="col-md-4">
			<div class="kpi-box bg-gastos-op">
				<span class="kpi-title">Gastos Operativos</span>
				<span class="kpi-value"><?php echo $this->Number->currency($I2_GastosOp); ?></span>
			</div>
		</div>

		<div class="col-md-4">
			<div class="kpi-box bg-operativo">
				<span class="kpi-title">Resultado Operativo</span>
				<span class="kpi-value"><?php echo $this->Number->currency($I3_ResOperativo); ?></span>
			</div>
		</div>

		<div class="col-md-4">
			<div class="kpi-box bg-pendiente">
				<span class="kpi-title">Balance Pendiente</span>
				<span class="kpi-value"><?php echo $this->Number->currency($I4_BalancePendiente); ?></span>
			</div>
		</div>

		<div class="col-md-4">
			<div class="kpi-box bg-pct-gastos">
				<span class="kpi-title">% Gastos <small style="opacity: 0.8;">(Gas. Op / Res.Op)</small></span>
				<span class="kpi-value"><?php echo number_format($I5_PctGastos, 2); ?>%</span>

			</div>
		</div>

		<div class="col-md-4">
			<div class="kpi-box bg-pct-utilidad">
				<span class="kpi-title">% Utilidad <small style="opacity: 0.8;">(Res. Op / Gas. Op)</small></span>
				<span class="kpi-value"><?php echo number_format($I6_PctUtilidad, 2); ?>%</span>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		var start = moment('<?php echo $this->request->data['Dashboard']['fecha_inicio']; ?>');
		var end = moment('<?php echo $this->request->data['Dashboard']['fecha_fin']; ?>');

		function updateFields(start, end) {
			$('#start_val').val(start.format('YYYY-MM-DD'));
			$('#end_val').val(end.format('YYYY-MM-DD'));
		}

		$('#date_selector').daterangepicker({
			startDate: start,
			endDate: end,
			locale: {
				format: 'DD/MM/YYYY',
				applyLabel: 'Aplicar',
				cancelLabel: 'Cancelar',
				customRangeLabel: 'Personalizado'
			},
			ranges: {
				'Este Mes': [moment().startOf('month'), moment().endOf('month')],
				'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
				'Últimos 30 días': [moment().subtract(29, 'days'), moment()]
			}
		}, function(start, end) {
			updateFields(start, end);
		});

		// Sincronización inicial
		updateFields(start, end);
	});
</script>

<?php
echo $this->Html->script(
	array(
		'/vendors/moment/js/moment.min',
		'/vendors/daterangepicker/js/daterangepicker',
	),
	array('inline'=>false));
?>
