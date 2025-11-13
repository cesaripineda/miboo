<?php
App::uses('AppModel', 'Model');
/**
 * Cuenta Model
 *
 */
class Interjugador extends AppModel {

	public $displayField = 'id'; // Puedes cambiarlo al campo que desees que se muestre como principal

	public $belongsTo = array(
		'Remitente'=>array(
			'className' => 'Jugador',
			'foreignKey' => 'remitente_id'
		),
		'Receptor'=>array(
			'className' => 'Jugador',
			'foreignKey' => 'receptor_id'
		)
	);

}
