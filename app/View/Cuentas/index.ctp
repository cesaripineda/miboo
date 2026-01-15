<?= $this->Html->css(
	array(
		'/vendors/select2/css/select2.min',
		'/vendors/datatables/css/scroller.bootstrap.min',
		'/vendors/datatables/css/colReorder.bootstrap.min',
		'/vendors/datatables/css/dataTables.bootstrap.min',
		'pages/dataTables.bootstrap',
		'plugincss/responsive.dataTables.min',
		'pages/tables',

		'/vendors/bootstrap-switch/css/bootstrap-switch.min',
		'/vendors/switchery/css/switchery.min',
		'/vendors/radio_css/css/radiobox.min',
		'/vendors/checkbox_css/css/checkbox.min',
		'pages/radio_checkbox'
	),
	array('inline'=>false));
?>
<?php
	$formas_pago=array(
		'Efectivo'=>'Efectivo',
		'Transferencia'=>'Transferencia',
		'Mixto'=>'Mixto',
	);

	$cuentas_list = array();
	foreach ($cuentas as $cuenta) {
		$cuentas_list[$cuenta['Cuenta']['id']] = $cuenta['Cuenta']['nombre'];
	}
?>

<style>
	.hide {
		display: none;
	}
</style>

<div class="modal fade" id="addCuenta" tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog" style="max-width:900px !important">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel1" style="color:black">
					<i class="fa fa-plus"></i>
					<label id="titulo_modal_cuenta">Nueva Cuenta</label>
				</h4>
			</div>
			<?= $this->Form->create('Cuenta',array('url'=>array('action'=>'add'),'class'=>'form-horizontal'))?>
			<div class="modal-body">
				<div class="form-group row">
					<?= $this->Form->input('id')?>
					<?= $this->Form->input('nombre',array('type'=>'text','class'=>'form-control','div'=>'col-md-6','label'=>'Alias',));?>
					<?= $this->Form->input('beneficiario',array('type'=>'text','class'=>'form-control','div'=>'col-md-6','label'=>'Beneficiario',));?>
					<?= $this->Form->input('banco',array('type'=>'text','class'=>'form-control','div'=>'col-md-6','label'=>'Banco',));?>
					<?= $this->Form->input('cuenta_bancaria',array('type'=>'text','class'=>'form-control','div'=>'col-md-6','label'=>'Cuenta',));?>
					<?= $this->Form->input('spei',array('type'=>'number','class'=>'form-control','div'=>'col-md-6','label'=>'CLABE',));?>
					<?= $this->Form->input('debito',array('type'=>'text','class'=>'form-control','div'=>'col-md-6','label'=>'Tarjeta de Débito',));?>
					<?= $this->Form->input('credito',array('type'=>'text','class'=>'form-control','div'=>'col-md-6','label'=>'Tarjeta de Crédito',));?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger float-xs-right" data-dismiss="modal">
					Cerrar
					<i class="fa fa-times"></i>
				</button>
				<?= $this->Form->submit('Agregar Cuenta',array('class'=>'btn btn-success pull-left'))?>
			</div>
			<?= $this->Form->end()?>
		</div>
	</div>
</div>

<div class="modal fade" id="addPago" tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog" style="max-width:900px !important">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel1" style="color:black">
					<i class="fa fa-plus"></i>
					Registrar Movimiento
				</h4>
			</div>
			<?= $this->Form->create('Jugador',array('url'=>array('action'=>'addPago'),'class'=>'form-horizontal'))?>
			<div class="modal-body">
				<div class="form-group row">
					<div class="col-12">
						<h4>Jugador: Juan 11</h4>
					</div>
					<?= $this->Form->input('monto',array('type'=>'number','class'=>'form-control','step'=>0.01,'div'=>'col-md-6','required'=>true,'label'=>'Monto',));?>
					<?= $this->Form->input('cuenta_id',array('type'=>'select','options'=>$cuentas,'empty'=>'Seleccionar Cuenta','required'=>true,'class'=>'form-control','div'=>'col-md-6','label'=>'Cuenta',));?>
					<?= $this->Form->input('tipo_movimiento',array('type'=>'select','options'=>array(1=>'Ingreso',2=>'Egreso'),'empty'=>'Seleccionar Tipo Movimiento','required'=>true,'class'=>'form-control','div'=>'col-md-6','label'=>'Cuenta',));?>
					<?= $this->Form->input('fecha_aplicacion',array('type'=>'text','class'=>'form-control fecha','div'=>'col-md-6','label'=>'Fecha',));?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger float-xs-right" data-dismiss="modal">
					Cerrar
					<i class="fa fa-times"></i>
				</button>
				<?= $this->Form->submit('Registrar movimiento',array('class'=>'btn btn-success pull-left'))?>
			</div>
			<?= $this->Form->end()?>
		</div>
	</div>
