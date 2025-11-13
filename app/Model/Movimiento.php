<?php
App::uses('AppModel', 'Model');
/**
 * Cuenta Model
 *
 */
class Movimiento extends AppModel {

	public $displayField = 'id'; // Puedes cambiarlo al campo que desees que se muestre como principal

	public $belongsTo = array(
		'Comisionista'=>array(
			'className' => 'Comisionista',
			'foreignKey' => 'comisionista_id'
		),
		'Jugador'=>array(
			'className' => 'Jugador',
			'foreignKey' => 'jugador_id'
		),
		'Cuenta'=>array(
			'className' => 'Cuenta',
			'foreignKey' => 'cuenta_id'
		),
	);

}
