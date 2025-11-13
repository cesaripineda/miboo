<?php
App::uses('AppModel', 'Model');
/**
 * Cuenta Model
 *
 */
class Ganancia extends AppModel {

	public $displayField = 'id'; // Puedes cambiarlo al campo que desees que se muestre como principal

	public $belongsTo = array(
		'Jugador'=>array(
			'className' => 'Jugador',
			'foreignKey' => 'jugador_id'
		),
		'Comisionista'=>array(
			'className' => 'Comisionista',
			'foreignKey' => 'comisionista_id'
		)
	);

}
