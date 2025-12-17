<?php

App::uses('AppController', 'Controller');

class DashboardsController extends AppController
{

	// Indica los modelos que usará el controlador
	public $uses = array('Ganancia', 'Movimiento', 'Jugador');

	/*
		Indicador 1 (Balance).- Sumatoria de la columna ganancia_neta (tabla ganancias) de la tabla ganancias
		Indicador 2 (Gastos).- Sumatoria de la columna monto (tabla movimientos) cuando el valor de la columna tipo_gasto = 0
		Indicador 3 (Utilidad).- Resta de Indicador 1 (Balance) - Indicador 2 (Gastos)
		Indicador 4 (% Gastos).- División entre Indicador 2 (Gastos) / Indicador 3 (Utilidad), multiplicado por 100. (Para que de en porcentaje)
		Indicador 5 (% de Utilidad).- División entre Indicador 3 (Utilidad) / Indicador 2 (Gastos), multiplicado por 100. (Para que de en porcentaje)
		Indicador 6 (Cuentas Pendientes).- Se tiene que sumar de la tabla jugadors la columna (saldo_inicial). Después sumarle el total de ganacia_neta (tabla ganancias). Finalmente, se tiene que sacar un balance de aportaciones y pagos que se hace de la siguiente manera: en la tabla de movimientos, solo obtener los que tengan un jugador_id IS NOT NULL, después, sumar los que tengan tipo_movimiento = 1 y restar los que tengan tipo_movimiento = 2. El total de todo esto, es la cuenta pendiente.
	 */
	public function index_old()
	{
		$this->set('titulo_seccion','Tablero de Control MiBoo');
		// Inicialización de las variables de fecha
		$fecha_inicio = null;
		$fecha_fin = null;
		$condiciones_ganancia = array();
		$condiciones_movimiento = array();

		// 📝 1. Procesamiento del Formulario de Filtro
		if ($this->request->is('post') && !empty($this->request->data['Dashboard']['fecha_inicio']) && !empty($this->request->data['Dashboard']['fecha_fin'])) {
			$fecha_inicio = $this->request->data['Dashboard']['fecha_inicio'];
			$fecha_fin = $this->request->data['Dashboard']['fecha_fin'];

			// VALIDACIONES BÁSICAS: Asegurar que las fechas sean válidas.
			if (strtotime($fecha_inicio) > strtotime($fecha_fin)) {
				$this->Session->setFlash('La fecha de inicio no puede ser posterior a la fecha de fin.', 'default', array('class' => 'alert alert-warning'));
				// Si la validación falla, podemos usar las fechas iniciales para evitar errores
				$fecha_inicio = date('Y-m-d', strtotime('-30 days'));
				$fecha_fin = date('Y-m-d');
			}
		} else {
			// Valores por defecto (ej. últimos 30 días si no hay filtro)
			$fecha_inicio = date('Y-m-d', strtotime('-30 days'));
			$fecha_fin = date('Y-m-d');
		}

		// 🔄 2. Definición de Condiciones de Filtrado

		// Condiciones para Ganancias (semana y anio)
		// Necesitamos convertir las fechas de inicio y fin a sus correspondientes semana y anio.
		// PHP ofrece formatos 'W' (semana ISO 8601) y 'Y' (año).
		// Nota: Si el campo 'semana' en tu DB es solo un número (1-52), esto puede requerir ajustes
		// para manejar el cruce de años, pero esta es una buena base.
		$semana_inicio = date('W', strtotime($fecha_inicio));
		$anio_inicio = date('Y', strtotime($fecha_inicio));
		$semana_fin = date('W', strtotime($fecha_fin));
		$anio_fin = date('Y', strtotime($fecha_fin));

		// Filtro complejo para abarcar rangos de semanas y años
		$condiciones_ganancia['OR'] = array(
			// Casos donde el año es intermedio (totalmente cubierto)
			'Ganancia.anio >' => $anio_inicio,
			'Ganancia.anio <' => $anio_fin,

			// Casos donde el año es el de inicio (de esa semana en adelante)
			array(
				'Ganancia.anio' => $anio_inicio,
				'Ganancia.semana >=' => $semana_inicio
			),

			// Casos donde el año es el de fin (hasta esa semana)
			array(
				'Ganancia.anio' => $anio_fin,
				'Ganancia.semana <=' => $semana_fin
			),

			// Caso donde el rango es dentro del mismo año (de semana inicio a semana fin)
			array(
				'Ganancia.anio' => $anio_inicio,
				'Ganancia.anio' => $anio_fin, // Redundante si $anio_inicio == $anio_fin, pero explícito
				'Ganancia.semana >=' => $semana_inicio,
				'Ganancia.semana <=' => $semana_fin
			)
		);

		// Condiciones para Movimientos (fecha_aplicacion)
		$condiciones_movimiento['Movimiento.fecha_aplicacion >='] = $fecha_inicio;
		$condiciones_movimiento['Movimiento.fecha_aplicacion <='] = $fecha_fin;

		// Establecer las fechas seleccionadas en la vista para mantener el estado del filtro
		$this->request->data['Dashboard']['fecha_inicio'] = $fecha_inicio;
		$this->request->data['Dashboard']['fecha_fin'] = $fecha_fin;

		// 📊 3. Obtención de Indicadores

		// 3.1. Sumatoria de Ganancia Neta
		$ganancia_neta = $this->Ganancia->find('first', array(
			'fields' => array('SUM(Ganancia.ganancia_neta) AS total_ganancia_neta'),
			'conditions' => $condiciones_ganancia
		));
		$total_ganancia_neta = $ganancia_neta[0]['total_ganancia_neta'] ?: 0; // Uso de '?: 0' para evitar null

		// 3.2. Sumatoria de Comisión
		$comision = $this->Ganancia->find('first', array(
			'fields' => array('SUM(Ganancia.comision) AS total_comision'),
			'conditions' => $condiciones_ganancia
		));
		$total_comision = $comision[0]['total_comision'] ?: 0;

		// 3.3. Sumatoria de Sueldos (Movimiento.monto donde tipo_gasto=1)
		$condiciones_sueldos = $condiciones_movimiento;
		$condiciones_sueldos['Movimiento.tipo_gasto'] = 1;
		$sueldos = $this->Movimiento->find('first', array(
			'fields' => array('SUM(Movimiento.monto) AS total_sueldos'),
			'conditions' => $condiciones_sueldos
		));
		$total_sueldos = $sueldos[0]['total_sueldos'] ?: 0;

		// 3.4. Sumatoria de Gastos (Movimiento.monto donde tipo_gasto=0)
		$condiciones_gastos = $condiciones_movimiento;
		$condiciones_gastos['Movimiento.tipo_gasto'] = 0;
		$gastos = $this->Movimiento->find('first', array(
			'fields' => array('SUM(Movimiento.monto) AS total_gastos'),
			'conditions' => $condiciones_gastos
		));
		$total_gastos = $gastos[0]['total_gastos'] ?: 0;

		// Indicador de Utilidad Neta (Opcional, pero útil)
		$utilidad_neta = $total_ganancia_neta - $total_sueldos - $total_gastos - $total_comision;

		// 📤 4. Pasar los resultados a la Vista
		$this->set(compact('total_ganancia_neta', 'total_comision', 'total_sueldos', 'total_gastos', 'utilidad_neta'));
	}

