<?php
App::uses('Component', 'Controller');
App::uses('Security', 'Utility');

class AuthComponent extends Component {
	public $components = ['Cookie', 'Session'];
	public $uses = ['User'];

	/**
	 * String to prefix all our
	 * session keys with.
	 */
	const SESSION_PREFIX = 'auth';

	/**
	 * AuthComponent Initialize Hook
	 * 
	 * Auto logs out the user if their account has
	 * expired.  Also updates their last activity session time.
	 */
	public function initialize(Controller $controller) {
		// If we're logged in, make sure we haven't expired
		if ( $this->loggedIn() && $this->_isExpired($this->user('expiration')) ) {
			$this->logout();
		}

		if ( $this->loggedIn() ) {
			$this->Session->write(self::SESSION_PREFIX.'.last_activity', time());
		}
	}

	/**
	 * Checks if a timestamp has passed ('expired')
	 *
	 */
	private function _isExpired($expiration) {
		return ($expiration > 0) ? (time() > $expiration) : false;
	}

	/**
	 * Login
	 *
	 * @param $username The username of the account
	 * @param $password The password of the account
	 * @return boolean If the login was successful
	 */
	public function login($username, $password) {
		$UserModel = ClassRegistry::init('User');
		$user = $UserModel->findByUsername($username);

		// Does the user exist?
		if ( empty($user) ) return false;

		// Do we have the right password?
		$actual_password = $user['User']['password'];
		if ( Security::hash($password, 'blowfish', $actual_password) !== $actual_password ) return false;

		// Is the user active?
		if ( $user['User']['active'] == 0 ) return false;

		// Is the user expired?
		if ( $this->_isExpired($user['User']['expiration']) ) return false;

		// Save the data
		unset($user['User']['password']);
		$this->Session->write(self::SESSION_PREFIX, $user);
		$this->Session->write(self::SESSION_PREFIX.'.last_activity', time());

		return true;
	}

	/**
	 * Logout
	 *
	 * @return void
	 */
	public function logout() {
		$this->Session->destroy();
	}

	/**
	 * Emulates a user account
	 *
	 * Emulation will change the current session's user to the emulated user
	 * account. The current session will be saved, however.
	 * 
	 * @param $username The username of the account you are emulating
	 */
	public function emulate($username) {
		$UserModel = ClassRegistry::init('User');
		$user = $UserModel->findByUsername($username);

		if ( empty($user) ) {
			throw new RuntimeException('Unknown username!');
		}

		$oldSession = $this->Session->consume(self::SESSION_PREFIX);
		$this->Session->write(self::SESSION_PREFIX, $user);
		$this->Session->write([
			self::SESSION_PREFIX.'.last_activity' => time(),
			self::SESSION_PREFIX.'.oldSession'    => $oldSession,
			self::SESSION_PREFIX.'.emulating'     => true,
		]);
	}

	/**
	 * Ends an emulation session
	 *
	 * Kills the current session, and restores the previously saved session.
	 */
	public function emulateExit() {
		if ( $this->item('emulating') != true ) {
			throw new RuntimeException('A valid emulation session is not present!');
		}

		$newSession = $this->Session->comsume(self::SESSION_PREFIX.'.oldSession');
		$this->Session->destroy();
		
		$this->Session->write(self::SESSION_PREFIX, $newSession);
		$this->Session->write(self::SESSION_PREFIX.'.last_activity', time());
	}

	/**
	 * Logged In
	 *
	 * @return boolean If the user is logged in
	 */
	public function loggedIn() {
		return ($this->Session->check('auth'));
	}

	/**
	 * User Information Accessor
	 *
	 * @param string The key of the item you wish to access (optional)
	 * @return mixed The value of the item you are accessing
	 */
	public function user($item='') {
		$key = 'User'.(empty($item) ? '' : '.'.$item);
		return $this->item($key);
	}

	/**
	 * Group Information Accessor
	 *
	 * @param string The key of the item you wish to access (optional)
	 * @return mixed The value of the item you are accessing
	 */
	public function group($item='') {
		$key = 'Group'.(empty($item) ? '' : '.'.$item);
		return $this->item($key);
	}

	/**
	 * Item Accessor
	 *
	 * @param string The key of the item you wish to access (optional)
	 * @return mixed The value of the item you are accessing
	 */
	public function item($item) {
		$key = implode('.', [self::SESSION_PREFIX, $item]);
		return ($this->loggedIn() ? $this->Session->read($key) : '');
	}
}