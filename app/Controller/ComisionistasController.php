<?php
class ComisionistasController extends AppController {
	public $name = 'Comisionistas';

	function index(){
		$this->set('titulo_seccion','Lista de Agencias');
		$this->Comisionista->Behaviors->load('Containable');

		$comisionistas = $this->Comisionista->find(
			'all',
			array(
				'contain'=>array(
					'Jugadors'=>array(
						'conditions'=>array(
							'estatus'=>1
						)
					),
					'Movimientos',
					'Comisiones'
				)
			)
		);
		$this->set('comisionistas',$comisionistas);


		// Obtener los saldos iniciales agrupados por comisionista
		$this->loadModel('Jugador');
		$saldos_iniciales =  $this->Jugador->find(
			'all',
			array(
				'fields'=>array(
					'comisionista_id','SUM(saldo_inicial)'
				),
				'group'=>array(
					'comisionista_id'
				)
			)
		);
		$this->set('saldos_iniciales',$saldos_iniciales);

		//Obtener las ganancias totales de todos los jugadores
		$this->loadModel('Ganancia');
		$ganancias = $this->Ganancia->find(
			'all',
			array(
				'fields'=>array(
					'comisionista_id','SUM(ganancia_neta)'
				),
				'group'=>array(
					'comisionista_id'
				)
			)
		);
		$this->set('ganancias',$ganancias);

		$this->loadModel('Movimiento');
		$this->Movimiento->Behaviors->load('Containable');
		$movimientos = $this->Movimiento->query("
			SELECT
			movimientos.jugador_id,
			SUM(
				CASE
					WHEN tipo_movimiento = 1 THEN monto  -- Tipo 1 (Suma)
					WHEN tipo_movimiento = 2 THEN -monto -- Tipo 2 (Resta)
					ELSE 0 -- En caso de otro tipo desconocido
				END
			) AS saldo_neto_movimientos,
			jugadors.comisionista_id
		FROM
			movimientos,jugadors
		WHERE
			jugador_id IS NOT NULL AND jugador_id = jugadors.id
		GROUP BY
			jugador_id;
		");
		$this->set('movimientos',$movimientos);

		$movimientos_array = array();
		foreach($movimientos as $movimiento) {
			if(!isset($movimientos_array[$movimiento['jugadors']['comisionista_id']])){
				$movimientos_array[$movimiento['jugadors']['comisionista_id']] = 0;
			}
			$movimientos_array[$movimiento['jugadors']['comisionista_id']] += $movimiento[0]['saldo_neto_movimientos'];
		}
		$this->set('movimientos_arreglo',$movimientos_array);

		$movimientos_finales_array = array();
		foreach ($comisionistas as $comisionista) {
			$movimientos_finales_array[$comisionista['Comisionista']['id']] = array(
				'saldo_movimientos' => $movimientos_array[$comisionista['Comisionista']['id']]
			);
			foreach ($ganancias as $ganancia) {
				if($ganancia['Ganancia']['comisionista_id'] == $comisionista['Comisionista']['id']){
					$movimientos_finales_array[$comisionista['Comisionista']['id']]['ganancia'] = $ganancia[0]['SUM(ganancia_neta)'];
				}
			}
			foreach ($saldos_iniciales as $saldo_inicial) {
				if($saldo_inicial['Jugador']['comisionista_id'] == $comisionista['Comisionista']['id']){
					$movimientos_finales_array[$comisionista['Comisionista']['id']]['saldo_inicial'] = $saldo_inicial[0]['SUM(saldo_inicial)'];
				}
			}
		}
		$this->set('movimientos_finales',$movimientos_finales_array);

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
		$this->set('cuentas',$cuentas);

	}

	function add(){
		if($this->request->is('post')){
			if($this->Comisionista->save($this->request->data)) {
				/*$comisionista_id = $this->Comisionista->getInsertID();
				$this->loadModel('Cuenta');
				$contador = $this->request->data['Comisionista']['contador'];
				for ($i = 0; $i < $contador; $i++) {
					$nombre = $this->request->data['Comisionista']['banco_cuentas['.$i]['banco']." - ****".substr($this->request->data['Comisionista']['banco_cuentas['.$i]['cuenta_bancaria'],-4);
					$cuenta_obj = array(
						'comisionista_id' => $comisionista_id,
						'nombre' => $nombre,
						'banco' => $this->request->data['Comisionista']['banco_cuentas['.$i]['banco'],
						'cuenta_bancaria' => $this->request->data['Comisionista']['banco_cuentas['.$i]['cuenta_bancaria'],
						'spei'=>$this->request->data['Comisionista']['banco_cuentas['.$i]['spei'],
						'beneficiario'=>$this->request->data['Comisionista']['banco_cuentas['.$i]['beneficiario'],
					);
					$this->Cuenta->create();
					$this->Cuenta->save($cuenta_obj);
				}*/
				$this->Session->setFlash('La agencia ha sido registrado exitosamente.', 'default', array('class' => 'success_flash'));
				if (isset($this->request->data['Comisionista']['return'])){
					return $this->redirect(array('action' => 'view', $this->request->data['Comisionista']['id']));
				}
				return $this->redirect(array('action' => 'index','controller'=>'comisionistas'));
			}
		}
	}

	function view($id=null){
		$comisionista = $this->Comisionista->findById($id);
		$this->set('comisionista',$comisionista);
		$this->set('titulo_seccion','Detalle de Agencia '.$comisionista['Comisionista']['nombre']);

		// Obtener los saldos iniciales agrupados por comisionista
		$this->loadModel('Jugador');
		$saldos_iniciales =  $this->Jugador->find(
			'all',
			array(
				'fields'=>array(
					'comisionista_id','SUM(saldo_inicial)'
				),
				'group'=>array(
					'id'
				),
				'conditions'=>array(
					'comisionista_id' => $id
				)
			)
		);
		$this->set('saldos_iniciales',$saldos_iniciales);

		//Obtener las ganancias totales de todos los jugadores
		$this->loadModel('Ganancia');
		$ganancias = $this->Ganancia->find(
			'all',
			array(
				'fields'=>array(
					'jugador_id','SUM(ganancia_neta)'
				),
				'group'=>array(
					'jugador_id'
				),
				'conditions'=>array(
					'Ganancia.comisionista_id' => $id
				)
			)
		);
		$this->set('ganancias',$ganancias);

		$this->loadModel('Movimiento');
		$this->Movimiento->Behaviors->load('Containable');
		$movimientos = $this->Movimiento->query("
			SELECT
			movimientos.jugador_id,
			SUM(
				CASE
					WHEN tipo_movimiento = 1 THEN monto  -- Tipo 1 (Suma)
					WHEN tipo_movimiento = 2 THEN -monto -- Tipo 2 (Resta)
					ELSE 0 -- En caso de otro tipo desconocido
				END
			) AS saldo_neto_movimientos,
			jugadors.comisionista_id
		FROM
			movimientos,jugadors
		WHERE
			jugador_id IS NOT NULL AND jugador_id = jugadors.id
		AND
			jugadors.comisionista_id = ".$id."
		GROUP BY
			jugador_id;
		");
		$this->set('movimientos',$movimientos);

		$movimientos_array = array();
		foreach($movimientos as $movimiento) {
			if(!isset($movimientos_array[$movimiento['movimientos']['jugador_id']])){
				$movimientos_array[$movimiento['movimientos']['jugador_id']] = 0;
			}
			$movimientos_array[$movimiento['movimientos']['jugador_id']] += $movimiento[0]['saldo_neto_movimientos'];
		}
		$this->set('movimientos_arreglo',$movimientos_array);

		$movimientos_finales_array = array();
			foreach ($ganancias as $ganancia) {
				$movimientos_finales_array[$ganancia['Ganancia']['jugador_id']] = array(
					'saldo_movimientos' => $movimientos_array[$ganancia['Ganancia']['jugador_id']]
				);
				$movimientos_finales_array[$ganancia['Ganancia']['jugador_id']]['ganancia'] = $ganancia[0]['SUM(ganancia_neta)'];
			}
			foreach ($saldos_iniciales as $saldo_inicial) {
				$movimientos_finales_array[$saldo_inicial['Jugador']['id']]['saldo_inicial'] = $saldo_inicial[0]['SUM(saldo_inicial)'];
			}

		$this->set('movimientos_finales',$movimientos_finales_array);

	}

	function getComisionista(){
		$comisionista = $this->Comisionista->findById($this->request->data['id']);
		header('Content-Type: application/json');
		echo json_encode($comisionista);
		exit();
	}
}
