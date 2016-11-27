<?php
App::uses('AppController', 'Controller');

class InjectsController extends AppController {
	public $uses = ['Config', 'Schedule'];
	public $helpers = ['Inject'];

	private $groups = [];

	public function beforeFilter() {
		parent::beforeFilter();

		// Enforce logins
		$this->Auth->protect();

		// Mark the tab as active
		$this->set('at_injects', true);

		// Administrator blue team override
		$this->groups = $this->Auth->item('groups');
		if ( $this->Auth->isStaff() ) {
			$this->groups[] = env('GROUP_BLUE');
		}
	}

	public function beforeRender() {
		parent::beforeRender();

		$this->helpers['Inject'] = [
			'types' => json_decode($this->Config->getKey('engine.inject_types')),
		];
	}

	/**
	 * Inject Panel Page 
	 *
	 * @url /injects
	 * @url /injects/index
	 */
	public function index() {
		$this->set('injects', $this->Schedule->getInjects($this->groups));
	}

	public function index2() {
		$this->set('injects', $this->Schedule->getInjects($this->groups));
	}

	/**
	 * Inject View Page
	 *
	 * @url /injects/view/<schedule_id>
	 */
	public function view($sid=false) {
		if ( $sid === false || !is_numeric($sid) ) {
			throw new BadRequestException('Stop trying to be a smartass.');
		}

		$inject = $this->Schedule->getInject($sid, $this->groups);
		if ( empty($inject) ) {
			throw new BadRequestException('Unknown inject.');
		}

		$this->set('inject', $inject);
	}
}
