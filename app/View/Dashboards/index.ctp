<?= $this->Html->css(
	array(
		'bootstrap.min',
		'/vendors/daterangepicker/css/daterangepicker',
	),
	array('inline'=>false));
?>
<div class="dashboard-container">
	<h2>📊 Dashboard de Indicadores Financieros</h2>

	<div class="filter-box">
		<?php
		// Usamos el FormHelper de CakePHP
		echo $this->Form->create('Dashboard', array('url' => array('controller' => 'dashboards', 'action' => 'index')));
		?>

		<div class="filter-fields">
			<label for="date_range_picker">Seleccionar Rango de Fechas:</label>

			<input type="text" id="date_range_picker" style="min-width: 250px;" class="form-control" />

			<?php
			echo $this->Form->input('fecha_inicio', array(
				'type' => 'hidden',
				'label' => false,
				'div' => false,
				// Mantenemos el valor por defecto/post para que el controlador lo reciba
				'value' => isset($this->request->data['Dashboard']['fecha_inicio']) ? $this->request->data['Dashboard']['fecha_inicio'] : date('Y-m-d', strtotime('-30 days'))
			));

			echo $this->Form->input('fecha_fin', array(
				'type' => 'hidden',
				'label' => false,
				'div' => false,
				'value' => isset($this->request->data['Dashboard']['fecha_fin']) ? $this->request->data['Dashboard']['fecha_fin'] : date('Y-m-d')
			));
			?>

			<?php
			echo $this->Form->submit('Aplicar Filtro', array('div' => false, 'class' => 'button-filter'));
			?>
		</div>

		<?php echo $this->Form->end(); ?>
	</div>

	<hr>

	<div class="kpi-grid">
		<div class="kpi-card ganancia">
			<div class="kpi-title">💵 Ganancia Neta</div>
			<div class="kpi-value">
				<?php echo $this->Number->currency($total_ganancia_neta); ?>
			</div>
			<div class="kpi-subtitle">Sumatoria de Ganancia.ganancia_neta</div>
		</div>

		<div class="kpi-card comision">
			<div class="kpi-title">🤝 Comisión</div>
			<div class="kpi-value">
				<?php echo $this->Number->currency($total_comision); ?>
			</div>
			<div class="kpi-subtitle">Sumatoria de Ganancia.comision</div>
		</div>

		<div class="kpi-card sueldos">
			<div class="kpi-title">🧑‍💼 Sueldos</div>
			<div class="kpi-value">
				<?php echo $this->Number->currency($total_sueldos); ?>
			</div>
			<div class="kpi-subtitle">Movimiento.monto (tipo_gasto=1)</div>
		</div>

		<div class="kpi-card gastos">
			<div class="kpi-title">📉 Gastos y Sueldos</div>
			<div class="kpi-value">
				<?php echo $this->Number->currency($total_gastos); ?>
			</div>
			<div class="kpi-subtitle">Movimiento.monto (tipo_gasto=0)</div>
		</div>

		<div class="kpi-card utilidad">
			<div class="kpi-title">✅ Utilidad Neta Total</div>
			<div class="kpi-value">
				<?php echo $this->Number->currency($utilidad_neta); ?>
			</div>
			<div class="kpi-subtitle">Ganancia Neta - Comisiones - Sueldos - Gastos</div>
		</div>
	</div>

</div>

<style>
	/* ... (El CSS del dashboard iría aquí) ... */
	.dashboard-container { padding: 20px; }
	.filter-box { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
	.filter-fields { display: flex; gap: 15px; align-items: center; }
	.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
	.kpi-card { padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); color: white; }
	.kpi-title { font-size: 1.1em; font-weight: bold; margin-bottom: 5px; }
	.kpi-value { font-size: 2.2em; font-weight: 900; }
	.kpi-subtitle { font-size: 0.8em; opacity: 0.8; }
	.ganancia { background-color: #4CAF50; }
	.comision { background-color: #2196F3; }
	.sueldos { background-color: #FF9800; }
	.gastos { background-color: #F44336; }
	.utilidad { background-color: #00BCD4; }
	.button-filter { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; }
	.button-filter:hover { background-color: #0056b3; }
</style>

<script type="text/javascript">
	$(function() {
		// 1. Obtener los valores actuales de los campos ocultos de CakePHP
		var start = moment('<?php echo $this->request->data['Dashboard']['fecha_inicio']; ?>');
		var end = moment('<?php echo $this->request->data['Dashboard']['fecha_fin']; ?>');

		function cb(start, end) {
			// Actualiza el campo visible con el formato de fecha amigable
			$('#date_range_picker').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));

			// Actualiza los campos ocultos con el formato que el controlador espera (YYYY-MM-DD)
			$('#DashboardFechaInicio').val(start.format('YYYY-MM-DD'));
			$('#DashboardFechaFin').val(end.format('YYYY-MM-DD'));
		}

		$('#date_range_picker').daterangepicker({
			startDate: start,
			endDate: end,
			locale: {
				format: 'DD/MM/YYYY',
				separator: ' - ',
				applyLabel: 'Aplicar',
				cancelLabel: 'Cancelar',
				fromLabel: 'Desde',
				toLabel: 'Hasta',
				customRangeLabel: 'Rango Personalizado',
				weekLabel: 'S',
				daysOfWeek: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
				monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
				firstDay: 1
			},
			ranges: {
				'Hoy': [moment(), moment()],
				'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Últimos 7 Días': [moment().subtract(6, 'days'), moment()],
				'Últimos 30 Días': [moment().subtract(29, 'days'), moment()],
				'Este Mes': [moment().startOf('month'), moment().endOf('month')],
				'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			}
		}, cb);

		// Inicializa el campo visible con los valores actuales al cargar la página
		cb(start, end);

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
