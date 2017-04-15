<?php
App::uses('ScoreEngineAppController', 'ScoreEngine.Controller');

class ScoreboardController extends ScoreEngineAppController {
	public $helpers = ['ScoreEngine.EngineOutputter'];
	public $uses = ['Config', 'ScoreEngine.Check', 'ScoreEngine.Service', 'ScoreEngine.Team', 'ScoreEngine.Round'];

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

		$overview = $this->Check->find('all', [
			'fields' => [
				'Check.total_passed', 'Check.total',
				'Team.name',
			],
			'group' => [
				'Team.id',
			],
		]);

		$this->set('at_staff', true);
		$this->set('round', $this->Round->getLastRound());
		$this->set('overview', $overview);
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