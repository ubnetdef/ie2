<?php
App::uses('ScoreEngineAppModel', 'ScoreEngine.Model');

class Check extends ScoreEngineAppModel {
	public $belongsTo = ['ScoreEngine.Service', 'ScoreEngine.Team'];
	public $recursive = 1;

	public $virtualFields = [
		'total_passed' => 'SUM(Check.passed = 1)',
		'total'        => 'COUNT(Check.passed)',
	];

	public function getChecks() {
		return $this->find('all', [
			'fields' => [
				'Check.total_passed', 'Check.total',
				'Team.name',
			],
			'group' => [
				'Check.team_id',
			],
			'order' => [
				'Team.id',
			],
		]);
	}

	public function getChecksTable($teams, $services) {
		$rtn = [];
		foreach ( $teams AS $t ) {
			$team_name = $t['Team']['name'];

			$rtn[$team_name] = [];
			foreach ( $services AS $s ) {
				$service_name = $s['Service']['name'];

				$rtn[$team_name][$service_name] = null;
			}
		}

		$data = $this->find('all', [
			'fields' => [
				'Check.passed', 'Team.name', 'Service.name',
				'Service.id',
			],

			'conditions' => [
				// We're going to use the highest number minus one
				'Check.round = (SELECT IF(MAX(round) > 2, MAX(round)-1, 1) FROM checks)',
			],

			'order' => [
				'Team.id ASC',
				'Service.id ASC',
			],
		]);

		foreach ( $data AS $d ) {
			$team_name = $d['Team']['name'];
			$service_name = $d['Service']['name'];

			$rtn[$team_name][$service_name] = ((bool) $d['Check']['passed']);
		}
		return $rtn;
	}

	public function getTeamChecks($tid) {
		return $this->find('all', [
			'fields' => [
				'Check.total_passed', 'Check.total',
				'Service.name', 'Service.id',
			],
			'conditions' => [
				'Team.id' => $tid,
			],
			'group' => [
				'Service.id',
			],
		]);
	}

	public function getLastTeamCheck($tid) {
		$data = $this->find('all', [
			'fields' => [
				'Service.id', 'Service.name', 'Check.passed',
			],

			'conditions' => [
				'Team.id' => $tid,
				'Check.round = (SELECT MAX(round) FROM checks)',
			],
		]);

		$rtn = [];
		foreach ( $data AS $d ) {
			$rtn[$d['Service']['name']] = $d['Check'];
		}

		return $rtn;
	}
}