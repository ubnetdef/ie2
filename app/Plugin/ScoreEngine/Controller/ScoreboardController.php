<?php
App::uses('ScoreEngineAppController', 'ScoreEngine.Controller');

class ScoreboardController extends ScoreEngineAppController {
	public $helpers = ['ScoreEngine.EngineOutputter'];
	public $uses = ['ScoreEngine.Check'];

	/**
	 * ScoreBoard Overview Page
	 *
	 * @url /scoreboard
	 * @url /score_engine/scoreboard
	 * @url /score_engine/scoreboard/index
	 */
	public function index() {
		$this->set('at_scoreboard', true);
	}
}