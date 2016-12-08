<?php
App::uses('BankWebAppController', 'BankWeb.Controller');

class AccountController extends BankWebAppController {

	public function beforeFilter() {
		parent::beforeFilter();

		// Set the active menu item
		$this->set('at_team', true);
	}

	/**
	 * Account List Page
	 *
	 * @url /bank/account
	 * @url /bank/account/index
	 */
	public function index() {
		$this->set('accounts', $this->BankApi->accounts());
	}

	/**
	 * Account Create
	 *
	 * @url /bank/account/create
	 */
	public function create() {
		if ( !$this->request->is('post') || !isset($_POST['pin']) || !is_numeric($_POST['pin']) ) {
			throw new BadRequestException('Please ensure your PIN is numeric!');
		}

		try {
			$res = $this->BankApi->newAccount($_POST['pin']);
		} catch ( Exception $e ) {
			$this->Flash->danger($e->getMessage());
		}
		
		if ( $res === true ) {
			$this->Flash->success('Created new account!');
		}

		return $this->redirect(['plugin' => 'bank_web', 'controller' => 'account', 'action' => 'index']);
	}

	/**
	 * Transfer Money
	 *
	 * @url /bank/account/transfer/<id>
	 */
	public function transfer($id=false) {
		if ( $id === false || !is_numeric($id) ) {
			return $this->redirect(['plugin' => 'bank_web', 'controller' => 'account', 'action' => 'index']);
		}

		if (
			$this->request->is('post') &&
			isset($this->request->data['srcAcc']) &&
			is_numeric($this->request->data['srcAcc']) &&
			isset($this->request->data['dstAcc']) &&
			is_numeric($this->request->data['dstAcc']) &&
			isset($this->request->data['amount']) &&
			is_numeric($this->request->data['amount']) &&
			isset($this->request->data['pin']) &&
			is_numeric($this->request->data['pin'])
		) {
			try {
				$res = $this->BankApi->transfer($_POST['srcAcc'], $_POST['dstAcc'], $_POST['amount'], $_POST['pin']);
			} catch ( Exception $e ) {
				$this->Flash->danger($e->getMessage());
			}

			if ( $res === true ) {
				$this->Flash->success('Successfully transferred money!');
			}

			return $this->redirect(['plugin' => 'bank_web', 'controller' => 'account', 'action' => 'index']);
		}

		$this->set('acc', htmlentities($id));
	}

	/**
	 * Transactions List
	 *
	 * @url /bank/account/transactions/<id>
	 */
	public function transactions($id=false) {
		if ( $id === false || !is_numeric($id) ) {
			return $this->redirect(['plugin' => 'bank_web', 'controller' => 'account', 'action' => 'index']);
		}

		$this->set('account', htmlentities($id));
		$this->set('logs', $this->BankApi->transfers($id));
	}

	/**
	 * Account PIN Change
	 *
	 * @url /bank/account/pin/<id>
	 */
	public function pin($id=false) {
		if ( $id === false || !is_numeric($id) ) {
			return $this->redirect(['plugin' => 'bank_web', 'controller' => 'account', 'action' => 'index']);
		}

		if (
			$this->request->is('post') &&
			isset($this->request->data['pin']) &&
			is_numeric($this->request->data['pin']) &&
			isset($this->request->data['newpin']) &&
			is_numeric($this->request->data['newpin'])
		) {
			try {
				$res = $this->BankApi->changePin($id, $_POST['pin'], $_POST['newpin']);
			} catch ( Exception $e ) {
				$this->Flash->danger($e->getMessage());
			}

			if ( $res === true ) {
				$this->Flash->success('Successfully updated pin!');
			}

			return $this->redirect(['plugin' => 'bank_web', 'controller' => 'account', 'action' => 'index']);
		}

		$this->set('account', htmlentities($id));
	}
}