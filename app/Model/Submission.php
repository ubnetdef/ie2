<?php
App::uses('AppModel', 'Model');

/**
 * Submission Model
 *
 */
class Submission extends AppModel {
	public $belongsTo = ['Inject', 'User', 'Group'];
	public $recursive = 1;

	/**
	 * Get Submissions
	 *
	 * This retrieves the the submissions for
	 * a specific inject done by a group. This
	 * will filter out deleted submissions, too.
	 *
	 * @param $id The inject ID
	 * @param $group The group ID
	 * @return array The submissions done by this group
	 * for this inject
	 */
	public function getSubmissions($id, $group) {
		return $this->find('all', [
			'conditions' => [
				'Inject.id'          => $id,
				'Group.id'           => $group,
				'Submission.deleted' => false,
			],
		]);
	}
}
