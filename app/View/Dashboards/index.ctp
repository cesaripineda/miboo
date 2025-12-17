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

		<div class="kpi-card balance">
			<div class="kpi-title">💰 Balance (I1)</div>
			<div class="kpi-value">
				<?php echo $this->Number->currency($I1_Balance); ?>
			</div>
			<div class="kpi-subtitle">SUM Ganancia.ganancia_neta</div>
		</div>

		<div class="kpi-card gastos">
			<div class="kpi-title">📉 Gastos (I2)</div>
			<div class="kpi-value">
				<?php echo $this->Number->currency($I2_Gastos); ?>
			</div>
			<div class="kpi-subtitle">SUM Movimiento.monto (tipo_gasto=0)</div>
		</div>

		<div class="kpi-card utilidad">
			<div class="kpi-title">✅ Utilidad (I3)</div>
			<div class="kpi-value">
				<?php echo $this->Number->currency($I3_Utilidad); ?>
			</div>
			<div class="kpi-subtitle">I1 (Balance) - I2 (Gastos)</div>
		</div>

		<div class="kpi-card pct-gastos">
			<div class="kpi-title">📊 % Gastos (I4)</div>
			<div class="kpi-value">
				<?php
				// Usamos toPercentage para formatear el valor
				echo $this->Number->toPercentage($I4_PctGastos, 2);
				?>
			</div>
			<div class="kpi-subtitle">(I2 / I3) * 100</div>
		</div>

		<div class="kpi-card pct-utilidad">
			<div class="kpi-title">📈 % de Utilidad (I5)</div>
			<div class="kpi-value">
				<?php
				echo $this->Number->toPercentage($I5_PctUtilidad, 2);
				?>
			</div>
			<div class="kpi-subtitle">(I3 / I2) * 100</div>
		</div>

		<div class="kpi-card cuentas-pendientes">
			<div class="kpi-title">⏱️ Cuentas Pendientes (I6)</div>
			<div class="kpi-value">
				<?php echo $this->Number->currency($I6_CuentasPendientes); ?>
			</div>
			<div class="kpi-subtitle">Saldo Inicial + Ganancia Neta + (Aportaciones - Pagos)</div>
		</div>
	</div>

</div>

<style>
	.dashboard-container { padding: 20px; }
	.filter-box { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9; }
	.filter-fields { display: flex; gap: 15px; align-items: center; }
	.kpi-grid {
		/* Ajuste para 3 columnas en pantallas grandes */
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 20px;
		margin-top: 20px;
	}
	.kpi-card {
		padding: 20px;
		border-radius: 10px;
		box-shadow: 0 4px 8px rgba(0,0,0,0.1);
		color: white;
	}
	.kpi-title { font-size: 1.1em; font-weight: bold; margin-bottom: 5px; }
	.kpi-value { font-size: 2.2em; font-weight: 900; }
	.kpi-subtitle { font-size: 0.8em; opacity: 0.8; }
	.balance { background-color: #20c628; } /* Azul Google */
	.gastos { background-color: #d93025; } /* Rojo */
	.utilidad { background-color: #0916ac; } /* Verde Oscuro */
	.pct-gastos { background-color: #7b0202; } /* Amarillo */
	.pct-utilidad { background-color: #3f9592; } /* Verde Claro */
	.cuentas-pendientes { background-color: #e3bf09; } /* Púrpura */

	.button-filter {
		padding: 10px 15px;
		background-color: #007bff;
		color: white;
		border: none;
		border-radius: 5px;
		cursor: pointer;
		transition: background-color 0.3s;
	}
	.button-filter:hover { background-color: #0056b3; }
</style>

<script type="text/javascript">
	// Importante: Asegúrate que jQuery, Moment.js y daterangepicker estén cargados.
	$(function() {
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
