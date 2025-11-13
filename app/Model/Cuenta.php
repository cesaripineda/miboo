<?php
App::uses('AppModel', 'Model');
/**
 * Cuenta Model
 *
 */
class Cuenta extends AppModel {

	public $displayField = 'nombre'; // Puedes cambiarlo al campo que desees que se muestre como principal

	public $belongsTo = array(
		'Jugador' => array(
			'className' => 'Jugador',
			'foreignKey' => 'jugador_id'
		)
	);

	public $hasMany = array(
		'Movimientos' => array(
			'className' => 'Movimiento',
			'foreignKey' => 'cuenta_id',
			'order' => 'Movimientos.fecha_aplicacion ASC'
		),
	);

}
