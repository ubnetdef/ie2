<?php
App::uses('AdminAppController', 'Admin.Controller');
use Respect\Validation\Rules;

class HintsController extends AdminAppController {
	public $uses = ['Hint', 'Inject'];

	public function beforeFilter() {
		parent::beforeFilter();

		// Setup the validators
		$this->validators = [
			'inject_id' => new Rules\AllOf(
				new Rules\Digit()
			),
			'parent_id' => new Rules\Optional(
				new Rules\Digit()
			),
			'title' => new Rules\AllOf(
				new Rules\NotEmpty()
			),
			'content' => new Rules\AllOf(
				new Rules\NotEmpty()
			),
			'time_wait' => new Rules\AllOf(
				new Rules\Digit()
			),
			'cost' => new Rules\AllOf(
				new Rules\Digit()
			),
		];
	}

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
		if ( $this->request->is('post') ) {
			// Validate the input
			$res = $this->_validate();

			if ( empty($res['errors']) ) {
				$this->Hint->create();
				$this->Hint->save($res['data']);

				$this->logMessage(
					'hints',
					sprintf('Created hint "%s"', $res['data']['title']),
					[],
					$id
				);

				$this->Flash->success('The hint has been created!');
				return $this->redirect(['plugin' => 'admin', 'controller' => 'hints', 'action' => 'index']);
			} else {
				$this->_errorFlash($res['errors']);
			}
		}

		$this->set('hints', $this->Hint->find('all'));
		$this->set('injects', $this->Inject->find('all'));
	}

	/**
	 * Edit Hint 
	 *
	 * @url /admin/hints/edit/<id>
	 */
	public function edit($id=false) {
		$hint = $this->Hint->findById($id);
		if ( empty($hint) ) {
			throw new NotFoundException('Unknown hint');
		}

		if ( $this->request->is('post') ) {
			// Validate the input
			$res = $this->_validate();

			if ( empty($res['errors']) ) {
				$this->Hint->id = $id;
				$this->Hint->save($res['data']);

				$this->logMessage(
					'hints',
					sprintf('Updated hint "%s"', $hint['Hint']['title']),
					[
						'old_hint' => $hint['Hint'],
						'new_hint' => $res['data'],
					],
					$id
				);

				$this->Flash->success('The hint has been updated!');
				return $this->redirect(['plugin' => 'admin', 'controller' => 'hints', 'action' => 'index']);
			} else {
				$this->_errorFlash($res['errors']);
			}
		}

		$this->set('hints', $this->Hint->find('all'));
		$this->set('injects', $this->Inject->find('all'));
		$this->set('hint', $hint);
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
	 * @url /admin/hints/view/<id>
	 */
	public function view($id=false) {
		$log = $this->Log->findById($id);
		if ( empty($log) ) {
			throw new NotFoundException('Unknown Log ID');
		}

		$this->set('log', $log);
	}
}
