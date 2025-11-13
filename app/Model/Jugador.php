<?php
App::uses('AppModel', 'Model');
/**
 * Cuenta Model
 *
 */
class Jugador extends AppModel {

	public $displayField = 'usuario'; // Puedes cambiarlo al campo que desees que se muestre como principal

	public $belongsTo = array(
		'Comisionista'=>array(
			'className' => 'Comisionista',
			'foreignKey' => 'comisionista_id'
		)
	);

	public $hasMany = array(
		'CuentasBancarias'=>array(
			'className' => 'Cuenta',
			'foreignKey' => 'jugador_id'
		),
		'Movimientos'=>array(
			'className' => 'Movimiento',
			'foreignKey' => 'jugador_id'
		),
		'Ganancias'=>array(
			'className' => 'Ganancia',
			'foreignKey' => 'jugador_id'
		)
	);

}
