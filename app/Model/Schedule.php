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

		return $this->find('all', [
			'fields' => [
				'Inject.*', 'Schedule.*', 'COUNT(Submission.id) AS submission_count',
			],
			'joins' => [
				[
					'type'  => 'LEFT',
					'table' => 'submissions',
					'alias' => 'Submission',
					'conditions' => [
						'Submission.inject_id = Inject.id',
					],
				],
			],

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

			'group' => [
				'Inject.id',
			],
		]);
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
			'fields' => [
				'Inject.*', 'Schedule.*', 'COUNT(Submission.id) AS submission_count',
			],
			'joins' => [
				[
					'type'  => 'LEFT',
					'table' => 'submissions',
					'alias' => 'Submission',
					'conditions' => [
						'Submission.inject_id = Inject.id',
					],
				],
			],

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
			$rtn[] = new InjectAbstraction($inject);
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
		
		if ( !empty($inject) ) $inject = new InjectAbstraction($inject);
		return $inject;
	}
}
