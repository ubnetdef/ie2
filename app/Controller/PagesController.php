<?php
App::uses('AppController', 'Controller');

class PagesController extends AppController {
	public $uses = ['Config'];

	/**
	 * Dynamic Index Page 
	 *
	 * @url /
	 * @url /pages/index
	 */
	public function index() {
		$this->set('at_home', true);
		
		$this->set('title', $this->Config->getKey('homepage.title'));
		$this->set('body', $this->Config->getKey('homepage.body'));
	}

	public function scoreboard() {
		$this->set('at_scoreboard', true);
	}
}
