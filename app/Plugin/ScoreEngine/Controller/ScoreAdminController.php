<?php
App::uses('ScoreEngineAppController', 'ScoreEngine.Controller');

class ScoreAdminController extends ScoreEngineAppController {
	public $uses = ['ScoreEngine.Team', 'ScoreEngine.Check','ScoreEngine.Service', 'ScoreEngine.TeamService'];

	public function beforeFilter() {
		parent::beforeFilter();

		// Enforce staff only
		$this->Auth->protect(env('GROUP_STAFF'));

		// Set the active menu item
		$this->set('at_staff', true);
	}

	/**
	 * ScoreEngine Admin Index Page
	 *
	 * @url /admin/scoreengine
	 * @url /score_engine/admin
	 * @url /admin/scoreengine/index
	 * @url /score_engine/admin/index
	 */
	public function index() {
		$this->set('teams', $this->Team->find('all'));
	}

	/**
	 * View Team Page
	 *
	 * @url /admin/scoreengine/team/<id>
	 * @url /score_engine/admin/team/<id>
	 */
	public function team($id=false) {
		$team = $this->Team->findById($id);
		if ( empty ($team) ) {
			throw new NotFoundException('Unknown team');
		}

		$tid = $team['Team']['id'];
		$this->set('team', $team);
		$this->set('data', $this->Check->getTeamChecks($tid));
		$this->set('latest', $this->Check->getLastTeamCheck($tid));
	}

	/**
	 * View Team Service Page
	 *
	 * @url /admin/scoreengine/service/<tid>/<sid>
	 * @url /score_engine/admin/config/<tid>/<sid>
	 */
	public function service($tid=false, $sid=false) {
		$team = $this->Team->findById($tid);
		if ( empty($team) ) {
			throw new NotFoundException('Unknown team');
		}
		if ( $sid === false || !is_numeric($sid) ) {
			throw new NotFoundException('Unknown service');
		}

		$this->set('team', $team);
		$this->set('data', $this->Check->find('all', [
			'conditions' => [
				'team_id' => $team['Team']['id'],
				'service_id' => $sid
			],
			'limit' => 20,
			'order' => 'time DESC',
		]));
	}

	/**
	 * Team Config Page
	 *
	 * @url /admin/scoreengine/config/<id>
	 * @url /score_engine/admin/config/<id>
	 */
	public function config($id=false) {
		$team = $this->Team->findById($id);
		if ( empty($team) ) {
			throw new NotFoundException('Unknown team');
		}

		$this->set('team', $team);
		$data = $this->TeamService->getData($team['Team']['id']);

		$updateOpt = function($id, $value) use(&$data) {
			foreach ( $data AS $group => &$options ) {
				foreach ( $options AS &$opt ) {
					if ( $opt['id'] == $id ) {
						$opt['value'] = $value;
					}
				}
			}

			return false;
		};

		if ( $this->request->is('post') ) {
			foreach ( $this->request->data AS $opt => $value ) {
				$opt = (int) str_replace('opt', '', $opt);
				if ( $opt < 0 || !is_numeric($opt) ) continue;

				$this->TeamService->updateConfig($opt, $value);
				$updateOpt($opt, $value);
			}

			// Message
			$this->Flash->success('Updated Score Engine Config!');
		}

		$this->set('data', $data);
	}

	/**
	 * Export Grades
	 *
	 * @url /admin/scoreengine/export
	 * @url /score_engine/admin/export
	 */
	public function export() {
		$teams = $this->Team->find('all');
		$services = $this->Service->find('all');
		$out = [];
		$chkOrder = [];

		// Helper function to grab the right check
		$grabCheckById = function($checks, $id) {
			foreach ( $checks AS $c ) {
				if ( $c['Service']['id'] == $id ) {
					return $c;
				}
			}
		};

		// Build the header
		$header = ['team_number'];
		foreach ( $services AS $s ) {
			$header[] = '"'.$s['Service']['name'].'"';
			$chkOrder[] = $s['Service']['id'];
		}
		$out[] = implode(',', $header);

		// Parse team scores
		foreach ( $teams AS $t ) {
			$tid = $t['Team']['id'];
			$line = [$tid];

			$checks = $this->Check->getTeamChecks($tid);

			foreach ( $chkOrder AS $id ) {
				$chk = $grabCheckById($checks, $id);
				$line[] = isset($chk['Check']['total_passed']) ? $chk['Check']['total_passed'] : 0;
			}

			$out[] = implode(',', $line);
		}

		return $this->ajaxResponse(implode(PHP_EOL, $out));
	}
}