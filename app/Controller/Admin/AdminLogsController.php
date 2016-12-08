<?php
App::uses('AdminAppController', 'Controller');

class AdminLogsController extends AdminAppController {
	public $uses = ['Log'];

	/**
	 * Log List Page 
	 *
	 * @url /adminlogs
	 * @url /admin/logs
	 * @url /adminlogs/index
	 * @url /admin/logs/index
	 */
	public function index() {
		// TODO
	}

	/**
	 * View Log 
	 *
	 * @url /adminlogs/view/<id>
	 * @url /admin/logs/view/<id>
	 */
	public function view($id=false) {
		// TODO
	}
}
