<?php
App::uses('AppController', 'Controller');

class ScheduleController extends AppController {
	public $uses = ['Config', 'Schedule'];

	/**
	 * Before Filter Hook
	 *
	 * Set's the active tab to be staff
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		// Require staff
		$this->Auth->protect(env('GROUP_STAFF'));

		// Tell the template we're in the staff dropdown
		$this->set('at_staff', true);
	}

	/**
	 * Overview Page 
	 *
	 * @url /schedule
	 * @url /schedule/index
	 */
	public function index() {		
		$this->set('injects', $this->Schedule->getAllSchedules());
	}

	/**
	 * Manager Page
	 *
	 * @url /schedule/manager
	 */
	public function manager() {
		$this->set('injects', $this->Schedule->getAllSchedules(false));
	}

	/**
	 * Flip the status of a schedule
	 *
	 * @url /schedule/flip/<sid>
	 */
	public function flip($sid=false) {
		$schedule = $this->Schedule->findById($sid);

		if ( !empty($schedule) ) {
			$this->Schedule->id = $sid;
			$this->Schedule->save([
				'active' => !($schedule['Schedule']['active']),
			]);

			$msg = sprintf('%sctivated inject "%s"', ($schedule['Schedule']['active'] ? 'Dea' : 'A'), $schedule['Inject']['title']);

			$this->logMessage(
				'schedule',
				$msg,
				[
					'old_status' => $schedule['Schedule']['active'],
					'new_status' => !$schedule['Schedule']['active'],
				],
				$sid
			);

			$this->Flash->success($msg.'!');
		}

		return $this->redirect('/schedule/manager');
	}

	/**
	 * Delete a schedule. SPOOKY
	 *
	 * @url /schedule/delete/<sid>
	 */
	public function delete($sid) {
		$schedule = $this->Schedule->findById($sid);
		if ( empty($schedule) ) {
			throw new NotFoundException('Unknown Schedule ID.');
		}

		if ( $this->request->is('post') ) {
			$this->Schedule->delete($sid);

			$msg = sprintf('Deleted schedule #%d', $sid);

			$this->logMessage(
				'schedule',
				$msg,
				[
					'schedule' => $schedule['Schedule'],
				],
				$sid
			);

			$this->Flash->success($msg);
			return $this->redirect('/schedule/manager');
		}

		$this->set('schedule', $schedule);
	}

	/**
	 * Edit a schedule.
	 *
	 * @url /schedule/edit/<sid>
	 */
	public function edit($sid) {
		$schedule = $this->Schedule->findById($sid);
		if ( empty($schedule) ) {
			throw new NotFoundException('Unknown Schedule ID.');
		}

		if ( $this->request->is('post') ) {
			$this->Schedule->id = $sid;

			$msg = sprintf('Edited schedule #%d', $sid);

			$this->logMessage(
				'schedule',
				$msg,
				[
					'old_schedule' => $schedule['Schedule'],
					'new_schedule' => $this->data,
				],
				$sid
			);

			$this->Flash->success($msg.'!');
			return $this->redirect('/schedule/manager');
		}

		// Load + setup the InjectStyler helper
		$this->helpers[] = 'InjectStyler';
		$this->helpers['InjectStyler'] = [
			'types'  => $this->Config->getInjectTypes(),
			'inject' => new stdClass(), // Nothing...for now
		];
		
		$this->set('schedule', $schedule);
	}
}
