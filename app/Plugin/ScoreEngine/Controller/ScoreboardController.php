<?php
App::uses('ScoreEngineAppController', 'ScoreEngine.Controller');

class ScoreboardController extends ScoreEngineAppController {
	public $helpers = ['ScoreEngine.EngineOutputter'];
	public $uses = ['ScoreEngine.Check', 'ScoreEngine.Service', 'ScoreEngine.Team', 'ScoreEngine.Round'];

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
		$this->set('round', $this->Round->getLastRound());
		$this->set('at_scoreboard', true);
	}
}