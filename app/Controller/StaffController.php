<?php
App::uses('AppController', 'Controller');
App::uses('InjectAbstraction', 'Lib');

class StaffController extends AppController {
	public $helpers = ['ScoreEngine.EngineOutputter'];
	public $uses = [
		'Config', 'Inject', 'UsedHint', 'Hint', 'Log', 'Grade', 'Group', 'Schedule', 'Submission',
		'ScoreEngine.Check', 'ScoreEngine.Service', 'ScoreEngine.Team', 'ScoreEngine.Round',
	];

	/**
	 * Pagination Settings
	 */
	public $paginate = [
		'OnlyGraded' => [
			'fields' => [
				'Submission.id', 'Submission.created', 'Submission.deleted',
				'Inject.id', 'Inject.title', 'Inject.sequence', 'Inject.type',
				'User.username', 'Group.name', 'Group.team_number',
				'Grade.created', 'Grade.grade', 'Grade.comments',
				'Grader.username',
			],
			'joins' => [
				[
					'table'      => 'users',
					'alias'      => 'Grader',
					'type'       => 'LEFT',
					'conditions' => [
						'Grader.id = Grade.grader_id',
					],
				]
			],
			'conditions' => [
				'OR' => [
					'Grade.created IS NOT NULL',
					'Submission.deleted' => true,
				],
			],
			'order' => [
				'Grade.created' => 'DESC',
				'Submission.created' => 'DESC',
			],
		],
	];

	public function beforeFilter() {
		parent::beforeFilter();

		// Enforce staff only
		$this->Auth->protect(env('GROUP_STAFF'));

		// Load + setup the InjectStyler helper
		$this->helpers[] = 'InjectStyler';
		$this->helpers['InjectStyler'] = [
			'types'  => $this->Config->getInjectTypes(),
			'inject' => new stdClass(), // Nothing...for now
		];

		// We're at the staff page
		$this->set('at_staff', true);
	}

	public function beforeRender() {
		parent::beforeRender();

		// Setup the ScoreEngine EngineOutputter
		$this->helpers['ScoreEngine.EngineOutputter']['data'] = $this->Check->getChecksTable(
			$this->Team->findAllByEnabled(true),
			$this->Service->findAllByEnabled(true)
		);
	}

	/**
	 * Competition Overview Page
	 *
	 * @url /staff
	 * @url /staff/index
	 */
	public function index() {
		// Static page
	}

	/**
	 * Competition Overview API
	 *
	 * @url /staff/api
	 */
	public function api() {
		$this->layout = 'ajax';

		$this->set('round', $this->Round->getLastRound());
		$this->set('active_injects', $this->Schedule->getInjects(env('GROUP_BLUE')));
		$this->set('recent_expired', $this->Schedule->getRecentExpired(env('GROUP_BLUE')));
		$this->set('recent_logs', $this->Log->find('all', [
			'fields' => [
				'Log.id', 'Log.time', 'Log.type', 'Log.data',
				'Log.ip', 'Log.message', 'User.username', 'User.group_id',
			],
			'contain' => [
				'User' => [
					'Group.name',
				]
			],
			'limit' => 20,
			'order' => [
				'Log.id' => 'DESC'
			],
		]));
	}

	/**
	 * Inject View Page
	 *
	 * @url /staff/inject/<id>
	 */
	public function inject($id=false) {
		$inject = $this->Inject->findById($id);
		if ( empty($inject) ) {
			throw new NotFoundException('Unknown Inject');
		}

		$inject = new InjectAbstraction($inject, 0);

		// Setup the InjectStyler helper with the latest inject
		$this->helpers['InjectStyler']['inject'] = $inject;

		$this->set('hints', $this->Hint->find('count', ['conditions' => ['inject_id' => $inject->getInjectId()]]));
		$this->set('inject', $inject);
	}

	/**
	 * Grader Island Page
	 *
	 * @url /staff/graders
	 */
	public function graders() {
		$this->set('ungraded', $this->Submission->getAllUngradedSubmissions());

		$this->Paginator->settings += $this->paginate['OnlyGraded'];
		$this->set('graded', $this->Paginator->paginate('Submission'));
	}

