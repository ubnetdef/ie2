<?php
App::uses('AppModel', 'Model');

/**
 * Log Model
 *
 */
class Log extends AppModel {
	public $belongsTo = ['User'];
	public $recursive = 2;

	/**
	 * Get Recent Logs
	 *
	 * @param $amt The amount of logs
	 * @return array The logs
	 */
	public function getRecent($amt=20) {
		return $this->find('all', [
			'fields' => [
				'Log.id', 'Log.time', 'Log.type', 'Log.data',
				'Log.ip', 'Log.message', 'User.username', 'User.group_id',
			],
			'contain' => [
				'User' => [
					'Group.name',
				]
			],
			'limit' => $amt,
			'order' => ['Log.id DESC']
		]);
	}
}
