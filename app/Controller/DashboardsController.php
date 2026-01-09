<?php
App::uses('AppController', 'Controller');

class DashboardsController extends AppController {
	public $uses = array('Ganancia', 'Movimiento', 'Jugador');
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

		// Filtros para Ganancia (semana/año) y Movimientos (fecha_aplicacion)
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

		// --- 2. Cálculos de los Nuevos Indicadores ---

		// I1: Resultado Juego Teórico (Azul Obscuro)
		$resI1 = $this->Ganancia->find('first', array(
			'fields' => array('SUM(Ganancia.ganancia_neta) as total'),
			'conditions' => $condGanancia
		));
		$I1_Teorico = (float)$resI1[0]['total'];

		// I2: Resultado Juego Realizado (Morado)
		// Sumatoria (tipo 1) - Sumatoria (tipo 2) donde jugador_id != null, menos I1
		$resI2 = $this->Movimiento->find('first', array(
			'fields' => array(
				'SUM(CASE WHEN tipo_movimiento = 1 THEN monto ELSE 0 END) as pos',
				'SUM(CASE WHEN tipo_movimiento = 2 THEN monto ELSE 0 END) as neg'
			),
			'conditions' => array_merge($condMov, array('Movimiento.jugador_id !=' => null))
		));
		$I2_Realizado = ((float)$resI2[0]['pos'] - (float)$resI2[0]['neg']) - $I1_Teorico;

		// I3: Gastos Operativos (Naranja)
		// tipo_movimiento = 2 AND (tipo_gasto = 0 OR tipo_gasto = 'Comisión')
		$resI3 = $this->Movimiento->find('first', array(
			'fields' => array('SUM(Movimiento.monto) as total'),
			'conditions' => array_merge($condMov, array(
				'Movimiento.tipo_movimiento' => 2,
				'OR' => array(
					'Movimiento.tipo_gasto' => 0,
					'Movimiento.tipo_gasto' => 'Comisión'
				)
			))
		));
		$I3_GastosOp = (float)$resI3[0]['total'];

		// I4: Resultado Operativo (Verde) = I2 - I3
		$I4_ResOperativo = $I2_Realizado - $I3_GastosOp;

		// I5: % Gastos Operativos (Rojo) = (I3 / I2) * 100
		$I5_PctGastos = ($I2_Realizado != 0) ? abs(($I3_GastosOp / $I2_Realizado) * 100) : 0;

		// I6: % Utilidad Operativa (Amarillo) = (I4 / I2) * 100
		$I6_PctUtilidad = ($I2_Realizado != 0) ? abs(($I4_ResOperativo / $I2_Realizado) * 100) : 0;

		// I7: Balance Pendiente (Gris Obscuro) = I1 - I2
		$I7_BalancePend = $I1_Teorico - $I2_Realizado;

		// I8: % Realización Resultado (Aqua) = (I2 / I1) * 100
		$I8_PctRealizacion = ($I1_Teorico != 0) ? abs(($I2_Realizado / $I1_Teorico) * 100) : 0;

		// Pasar a la vista
		$this->set(compact(
			'I1_Teorico', 'I2_Realizado', 'I3_GastosOp', 'I4_ResOperativo',
			'I5_PctGastos', 'I6_PctUtilidad', 'I7_BalancePend', 'I8_PctRealizacion'
		));

		$this->request->data['Dashboard']['fecha_inicio'] = $fecha_inicio;
		$this->request->data['Dashboard']['fecha_fin'] = $fecha_fin;
	}
}
