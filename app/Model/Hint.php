<?php
App::uses('AppModel', 'Model');

/**
 * Hint Model
 *
 */
class Hint extends AppModel {
	public $belongsTo = ['Inject'];
	public $recursive = 1;
}
