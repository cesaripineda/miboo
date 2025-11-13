<?php
class CuentasController extends AppController {
	public $name = 'Cuentas';

	function activar(){
		$cuenta = array(
			'id'=>$this->request->data['id'],
			'estado'=>$this->request->data['estado']
		);
		$mensaje = "";
		if($this->Cuenta->save($cuenta)){
			$mensaje = "Cuenta Actualizada";
		}else{
			$mensaje = "Cuenta No Actualizada";
		}
		header('Content-Type: application/json');
		echo json_encode($mensaje);
		exit();
	}

	function getCuenta(){
		$cuenta = $this->Cuenta->findById($this->request->data['id']);
		header('Content-Type: application/json');
		echo json_encode($cuenta);
		exit();
	}

	function index(){
		$this->set('titulo_seccion','Lista de Cuentas');
		$cuentas = $this->Cuenta->find(
			'all',
			array(
				'conditions'=>array(
					'Cuenta.jugador_id IS NULL',
					'Cuenta.comisionista_id IS NULL'
				)
			)
		);
		$i=0;
		foreach ($cuentas as $cuenta){
			$saldo = 0;
			foreach ($cuenta['Movimientos'] as $movimiento){
				if($movimiento['tipo_movimiento']==1){//Ingreso;
					$saldo += $movimiento['monto'];
				}else{
					$saldo -= $movimiento['monto'];
				}
			}
			$cuentas[$i]['saldo'] = $saldo;
			$i++;
		}
		$this->set('cuentas',$cuentas);
	}

	function add(){
		if($this->request->is('post')){
			$this->request->data['Cuenta']['estado']=1;
			if (!isset($this->request->data['Cuenta']['nombre'])){
				$this->request->data['Cuenta']['nombre'] = $this->request->data['Cuenta']['banco']." - ****".substr($this->request->data['Cuenta']['cuenta_bancaria'],-4);
			}
			if($this->Cuenta->save($this->request->data)){
				$this->Session->setFlash('La cuenta bancaria ha sido reigstrada','default',array('class'=>'success'));
				if (isset($this->request->data['Cuenta']['jugador_id'])){
					return $this->redirect(array('controller'=>'jugadors','action'=>'view',$this->request->data['Cuenta']['jugador_id']));
				}else if(isset($this->request->data['Cuenta']['comisionista_id'])){
					return $this->redirect(array('controller'=>'comisionistas','action'=>'view',$this->request->data['Cuenta']['comisionista_id']));
				}
				else{
					return $this->redirect(array('controller'=>'cuentas','action'=>'index'));
				}
			}
		}
	}

	function view($id = null){
		//$this->Cuenta->Behaviors->load('Containable');
		$cuenta = $this->Cuenta->find(
			'first',
			array(
				'conditions'=>array(
					'Cuenta.id' => $id
				)
			)
		);
		$this->set('titulo_seccion','Cuenta de banco '.$cuenta['Cuenta']['nombre']);
		$this->set('cuenta',$cuenta);

	}

	function comisionistas(){
		$this->set('titulo_seccion','Lista de Comisionistas');
		$comisionistas = array(
			1=>array(
				'Comisionista'=>array(
					'nombre'=>'César Pineda',
					'celular'=>'1111111111111',
					'numero_jugadores'=>rand(1,20),
					'por_cobrar'=>rand(-10000,10000),
					'forma_pago'=>'Transferencia',
					'comision'=>rand(0,30000),
				)

			),
			2=>array(
				'Comisionista'=>
				array(
					'nombre'=>'Omar Rihbany',
					'celular'=>'2222222222',
					'numero_jugadores'=>rand(1,20),
					'por_cobrar'=>rand(-10000,10000),
					'forma_pago'=>'Transferencia',
					'comision'=>rand(0,30000),
				)
			),
		);
		$this->set('comisionistas',$comisionistas);
	}

	public function updateInline($id = null) {
		// 1. Solo permitir peticiones POST y AJAX
		if (!$this->request->is('post') || !$this->request->is('ajax')) {
			throw new MethodNotAllowedException(); // Lanza error si no es POST/AJAX
		}

		// Deshabilitar la renderización de la vista para una respuesta AJAX limpia
		$this->autoRender = false;
		$this->layout = 'ajax'; // O puedes usar null si no quieres ningún layout

		$this->Cuenta->id = $id;

		if (!$this->Cuenta->exists()) {
			echo json_encode(['status' => 'error', 'message' => 'Cuenta no encontrada.']);
			return;
		}

		// 2. Obtener los datos enviados por AJAX
		$field = $this->request->data('field'); // 'nombre'
		$value = $this->request->data('value'); // El nuevo valor

		// 3. Asegurar que solo se pueda modificar el campo 'nombre' (Seguridad)
		if ($field !== 'nombre') {
			echo json_encode(['status' => 'error', 'message' => 'Campo no permitido para edición.']);
			return;
		}

		// 4. Guardar el nuevo valor
		$dataToSave = array(
			'id' => $id,
			$field => $value
		);

		if ($this->Cuenta->save($dataToSave)) {
			// Éxito: devolver una respuesta JSON
			echo json_encode(['estatus' => 'success', 'message' => 'Actualizado correctamente']);
		} else {
			// Error de validación o base de datos
			$error = $this->Cuenta->validationErrors;
			echo json_encode(['estatus' => 'error', 'message' => 'Error al guardar: ' . json_encode($error)]);
		}
	}
}
