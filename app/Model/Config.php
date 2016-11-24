<?php
App::uses('AppModel', 'Model');

/**
 * Config Model
 *
 */
class Config extends AppModel {
	public $useTable = 'config';

	public function getKey($key) {
		$data = $this->find('first', [
			'conditions' => [
				'key' => $key,
			],
		]);

		return empty($data) ? '' : $data['Config']['value'];
	}
}