	public function index() {
		// 1. Inicialización de Fechas y Filtros (Lógica de filtrado reutilizada)
		$fecha_inicio = null;
		$fecha_fin = null;
		$condiciones_ganancia = array();
		$condiciones_movimiento = array();

		if ($this->request->is('post') && !empty($this->request->data['Dashboard']['fecha_inicio']) && !empty($this->request->data['Dashboard']['fecha_fin'])) {
			$fecha_inicio = $this->request->data['Dashboard']['fecha_inicio'];
			$fecha_fin = $this->request->data['Dashboard']['fecha_fin'];

			if (strtotime($fecha_inicio) > strtotime($fecha_fin)) {
				$this->Session->setFlash('La fecha de inicio no puede ser posterior a la fecha de fin.', 'default', array('class' => 'alert alert-warning'));
				$fecha_inicio = date('Y-m-d', strtotime('-30 days'));
				$fecha_fin = date('Y-m-d');
			}
		} else {
			// Valores por defecto
			$fecha_inicio = date('Y-m-d', strtotime('2025-01-01'));
			$fecha_fin = date('Y-m-d');
		}

		// Definición de Condiciones de Filtrado

		$semana_inicio = date('W', strtotime($fecha_inicio));
		$anio_inicio = date('Y', strtotime($fecha_inicio));
		$semana_fin = date('W', strtotime($fecha_fin));
		$anio_fin = date('Y', strtotime($fecha_fin));

		// Filtro para Ganancias (semana y anio)
		$condiciones_ganancia['OR'] = array(
			'Ganancia.anio >' => $anio_inicio,
			'Ganancia.anio <' => $anio_fin,
			array('Ganancia.anio' => $anio_inicio, 'Ganancia.semana >=' => $semana_inicio),
			array('Ganancia.anio' => $anio_fin, 'Ganancia.semana <=' => $semana_fin),
			array('Ganancia.anio' => $anio_inicio, 'Ganancia.anio' => $anio_fin, 'Ganancia.semana >=' => $semana_inicio, 'Ganancia.semana <=' => $semana_fin)
		);

		// Filtro para Movimientos (fecha_aplicacion)
		$condiciones_movimiento['Movimiento.fecha_aplicacion >='] = $fecha_inicio;
		$condiciones_movimiento['Movimiento.fecha_aplicacion <='] = $fecha_fin;

		// Establecer las fechas seleccionadas en la vista
		$this->request->data['Dashboard']['fecha_inicio'] = $fecha_inicio;
		$this->request->data['Dashboard']['fecha_fin'] = $fecha_fin;

		// 2. Obtención de Indicadores Base (I1 y I2)

		// Indicador 1 (Balance) - Sumatoria de ganancia_neta (tabla ganancias)
		$balance = $this->Ganancia->find('first', array(
			'fields' => array('SUM(Ganancia.ganancia_neta) AS total_balance'),
			'conditions' => $condiciones_ganancia
		));
		$I1_Balance = $balance[0]['total_balance'] ?: 0;

		// Indicador 2 (Gastos) - Sumatoria de monto (tabla movimientos) donde tipo_gasto = 0
		$condiciones_gastos = $condiciones_movimiento;
		$condiciones_gastos['Movimiento.tipo_gasto'] = 0;
		$gastos = $this->Movimiento->find('first', array(
			'fields' => array('SUM(Movimiento.monto) AS total_gastos'),
			'conditions' => $condiciones_gastos
		));
		$I2_Gastos = $gastos[0]['total_gastos'] ?: 0;

		// 3. Cálculos Derivados (I3, I4, I5)

		// Indicador 3 (Utilidad) - I1 - I2
		$I3_Utilidad = $I1_Balance - $I2_Gastos;

		// Indicador 4 (% Gastos) - (I2 / I3) * 100
		$I4_PctGastos = 0;
		if ($I3_Utilidad != 0) {
			$I4_PctGastos = ($I2_Gastos / $I3_Utilidad) * 100;
		}

		// Indicador 5 (% de Utilidad) - (I3 / I2) * 100
		$I5_PctUtilidad = 0;
		if ($I2_Gastos != 0) {
			$I5_PctUtilidad = ($I3_Utilidad / $I2_Gastos) * 100;
		}

		// 4. Indicador 6 (Cuentas Pendientes)

		// 4.1. Saldo Inicial de Jugadores (sin filtro de fecha)
		$saldo_inicial = $this->Jugador->find('first', array(
			'fields' => array('SUM(Jugador.saldo_inicial) AS total_saldo_inicial')
		));
		$total_saldo_inicial = $saldo_inicial[0]['total_saldo_inicial'] ?: 0;

		// 4.2. Sumatoria de Aportaciones (Movimiento: jugador_id IS NOT NULL y tipo_movimiento = 1)
		$condiciones_aportaciones = $condiciones_movimiento;
		$condiciones_aportaciones['Movimiento.jugador_id NOT'] = null; // En Cake 2.x, 'NOT' con valor null para IS NOT NULL
		$condiciones_aportaciones['Movimiento.tipo_movimiento'] = 1;
		$aportaciones = $this->Movimiento->find('first', array(
			'fields' => array('SUM(Movimiento.monto) AS total_aportaciones'),
			'conditions' => $condiciones_aportaciones
		));
		$total_aportaciones = $aportaciones[0]['total_aportaciones'] ?: 0;

		// 4.3. Sumatoria de Pagos (Movimiento: jugador_id IS NOT NULL y tipo_movimiento = 2)
		$condiciones_pagos = $condiciones_movimiento;
		$condiciones_pagos['Movimiento.jugador_id NOT'] = null;
		$condiciones_pagos['Movimiento.tipo_movimiento'] = 2;
		$pagos = $this->Movimiento->find('first', array(
			'fields' => array('SUM(Movimiento.monto) AS total_pagos'),
			'conditions' => $condiciones_pagos
		));
		$total_pagos = $pagos[0]['total_pagos'] ?: 0;

		// Indicador 6 (Cuentas Pendientes)
		// Saldo Inicial + Ganancia Neta + (Aportaciones - Pagos)
		$I6_CuentasPendientes = $total_saldo_inicial + $I1_Balance + ($total_aportaciones - $total_pagos);

		// 5. Pasar los resultados a la Vista
		$this->set(compact(
			'I1_Balance',
			'I2_Gastos',
			'I3_Utilidad',
			'I4_PctGastos',
			'I5_PctUtilidad',
			'I6_CuentasPendientes'
		));
	}
}
