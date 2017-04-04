<?php
App::uses('ClassRegistry', 'Utility');

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
		'team_number' => [
			'type'    => 'integer',
			'null'    => true,
			'default' => null,
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
		'sequence' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 0,
		],
		'title' => [
			'type' => 'string',
			'null' => false,
		],
		'content' => [
			'type' => 'text',
			'null' => false,
		],
		'from_name' => [
			'type' => 'string',
			'null' => false,
		],
		'from_email' => [
			'type' => 'string',
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
			'type'    => 'string',
			'null'    => false,
			'default' => 'noop',
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
		'order' => [
			'type'    => 'integer',
			'null'    => false,
			'default' => 0,
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

	public $announcements = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'content' => [
			'type' => 'text',
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
			'PRIMARY' => ['column' => 'id', 'unqiue' => true],
		],
	];

	public $logs = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key'  => 'primary',
		],
		'time' => [
			'type' => 'integer',
			'null' => false,
		],
		'type' => [
			'type' => 'string',
			'null' => false,
		],
		'user_id' => [
			'type' => 'integer',
		],
		'related_id' => [
			'type' => 'integer',
		],
		'data' => [
			'type' => 'text',
		],
		'ip' => [
			'type'   => 'string',
			'length' => 15,
		],
		'message' => [
			'type' => 'string',
			'null' => false,
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
					'value' => '<p>It works!</p>',
				]);

				$this->_create('Config', [
					'key' => 'competition.start',
					'value' => time(),
				]);

				$this->_create('Config', [
					'key' => 'engine.install_date',
					'value' => time(),
				]);

				// Default inject types built in.
				// THIS MUST MAP TO A FILE INSIDE:
				// app/Lib/InjectTypes/<name>.php
				$this->_create('Config', [
					'key' => 'engine.inject_types',
					'value' => json_encode(
						[
							'FileSubmission', 'TextSubmission', 'ManualCheck',
							'FlagSubmission', 'NoOpSubmission',
						]
					),
				]);
			break;

			case 'injects':
				$this->_create('Inject', [
					'title'         => 'Learn about the InjectEngine',
					'content'       => '<p>Maybe check the wiki?</p>',
					'from_name'     => 'James Droste',
					'from_email'    => 'ubnetdef@buffalo.edu',
					'grading_guide' => '<p>You\'ll know when you got this.</p>',
					'max_points'    => 100,
					'type'          => 'noop',
				]);
			break;

			case 'schedules':
				$this->_create('Schedule', [
					'inject_id'     => 1,
					'group_id'      => env('GROUP_STAFF'),
					'active'        => true,
				]);
			break;

			case 'announcements':
				$this->_create('Announcement', array(
					'content'    => 'ie<sup>2</sup> was just installed. Go configure it!',
					'active'     => true,
					'expiration' => 0,
				));
			break;

			case 'submissions':
				// We have to change BLOB -> LONGBLOB
				ClassRegistry::init('submissions')->query('ALTER TABLE submissions MODIFY data LONGBLOB');
			break;

			case 'logs':
				$this->_create('Log', [
					'time'    => time(),
					'type'    => 'general',
					'user_id' => 1,
					'data'    => json_encode([]),
					'ip'      => '127.0.0.1',
					'message' => 'InjectEngine was just installed.',
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