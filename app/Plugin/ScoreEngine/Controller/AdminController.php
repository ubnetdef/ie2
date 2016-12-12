<?php
App::uses('ScoreEngineAppController', 'ScoreEngine.Controller');

class AdminController extends ScoreEngineAppController {

	public function beforeFilter() {
		parent::beforeFilter();

		// Set the active menu item
		$this->set('at_backend', true);
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
	}

	/**
	 * View Team Page
	 *
	 * @url /admin/scoreengine/team/<id>
	 * @url /score_engine/admin/team/<id>
	 */
	public function team($id=false) {
	}

	/**
	 * View Team Service Page
	 *
	 * @url /admin/scoreengine/service/<tid>/<sid>
	 * @url /score_engine/admin/config/<tid>/<sid>
	 */
	public function service($tid=false, $sid=false) {
	}

	/**
	 * Team Config Page
	 *
	 * @url /admin/scoreengine/config/<id>
	 * @url /score_engine/admin/config/<id>
	 */
	public function config($id=false) {
	}
}