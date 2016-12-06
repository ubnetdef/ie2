<?php
App::uses('ClassRegistry', 'Utility');

class BankWebSchema extends CakeSchema {
	public $account_mappings = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'object' => [
			'type' => 'string',
			'null' => false,
		],
		'object_id' => [
			'type' => 'integer',
			'null' => false,
		],
		'username' => [
			'type' => 'text',
		],
		'password' => [
			'type' => 'text',
		],

		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unqiue' => true],
		],
	];

	// ================================

	public function before($event = array()) {
		ConnectionManager::getDataSource('default')->cacheSources = false;

		return true;
	}

	public function after($event = array()) {
		if ( !isset($event['create']) ) return;

		switch ( $event['create'] ) {
			case 'account_mappings':
				$this->_create('AccountMapping', [
					'object'    => 'Group',
					'object_id' => env('GROUP_STAFF'),
					'username'  => 'admin',
					'password'  => 'admin',
				]);
			break;
		}
	}

	private function _create($tbl, $data) {
		$table = ClassRegistry::init($tbl);

		$table->create();
		$table->save(array($tbl => $data));
	}
}