	/**
	 * Submission Grade Page
	 *
	 * @url /staff/grade/<sid>
	 */
	public function grade($sid=false) {
		$submission = $this->Submission->getSubmission($sid);
		if ( empty($submission) ) {
			throw new NotFoundException('Unknown submission');
		}

		if ( $this->request->is('post') ) {
			if (
				!isset($this->request->data['grade']) ||
				!isset($this->request->data['comments']) ||
				(empty($this->request->data['grade']) && $this->request->data['grade'] != 0) ||
				empty($this->request->data['comments']) ||
				$this->request->data['grade'] > $submission['Inject']['max_points']
			) {
				$this->Flash->danger('Incomplete data. Please try again.');
				return $this->redirect('/staff/grade/'.$sid);
			}

			$data = [
				'grade'    => $this->request->data['grade'],
				'comments' => $this->request->data['comments'],
			];
			$grade = $this->Grade->findBySubmissionId($sid);

			if ( empty($grade) ) {
				$this->Grade->create();

				$data['submission_id'] = $sid;
				$data['grader_id']     = $this->Auth->user('id');
				$data['created']       = time();

				$logMessage = sprintf('Graded submission #%d for %s', $sid, $submission['Group']['name']);
			} else {
				$this->Grade->id = $grade['Grade']['id'];

				$logMessage = sprintf('Edited submission #%d for %s', $sid, $submission['Group']['name']);
			}

			// Save + log
			$this->Grade->save($data);
			$this->logMessage(
				'grading',
				$logMessage,
				[
					'previous_grade'    => (empty($grade) ? null : $grade['Grade']['grade']),
					'previous_comments' => (empty($grade) ? null : $grade['Grade']['comments']),
					'new_grade'         => $data['grade'],
					'new_comments'      => $data['comments'],
				],
				$this->Grade->id
			);

			// Return home, ponyboy
			$this->Flash->success('Saved!');
			return $this->redirect('/staff/graders');
		}

		$this->set('submission', $submission);
	}

	/**
	 * View (download) Submission
	 *
	 * @url /staff/submission/<sid>
	 */
	public function submission($sid=false) {
		$submission = $this->Submission->getSubmission($sid);
		if ( empty($submission) ) {
			throw new NotFoundException('Unknown submission');
		}

		$data = json_decode($submission['Submission']['data'], true);
		$download = (isset($this->params['url']['download']) && $this->params['url']['download'] == true);

		// Let's verify our data is correct
		if ( md5(base64_decode($data['data'])) !== $data['hash'] ) {
			throw new InternalErrorException('Data storage failure.');
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
	 * Export Grades
	 *
	 * @url /staff/export
	 */
	public function export() {
		$blueTeams = array_merge($this->Group->getChildren(env('GROUP_BLUE')), [env('GROUP_BLUE')]);
		$submissions = $this->Submission->getAllSubmissions($blueTeams, true);
		$injects = $this->Schedule->getInjects($blueTeams, false);
		$used_hints = $this->UsedHint->find('all');
		$out = [];

		// Lookup for hint deductions
		$hintDeduction = function($team, $inject) use($used_hints) {
			$deduction = 0;

			foreach ( $used_hints AS $h ) {
				if ( $h['UsedHint']['group_id'] != $team ) continue;
				if ( $h['Hint']['inject_id'] != $inject ) continue;

				$deduction += $h['Hint']['cost'];
			}

			return $deduction;
		};

		// Grab all the injects
		$seenInjects = [];
		foreach ( $injects AS $i ) {
			if ( in_array($i->getSequence(), $seenInjects) ) continue;

			$seenInjects[] = $i->getSequence();
		}
		sort($seenInjects);

		// Build the header
		$header = ['team_number'];
		foreach ( $seenInjects AS $i ) {
			$header[] = sprintf('inject_%d_grade', $i);
		}
		$out[] = implode(',', $header);

		// Parse the (fun) data
		$scores = [];
		foreach ( $submissions AS $s ) {
			$tn = $s['Group']['id'];
			$inject = $s['Inject']['sequence'];

			if ( !isset($scores[$tn]) ) {
				$scores[$tn] = [];
			}

			$scores[$tn][$inject] = $s['Grade']['grade'] - $hintDeduction($tn, $s['Inject']['id']);
		}

		// Output the grades
		foreach ( $scores AS $team => $data ) {
			$line = [$team];

			foreach ( $seenInjects AS $i ) {
				// Default the grade to 0 if it's not submitted
				$line[] = isset($data[$i]) ? $data[$i] : 0;
			}

			$out[] = implode(',', $line);
		}

		return $this->ajaxResponse(implode(PHP_EOL, $out));
	}
}
