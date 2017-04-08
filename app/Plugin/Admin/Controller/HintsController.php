<?php
App::uses('AdminAppController', 'Admin.Controller');

class HintsController extends AdminAppController {
	public $uses = ['Hint'];

	/**
	 * Hint List Page 
	 *
	 * @url /admin/hints
	 * @url /admin/hints/index
	 */
	public function index() {
		$this->set('hints', $this->Hint->find('all'));
	}

	/**
	 * Create Hint 
	 *
	 * @url /admin/hints/create
	 */
	public function create() {
		// TODO
	}

	/**
	 * Edit Hint 
	 *
	 * @url /admin/hints/edit/<id>
	 */
	public function edit($id=false) {
		// TODO
	}

	/**
	 * Delete Hint 
	 *
	 * @url /admin/hints/delete/<id>
	 */
	public function delete($id=false) {
		$hint = $this->Hint->findById($id);
		if ( empty($hint) ) {
			throw new NotFoundException('Unknown hint');
		}

		if ( $this->request->is('post') ) {
			$this->Hint->delete($id);

			$msg = sprintf('Deleted hint "%s"', $hint['Hint']['title']);
			$this->logMessage('hints', $msg, ['hint' => $hint], $id);
			$this->Flash->success($msg.'!');
			return $this->redirect(['plugin' => 'admin', 'controller' => 'hints', 'action' => 'index']);
		}

		$this->set('hint', $hint);
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