</div>

<div class="modal fade" id="addTransferencia" tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog" style="max-width:900px !important">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel1" style="color:black">
					<i class="fa fa-plus"></i>
					Registrar Transferencia entre cuentas
				</h4>
			</div>
			<?= $this->Form->create('Movimiento',array('url'=>array('action'=>'transferencia','controller'=>'movimientos'),'class'=>'form-horizontal'))?>
			<div class="modal-body">
				<div class="form-group row">
					<?= $this->Form->input('referencia',array('type'=>'text','class'=>'form-control','div'=>'col-md-6','required'=>true,'label'=>'Referencia',));?>
					<?= $this->Form->input('cuenta_origen',array('onchange'=>'javascript:validarCuenta()','type'=>'select','options'=>$cuentas_list,'class'=>'form-control','div'=>'col-md-3','required'=>true,'label'=>'Seleccionar Cuenta Origen',));?>
					<?= $this->Form->input('cuenta_destino',array('onchange'=>'javascript:validarCuenta()','type'=>'select','options'=>$cuentas_list,'class'=>'form-control','div'=>'col-md-3','required'=>true,'label'=>'Seleccionar Cuenta Destino',));?>
					<?= $this->Form->input('monto',array('type'=>'number','step'=>0.01,'class'=>'form-control','div'=>'col-md-4','required'=>true,'label'=>'Monto',));?>
					<?= $this->Form->input('fecha_aplicacion',array('type'=>'text','class'=>'form-control fecha','div'=>'col-md-4','label'=>'Fecha',));?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger float-xs-right" data-dismiss="modal">
					Cerrar
					<i class="fa fa-times"></i>
				</button>
				<?= $this->Form->submit('Registrar movimiento',array('class'=>'btn btn-success pull-left'))?>
			</div>
			<?= $this->Form->end()?>
		</div>
	</div>
</div>

<div class="outer" style="width: 86vw;">
	<div class="inner bg-container">
		<div class="row">
			<div class="col">
				<div class="card">
					<div class="card-header bg-white">
						Lista de Cuentas
						<div style="float: right">
							<?= $this->Html->link('<i class="fa fa-eye"></i> Ver Todos','javascript:verTodas()',array('class'=>'btn btn-primary','escape'=>false))?>
							<?php echo $this->Html->link('<i class="fa fa-plus" data-pack="default" data-tags=""></i> Agregar Cuenta','javascript:addCuenta()',array('escape'=>false,'class'=>'btn btn-success')); ?>
							<?php echo $this->Html->link('<i class="fa fa-refresh" data-pack="default" data-tags=""></i> Transferencia Interna','#',array('escape'=>false,'data-target'=>'#addTransferencia','data-toggle'=>'modal','class'=>'btn btn-primary')); ?>
						</div>
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
									<th>Alias</th>
									<th>Banco</th>
									<th>Cuenta</th>
									<th>CLABE</th>
									<th>Débito</th>
									<th>Crédito</th>
									<th>Balance</th>
									<th>Status</th>
									<th style="text-align: center">Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$i = 0;
									foreach ($cuentas as $cuenta):
								?>
									<tr id = 'tr_<?= $i?>' class="<?= $cuenta['Cuenta']['estado'] == 0 ? 'hide' : '' ?>">
										<td><?= $this->Html->link($cuenta['Cuenta']['nombre'],array('action'=>'view','controller'=>'cuentas',$cuenta['Cuenta']['id']))?></td>
										<td><?= $cuenta['Cuenta']['banco']?></td>
										<td><?= $cuenta['Cuenta']['cuenta_bancaria']?></td>
										<td><?= $cuenta['Cuenta']['spei']?></td>
										<td><?= $cuenta['Cuenta']['debito']?></td>
										<td><?= $cuenta['Cuenta']['credito']?></td>
										<td>$<?= number_format($cuenta['saldo'],2)?></td>
										<td>
											<div class="form-group radio_basic_swithes_padbott">
												<input onchange="javascript:activarCuenta('<?= $cuenta['Cuenta']['id']?>',this)" class="make-switch-radio" type="checkbox" data-on-color="success" data-off-color="danger" <?= $cuenta['Cuenta']['estado'] ? "checked" : ""?>>
											</div>
										</td>
										<td style="text-align: center">
											<?= $this->Html->link('<i class="fa fa-edit fa-lg"></i>',"javascript:editCuenta(".$cuenta['Cuenta']['id'].")",array('escape'=>false,'data-toggle'=>'tooltip', 'data-placement'=>'top' ,'title'=>'Editar Cuenta'))?>

										</td>
									</tr>
								<?php $i++; endforeach;?>
							</tbody>
						</table>
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

	function validarCuenta(){
		if(document.getElementById('MovimientoCuentaOrigen').value == document.getElementById('MovimientoCuentaDestino').value){
			alert("No se pueden elegir las mismas cuentas para el movimiento");
		}
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
		'/vendors/datepicker/js/bootstrap-datepicker.min',

		'/vendors/bootstrap-switch/js/bootstrap-switch.min',
		'/vendors/switchery/js/switchery.min',
		'pages/radio_checkbox'
	),
	array('inline'=>false));
