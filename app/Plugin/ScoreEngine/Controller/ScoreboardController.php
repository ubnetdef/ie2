<?php
App::uses('ScoreEngineAppController', 'ScoreEngine.Controller');

class ScoreboardController extends ScoreEngineAppController {
	public $helpers = ['ScoreEngine.EngineOutputter'];
	public $uses = [
		'ScoreEngine.Check', 'ScoreEngine.Service', 'ScoreEngine.Team', 'ScoreEngine.Round',
		'Config', 'Submission', 'Group'
	];

	public function beforeRender() {
		parent::beforeRender();

		// Setup the ScoreEngine EngineOutputter
		$this->helpers['ScoreEngine.EngineOutputter']['data'] = $this->Check->getChecksTable(
			$this->Team->findAllByEnabled(true),
			$this->Service->findAllByEnabled(true)
		);
	}

	/**
	 * ScoreBoard Overview Page
	 *
	 * @url /scoreboard
	 * @url /score_engine/scoreboard
	 * @url /score_engine/scoreboard/index
	 */
	public function index() {
		$sponsors = $this->Config->getKey('competition.sponsors');
		if ( !empty($sponsors) ) {
			$sponsors = json_decode($sponsors, true);
		}

		$this->set('sponsors', $sponsors);
		$this->set('at_scoreboard', true);
	}

	/**
	 * ScoreBoard Overview Page
	 *
	 * @url /scoreboard/overview
	 * @url /score_engine/scoreboard/overview
	 */
	public function overview() {
		// Require staff
		$this->Auth->protect(env('GROUP_STAFF'));

		// Generate team mappings
		$group_names = $this->Group->find('all', [
			'conditions' => [
				'Group.team_number IS NOT NULL',
			],
		]);
		$team_mappings = [];
		foreach ( $group_names AS $g ) {
			$team_mappings[$g['Group']['team_number']] = $g['Group']['name'];
		}

		// Grab the check overview
		$overview = $this->Check->find('all', [
			'fields' => [
				'Check.total_passed', 'Check.total', 'Team.id',
			],
			'group' => [
				'Team.id',
			],
			'order' => [
				'Check.total_passed DESC',
			],
		]);

		// Grab the grade overview
		$grades = $this->Submission->getGrades($this->Group->getChildren(env('GROUP_BLUE')));
		$grade_team_mappings = [];
		foreach ( $grades AS $g ) {
			$grade_team_mappings[$g['Group']['team_number']] = $g['Submission']['total_grade'];
		}

		$this->set('at_staff', true);
		$this->set('round', $this->Round->getLastRound());
		$this->set('overview', $overview);
		$this->set('grades', $grades);
		$this->set('grade_team_mappings', $grade_team_mappings);
		$this->set('team_mappings', $team_mappings);
	}

	/**
	 * ScoreBoard API Content
	 *
	 * @url /scoreboard/api
	 * @url /score_engine/scoreboard/api
	 */
	public function api() {
		$this->layout = 'ajax';
		$this->set('round', $this->Round->getLastRound());
	}
}