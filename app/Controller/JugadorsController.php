<?php
class JugadorsController extends AppController {
	public $name = 'Jugadors';

	function index($all = null) {
		$this->set('titulo_seccion','Lista de Jugadores');

		$this->loadModel('Comisionista');
		$comisionistas = $this->Comisionista->find('list');
		if (isset($all)){
			$jugadores = $this->Jugador->find('all');
			$this->set('all', $all);
		}else{
			$jugadores = $this->Jugador->find('all',array('conditions'=>array('Jugador.estatus'=>1)));
		}
		$jugadores_list = $this->Jugador->find('list',array('conditions'=>array('Jugador.estatus')));
		$i=0;
		foreach ($jugadores as $jugador) {
			$saldo = $jugador['Jugador']['saldo_inicial'];
			foreach ($jugador['Movimientos'] as $movimiento) {
				switch ($movimiento['tipo_movimiento']) {
					case 1: //Depósito, se suma
						$saldo += $movimiento['monto'];
						break;
					case 2:
						$saldo -= $movimiento['monto'];
						break;
				}

			}
			foreach ($jugador['Ganancias'] as $ganancia) {
				$saldo += $ganancia['ganancia_neta'];
			}
			$jugadores[$i]['Saldo'] = $saldo;
			$i++;
		}

		$this->loadModel('Cuenta');
		$cuentas = $this->Cuenta->find(
			'list',
			array(
				'conditions'=>array(
					'Cuenta.jugador_id IS NULL',
					'Cuenta.comisionista_id IS NULL',
					'Cuenta.estado'=>1
				)
			)
		);


		$this->set('comisionistas',$comisionistas);
		$this->set('jugadores',$jugadores);
		$this->set('jugadores_list',$jugadores_list);
		$this->set('cuentas',$cuentas);
	}

	function ganancias_semanales(){
		$this->set('titulo_seccion','Lista de Jugadores');
		$this->Jugador->Behaviors->load('Containable');
		$jugadores = $this->Jugador->find('all',array('conditions'=>array('Jugador.estatus'=>1)));
		$i=0;
		foreach ($jugadores as $jugador) {
			$saldo = $jugador['Jugador']['saldo_inicial'];
			foreach ($jugador['Movimientos'] as $movimiento) {
				switch ($movimiento['tipo_movimiento']) {
					case 1: //Depósito, se suma
						$saldo += $movimiento['monto'];
						break;
					case 2:
						$saldo -= $movimiento['monto'];
						break;
				}

			}
			foreach ($jugador['Ganancias'] as $ganancia) {
				$saldo += $ganancia['ganancia_neta'];
			}
			$jugadores[$i]['Saldo'] = $saldo;
			$i++;
		}
		$this->set('jugadores',$jugadores);
	}

	function add(){
		if($this->request->is('post')){
			if($this->Jugador->save($this->request->data)) {
				//$jugador_id = $this->Jugador->getInsertID();
				//$this->loadModel('Cuenta');
				/*if (isset($this->request->data['Jugador']['contador'])){
					$contador = $this->request->data['Jugador']['contador'];
					for ($i = 0; $i < $contador; $i++) {
						$nombre = $this->request->data['Jugador']['banco_cuentas['.$i]['banco']." - ****".substr($this->request->data['Jugador']['banco_cuentas['.$i]['cuenta_bancaria'],-4);
						$cuenta_obj = array(
							'jugador_id' => $jugador_id,
							'nombre' => $nombre,
							'banco' => $this->request->data['Jugador']['banco_cuentas['.$i]['banco'],
							'cuenta_bancaria' => $this->request->data['Jugador']['banco_cuentas['.$i]['cuenta_bancaria'],
							'spei'=>$this->request->data['Jugador']['banco_cuentas['.$i]['spei'],
							'beneficiario'=>$this->request->data['Jugador']['banco_cuentas['.$i]['beneficiario'],
						);
						$this->Cuenta->create();
						$this->Cuenta->save($cuenta_obj);
					}
				}*/
				$this->Session->setFlash('El Jugador ha sido registrado exitosamente.', 'default', array('class' => 'success_flash'));
				if (isset($this->request->data['Jugador']['return'])){
					return $this->redirect(array('action' => 'view', $this->request->data['Jugador']['id']));
				}
				return $this->redirect(array('action' => 'index','controller'=>'jugadors'));
			}
		}
	}

	function getJugador(){
		$jugador = $this->Jugador->findById($this->request->data['id']);
		header('Content-Type: application/json');
		echo json_encode($jugador);
		exit();
	}

	function activar(){
		$jugador = array(
			'id'=>$this->request->data['id'],
			'estatus'=>$this->request->data['estado']
		);
		$mensaje = "";
		if($this->Jugador->save($jugador)){
			$mensaje = "Jugador Actualizado";
		}else{
			$mensaje = "Jugador No Actualizado";
		}
		header('Content-Type: application/json');
		echo json_encode($mensaje);
		exit();
	}

	function view($id=null){
		$jugador = $this->Jugador->findById($id);
		$this->set('jugador',$jugador);
		$this->loadModel('Comisionista');
		$comisionistas = $this->Comisionista->find('list');
		$this->set('comisionistas',$comisionistas);
		$this->set('titulo_seccion',"Jugador: ".$jugador['Jugador']['nombre']);
	}

	function getDuplicado(){
		$valor = $this->request->data['str'];
		$mensaje = 0;
		$duplicados = $this->Jugador->find('count',array('conditions'=>array('OR'=>array('Jugador.celular'=>$valor, 'Jugador.email'=>$valor))));
		if($duplicados > 0){
			$mensaje = 1;
		}
		header('Content-Type: application/json');
		echo json_encode($mensaje);
		exit();
	}


}
