<?= $this->Html->css(
	array(
		'/vendors/select2/css/select2.min',
		'/vendors/datatables/css/scroller.bootstrap.min',
		'/vendors/datatables/css/colReorder.bootstrap.min',
		'/vendors/datatables/css/dataTables.bootstrap.min',
		'pages/dataTables.bootstrap',
		'plugincss/responsive.dataTables.min',
		'pages/tables'
	),
	array('inline'=>false));
	$comisionista = $ganancias[0]['Comisionista']['nombre'];
?>
<div class="outer" style="width: 86vw;">
	<div class="inner bg-container">
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-header bg-white">
						<?= "$titulo_seccion ($periodo) de comisionista $comisionista" ?>
					</div>
					<div class="card-body p-t-50">
						<div class="">
							<div class="pull-sm-right">
								<div class="tools pull-sm-right"></div>
							</div>
						</div>
						<table id="sample_1" class="table-striped table-bordered table-hover table m-t-15" style="width:100%">
							<thead>
								<tr>
									<th>Usuario</th>
									<th>Jugador</th>
									<th>Ganancia / Pérdida</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$total = 0;
									foreach ($ganancias as $ganancia):
										$total += $ganancia['Ganancia']['comision'];
								?>
									<tr>
										<td><?= $ganancia['Jugador']['usuario']?></td>
										<td><?= $ganancia['Jugador']['nombre']?></td>
										<td><?= "$".number_format($ganancia['Ganancia']['comision'],2)?></td>
									</tr>
								<?php endforeach;?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2">TOTAL</th>
									<th><?= "$".number_format($total,2)?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


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
		'/vendors/datatables/js/dataTables.scroller.min'
	),
	array('inline'=>false));
?>

<script>

	function registrarPago(id_comisionista){
		$('#addPago').modal('show');
		document.getElementById('comisionista_id_edit').value = id_comisionista;
		var dataString = 'id='+ id_comisionista;
		$.ajax({
			type: "POST",
			url: '<?php echo Router::url(array("controller" => "comisionistas", "action" => "getComisionista"), TRUE); ?>' ,
			data: dataString,
			cache: false,
			success: function(html) {
				document.getElementById('comisionista_name').innerHTML = html.Comisionista.nombre;
			}
		});
	}

	'use strict';
	$(document).ready(function() {
		TableAdvanced.init();
		$(".dataTables_scrollHeadInner .table").addClass("table-responsive");
		$("#sample_5_wrapper table").removeClass("table-responsive");
		$(".dataTables_wrapper .dt-buttons .btn").addClass('btn-secondary').removeClass('btn-default');
		$(".dataTables_wrapper").removeClass("form-inline");

		$(document).on('click', '.add-row', function(e) {
			e.preventDefault();

			//Agregar número a contador
			let contador = document.getElementById('ComisionistaContador').value;
			document.getElementById('ComisionistaContador').value = Number(contador) + 1;
			// Obtener la fila actual para clonarla
			let currentRow = $(this).closest('.cuenta-row');
			let newRow = currentRow.clone();

			// Obtener el índice de la nueva fila
			let newIndex = $('#cuentas-container .cuenta-row').length;

			// Actualizar los nombres de los campos en la nueva fila
			newRow.find('input').each(function() {
				let oldName = $(this).attr('name');
				let newName = oldName.replace(/\[\d+\]/, '[' + newIndex + ']');
				$(this).attr('name', newName);
				// Limpiar los valores de los nuevos campos
				$(this).val('');
			});

			// Reemplazar el botón 'agregar' de la fila anterior por un botón 'quitar'
			let previousAddBtn = currentRow.find('.add-row');
			if (previousAddBtn.length) {
				previousAddBtn.removeClass('add-row btn-success').addClass('remove-row btn-danger')
					.html('<i class="fa fa-minus"></i>');
			}

			// Agregar la nueva fila al contenedor
			$('#cuentas-container').append(newRow);
		});

		// Función para quitar una fila
		$(document).on('click', '.remove-row', function(e) {
			e.preventDefault();

			let contador = document.getElementById('ComisionistaContador').value;
			document.getElementById('ComisionistaContador').value = Number(contador) - 1;

			// Obtener la fila a eliminar
			let rowToRemove = $(this).closest('.cuenta-row');

			// Eliminar la fila
			rowToRemove.remove();

			// Re-indexar los campos restantes
			$('#cuentas-container .cuenta-row').each(function(index) {
				$(this).find('input').each(function() {
					let oldName = $(this).attr('name');
					let newName = oldName.replace(/\[\d+\]/, '[' + index + ']');
					$(this).attr('name', newName);
				});
			});
		});

	});
	var TableAdvanced = function() {
		// ===============table 1====================
		var initTable1 = function() {
			var table = $('#sample_1');
			/* Table tools samples: https://www.datatables.net/release-datatables/extras/TableTools/ */
			/* Set tabletools buttons and button container */
			table.DataTable({
				dom: "Bflr<'table-responsive't><'row'<'col-md-5 col-12'i><'col-md-7 col-12'p>>",
				buttons: [
					'copy', 'csv', 'print'
				],
				order:[[0,'asc']],
				lengthMenu: [
					[100, 300, 500, -1], // Values for the dropdown: 10, 25, 50, All
					[100, 300, 500, "Todos"] // Display text for the dropdown
				],
				pageLength: 500.
			});
			var tableWrapper = $('#sample_1_wrapper'); // datatable creates the table wrapper by adding with id {your_table_id}_wrapper
			tableWrapper.find('.dataTables_length select').select2(); // initialize select2 dropdown
		}
		// ===============table 1===============

		return {
			//main function to initiate the module
			init: function() {
				if (!jQuery().dataTable) {
					return;
				}
				initTable1();
			}
		};
	}();

</script>
