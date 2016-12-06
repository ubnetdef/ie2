<?php
App::uses('AppController', 'Controller');

class BankWebAppController extends AppController {
	public $components = ['BankWeb.BankApi'];
	public $uses = ['BankWeb.AccountMapping'];

	public function beforeFilter() {
		parent::beforeFilter();

		// Grab the account + set the credentials for the API
		$account = $this->AccountMapping->getAccount($this->Auth->user('id'), $this->Auth->item('groups'));
		$this->BankApi->setCredentials($account['AccountMapping']['username'], $account['AccountMapping']['password']);

		// Ensure logins
		$this->Auth->protect();

		// Set the active menu item
		$this->set('at_bank', true);
	}
}