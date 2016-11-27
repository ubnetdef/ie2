<?php
App::uses('AppController', 'Controller');

class UserController extends AppController {
	public $uses = ['Config', 'Group', 'User'];

	/**
	 * User Login Page 
	 *
	 * @url /user/login
	 */
	public function login() {
		if ( $this->Auth->loggedIn() ) return $this->redirect('/');
		
		$username = '';

		if ( $this->request->is('post') ) {
			$username = $this->request->data['username'];
			$password = $this->request->data['password'];

			if ( $this->Auth->login($username, $password) ) {
				return $this->redirect($this->Auth->redirectURL());
			}

			$this->Flash->danger('Unknown username or password!');
		}

		$this->set('at_login', true);
		$this->set('username', $username);
	}

	/**
	 * User Logout Page 
	 *
	 * @url /user/logout
	 */
	public function logout() {
		$this->Auth->protect();
		$this->Auth->logout();

		return $this->redirect('/');
	}

	/**
	 * User Profile Page 
	 *
	 * @url /user/profile
	 */
	public function profile() {
		$this->Auth->protect();

		if ( $this->request->is('post') ) {
			// Update Password
			$old_password  = $this->request->data['old_password'];
			$new_password  = $this->request->data['new_password'];
			$new_password2 = $this->request->data['new_password2'];

			if ( $new_password != $new_password2 ) {
				$this->Flash->danger('Your new password does not match.');
			} else {
				// Fetch the current password
				$user = $this->User->findById($this->Auth->user('id'));
				$cur_password = $user['User']['password'];

				if ( Security::hash($old_password, 'blowfish', $cur_password) === $cur_password ) {
					// Update password
					$this->User->id = $this->Auth->user('id');
					$this->User->save([
						'password' => $new_password,
					]);

					// Log it
					//$this->logMessage('USER', 'User just updated his/her password');

					// Message it
					$this->Flash->success('Profile updated!');
				} else {
					$this->Flash->danger('You entered the wrong current password.');
				}
			}
		}

		$this->set('at_profile', true);
		$this->set('group_path', $this->Group->getGroupPath($this->Auth->group('id')));
	}

	/**
	 * User Emulation Clear Page 
	 *
	 * @url /user/emulate_clear
	 */
	public function emulate_clear() {
		$this->Auth->protect();

		if ( $this->Auth->item('emulating') == true ) {
			$this->Auth->emulateExit();
		}

		return $this->redirect('/');
	}
}
