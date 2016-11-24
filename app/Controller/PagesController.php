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
		$this->set('title', $this->Config->getKey('homepage.title'));
		$this->set('body', $this->Config->getKey('homepage.body'));
	}
}
