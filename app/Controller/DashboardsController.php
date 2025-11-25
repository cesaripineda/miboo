<?php

App::uses('AppController', 'Controller');

class DashboardsController extends AppController
{

	// Indica los modelos que usará el controlador
	public $uses = array('Ganancia', 'Movimiento');

	public function index()
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
}
