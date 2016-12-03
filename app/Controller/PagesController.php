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

	/**
	 * Announcement Read Endpoint 
	 *
	 * @url /pages/announcement_read
	 */
	public function announcement_read($aid=false) {
		if ( $aid == false || !is_numeric($aid) ) return $this->ajaxResponse(null);

		$read = $this->Session->consume('read_announcements');
		$read[] = $aid;
		$this->Session->write('read_announcements', $read);
		
		return $this->ajaxResponse(null);
	}
}
