<?php
App::uses('ScoreEngineAppController', 'ScoreEngine.Controller');

class ScoreboardController extends ScoreEngineAppController {
	public $helpers = ['ScoreEngine.EngineOutputter'];
	public $uses = ['ScoreEngine.Check'];

	public function index() {
		$this->set('at_scoreboard', true);
	}

	public function debug() {
		debug($this->Check->getChecksTable());
	}
}