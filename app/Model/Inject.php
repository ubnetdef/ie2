<?php
App::uses('AppModel', 'Model');

/**
 * Inject Model
 *
 */
class Inject extends AppModel {
	public $hasMany = ['Attachment'];
}
