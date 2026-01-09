<?= $this->Html->css(
	array(
		'bootstrap.min',
		'/vendors/daterangepicker/css/daterangepicker',
	),
	array('inline'=>false));
?>
<style>
	.kpi-card { border-radius: 8px; padding: 20px; color: white; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
	.kpi-title { font-size: 12px; text-transform: uppercase; font-weight: bold; opacity: 0.8; letter-spacing: 0.5px; }
	.kpi-value { font-size: 24px; font-weight: bold; display: block; margin-top: 5px; }

	/* Clases de Colores Personalizadas */
	.bg-teorico { background-color: #002366; } /* Azul Obscuro */
	.bg-realizado { background-color: #6a0dad; } /* Morado */
	.bg-gastos { background-color: #ff8c00; } /* Naranja */
	.bg-operativo { background-color: #228b22; } /* Verde */
	.bg-pct-gastos { background-color: #cc0000; } /* Rojo */
	.bg-pct-utilidad { background-color: #ffd700; color: #333; } /* Amarillo */
	.bg-pendiente { background-color: #333333; } /* Gris Obscuro */
	.bg-realizacion { background-color: #00ffff; color: #333; } /* Aqua */

	.filter-bar { background: #f4f4f4; padding: 15px; border-radius: 5px; margin-bottom: 25px; border: 1px solid #ddd; }
</style>

<div class="container-fluid" style="padding: 20px;">
	<h2>Panel de Indicadores - Nueva Definición</h2>

	<div class="filter-bar">
		<?php echo $this->Form->create('Dashboard', array('class' => 'form-inline')); ?>
		<div class="form-group">
			<label>Periodo: </label>
			<input type="text" id="range_picker" class="form-control" style="width: 250px; margin: 0 10px;">
			<?php echo $this->Form->button('Calcular', array('class' => 'btn btn-primary')); ?>
		</div>
		<?php
		echo $this->Form->input('fecha_inicio', array('type' => 'hidden', 'id' => 'start_field'));
		echo $this->Form->input('fecha_fin', array('type' => 'hidden', 'id' => 'end_field'));
		?>
		<?php echo $this->Form->end(); ?>
	</div>

	<div class="row">
		<div class="col-md-3">
			<div class="kpi-card bg-teorico">
				<span class="kpi-title">Resultado Juego Teórico</span>
				<span class="kpi-value"><?php echo $this->Number->currency($I1_Teorico); ?></span>
			</div>
		</div>

		<div class="col-md-3">
			<div class="kpi-card bg-realizado">
				<span class="kpi-title">Resultado Juego Realizado</span>
				<span class="kpi-value"><?php echo $this->Number->currency($I2_Realizado); ?></span>
			</div>
		</div>

		<div class="col-md-3">
			<div class="kpi-card bg-gastos">
				<span class="kpi-title">Gastos Operativos</span>
				<span class="kpi-value"><?php echo $this->Number->currency($I3_GastosOp); ?></span>
			</div>
		</div>

		<div class="col-md-3">
			<div class="kpi-card bg-operativo">
				<span class="kpi-title">Resultado Operativo</span>
				<span class="kpi-value"><?php echo $this->Number->currency($I4_ResOperativo); ?></span>
			</div>
		</div>

		<div class="col-md-3">
			<div class="kpi-card bg-pct-gastos">
				<span class="kpi-title">% Gastos Operativos</span>
				<span class="kpi-value"><?php echo number_format($I5_PctGastos, 2); ?>%</span>
			</div>
		</div>

		<div class="col-md-3">
			<div class="kpi-card bg-pct-utilidad">
				<span class="kpi-title">% Utilidad Operativa</span>
				<span class="kpi-value"><?php echo number_format($I6_PctUtilidad, 2); ?>%</span>
			</div>
		</div>

		<div class="col-md-3">
			<div class="kpi-card bg-pendiente">
				<span class="kpi-title">Balance Pendiente</span>
				<span class="kpi-value"><?php echo $this->Number->currency($I7_BalancePend); ?></span>
			</div>
		</div>

		<div class="col-md-3">
			<div class="kpi-card bg-realizacion">
				<span class="kpi-title">% Realización Resultado</span>
				<span class="kpi-value"><?php echo number_format($I8_PctRealizacion, 2); ?>%</span>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		var start = moment('<?php echo $this->request->data['Dashboard']['fecha_inicio']; ?>');
		var end = moment('<?php echo $this->request->data['Dashboard']['fecha_fin']; ?>');

		$('#range_picker').daterangepicker({
			startDate: start,
			endDate: end,
			locale: { format: 'DD/MM/YYYY' }
		}, function(start, end) {
			$('#start_field').val(start.format('YYYY-MM-DD'));
			$('#end_field').val(end.format('YYYY-MM-DD'));
		});

		$('#start_field').val(start.format('YYYY-MM-DD'));
		$('#end_field').val(end.format('YYYY-MM-DD'));
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
