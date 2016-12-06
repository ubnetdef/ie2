<?php
App::uses('ScoreEngineAppController', 'ScoreEngine.Controller');

class TeamController extends ScoreEngineAppController {
	public $uses = ['ScoreEngine.Check'];

	/**
	 * The current user's team number
	 */
	private $team;

	/**
	 * Before Filter
	 *
	 * Locks the page to blue teams' group,
	 * as well as sets up the user's team number
	 */
	public function beforeFilter() {
		// Only blue teams may access
		$this->Auth->protect(env('GROUP_BLUE'));

		// Set the team number
		$this->team = $this->Auth->group('team_number');
	}

	/**
	 * Team Overview Page
	 *
	 * @url /team
	 * @url /score_engine/team
	 * @url /score_engine/team/index
	 */
	public function index() {
		$this->set('data', $this->Check->getTeamChecks($this->team));
		$this->set('latest', $this->Check->getLastTeamCheck($this->team));
	}

	/**
	 * Service Overview Page
	 *
	 * @url /team/service/<sid>
	 * @url /score_engine/team/service/<sid>
	 */
	public function service($sid=false) {
		if ( $sid === false || !is_numeric($sid) ) {
			throw new NotFoundException('Unknown service!');
		}

		$this->set('data', $this->Check->find('all', [
			'conditions' => [
				'team_id' => $this->team,
				'service_id' => $sid
			],
			'limit' => 20,
			'order' => 'time DESC',
		]));
	}
}