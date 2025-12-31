<?php
App::uses('AppController', 'Controller');

class DashboardsController extends AppController {
	public $uses = array('Ganancia', 'Movimiento', 'Jugador');
	public $helpers = array('Html', 'Form', 'Number');

	public function index() {
		$this->set('titulo_seccion',"Dahsboard MiBoo");
		// --- 1. Gestión de Fechas (Filtro) ---
		if ($this->request->is('post') && !empty($this->request->data['Dashboard']['fecha_inicio'])) {
			$fecha_inicio = $this->request->data['Dashboard']['fecha_inicio'];
			$fecha_fin = $this->request->data['Dashboard']['fecha_fin'];
		} else {
			$fecha_inicio = date('Y-m-d', strtotime('2025-01-01'));
			$fecha_fin = date('Y-m-d');
		}

		// Conversión para modelo Ganancia (semana y año)
		$semana_ini = date('W', strtotime($fecha_inicio));
		$anio_ini = date('Y', strtotime($fecha_inicio));
		$semana_fin = date('W', strtotime($fecha_fin));
		$anio_fin = date('Y', strtotime($fecha_fin));

		$condGanancia = array(
			'OR' => array(
				array('Ganancia.anio >' => $anio_ini, 'Ganancia.anio <' => $anio_fin),
				array('Ganancia.anio' => $anio_ini, 'Ganancia.semana >=' => $semana_ini),
				array('Ganancia.anio' => $anio_fin, 'Ganancia.semana <=' => $semana_fin)
			)
		);

		$condMov = array(
			'Movimiento.fecha_aplicacion >=' => $fecha_inicio,
			'Movimiento.fecha_aplicacion <=' => $fecha_fin
		);

		// --- 2. Cálculos de Indicadores ---

		// INDICADOR 1: Resultado Bruto (Azul)
		$resI1 = $this->Ganancia->find('first', array(
			'fields' => array('SUM(Ganancia.ganancia_neta) as total'),
			'conditions' => $condGanancia
		));
		$I1_ResBruto = (float)$resI1[0]['total'];

		// INDICADOR 2: Gastos Operativos (Naranja)
		// tipo_gasto IN (0, 1, 'Comisión') AND tipo_movimiento = 2
		$resI2 = $this->Movimiento->find('first', array(
			'fields' => array('SUM(Movimiento.monto) as total'),
			'conditions' => array_merge($condMov, array(
				'Movimiento.tipo_movimiento' => 2,
				'OR' => array(
					'Movimiento.tipo_gasto' => array(0, 1, 'Comisión')
				)
			))
		));
		$I2_GastosOp = (float)$resI2[0]['total'];

		// INDICADOR 3: Resultado Operativo (Verde)
		// (tipo_gasto IS NULL OR 'Interjugador') AND tipo_movimiento = 1
		$resI3 = $this->Movimiento->find('first', array(
			'fields' => array('SUM(Movimiento.monto) as total'),
			'conditions' => array_merge($condMov, array(
				'Movimiento.tipo_movimiento' => 1,
				'OR' => array(
					'Movimiento.tipo_gasto = ""',
					'Movimiento.tipo_gasto' => 'Interjugador'
				)
			))
		));
		$I3_ResOperativo = (float)$resI3[0]['total'];

		// INDICADOR 4: Balance Pendiente (Morado)
		// a) Saldo inicial jugadores (Total histórico)
		$resSaldoIni = $this->Jugador->find('first', array('fields' => array('SUM(Jugador.saldo_inicial) as total')));
		$totalSaldoInicial = (float)$resSaldoIni[0]['total'];

		// b) Movimientos con jugador_id (tipo 1 suma, tipo 2 resta) dentro del rango
		$resMovPlayer = $this->Movimiento->find('first', array(
			'fields' => array(
				'SUM(CASE WHEN tipo_movimiento = 1 THEN monto ELSE 0 END) as aportaciones',
				'SUM(CASE WHEN tipo_movimiento = 2 THEN monto ELSE 0 END) as pagos'
			),
			'conditions' => array_merge($condMov, array('Movimiento.jugador_id !=' => null))
		));
		$netoMovimientos = (float)$resMovPlayer[0]['aportaciones'] - (float)$resMovPlayer[0]['pagos'];

		// Cálculo final I4
		$I4_BalancePendiente = $totalSaldoInicial + $I1_ResBruto + $netoMovimientos;

		// INDICADOR 5: % Gastos (Rojo) -> I3 / I4
		$I5_PctGastos = ($I2_GastosOp != 0) ? ($I2_GastosOp / $I3_ResOperativo) * 100 : 0;

		// INDICADOR 6: % Utilidad (Amarillo) -> I4 / I3
		$I6_PctUtilidad = ($I3_ResOperativo != 0) ? ($I3_ResOperativo / $I2_GastosOp) * 100 : 0;

		// --- 3. Enviar a la Vista ---
		$this->set(compact('I1_ResBruto', 'I2_GastosOp', 'I3_ResOperativo', 'I4_BalancePendiente', 'I5_PctGastos', 'I6_PctUtilidad'));
		$this->request->data['Dashboard']['fecha_inicio'] = $fecha_inicio;
		$this->request->data['Dashboard']['fecha_fin'] = $fecha_fin;
	}
}
