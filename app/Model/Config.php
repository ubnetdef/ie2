<?php
App::uses('AppModel', 'Model');

/**
 * Config Model
 *
 */
class Config extends AppModel {
	public $useTable = 'config';

	const CACHE_KEY = 'Config.%s';

	public function afterSave($created, $options=[]) {
		if ( !$created ) {
			Cache::delete(sprintf(self::CACHE_KEY, $this->data['key']));
		}
	}

	public function getKey($key) {
		$data = Cache::read(sprintf(self::CACHE_KEY, $key));

		if ( $data === false ) {
			$data = $this->find('first', [
				'conditions' => [
					'key' => $key,
				],
			]);

			Cache::write(sprintf(self::CACHE_KEY, $key), $data);
		}

		return empty($data) ? '' : $data['Config']['value'];
	}
}
