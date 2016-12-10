<?php
App::uses('AdminAppController', 'Controller');

class AdminLogsController extends AdminAppController {
	public $uses = ['Log'];

	public $paginate = [
		'fields' => [
			'Log.id', 'Log.time', 'Log.type', 'Log.data',
			'Log.ip', 'Log.message', 'User.username', 'User.group_id',
		],
		'contain' => [
			'User' => [
				'Group.name',
			]
		],
		'order' => [
			'Log.id' => 'DESC'
		],
	];

	/**
	 * Log List Page 
	 *
	 * @url /adminlogs
	 * @url /admin/logs
	 * @url /adminlogs/index
	 * @url /admin/logs/index
	 */
	public function index() {
		$this->Paginator->settings = $this->paginate;
		$this->set('recent_logs', $this->Paginator->paginate('Log'));
	}

	/**
	 * View Log 
	 *
	 * @url /adminlogs/view/<id>
	 * @url /admin/logs/view/<id>
	 */
	public function view($id=false) {
		$log = $this->Log->findById($id);
		if ( empty($log) ) {
			throw new NotFoundException('Unknown log!');
		}

		$this->set('log', $log);
	}
}
