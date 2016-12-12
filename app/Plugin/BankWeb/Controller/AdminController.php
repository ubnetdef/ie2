<?php
App::uses('BankWebAppController', 'BankWeb.Controller');

class AdminController extends BankWebAppController {

	public function beforeFilter() {
		parent::beforeFilter();

		// Set the active menu item
		$this->set('at_backend', true);
	}

	/**
	 * BankWEB Admin Index Page
	 *
	 * @url /admin/bank
	 * @url /bank_web/admin
	 * @url /admin/bank/index
	 * @url /bank_web/admin/index
	 */
	public function index() {
	}

	/**
	 * BankWEB Admin API
	 *
	 * @url /admin/bank/api/<id>
	 * @url /bank_web/admin/api/<id>
	 */
	public function api($id=false) {
	}

	/**
	 * BankWEB Credentials Save
	 *
	 * @url /admin/bank/credentials
	 * @url /bank_web/admin/credentials
	 */
	public function credentials() {
	}
}