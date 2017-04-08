<?php
App::uses('AdminAppController', 'Admin.Controller');

class HintsController extends AdminAppController {
	public $uses = ['Log'];

	/**
	 * Hint List Page 
	 *
	 * @url /admin/hints
	 * @url /admin/hints/index
	 */
	public function index() {
		
	}

	/**
	 * View Hint 
	 *
	 * @url /admin/logs/view/<id>
	 */
	public function view($id=false) {
		$log = $this->Log->findById($id);
		if ( empty($log) ) {
			throw new NotFoundException('Unknown Log ID');
		}

		$this->set('log', $log);
	}
}
