<?php
App::uses('AppController', 'Controller');
App::uses('InjectAbstraction', 'Lib');

class ScheduleController extends AppController {
	public $uses = ['Config', 'Inject', 'Group', 'Schedule'];

	/**
	 * Before Filter Hook
	 *
	 * Set's the active tab to be staff
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		if ( in_array($this->request->action, ['index', 'api']) ) {
			$this->Auth->protect(env('GROUP_STAFF'));
			$this->set('at_staff', true);
		} else {
			$this->Auth->protect(env('GROUP_ADMINS'));
			$this->set('at_backend', true);
		}
	}

	/**
	 * Overview Page 
	 *
	 * @url /schedule
	 * @url /schedule/index
	 */
	public function index() {
		$bounds = $this->Schedule->getScheduleBounds();

		$this->set('start', $bounds['min']);
		$this->set('end', $bounds['max']);
	}

	/**
	 * Overview API Page
	 *
	 * @url /schedule/api
	 */
	public function api() {
		if (
			$this->request->is('post') &&
			isset($this->request->data['changes']) &&
			is_array($this->request->data['changes'])
		) {
			foreach ( $this->request->data['changes'] AS $c ) {
				$schedule = $this->Schedule->findById($c['id']);
				if ( empty($schedule) ) continue;

				$start = ($schedule['Schedule']['fuzzy'] ? $c['start'] - COMPETITION_START : $c['start']);
				$end = ($schedule['Schedule']['fuzzy'] ? $c['end'] - COMPETITION_START : $c['end']);

				// Bad time - we don't want negatives
				if ( 0 > $start || 0 > $end ) continue;

				$this->Schedule->id = $c['id'];
				$this->Schedule->save([
					'start' => $start,
					'end'   => $end,
				]);
			}

			return $this->ajaxResponse(true);
		}
		$out = ['data' => []];

		$schedules = $this->Schedule->getAllSchedules();
		$bounds = $this->Schedule->getScheduleBounds();

		foreach ( $schedules AS $s ) {
			$out['data'][] = [
				'id'         => $s->getScheduleId(),
				'inject_id'  => $s->getInjectId(),
				'text'       => $s->getTitle().' ('.$s->getGroupName().')',
				'group'      => $s->getGroupName(),
				'start_date' => date('d-m-Y G:i:s', $s->getStart() > 0 ? $s->getStart() : $bounds['min']),
				'start_ts'   => $s->getStart(),
				'end_date'   => date('d-m-Y G:i:s', $s->getEnd() > 0 ? $s->getEnd() : $bounds['max']),
				'end_ts'     => $s->getEnd(),
			];
		}

		return $this->ajaxResponse($out);
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
	 * Create a schedule.
	 *
	 * @url /schedule/create
	 * @url /schedule/create/<sid>
	 */
	public function create($sid=false) {
		if ( $this->request->is('post') ) {
			$create = [];
			$missing = [];
			foreach ( array_keys($this->Schedule->schema()) AS $key ) {
				if ( in_array($key, ['id']) ) continue;

				if ( !isset($this->request->data[$key]) ) {
					$missing[] = $key;
					continue;
				}

				// Fix dependency_id to be NULL if the ID is 0
				if ( $key == 'dependency_id' && $this->request->data['dependency_id'] == 0 ) {
					$this->request->data['dependency_id'] = NULL;
				}

				$create[$key] = $this->request->data[$key];
			}

			if ( empty($missing) ) {
				$this->Schedule->create();
				$this->Schedule->save($create);

				$msg = sprintf('Created schedule #%d', $this->Schedule->id);

				$this->logMessage(
					'schedule',
					$msg,
					[
						'schedule' => $create,
					],
					$sid
				);

				$this->Flash->success($msg.'!');
				return $this->redirect('/schedule/manager');
			} else {
				$this->Flash->danger(sprintf('You are missing %s!', implode(', ', $missing)));
				return $this->redirect('/schedule/create');
			}
		}

		$this->set('injects', $this->Inject->find('all'));
		$this->set('groups', $this->Group->generateTreeList(null, null, null, '--'));

		if ( $sid !== false && is_numeric($sid) ) {
			$schedule = $this->Schedule->findById($sid);
			if ( empty($schedule) ) {
				throw new NotFoundException('Unknown Schedule ID');
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
		} else {
			$this->Flash->danger('Unknown Schedule ID');
		}

		return $this->redirect('/schedule/manager');
	}

	/**
	 * Edit a schedule.
	 *
	 * @url /schedule/edit/<sid>
	 */
	public function edit($sid) {
		$schedule = $this->Schedule->findById($sid);
		if ( empty($schedule) ) {
			throw new NotFoundException('Unknown Schedule ID');
		}

		if ( $this->request->is('post') ) {
			$this->Schedule->id = $sid;

			// Fix dependency_id to be NULL if the ID is 0
			if ( isset($this->request->data['dependency_id']) && $this->request->data['dependency_id'] == 0 ) {
				$this->request->data['dependency_id'] = NULL;
			}

			$update = [];
			foreach ( $schedule['Schedule'] AS $k => $v ) {
				if ( !isset($this->request->data[$k]) ) continue;
				if ( $this->request->data[$k] == $v ) continue;

				$update[$k] = $this->request->data[$k];
			}

			if ( !empty($update) ) {
				$this->Schedule->save($update);

				$msg = sprintf('Edited schedule #%d', $sid);

				$this->logMessage(
					'schedule',
					$msg,
					[
						'old_schedule' => $schedule['Schedule'],
						'new_schedule' => $this->request->data,
						'delta'        => $update,
					],
					$sid
				);

				$this->Flash->success($msg.'!');
			} else {
				$this->Flash->danger('There are no changes to save');
			}

			return $this->redirect('/schedule/manager');
		}

		// Load + setup the InjectStyler helper
		$this->helpers[] = 'InjectStyler';
		$this->helpers['InjectStyler'] = [
			'types'  => $this->Config->getInjectTypes(),
			'inject' => new stdClass(), // Nothing...for now
		];

		$this->set('injects', $this->Inject->find('all'));
		$this->set('groups', $this->Group->generateTreeList(null, null, null, '--'));
		$this->set('schedule', $schedule);
	}

	/**
	 * Delete a schedule. SPOOKY
	 *
	 * @url /schedule/delete/<sid>
	 */
	public function delete($sid) {
		$schedule = $this->Schedule->findById($sid);
		if ( empty($schedule) ) {
			throw new NotFoundException('Unknown Schedule ID');
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

		// Load + setup the InjectStyler helper
		$this->helpers[] = 'InjectStyler';
		$this->helpers['InjectStyler'] = [
			'types'  => $this->Config->getInjectTypes(),
			'inject' => new stdClass(), // Nothing...for now
		];

		$this->set('schedule', new InjectAbstraction($schedule, 0));
	}
}
