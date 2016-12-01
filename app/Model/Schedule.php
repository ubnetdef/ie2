<?php
App::uses('AppModel', 'Model');
App::uses('InjectAbstraction', 'Lib');

/**
 * Schedule Model
 *
 */
class Schedule extends AppModel {
	public $belongsTo = ['Inject'];
	public $recursive = 1;

	/**
	 * Get Active Injects (RAW)
	 *
	 * Okay, this is the monster in the room.
	 * I'm sorry. It'll grab injects based on
	 * if they're active, AND their start time
	 * has passed
	 *
	 * @param $groups The groups to check for injects in
	 * @return array All the active injects
	 */
	public function getInjectsRaw($groups) {
		$now = time();

		$injects = $this->find('all', [
			'conditions' => [
				'Schedule.group_id' => $groups,
				'Schedule.active' => true,
				'OR' => [
					[
						'Schedule.fuzzy' => false,
						'Schedule.start <=' => $now,
					],
					[
						'Schedule.fuzzy' => true,
						'Schedule.start <=' => ($now - COMPETITION_START)
					],
				],
			],

			// Ordering is hard. Sorry.
			// We'll do base ordering on the order.
			// Then start times, then end times that aren't
			// forever.
			'order' => [
				'Schedule.order ASC',
				'Schedule.start ASC',
				'(Schedule.end > 0) DESC',
				'Schedule.end ASC',
			],
		]);

		// Now remove any duplicates
		$cache = [];
		foreach ( $injects AS $k => $v ) {
			$injectID = $v['Inject']['id'];
			$newEnd   = $v['Schedule']['end'];

			if ( !isset($cache[$injectID]) ) {
				$cache[$injectID] = [
					'end' => $newEnd,
					'key' => $k,
				];

				continue;
			}

			$oldEnd = $cache[$injectID]['end'];
			$oldKey = $cache[$injectID]['key'];

			// So we're going to prefer an inject
			// with the latest end time
			if ( ($newEnd == 0 && $oldEnd > 0) || ($oldEnd > 0 && $newEnd > $oldEnd) ) {
				unset($injects[$oldKey]);

				$cache[$injectID] = [
					'end' => $newEnd,
					'key' => $k,
				];
			} else {
				unset($injects[$k]);
			}
		}

		return $injects;
	}

	/**
	 * Get (an) Inject (RAW)
	 *
	 * A little nicer than getInjects...but still we have dragons :(
	 *
	 * @param $id The schedule ID of the inject
	 * @param $groups The groups the current user is in
	 * @param $show_expired [Optional] Still return the inject, even
	 * if it's expired
	 * @return array The inject (if it's active/exists)
	 */
	public function getInjectRaw($id, $groups, $show_expired=false) {
		$conditions = [
			'Schedule.id' => $id,
			'Schedule.group_id' => $groups,
			'Schedule.active' => true,
		];

		if ( !$show_expired ) {
			$now = time();

			$conditions['OR'] = [
				[
					'Schedule.fuzzy' => false,
					'Schedule.start <=' => $now,
				],
				[
					'Schedule.fuzzy' => true,
					'Schedule.start <=' => ($now - COMPETITION_START)
				]
			];
		}

		return $this->find('first', [
			'conditions' => $conditions,
		]);
	}

	/**
	 * Get Active Injects (and wrap them)
	 *
	 * This function uses the raw data from
	 * `getInjectsRaw` and wraps every inject
	 * inside an InjectAbstraction class
	 *
	 * @param $groups The groups to check for injects in
	 * @return array All the active injects
	 */
	public function getInjects($groups) {
		$rtn = [];

		foreach ( $this->getInjectsRaw($groups) AS $inject ) {
			$submissionCount = ClassRegistry::init('Submission')->getCount($inject['Inject']['id'], $groups);
			$rtn[] = new InjectAbstraction($inject, $submissionCount);
		}

		return $rtn;
	}

	/**
	 * Get (an) Inject (and wrap it)
	 *
	 * This function uses the raw data from
	 * `getInjectRaw` and wraps the inject
	 * inside an InjectAbstraction class
	 *
	 * @param $id The schedule ID of the inject
	 * @param $groups The groups the current user is in
	 * @param $show_expired [Optional] Still return the inject, even
	 * if it's expired
	 * @return array The inject (if it's active/exists)
	 */
	public function getInject($id, $groups, $show_expired=false) {
		$inject = $this->getInjectRaw($id, $groups, $show_expired);
		
		if ( !empty($inject) ) {
			$submissionCount = ClassRegistry::init('Submission')->getCount($inject['Inject']['id'], $groups);
			$inject = new InjectAbstraction($inject, $submissionCount);
		}

		return $inject;
	}

	/**
	 * Get all active injects
	 *
	 * @param $groups The groups to check for injects in
	 * @return array All the active injects
	 */
	public function getActiveInjects($groups) {
		$rtn = [];

		foreach ( $this->getInjects($groups) AS $inject ) {
			if ( !$inject->isExpired() ) {
				$rtn[] = $inject;
			}
		}

		return $rtn;
	}

	/**
	 * Get recently expired injects
	 *
	 * This function uses the raw data from
	 * `getInjectsRaw` and wraps every inject
	 * inside an InjectAbstraction class
	 *
	 * @param $groups The groups to check for injects in
	 * @param $howRecent How recent the injects have expired
	 * @return array All the active injects
	 */
	public function getRecentExpired($groups, $howRecent=(90 * 60)) {
		$now = time();
		$nowCS = ($now - COMPETITION_START);

		$data = $this->find('all', [
			'conditions' => [
				'Schedule.active'   => true,
				'Schedule.group_id' => $groups,
				'Schedule.end !='   => 0,
				'OR' => [
					[
						'Schedule.fuzzy'  => true, 
						'Schedule.end <'  => $nowCS,
						'Schedule.end >=' => ($nowCS - $howRecent),
					],
					[
						'Schedule.fuzzy'  => false,
						'Schedule.end <'  => $now,
						'Schedule.end >=' => ($now - $howRecent),
					]
				],
			],
		]);

		$rtn = [];
		foreach ( $data AS $d ) {
			$rtn[] = new InjectAbstraction($d, 0);
		}

		return $rtn;
	}
}
