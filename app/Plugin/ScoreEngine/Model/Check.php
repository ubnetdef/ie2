<?php
App::uses('ScoreEngineAppModel', 'ScoreEngine.Model');

class Check extends ScoreEngineAppModel {
	public $belongsTo = ['ScoreEngine.Service', 'ScoreEngine.Team'];
	public $recursive = 1;

	public $virtualFields = [
		'total_passed' => 'SUM(Check.passed = 1)',
		'total'        => 'COUNT(Check.passed)',
	];

	public function getChecksTable($teams, $services) {
		$rtn = [];

		$enabled_services = [];
		foreach ( $services AS $s ) {
			$enabled_services[] = $s['Service']['id'];
		}

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
				'Check.round = (SELECT MAX(number) FROM rounds WHERE completed = 1)',
			],

			'order' => [
				'Team.id ASC',
				'Service.id ASC',
			],
		]);

		foreach ( $data AS $d ) {
			if ( !in_array($d['Service']['id'], $enabled_services) ) continue;

			$team_name = $d['Team']['name'];
			$service_name = $d['Service']['name'];

			$rtn[$team_name][$service_name] = ((bool) $d['Check']['passed']);
		}
		return $rtn;
	}

	public function getTeamChecks($tid, $onlyEnabled=true) {
		$conditions = [
			'fields' => [
				'Check.total_passed', 'Check.total',
				'Service.name', 'Service.id', 'Service.enabled',
			],
			'conditions' => [
				'Team.id' => $tid,
			],
			'group' => [
				'Service.id',
			],
		];

		if ( $onlyEnabled ) {
			$conditions['conditions']['Service.enabled'] = true;
		}

		return $this->find('all', $conditions);
	}

	public function getLastTeamCheck($tid) {
		$data = $this->find('all', [
			'fields' => [
				'Service.id', 'Service.name', 'Check.passed',
			],

			'conditions' => [
				'Team.id' => $tid,
				'Service.enabled' => true,
				'Check.round = (SELECT MAX(number) FROM rounds WHERE completed = 1)',
			],
		]);

		$rtn = [];
		foreach ( $data AS $d ) {
			$rtn[$d['Service']['name']] = $d['Check'];
		}

		return $rtn;
	}
}