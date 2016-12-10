<?php
App::uses('AdminAppController', 'Controller');
use Respect\Validation\Rules;

class AdminSiteController extends AdminAppController {
	public $uses = ['Config'];

	public function beforeFilter() {
		parent::beforeFilter();

		// Set validators
		$this->validators = [
			'id' => new Rules\AllOf(
				new Rules\Digit()
			),
			'key' => new Rules\AllOf(
				new Rules\NotEmpty()
			),
			'value' => new Rules\AllOf(
				new Rules\NotEmpty()
			),
		];
	}

	/**
	 * Config Index Page 
	 *
	 * @url /adminsite
	 * @url /admin/site
	 * @url /adminsite/index
	 * @url /admin/site/index
	 */
	public function index() {
		$this->set('config', $this->Config->find('all'));
	}

	/**
	 * Config API Page 
	 *
	 * @url /adminsite/api/<id>
	 * @url /admin/site/api/<id>
	 */
	public function api($id=false) {
		$config = $this->Config->findById($id);
		if ( empty($config) ) {
			throw new NotFoundException('Unknown config');
		}

		return $this->ajaxResponse($config['Config']);
	}

	/**
	 * Config Edit/Create URL 
	 *
	 * @url /adminsite/api/<id>
	 * @url /admin/site/api/<id>
	 */
	public function config() {
		if ( !$this->request->is('post') ) {
			throw new MethodNotAllowedException();
		}

		// Validate the input
		$res = $this->_validate();

		if ( !empty($res['errors']) ) {
			$this->_errorFlash($res['errors']);

			return $this->redirect('/admin/site');
		}

		if ( $res['data']['id'] > 0 ) {
			$config = $this->Config->findById($res['data']['id']);
			if ( empty($config) ) {
				throw new NotFoundException('Unknown config');
			}

			$this->Config->id = $res['data']['id'];
			$this->Config->save($res['data']);

			$msg = sprintf('Edited config value "%s"', $config['Config']['key']);

			$this->logMessage('config', $msg, ['old_config' => $config['Config'], 'new_config' => $res['data']], $config['Config']['id']);
			$this->Flash->success($msg.'!');
		} else {
			// Fix the data
			unset($res['data']['id']);

			$this->Config->create();
			$this->Config->save($res['data']);

			$msg = sprintf('Created config value "%s"', $res['data']['key']);
			$this->logMessage('config', $msg, [], $this->Config->id);
			$this->Flash->success($msg.'!');
		}

		return $this->redirect('/admin/site');
	}

	/**
	 * Config Delete 
	 *
	 * @url /adminsite/delete/<id>
	 * @url /admin/site/delete/<id>
	 */
	public function delete($id=false) {
		$config = $this->Config->findById($id);
		if ( empty($config) ) {
			throw new NotFoundException('Unknown config');
		}

		if ( $this->request->is('post') ) {
			$this->Config->delete($id);

			$msg = sprintf('Deleted config value "%s"', $config['Config']['key']);
			$this->logMessage('config', $msg. ['config' => $config], $id);
			$this->Flash->success($msg.'!');
			return $this->redirect('/admin/site');
		}

		$this->set('config', $config);
	}
}
