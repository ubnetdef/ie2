<?php
App::uses('AppController', 'Controller');

class UserController extends AppController {
	public $uses = ['Config'];

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

	public function logout() {
		$this->Auth->logout();

		return $this->redirect('/');
	}
}
