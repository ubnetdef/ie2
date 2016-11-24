<?php
App::uses('ClassRegistry', 'Utility');
App::uses('Security', 'Utility');

class AppSchema extends CakeSchema {

	public $config = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'key' => [
			'type' => 'string',
			'null' => false,
		],
		'value' => [
			'type' => 'text',
		],

		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unqiue' => true],
		],
	];

	public $users = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'username' => [
			'type' => 'string',
			'null' => false,
		],
		'password' => [
			'type' => 'string',
			'null' => false,
		],
		'group_id' => [
			'type' => 'integer',
			'null' => false,
		],
		'active' => [
			'type'    => 'boolean',
			'null'    => false,
			'default' => false,
		],
		'expiration' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 0,
		],

		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => true],
		],
	];

	public $groups = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'name' => [
			'type' => 'string',
			'null' => false,
		],
		'parent_id' => [
			'type'    => 'integer',
			'null'    => true,
			'default' => null,
		],
		'lft' => [
			'type'    => 'integer',
			'null'    => true,
			'default' => null,
		],
		'rght' => [
			'type'    => 'integer',
			'null'    => true,
			'default' => null,
		],

		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unqiue' => true],
		],
	];

	public $injects = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'title' => [
			'type' => 'string',
			'null' => false,
		],
		'content' => [
			'type' => 'text',
			'null' => false,
		],
		'grading_guide' => [
			'type'    => 'text',
			'null'    => false,
			'default' => '',
		],
		'max_points' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 0,
		],
		'max_submissions' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 1,
		],
		'type' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 1,
		],

		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unqiue' => true],
		],
	];

	public $schedules = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'inject_id' => [
			'type' => 'integer',
			'null' => false,
		],
		'group_id' => [
			'type' => 'integer',
			'null' => false,
		],
		'dependency_id' => [
			'type'    => 'integer',
			'null'    => true,
			'default' => null,
		],
		'start' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 0,
		],
		'end' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 0,
		],
		'fuzzy' => [
			'type'    => 'boolean',
			'null'    => false,
			'default' => false,
		],
		'active' => [
			'type'    => 'boolean',
			'null'    => false,
			'default' => false,
		],

		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unqiue' => true],
		],
	];

	public $submissions = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'inject_id' => [
			'type' => 'integer',
			'null' => false,
		],
		'user_id' => [
			'type' => 'integer',
			'null' => false,
		],
		'group_id' => [
			'type' => 'integer',
			'null' => false,
		],
		'created' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 0,
		],
		'data' => [
			'type'    => 'binary',
			'null'    => false,
		],
		'deleted' => [
			'type'    => 'boolean',
			'null'    => false,
			'default' => false,
		],

		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unqiue' => true],
		],
	];

	public $grades = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'submission_id' => [
			'type' => 'integer',
			'null' => false,
		],
		'grader_id' => [
			'type' => 'integer',
			'null' => false,
		],
		'created' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 0,
		],
		'grade' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 0,
		],
		'comments' => [
			'type'    => 'text',
			'null'    => false,
		],

		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unqiue' => true],
		],
	];

	public $hints = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'inject_id' => [
			'type' => 'integer',
			'null' => false,
		],
		'dependency_id' => [
			'type'    => 'integer',
			'null'    => true,
			'default' => null,
		],
		'time_dependency' => [
			'type'    => 'integer',
			'null'    => true,
			'default' => null,
		],
		'title' => [
			'type'    => 'string',
			'null'    => false,
		],
		'content' => [
			'type'    => 'text',
			'null'    => false,
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
			case 'config':
				$this->_create('Config', [
					'key' => 'homepage.title',
					'value' => 'Hello, World',
				]);
				$this->_create('Config', [
					'key' => 'homepage.body',
					'value' => 'It works!',
				]);

				$this->_create('Config', [
					'key' => 'competition.start',
					'value' => '0',
				]);

				$this->_create('Config', [
					'key' => 'engine.install_date',
					'value' => time(),
				]);
			break;

			case 'injects':
				$this->_create('Inject', [
					'title'         => 'Learn about the InjectEngine',
					'content'       => '<p>Maybe check the wiki?</p>',
					'grading_guide' => '<p>You\'ll know when you got this.</p>',
					'max_points'    => 100,
					'type'          => 1,
				]);
			break;

			case 'schedules':
				$this->_create('Schedule', [
					'inject_id'     => 1,
					'group_id'      => env('GROUP_STAFF'),
					'active'        => true,
				]);
			break;

			case 'logs_TODO':
				$this->_create('Log', array(
					'time'       => time(),
					'type'       => 1,
					'user_id'    => 1,
					'related_id' => 0,
					'extra_data' => json_encode(array()),
					'ip'         => '127.0.0.1',
					'message'    => 'InjectEngine was just installed.',
				));
			break;
		}
	}

	private function _create($tbl, $data) {
		$table = ClassRegistry::init($tbl);

		$table->create();
		$table->save(array($tbl => $data));
	}
}