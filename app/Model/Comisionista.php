<?php
App::uses('AppModel', 'Model');
/**
 * Cuenta Model
 *
 */
class Comisionista extends AppModel {

	public $displayField = 'usuario'; // Puedes cambiarlo al campo que desees que se muestre como principal

	public $hasMany = array(
		'Jugadors'=>array(
			'class'=>'Jugador',
			'foreignKey'=>'comisionista_id',
		),
		'Cuentas'=>array(
			'class'=>'Cuenta',
			'foreignKey'=>'comisionista_id',
		),
		'Movimientos'=>array(
			'class'=>'Movimiento',
			'foreignKey'=>'comisionista_id',
		),
		'Comisiones'=>array(
			'className'=>'Ganancia',
			'foreignKey'=>'comisionista_id',
		)
	);

}
