<?php
App::uses('ScoreEngineAppModel', 'ScoreEngine.Model');

class TeamService extends ScoreEngineAppModel {
	public $useTable = 'team_service';
	public $belongsTo = ['ScoreEngine.Team', 'ScoreEngine.Service'];
	public $recursive = 1;

	public function getData($tid) {
		$data = $this->find('all', [
			'fields' => [
				'TeamService.id', 'TeamService.key', 'TeamService.value',
				'TeamService.edit', 'Service.name', 'Service.id',
			],

			'conditions' => [
				'Team.id' => $tid,
			],
		]);

		$rtn = [];
		foreach ( $data AS $d ) {
			if ( !isset($rtn[$d['Service']['name']]) ) {
				$rtn[$d['Service']['name']] = [];
			}

			$rtn[$d['Service']['name']][] = $d['TeamService'];
		}

		return $rtn;
	}

	public function getConfig($tid, $sid, $key=false) {
		$conditions = [
			'Team.id'   => $tid,
			'Service.id' => $sid,
		];

		if ( $key !== false ) {
			$conditions['TeamService.key'] = $key;
		}

		return $this->find('all', [
			'conditions' => $conditions,
		]);
	}

	public function updateConfig($id, $value) {
		$this->id = $id;
		$this->save([
			'value' => $value,
		]);
	}
}