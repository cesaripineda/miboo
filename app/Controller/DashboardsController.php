<?php
App::uses('AppController', 'Controller');

class DashboardsController extends AppController {
	public $uses = array('Ganancia', 'Movimiento');
	public $helpers = array('Html', 'Form', 'Number');

	public function index() {
		$this->set('titulo_seccion',"Dashboard");
		// --- 1. Gestión de Fechas ---
		if ($this->request->is('post') && !empty($this->request->data['Dashboard']['fecha_inicio'])) {
			$fecha_inicio = $this->request->data['Dashboard']['fecha_inicio'];
			$fecha_fin = $this->request->data['Dashboard']['fecha_fin'];
		} else {
			$fecha_inicio = '2025-01-01';
			$fecha_fin = date('Y-m-d');
		}

		$semana_ini = date('W', strtotime($fecha_inicio));
		$anio_ini = date('Y', strtotime($fecha_inicio));
		$semana_fin = date('W', strtotime($fecha_fin));
		$anio_fin = date('Y', strtotime($fecha_fin));

		$condGanancia = array(
			'AND' => array(
				array('Ganancia.anio >=' => $anio_ini, 'Ganancia.anio <=' => $anio_fin),
				array('Ganancia.semana >=' => $semana_ini, 'Ganancia.semana <=' => $semana_fin)
			)
		);

		$condMov = array(
			'Movimiento.fecha_aplicacion >=' => $fecha_inicio,
			'Movimiento.fecha_aplicacion <=' => $fecha_fin
		);

		// --- 2. Cálculos según la nueva tabla ---

		// I1: Resultado Juego Teórico (Azul Marino)
		$resI1 = $this->Ganancia->find('first', array(
			'fields' => array('SUM(Ganancia.ganancia_neta) as total'),
			'conditions' => $condGanancia
		));
		$I1 = (float)$resI1[0]['total'];

		// I2: Resultado Juego Realizado (Morado)
		// Sumatoria monto donde jugador_id != null (Neto: Aportaciones - Pagos)
		$resI2 = $this->Movimiento->find('first', array(
			'fields' => array(
				'SUM(CASE WHEN tipo_movimiento = 1 THEN monto ELSE 0 END) as pos',
				'SUM(CASE WHEN tipo_movimiento = 2 THEN monto ELSE 0 END) as neg'
			),
			'conditions' => array_merge($condMov, array('Movimiento.jugador_id !=' => null))
		));
		$I2 = (float)$resI2[0]['neg'] - (float)$resI2[0]['pos'];

		// I3: Gastos Operativos (Naranja)
		// Sumatoria monto donde tipo_gasto es 2, 0 o 'Comisión' y tipo_movimiento = 2
		$resI3 = $this->Movimiento->find('first', array(
			'fields' => array('SUM(Movimiento.monto) as total'),
			'conditions' => array_merge($condMov, array(
				'Movimiento.tipo_movimiento' => 2,
				'OR' => array(
					'Movimiento.tipo_gasto' => array(0, 2, 'Comisión')
				)
			))
		));
		$I3 = (float)$resI3[0]['total'];

		// I4: Resultado Operativo (Verde) = I2 + I3
		$I4 = $I2 + $I3;

		// I5: % Gastos Operativos (Rojo) = I3 / I2
		$I5 = ($I2 != 0) ? ($I3 / $I2) * 100 : 0;
		$I5 = abs($I5);

		// I6: % Utilidad Operativa (Amarillo) = I4 / I2
		$I6 = ($I2 != 0) ? ($I4 / $I2) * 100 : 0;
		$I6 = abs($I6);

		// I7: Balance Pendiente (Negro) = I1 - I2
		$I7 = $I1 - $I2;

		// I8: % Realización Resultado (Aqua) = I2 / I1
		$I8 = ($I1 != 0) ? ($I2 / $I1) * 100 : 0;
		$I8 = abs($I8);

		// --- 3. Pasar a la Vista ---
		$this->set(compact('I1', 'I2', 'I3', 'I4', 'I5', 'I6', 'I7', 'I8'));
		$this->request->data['Dashboard']['fecha_inicio'] = $fecha_inicio;
		$this->request->data['Dashboard']['fecha_fin'] = $fecha_fin;
	}
}
