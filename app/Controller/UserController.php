<?php
App::uses('AppController', 'Controller');

class UserController extends AppController {
	public $uses = ['Config'];

	/**
	 * User Login Page 
	 *
	 * @url /user/login
	 */
	public function login() {
		$username = '';

		if ( $this->request->is('post') ) {
			$username = $this->request->data['username'];
			$password = $this->request->data['password'];

			if ( $this->Auth->login($username, $password) ) {
				return $this->redirect('/');
			}

			$this->Flash->danger('Unknown username or password!');
		}

		$this->set('username', $username);
	}

	/**
	 * User Logout Page 
	 *
	 * @url /user/logout
	 */
	public function logout() {
		$this->Auth->logout();

		return $this->redirect('/');
	}

	/**
	 * User Emulation Clear Page 
	 *
	 * @url /user/emulate_clear
	 */
	public function emulate_clear() {
		if ( $this->Auth->item('emulating') == true ) {
			$this->Auth->emulateExit();
		}

		return $this->redirect('/');
	}
}