?>

<script>

	function addCuenta(){
		$("#addCuenta").modal('show');
		document.getElementById('titulo_modal_cuenta').innerHTML = 'Agregar cuenta';
		document.getElementById('CuentaId').value = "";
		document.getElementById('CuentaNombre').value = "";
		document.getElementById('CuentaBeneficiario').value = "";
		document.getElementById('CuentaBanco').value = "";
		document.getElementById('CuentaCuentaBancaria').value = "";
		document.getElementById('CuentaSpei').value = "";
		document.getElementById('CuentaDebito').value = "";
		document.getElementById('CuentaCredito').value = "";
	}

	function editCuenta(id){
		$("#addCuenta").modal('show');
		document.getElementById('titulo_modal_cuenta').innerHTML = 'Editar cuenta';
		document.getElementById('CuentaId').value = id;

		var dataString = 'id='+ id;

		$.ajax({
			type: "POST",
			url: '<?php echo Router::url(array("controller" => "cuentas", "action" => "getCuenta"), TRUE); ?>' ,
			data: dataString,
			cache: false,
			success: function(html) {
				document.getElementById('CuentaNombre').value = html.Cuenta.nombre;
				document.getElementById('CuentaBeneficiario').value = html.Cuenta.beneficiario;
				document.getElementById('CuentaBanco').value = html.Cuenta.banco;
				document.getElementById('CuentaCuentaBancaria').value = html.Cuenta.cuenta_bancaria;
				document.getElementById('CuentaSpei').value = html.Cuenta.spei;
				document.getElementById('CuentaDebito').value = html.Cuenta.debito;
				document.getElementById('CuentaCredito').value = html.Cuenta.credito;
			}
		});
	}

	function activarCuenta(id,input) {
		var estado = input.checked ? 1 : 0
		var dataString = 'id=' + id + '&estado=' + estado;
		console.log(dataString);
		$.ajax({
			type: "POST",
			url: '<?php echo Router::url(array("controller" => "cuentas", "action" => "activar"), TRUE); ?>',
			data: dataString,
			cache: false,
			success: function (html) {
				console.log(html);
			}
		});
	}

	function verTodas(){
		for(i=0;i<=<?= $i?>;i++){
			document.getElementById('tr_'+i).classList.remove('hide');
		}
	}


	'use strict';
	$(document).ready(function() {
		TableAdvanced.init();
		$(".dataTables_scrollHeadInner .table").addClass("table-responsive");
		$("#sample_5_wrapper table").removeClass("table-responsive");
		$(".dataTables_wrapper .dt-buttons .btn").addClass('btn-secondary').removeClass('btn-default');
		$(".dataTables_wrapper").removeClass("form-inline");

		$('.fecha').datepicker({
			format: 'yyyy-mm-dd',
			todayHighlight: true,
			autoclose: true,
			orientation:"bottom"
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
