<?php
App::uses('AdminAppController', 'Admin.Controller');

class LogsController extends AdminAppController {
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
	 * @url /admin/logs
	 * @url /admin/logs/index
	 */
	public function index() {
		$this->Paginator->settings = $this->paginate;
		$this->set('recent_logs', $this->Paginator->paginate('Log'));
	}

	/**
	 * View Log 
	 *
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
