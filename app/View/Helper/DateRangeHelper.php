<?php
App::uses('AppHelper', 'View/Helper');

class DateRangeHelper extends AppHelper {

	/**
	 * Convierte un identificador de semana (YYYY-WW) en un rango de fechas.
	 * Versión robusta para evitar errores de formato/ISO.
	 *
	 * @param string $semana_anio Cadena en formato 'YYYY-WW' (ej. '2025-39').
	 * @return array Un arreglo con 'inicio' y 'fin' en formato 'd/m/Y'.
	 */
	public function getRange($semana_anio) {
		// Aseguramos que la entrada sea 'AAAA-SS' (ej. '2025-39')
		if (!preg_match('/^\d{4}-\d{2}$/', $semana_anio)) {
			return [
				'inicio' => 'Formato Inválido',
				'fin' => 'Formato Inválido'
			];
		}

		// Separar Año y Semana
		list($anio, $semana) = explode('-', $semana_anio);

		try {
			// 1. Crear un objeto DateTime a partir del formato ISO: 'oW'
			// '%o' es el año ISO, '%W' es la semana ISO.
			// Esto define el Lunes de la semana 'WW' del año 'YYYY'.
			$date = DateTime::createFromFormat('oW', $anio . $semana);

			if ($date === false) {
				return [
					'inicio' => 'Error de Conversión',
					'fin' => 'Error de Conversión'
				];
			}

			// 2. Ajustar al Lunes (Día 1) de la semana ISO.
			// setISODate() lo hace de forma más confiable.
			$date->setISODate($anio, $semana, 1);
			$inicio = $date;

			// 3. Determinar el fin de la semana (Domingo)
			$fin = clone $inicio;
			$fin->modify('+6 days');

			return [
				'inicio' => $inicio->format('d/m/Y'),
				'fin' => $fin->format('d/m/Y')
			];

		} catch (Exception $e) {
			// Capturar cualquier excepción de la clase DateTime
			return [
				'inicio' => 'Error en PHP',
				'fin' => 'Error en PHP'
			];
		}
	}
}
