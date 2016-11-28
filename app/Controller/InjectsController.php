<?php
App::uses('AppController', 'Controller');

class InjectsController extends AppController {
	public $uses = ['Config', 'Schedule', 'Submission'];

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

	/**
	 * Inject Inbox Page 
	 *
	 * @url /injects
	 * @url /injects/index
	 */
	public function index() {
		if ( (bool)env('INJECT_INBOX_STREAM_VIEW') ) {
			// Load + setup the InjectStyler helper
			$this->helpers[] = 'InjectStyler';
			$this->helpers['InjectStyler'] = [
				'types'  => $this->Config->getInjectTypes(),
				'inject' => new stdClass(), // Nothing...for now
			];

			$this->set('injects', $this->Schedule->getInjects($this->groups));
			return $this->render('index_stream');
		} else {
			return $this->render('index_list');
		}
	}

	/**
	 * API Endpoint for Injects
	 *
	 * The Inject Inbox Page will call
	 * this endpoint every xx seconds.
	 *
	 * @url /injects/api
	 */
	public function api() {
		return $this->ajaxResponse([
			'injects' => $this->Schedule->getInjects($this->groups),
		]);
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

		$submissions = $this->Submission->getSubmissions($inject->getInjectID(), $this->Auth->group('id'));

		// Load + setup the InjectStyler helper
		$this->helpers[] = 'InjectStyler';
		$this->helpers['InjectStyler'] = [
			'types'  => $this->Config->getInjectTypes(),
			'inject' => $inject,
		];

		$this->set('inject', $inject);
		$this->set('submissions', $submissions);
	}

	/**
	 * Inject Submission Endpoint
	 *
	 * @url /injects/submit
	 */
	public function submit() {
		if ( !$this->request->is('post') ) {
			throw new BadMethodCallException('Unauthorized');
		}

		if ( !isset($this->request->data['id']) || !isset($this->request->data['content']) ) {
			throw new BadMethodCallException('Missing id/content');
		}

		$inject = $this->Schedule->getInject($this->request->data['id'], $this->groups);
		if ( empty($inject) ) {
			throw new BadRequestException('Unknown inject.');
		}

		if ( $inject->isAcceptingSubmissions() ) {
			throw new BadRequestException('Inject is no longer accepting submissions!');
		}

		// Create a new InjectType Manager
		$typeManager = new InjectTypes\Manager($this->Config->getInjectTypes());
		$injectType = $typeManager->get($inject->getType());

		if ( !$injectType->validateSubmission($inject, $this->request->data) ) {
			throw new BadRequestException('Non-valid submission.');
		}

		// We're good! Save it!
		$this->Submission->create();
		$this->Submission->save([
			'inject_id' => $inject->getInjectID(),
			'user_id'   => $this->Auth->user('id'),
			'group_id'  => $this->Auth->group('id'),
			'created'   => time(),
			'data'      => $injectType->handleSubmission($inject, $this->request->data),
		]);

		$this->Flash->success('Successfully submitted!');
		return $this->redirect('/injects/view/'.$inject->getScheduleID());
	}
}
