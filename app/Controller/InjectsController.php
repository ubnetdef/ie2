<?php
App::uses('AppController', 'Controller');

class InjectsController extends AppController {
	public $uses = ['Attachment', 'Config', 'Hint', 'Schedule', 'Submission'];

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
			$this->groups[] = (int) env('GROUP_BLUE');
		}

		// Load + setup the InjectStyler helper
		$this->helpers[] = 'InjectStyler';
		$this->helpers['InjectStyler'] = [
			'types'  => $this->Config->getInjectTypes(),
			'inject' => new stdClass(), // Nothing...for now
		];
	}

	/**
	 * Inject Inbox Page 
	 *
	 * @url /injects
	 * @url /injects/index
	 */
	public function index() {
		if ( (bool)env('INJECT_INBOX_STREAM_VIEW') ) {
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
		$inject = $this->Schedule->getInject($sid, $this->groups);
		if ( empty($inject) ) {
			throw new NotFoundException('Unknown inject');
		}

		$submissions = $this->Submission->getSubmissions($inject->getInjectId(), $this->Auth->group('id'));

		// Setup the InjectStyler helper with the latest inject
		$this->helpers['InjectStyler']['inject'] = $inject;

		$this->set('hints', $this->Hint->find('count', ['conditions' => ['inject_id' => $inject->getInjectId()]]));
		$this->set('inject', $inject);
		$this->set('submissions', $submissions);
	}

	/**
	 * Inject Submission Endpoint
	 *
	 * @url /injects/submit
	 */
	public function submit() {
		if ( !$this->request->is('post') || !isset($this->request->data['id'])) {
			throw new MethodNotAllowedException('Unauthorized');
		}

		$inject = $this->Schedule->getInject($this->request->data['id'], $this->groups);
		if ( empty($inject) ) {
			throw new NotFoundException('Unknown inject');
		}
		if ( !$inject->isAcceptingSubmissions() ) {
			throw new UnauthorizedException('Inject is no longer accepting submissions');
		}

		// Create a new InjectType Manager
		$typeManager = new InjectTypes\Manager($this->Config->getInjectTypes());
		$injectType = $typeManager->get($inject->getType());

		if ( !$injectType->validateSubmission($inject, $this->request->data) ) {
			throw new BadRequestException('Non-valid submission');
		}

		// We're good! Save it!
		$this->Submission->create();
		$this->Submission->save([
			'inject_id' => $inject->getInjectId(),
			'user_id'   => $this->Auth->user('id'),
			'group_id'  => $this->Auth->group('id'),
			'created'   => time(),
			'data'      => $injectType->handleSubmission($inject, $this->request->data),
		]);

		$this->logMessage(
			'submission',
			sprintf('Created submission for Inject #%d', $inject->getSequence()),
			[],
			$this->Submission->id
		);
		$this->Flash->success('Successfully submitted!');
		return $this->redirect('/injects/view/'.$inject->getScheduleId());
	}

	/**
	 * Delete Inject Submission
	 *
	 * @url /injects/delete/<sid>
	 */
	public function delete($sid=false) {
		$submission = $this->Submission->findById($sid);
		if ( empty($submission) || !in_array($submission['Group']['id'], $this->groups) ) {
			throw new NotFoundException('Unknown submission');
		}

		$this->Submission->id = $submission['Submission']['id'];
		$this->Submission->save([
			'deleted' => true,
		]);

		$this->logMessage(
			'submission',
			sprintf('Deleted submission #%d on Inject #%d', $sid, $submission['Inject']['sequence']),
			[
				'user' => $this->Auth->user('username'),
			],
			$sid
		);
		$this->Flash->success('Successfully deleted the submission!');
		$this->redirect($this->referer());
	}

	/**
	 * View (download) Submission
	 *
	 * @url /injects/submission/<sid>
	 */
	public function submission($sid=false) {
		$submission = $this->Submission->getSubmission($sid, $this->Auth->group('id'), true);
		if ( empty($submission) ) {
			throw new NotFoundException('Unknown submission');
		}

		$data = json_decode($submission['Submission']['data'], true);
		$download = (isset($this->params['url']['download']) && $this->params['url']['download'] == true);

		// Let's verify our data is correct
		if ( md5(base64_decode($data['data'])) !== $data['hash'] ) {
			throw new InternalErrorException('Data storage failure');
		}

		// Create the new response for the data
		$response = new CakeResponse();
		$response->type($data['extension']);
		$response->body(base64_decode($data['data']));
		$response->disableCache();

		$type = ($download ? 'attachment' : 'inline');
		$filename = $data['filename'];
		$response->header('Content-Disposition', $type.'; filename="'.$filename.'"');

		return $response;
	}

	/**
	 * Inject Attachment View Page
	 *
	 * @url /injects/attachment/<schedule_id>/<attachment_id>
	 */
	public function attachment($sid=false, $aid=false) {
		$inject = $this->Schedule->getInject($sid, $this->groups);
		if ( empty($inject) ) {
			throw new NotFoundException('Unknown inject');
		}

		// Get the inject id, then select the Attachment
		$injectID = $inject->getInjectId();

		$attachment = $this->Attachment->findByIdAndInjectId($aid, $injectID);
		if ( empty($attachment) ) {
			throw new NotFoundException('Unknown attachment');
		}

		$data = json_decode($attachment['Attachment']['data'], true);
		$download = (isset($this->params['url']['download']) && $this->params['url']['download'] == true);

		// Let's verify our data is correct
		if ( md5(base64_decode($data['data'])) !== $data['hash'] ) {
			throw new InternalErrorException('Data storage failure');
		}

		// Create the new response for the data
		$response = new CakeResponse();
		$response->type($data['extension']);
		$response->body(base64_decode($data['data']));
		$response->disableCache();

		$type = ($download ? 'attachment' : 'inline');
		$filename = $attachment['Attachment']['name'];
		$response->header('Content-Disposition', $type.'; filename="'.$filename.'"');

		return $response;
	}

	/**
	 * API Endpoint for Hints
	 *
	 * Gets all the unlocked hints for an inject
	 *
	 * @url /injects/hints/<inject_id>
	 */
	public function hints($id=false) {
		$hint = $this->Hint->findById($id);
		if ( empty($hint) ) {
			throw new NotFoundException('Unknown hint');
		}

		$this->layout = 'ajax';
		$this->set('hint', $hint);
	}

	/**
	 * API Endpoint to Unlock a Hint
	 *
	 * Unlocks a hint
	 *
	 * @url /injects/unlock_hint/<hint_id>
	 */
	public function unlock_hint($id=false) {
		return $this->ajaxResponse(true);
	}
}
