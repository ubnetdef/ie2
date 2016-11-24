<?php
App::uses('Component', 'Controller');
App::uses('Security', 'Utility');

class AuthComponent extends Component {
	public $components = ['Cookie', 'Session'];
	public $uses = ['User'];

	const SESSION_PREFIX = 'auth';

	public function initialize(Controller $controller) {
		// If we're logged in, make sure we haven't expired
		if ( $this->loggedIn() && $this->_isExpired($this->user('expiration')) ) {
			$this->logout();
		}

		if ( $this->loggedIn() ) {
			$this->Session->write(self::SESSION_PREFIX.'.last_activity', time());
		}
	}

	private function _isExpired($expiration) {
		return ($expiration > 0) ? (time() > $expiration) : false;
	}

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

	public function logout() {
		$this->Session->destroy();
	}

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

	public function emulateExit() {
		if ( $this->item('emulating') != true ) {
			throw new RuntimeException('A valid emulation session is not present!');
		}

		$newSession = $this->Session->comsume(self::SESSION_PREFIX.'.oldSession');
		$this->Session->destroy();
		
		$this->Session->write(self::SESSION_PREFIX, $newSession);
		$this->Session->write(self::SESSION_PREFIX.'.last_activity', time());
	}

	public function loggedIn() {
		return ($this->Session->check('auth'));
	}

	public function user($item='') {
		$key = 'User'.(empty($item) ? '' : '.'.$item);
		return $this->item($key);
	}

	public function group($item='') {
		$key = 'Group'.(empty($item) ? '' : '.'.$item);
		return $this->item($key);
	}

	public function item($item) {
		$key = implode('.', [self::SESSION_PREFIX, $item]);
		return ($this->loggedIn() ? $this->Session->read($key) : '');
	}
}